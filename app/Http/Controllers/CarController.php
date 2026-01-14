<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use Inertia\Inertia;
use App\Jobs\SyncCarJob;
use App\Jobs\SyncTrimJob;
use Illuminate\Support\Facades\Bus;
use App\Requests\ProductRequest;

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
            ->onQueue('pim-sync')
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'batch_id' => $batch->id,
            'message' => 'Sync started',
        ]);
    }
}
