<?php

namespace App\Requests;

use Illuminate\Support\Collection;

class ProductRequest extends PimRequest
{
    public function getProductResponse(int $productId): Collection
    {
        return $this->request("products/{$productId}");
    }

    public function getProductAttributesResponse(int $productId): Collection
    {
        return $this->request("products/{$productId}/attributevalues", 'Values');
    }

    public function getProductAttributesReferencesResponse(int $productId): Collection
    {
        return $this->request("products/{$productId}/attributevalues?globalListValueReferencesOnly=true", 'Values');
    }

    public function getProductVariantsIdsByProductId(int $productId): Collection
    {
        return $this->request("products/{$productId}/variants");
    }
}
