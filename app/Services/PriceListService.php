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

        $acChargingPercentage = $car->trims
        ->flatMap(fn ($trim) => $trim->powertrains)
        ->first()->technical_specifications->ac_charging_percentage;

        $dcChargingPercentage = $car->trims
        ->flatMap(fn ($trim) => $trim->powertrains)
        ->first()->technical_specifications->dc_charging_percentage;

        return [
            'car' => $car,
            'trims' => $trims,
            'acChargingPercentage' => $acChargingPercentage,
            'dcChargingPercentage' => $dcChargingPercentage,
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
                fn ($item) => $item->images->count() > 0
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
        $fullPath = storage_path('app/public/' . $fileName);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $pdf = Pdf::view('price-list', $data);

        if (app()->environment('production')) {
            $pdf->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot
                    ->waitUntilNetworkIdle()
                    ->setOption('args', [
                        '--no-sandbox',
                        '--disable-font-subpixel-positioning',
                        '--disable-web-security',
                    ])
                    ->setChromePath('/usr/bin/chromium')
                    ->setEnvironmentOptions([
                        'CHROME_CONFIG_HOME' => storage_path('app/chrome/.config'),
                    ]);
            });
        }

        $pdf->format(Format::A4)
            ->name('Prisliste - ' . $data['car']->name)
            ->margins(6, 6, 6, 6)
            ->save($fullPath);

        chmod($fullPath, 0644);

        return $fileName;
    }

    public function pdf(Car $car)
    {
        $data = $this->build($car);

        $pdf = Pdf::view('price-list', $data);

        if (app()->environment('production')) {
            $pdf->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot
                    ->waitUntilNetworkIdle()
                    ->setOption('args', [
                        '--no-sandbox',
                        '--disable-font-subpixel-positioning',
                        '--disable-web-security',
                    ])
                    ->setChromePath('/usr/bin/chromium')
                    ->setEnvironmentOptions([
                        'CHROME_CONFIG_HOME' => storage_path('app/chrome/.config'),
                    ]);
            });
        }

        return $pdf->format(Format::A4)
            ->name('Prisliste - ' . $data['car']->name)
            ->margins(6, 6, 6, 6);
    }

    public function view(Car $car)
    {
        $data = $this->build($car);

        return view('price-list', $data);
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