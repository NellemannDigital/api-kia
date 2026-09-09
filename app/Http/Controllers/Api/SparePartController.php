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
        $id = $productsSearchRequest->getSparePartId($partNumber);

        $sparePartData = $pimService->getSpacePart($id);

        return response()->json($sparePartData);
    }
}