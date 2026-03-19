<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use Inertia\Inertia;
use App\Jobs\SyncCarJob;
use App\Jobs\SyncTrimJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use App\Requests\ProductRequest;
use App\Services\ComplianceTextService;
use function Spatie\LaravelPdf\Support\pdf;
use Spatie\LaravelPdf\Enums\Format;
use Illuminate\Support\Collection;
use App\ViewModels\Specifications;

class CarController extends Controller
{
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
        //return Car::where('web_id', $id)->firstOrFail();
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

        $batch = Bus::batch($jobs)
            ->onQueue('pim')
            ->dispatch();

        return response()->json([
            'batch_id' => $batch->id,
            'message' => 'Sync started',
        ]);
    }

    public function specifications()
    {
        $car = Car::with([
            'trims.powertrains'
        ])->findOrFail(1);

        $trims = $car->trims->values(); 

        $sections = new Specifications($trims)->sections();

        return view('specifications', compact('car', 'trims', 'sections'));
    }

    public function specificationsDownload()
    {
        $car = Car::findOrFail(1);

        $trims = $car->trims->values(); 

        return pdf('specifications', compact('car', 'trims'))
        ->landscape()
        ->format(Format::A4)
        ->name('Specifications')
        ->margins(5, 5, 5, 5)
        ->download();
    }

    public function priceList()
    {
        $car = Car::with([
            'trims.extraEquipmentPackages.latestPrice',
            'trims.colors.latestPrice'
        ])->findOrFail(1);

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
            ->groupBy(fn($trim) => $trim->interior->code)
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

        $complianceService = app(ComplianceTextService::class);

        $complianceTexts = [
            'price' => $complianceService->getForCar($car, 'price'),
            'consumption' => $complianceService->getForGlobal('consumption'),
            'changes' => $complianceService->getForGlobal('changes'),
        ];

        return view('price-list', compact('car', 'trims', 'complianceTexts', 'colorMatrix',  'extraEquipmentPackageMatrix', 'groupedEquipment', 'groupedExtraEquipmentPackages', 'interiors'));
    }

    public function priceListDownload()
    {

        $car = Car::with([
            'trims.equipment',
            'trims.powertrains.configuration',
            'trims.colors.prices',
            'trims.extraEquipmentPackages.prices',
            'trims.extraEquipmentPackages.equipment'
        ])->findOrFail(1);

        $colorMatrix = OptionMatrixBuilder::build($car->trims, 'colors', 'code');
        $extraEquipmentPackageMatrix = OptionMatrixBuilder::build($car->trims, 'extraEquipmentPackages','code');

        $complianceTexts = [
            'price' => app(ComplianceTextService::class)->getForCar($car, 'price'),
            'consumption' => app(ComplianceTextService::class)->getForGlobal('consumption'),
            'changes' => app(ComplianceTextService::class)->getForGlobal('changes'),
        ];

        return pdf('price-list', compact('car', 'complianceTexts', 'colorMatrix',  'extraEquipmentPackageMatrix'))
        ->format(Format::A4)
        ->name('Prisliste')
        ->margins(6, 6, 6, 6)
        ->download();
    }

    public function priceListAccessories()
    {

        $car = Car::with([
            'trims.accessories'
        ])->findOrFail(1);

        $accessories = $car->trims
            ->flatMap(function ($trim) {
                return $trim->accessories->map(function ($accessory) use ($trim) {
                    $accessory->trim_name = $trim->name;
                    return $accessory;
                });
            })
            ->groupBy('struct_id')
            ->map(function ($group) {
                $accessory = $group->first();
                $accessory->trim_names = $group->pluck('trim_name')->unique()->values();
                return $accessory;
            })
            ->sortBy('name')
            ->values();

            $groupedAccessories = collect();

            foreach ($accessories as $accessory) {
                $categories = is_array($accessory->categories) 
                    ? $accessory->categories 
                    : json_decode($accessory->categories, true);

                foreach ($categories as $category) {
                    if (! $groupedAccessories->has($category)) {
                        $groupedAccessories[$category] = collect();
                    }

                    $groupedAccessories[$category]->push($accessory);
                }
            }

        $rows = collect();

        foreach ($groupedAccessories as $category => $accessories) {
            foreach ($accessories->chunk(3) as $row) {
                $rows->push([
                    'category' => $category,
                    'items' => $row
                ]);
            }
        }

        $pages = $rows->chunk(3);

        $complianceTexts = [
            'price' => app(ComplianceTextService::class)->getForCar($car, 'price'),
            'consumption' => app(ComplianceTextService::class)->getForGlobal('consumption'),
            'changes' => app(ComplianceTextService::class)->getForGlobal('changes'),
        ];

        return view('price-list-accessories', compact('car', 'complianceTexts', 'pages'));
    }

    public function priceListAccessoriesDownload()
    {

        $car = Car::with([
            'trims.accessories'
        ])->findOrFail(1);

        $accessories = $car->trims
            ->flatMap(function ($trim) {
                return $trim->accessories->map(function ($accessory) use ($trim) {
                    $accessory->trim_name = $trim->name;
                    return $accessory;
                });
            })
            ->groupBy('struct_id')
            ->map(function ($group) {
                $accessory = $group->first();
                $accessory->trim_names = $group->pluck('trim_name')->unique()->values();
                return $accessory;
            })
            ->sortBy('name')
            ->values();

            $groupedAccessories = collect();

            foreach ($accessories as $accessory) {
                $categories = is_array($accessory->categories) 
                    ? $accessory->categories 
                    : json_decode($accessory->categories, true);

                foreach ($categories as $category) {
                    if (! $groupedAccessories->has($category)) {
                        $groupedAccessories[$category] = collect();
                    }

                    $groupedAccessories[$category]->push($accessory);
                }
            }

            $rows = collect();

        foreach ($groupedAccessories as $category => $accessories) {
            foreach ($accessories->chunk(3) as $row) {
                $rows->push([
                    'category' => $category,
                    'items' => $row
                ]);
            }
        }

        $pages = $rows->chunk(3);

        $complianceTexts = [
            'price' => app(ComplianceTextService::class)->getForCar($car, 'price'),
            'consumption' => app(ComplianceTextService::class)->getForGlobal('consumption'),
            'changes' => app(ComplianceTextService::class)->getForGlobal('changes'),
        ];

        return pdf('price-list-accessories', compact('car', 'complianceTexts', 'pages'))
        ->format(Format::A4)
        ->name('Prisliste')
        ->margins(10, 10, 10, 10)
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