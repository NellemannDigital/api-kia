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
        return StockCar::with([
            'dealer',
            'configuration.extraEquipmentPackages',
            'configuration.powertrain.trim.car',
            'configuration.powertrain.prices'
        ])->paginate();
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
