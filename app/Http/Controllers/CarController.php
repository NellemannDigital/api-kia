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
use App\Services\OptionMatrixBuilder;
use function Spatie\LaravelPdf\Support\pdf;
use Spatie\LaravelPdf\Enums\Format;

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

    public function priceList()
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

        return view('price-list', compact('car', 'complianceTexts', 'colorMatrix',  'extraEquipmentPackageMatrix'));
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
        ->download();;
    }
}