<?php

namespace App\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class StockCarsRequest
{
    public function getStockCars(array $stockStatuses, ?Carbon $modifiedFrom = null): Collection
    {
        $params = [
            'brands' => 'kia',
            'stockStatuses' => implode(',', $stockStatuses),
        ];

        if ($modifiedFrom) {
            $params['modifiedFrom'] = $modifiedFrom->toDateString();
        }

        $response = Http::nellemannAzure()
            ->timeout(300) // 5 minutes
            ->get('GetStockCarList', $params);

        if ($response->failed()) {
            throw new \Exception("Azure request failed [{$response->status()}]");
        }

        $responseData = $response->json('value', []);

        return collect($responseData);
    }
}
