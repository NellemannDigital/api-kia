<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Car::query()
            ->addChannels(['web_channel', 'price_channel'])
            ->where('variant->b2b', false)
            ->with('trims.powertrains.configuration')
            ->orderBy('name')
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
        return Car::query()
            ->addChannels(['web_channel'])
            ->where('web_id', $id)
            ->with([
                'trims.extraEquipmentPackages.prices',
                'trims.colors.prices',
                'trims.powertrains.configuration',
                'trims.powertrains.prices',
                'trims.equipment',
                'trims.accessories.prices'
            ])
            ->firstOrFail();
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
