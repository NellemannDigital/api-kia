<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Dealer::all();
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
        $dealer = Dealer::findOrFail($id);

        return $dealer;
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
