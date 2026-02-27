<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\StockCar;
use App\Http\Controllers\Controller;

class StockCarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockCar::withoutGlobalScopes()
            ->with([
                'dealer' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.extraEquipmentPackages' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.car' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.trim' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.powertrain' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.powertrain.prices',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filter: Dealer
        |--------------------------------------------------------------------------
        */
        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter: Model (via powertrain -> trim -> car)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('model_id')) {
            $query->whereHas(
                'configuration.powertrain.trim.car',
                fn ($q) => $q->where('id', $request->model_id)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter: Price
        |--------------------------------------------------------------------------
        */
        if ($request->filled('min_price') || $request->filled('max_price')) {

            $query->whereHas('configuration.powertrain.prices', function ($q) use ($request) {

                if ($request->filled('min_price')) {
                    $q->where('suggested_retail_price', '>=', $request->min_price);
                }

                if ($request->filled('max_price')) {
                    $q->where('suggested_retail_price', '<=', $request->max_price);
                }

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $perPage = $request->get('per_page', 15);

        return $query->paginate($perPage);
    }

    public function priceRange()
    {
        $min = StockCar::query()
            ->whereHas('configuration.powertrain.prices')
            ->with('configuration.powertrain.prices')
            ->get()
            ->flatMap(fn($car) => $car->configuration->powertrain->prices ?? [])
            ->min(fn($price) => $price->suggested_retail_price);

        $max = StockCar::query()
            ->whereHas('configuration.powertrain.prices')
            ->with('configuration.powertrain.prices')
            ->get()
            ->flatMap(fn($car) => $car->configuration->powertrain->prices ?? [])
            ->max(fn($price) => $price->suggested_retail_price);

        return response()->json([
            'min_price' => $min,
            'max_price' => $max,
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
        //
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

}
