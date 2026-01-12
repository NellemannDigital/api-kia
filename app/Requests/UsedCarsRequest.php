<?php

namespace App\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class UsedCarsRequest
{
    public function getUsedCars(): Collection
    {
        ini_set('memory_limit', '256M');

        $response = Http::nellemannBilInfo()
            ->timeout(300) // 5 minutes
            ->get('listingapi/api/export');

        if ($response->failed()) {
            throw new \Exception("BilInfo request failed [{$response->status()}]");
        }

        $responseData = $response->json('Vehicles', []);

        return collect($responseData);
    }
}
