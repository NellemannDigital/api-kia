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
    public function index()
    {
        return StockCar::withoutGlobalScopes()
            ->with([
                'dealer' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.extraEquipmentPackages' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.car' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.trim' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.powertrain' => fn ($q) => $q->withoutGlobalScopes(),

                'configuration.powertrain.prices',
            ])
            ->paginate();
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
