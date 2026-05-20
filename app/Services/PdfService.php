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
use App\ViewModels\Specifications;

class PdfService
{

    public function generatePdfs(array $data): array
    {
         return [
            'prices' => $this->generatePdf($data, 'prices', '-priser'),
            'accessories' => $this->generatePdf($data, 'accessories', '-tilbehoer'),
            'specfications' => $this->generatePdf($data, 'specifications', '-specifikationer', true),
        ];
    }

    private function generatePdf(array $data, string $view, string $slugSuffix = '', $landscape = false): string
    {
        Storage::disk('private')->makeDirectory('dokumenter');

        $baseSlug = Str::slug($data['car']->name);
        $fileName = "dokumenter/{$baseSlug}{$slugSuffix}.pdf";
        $fullPath = storage_path('app/private/' . $fileName);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $pdf = Pdf::view($view, $data);

        if ($landscape) {
            $pdf->landscape();
        }

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
            ->margins(6, 6, 6, 6)
            ->save($fullPath);

        chmod($fullPath, 0644);

        return $fileName;
    }

    public function pdf(Car $car, $view = 'prices', $landscape = false)
    {
        $data = $this->build($car);

        $pdf = Pdf::view($view, $data);

        if ($landscape) {
            $pdf->landscape();
        }

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
            ->margins(6, 6, 6, 6);
    }

    public function view(Car $car, $view = 'prices')
    {
        $data = $this->build($car);

        return view($view, $data);
    }

    public function loadCar(int $carStructId): Car
    {
        return Car::with(['trims.powertrains.configuration', 'trims.accessories'])->where('struct_id', $carStructId)->firstOrFail();
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

            'accessories' => $this->buildAccessories($trims),

            'specifications' => new Specifications($trims)->build(),
        ];
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

    protected function buildAccessories(Collection $trims): Collection
    {
        return $trims
            ->flatMap(function ($trim) {
                return $trim->accessories->map(function ($accessory) use ($trim) {
                    $accessory->trim_name = $trim->name;
                    return $accessory;
                });
            })
            ->groupBy('struct_id')
            ->map(function ($group) {

                $first = $group->first();

                $first->trim_names = $group
                    ->pluck('trim_name')
                    ->unique()
                    ->values();

                $first->categories = collect(
                    is_array($first->categories)
                        ? $first->categories
                        : json_decode($first->categories, true) ?? []
                )->values();

                return $first;
            })
            ->sortBy('name')
            ->values()
            ->groupBy(function ($accessory) {
                $categories = collect($accessory->categories ?? [])
                    ->filter()
                    ->values();

                if ($categories->isEmpty()) {
                    return 'Ukategoriseret';
                }

                return $categories->first();
            })
            ->sortKeysUsing(function ($a, $b) {

                $specialOrder = [
                    'Diverse' => 1,
                    'Ukategoriseret' => 2,
                ];

                $aRank = $specialOrder[$a] ?? 0;
                $bRank = $specialOrder[$b] ?? 0;

                if ($aRank === 0 && $bRank === 0) {
                    return strcasecmp($a, $b);
                }

                if ($aRank === 0) {
                    return -1;
                }

                if ($bRank === 0) {
                    return 1;
                }

                return $aRank <=> $bRank;
            })
            ->flatMap(function ($group, $category) {
                return $group
                    ->chunk(3)
                    ->map(fn ($chunk) => [
                        'category' => $category,
                        'items' => $chunk->values(),
                    ]);
            })
            ->values()
            ->chunk(3);
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
        string $priceField = 'suggested_retail_price',
        string $priceExVatField = 'retail_price_ex_vat',
        string $campaignPrice = 'campaign_retail_price'
    ): Collection {

        $trimEquipmentCodes = $trims->mapWithKeys(fn ($trim) => [
            $trim->id => $trim->equipment->pluck('code')->all()
        ]);

        $flatOptions = $trims->flatMap(function ($trim) use (
            $relation,
            $optionIdentifier,
            $priceField,
            $priceExVatField,
            $campaignPrice
        ) {
            $options = $trim->$relation ?? collect();

            return $options->map(function ($option) use (
                $trim,
                $optionIdentifier,
                $priceField,
                $priceExVatField,
                $campaignPrice
            ) {
                return [
                    'option_id' => $option->$optionIdentifier,
                    'option_obj' => $option,
                    'trim_id'   => $trim->id,
                    'price'     => $option->latestPrice?->$priceField,
                    'campaignPrice'     => $option->latestPrice?->$campaignPrice,
                    'priceExVat' => $option->latestPrice?->$priceExVatField,

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

                    $prices[$trim->id] = [
                        'price' => $row['price'] ?? null,
                        'priceExVat' => $row['priceExVat'] ?? null,
                        'campaignPrice' => $row['campaignPrice'] ?? null,
                    ];

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