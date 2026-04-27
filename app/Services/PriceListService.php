<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;
use function Spatie\LaravelPdf\Support\pdf;

class PriceListService
{
    public function loadCar(int $carStructId): Car
    {
        return Car::with('trims.powertrains.configuration')->where('struct_id', $carStructId)->firstOrFail();
    }

    public function build(Car $car): array
    {
        $trims = $car->trims->values();

        return [
            'car' => $car,
            'trims' => $trims,
            'colorMatrix' => $this->matrix(
                $trims,
                'colors',
                'code'
            ),
            'extraEquipmentPackageMatrix' => $this->matrix(
                $trims,
                'extraEquipmentPackages',
                'code'
            ),
            'groupedEquipment' => $this->group(
                $trims,
                'equipment',
                fn ($item) => $item->images
            ),
            'groupedExtraEquipmentPackages' => $this->group(
                $trims,
                'extraEquipmentPackages',
                fn ($item) => $item->image
            ),
            'interiors' => $this->buildInteriors($trims),
        ];
    }

    public function generatePdf(array $data): string
    {
        Storage::disk('public')->makeDirectory('prislister');

        $fileName = 'prislister/' . Str::slug($data['car']->name) . '.pdf';

        if (Storage::disk('public')->exists($fileName)) {
            Storage::disk('public')->delete($fileName);
        }

        Pdf::view('price-list', $data)
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot
                    ->waitUntilNetworkIdle()
                    ->setChromePath('/usr/bin/chromium')
                    ->setEnvironmentOptions([
                        'CHROME_CONFIG_HOME' => storage_path('app/chrome/.config'),
                    ]);
            })
            ->format(Format::A4)
            ->name('Prisliste - ' . $data['car']->name)
            ->margins(6, 6, 6, 6)
            ->disk('public')
            ->save($fileName);

        return $fileName;
    }

    public function pdf(Car $car)
    {
        $data = $this->build($car);

        return Pdf::view('price-list', $data)
            ->format(Format::A4)
            ->name('Prisliste - ' . $car->name)
            ->margins(6, 6, 6, 6);
    }

    protected function buildInteriors(Collection $trims): Collection
    {
        return $trims
            ->filter(fn ($trim) => $trim->interior)
            ->groupBy(fn ($trim) => $trim->interior->code)
            ->map(function ($group) {
                return [
                    'interior' => $group->first()->interior,
                    'trim_names' => $group->pluck('name')->unique()->values()->all(),
                ];
            })
            ->values();
    }

    protected function group($trims, $relation, $filter)
    {
        return $trims
            ->flatMap(function ($trim) use ($relation, $filter) {
                return $trim->{$relation}
                    ->filter($filter)
                    ->map(function ($item) use ($trim) {
                        $item->trim_names = [$trim->name];
                        return $item;
                    });
            })
            ->groupBy('code')
            ->map(function ($items) {
                $first = $items->first();

                $first->trim_names = $items
                    ->flatMap->trim_names
                    ->unique()
                    ->values()
                    ->all();

                return $first;
            })
            ->sortBy('name')
            ->groupBy('category');
    }

    protected function matrix(
        Collection $trims,
        string $relation,
        string $optionIdentifier = 'id',
        string $priceField = 'suggested_retail_price'
    ): Collection {

        $trimEquipmentCodes = $trims->mapWithKeys(fn ($trim) => [
            $trim->id => $trim->equipment->pluck('code')->all()
        ]);

        $flatOptions = $trims->flatMap(function ($trim) use (
            $relation,
            $optionIdentifier,
            $priceField
        ) {
            $options = $trim->$relation ?? collect();

            return $options->map(function ($option) use (
                $trim,
                $optionIdentifier,
                $priceField
            ) {
                return [
                    'option_id' => $option->$optionIdentifier,
                    'option_obj' => $option,
                    'trim_id'   => $trim->id,
                    'price'     => $option->latestPrice?->$priceField,
                ];
            });
        });

        return $flatOptions
            ->groupBy('option_id')
            ->map(function ($rows) use ($trims, $relation, $trimEquipmentCodes) {

                $option = $rows->first()['option_obj'];

                $rowsByTrim = $rows->keyBy('trim_id');

                $packageCodes = $option->equipment?->pluck('code')->all() ?? [];

                $prices = [];
                $included = [];

                foreach ($trims as $trim) {

                    $row = $rowsByTrim[$trim->id] ?? null;

                    $prices[$trim->id] = $row['price'] ?? null;

                    if (empty($packageCodes)) {
                        $included[$trim->id] = false;
                        continue;
                    }

                    $trimCodes = $trimEquipmentCodes[$trim->id];

                    $included[$trim->id] = !array_diff($packageCodes, $trimCodes);
                }

                return [
                    $relation => $option,
                    'prices' => $prices,
                    'included' => $included,
                ];
            })
            ->values();
    }

}