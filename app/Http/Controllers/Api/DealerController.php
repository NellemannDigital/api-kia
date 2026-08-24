<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\GeocodingService;
use App\Models\PostalCode;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GeocodingService $geocodingService)
    {        
        $query = Dealer::query()
            ->when(
                $request->filled('ids'),
                function ($query) use ($request) {
                    $query->whereIn(
                        'dealer_guid',
                        explode(',', $request->ids)
                    );
                }
            );

        $lat = null;
        $lng = null;

        if ($request->filled('zip')) {
            $geoData = $geocodingService->fromZip($request->zip);

            if ($geoData) {
                $lat = $geoData['lat'];
                $lng = $geoData['lng'];
            }
        }

        if ($request->filled(['lat', 'lng'])) {
            $lat = $request->lat;
            $lng = $request->lng;
        }

        if ($lat && $lng) {
            $query
                ->selectRaw("
                    dealers.*,
                    (
                        6371 * acos(
                            cos(radians(?))
                            * cos(radians(latitude))
                            * cos(radians(longitude) - radians(?))
                            + sin(radians(?))
                            * sin(radians(latitude))
                        )
                    ) AS distance
                ", [$lat, $lng, $lat])
                ->orderBy('distance');

            return $query->paginate(6);
        }

        return $query
            ->orderBy('city')
            ->get();
    }

    public function postalCodes(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        return PostalCode::query()
            ->where('postal_code', 'like', "{$query}%")
            ->orWhere('city', 'like', "%{$query}%")
            ->orderByRaw("
                CASE 
                    WHEN postal_code LIKE ? THEN 1
                    WHEN city LIKE ? THEN 2
                    ELSE 3
                END
            ", ["{$query}%", "%{$query}%"])
            ->limit(10)
            ->get(['postal_code', 'city']);
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
        return Dealer::query()
            ->where('dealer_guid', $id)
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
