<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Requests\ProductsSearchRequest;
use App\Services\PimService;

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

        $sparePartData = $pimService->getSpacePart($id);

        return response()->json($sparePartData);
    }
}