<?php

namespace App\Http\Controllers;

use App\Jobs\SyncCarJob;
use App\Jobs\SyncTrimJob;
use App\Jobs\GeneratePriceListJob;
use App\Models\Car;
use App\Requests\ProductRequest;
use App\Services\ComplianceTextService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Inertia\Inertia;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use App\Services\PriceListService;
use function Spatie\LaravelPdf\Support\pdf;

class CarController extends Controller
{
    public function __construct(
        private PriceListService $priceListService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('cars/index', [
            'cars' => Car::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // return Car::where('web_id', $id)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function sync($id)
    {
        $jobs = [
            new SyncCarJob($id),
        ];

        $productVariantIds = (new ProductRequest)->getProductVariantsIdsByProductId($id);

        foreach ($productVariantIds as $variantId) {
            $jobs[] = new SyncTrimJob($variantId);
        }

        $jobs[] = new GeneratePriceListJob($id);

        $batch = Bus::batch($jobs)
            ->onQueue('pim')
            ->dispatch();

        return redirect()->back()->with([
            'batch_id' => $batch->id,
            'message' => 'Sync started',
        ]);
    }

    public function priceList($id)
    {
        $car = $this->priceListService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->priceListService
            ->pdf($car);
    }

    public function priceListView($id)
    {
        $car = $this->priceListService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->priceListService
            ->view($car);
    }

    public function priceListAccessories($id)
    {
        $car = $this->priceListService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->priceListService
            ->pdf($car, 'price-list-accessories');
    }

    public function priceListAccessoriesView($id)
    {
        $car = $this->priceListService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->priceListService
            ->view($car, 'price-list-accessories');
    }

    public function specifications($id)
    {
        $car = $this->priceListService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->priceListService
            ->pdf($car, 'specifications', true);
    }

    public function specificationsView($id)
    {
        $car = $this->priceListService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->priceListService
            ->view($car, 'specifications');
    }


    // WIP

    public function specificationsDownload()
    {
        $car = Car::with([
            'trims.powertrains',
        ])->findOrFail(1);

        $trims = $car->trims->values();

        $sections = new Specifications($trims)->sections();

        return pdf('specifications', compact('car', 'trims', 'sections'))
            ->landscape()
            ->format(Format::A4)
            ->name('Specifications')
            ->margins(5, 5, 5, 5)
            ->download();
    }

    public function priceListDownload($id)
    {
        $car = Car::with([
            'trims.extraEquipmentPackages.latestPrice',
            'trims.colors.latestPrice',
        ])->findOrFail($id);

        $trims = $car->trims->values();

        $colorMatrix = $this->matrix(
            $trims,
            'colors',
            'code'
        );

        $extraEquipmentPackageMatrix = $this->matrix(
            $trims,
            'extraEquipmentPackages',
            'code'
        );

        $groupedEquipment = $this->group(
            $trims,
            'equipment',
            fn ($item) => $item->images
        );

        $groupedExtraEquipmentPackages = $this->group(
            $trims,
            'extraEquipmentPackages',
            fn ($item) => $item->image
        );

        $interiors = $trims
            ->filter->interior
            ->groupBy(fn ($trim) => $trim->interior->code)
            ->map(function ($group) {
                $interior = $group->first()->interior;

                $interior->trim_names = $group
                    ->pluck('name')
                    ->unique()
                    ->values()
                    ->all();

                return $interior;
            })
            ->values();

        return pdf('price-list', compact('car', 'trims', 'colorMatrix', 'extraEquipmentPackageMatrix', 'groupedEquipment', 'groupedExtraEquipmentPackages', 'interiors'))
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->waitUntilNetworkIdle();
                $browsershot->setChromePath('/usr/bin/chromium');
                $browsershot->setEnvironmentOptions([
                    'CHROME_CONFIG_HOME' => storage_path('app/chrome/.config'),
                ]);
            })
            ->format(Format::A4)
            ->name('Prisliste - '.$car->name)
            ->margins(6, 6, 6, 6)
            ->download();
    }


    private function group($trims, $relation, $filter)
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

    private function matrix(
        Collection $trims,
        string $relation,
        string $optionIdentifier = 'id',
        string $priceField = 'suggested_retail_price'
    ): Collection {

        $trimEquipmentCodes = $trims->mapWithKeys(fn ($trim) => [
            $trim->id => $trim->equipment->pluck('code')->all(),
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
                    'trim_id' => $trim->id,
                    'price' => $option->latestPrice?->$priceField,
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

                    $included[$trim->id] = ! array_diff($packageCodes, $trimCodes);
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
