<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FoService
{
    public function getSparePart(string $partNumber): ?array
    {
        $partNumber = str_replace("'", "''", $partNumber);

        $response = Http::nellemannFO()
            ->timeout(30)
            ->get('/data/ReleasedProductsV2_NEL', [
                'cross-company' => 'true',
                '$filter' => "dataAreaId eq 'KIT' and ProductNumber eq '{$partNumber}'",
                '$select' => 'ProductNumber,SalesPrice,SalesLineDiscountProductGroupCode',
                '$top' => 1,
            ]);

        $response->throw();

        return Arr::first($response->json('value', []));
    }

    public function getStock(string $partNumber): int|float
    {
        $partNumber = str_replace("'", "''", $partNumber);

        $response = Http::nellemannFO()
            ->timeout(30)
            ->get('/data/IntegrationOutSparePartsInvtoryQty_NEL', [
                'cross-company' => 'true',
                '$filter' => "dataAreaId eq 'KIT' and ItemId eq '{$partNumber}'",
                '$select' => 'AvailPhysical',
            ]);
        
        $response->throw();

        return (float) data_get($response->json(), 'value.0.AvailPhysical', 0);
    }
}