<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Requests\ProductsSearchRequest;
use App\Services\PimService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class SparePartController extends Controller
{
    public function show(
        ProductsSearchRequest $productsSearchRequest,
        PimService $pimService,
        string $partNumber
    ) {
        $id = $productsSearchRequest->getSparePartId(
            '2b23c5e6-b02a-43ff-8178-0837725f92b8',
            $partNumber
        );

        if (!$id) {
            return response()->json([
                'message' => 'Spare part not found.',
            ], 404);
        }

        $sparePartData = $pimService->getSparePart(
            $id,
            $partNumber
        );

        return response()->json($sparePartData);
    }


    public function get(): Collection
    {
        $response = Http::nellemannFO()
            ->timeout(300)
            ->get('/data/IntegrationOutSparePartsInvtoryQty_NEL', [
                'cross-company' => 'true',
                '$filter' => "dataAreaId eq 'KIT' and ItemId eq 'KI-PS-KIA1'",
                '$select' => 'AvailPhysical',
            ]);

        if ($response->failed()) {
            throw new \Exception(
                "FO request failed [{$response->status()}]: {$response->body()}"
            );
        }

        return collect($response->json('value', []));
    }
}