<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function fromZip(string $zip): ?array
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $zip . ', DK',
            'key' => config('nellemann.google.maps_key'),
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        if (empty($data['results'][0])) {
            return null;
        }

        return [
            'lat' => $data['results'][0]['geometry']['location']['lat'],
            'lng' => $data['results'][0]['geometry']['location']['lng'],
        ];
    }
}