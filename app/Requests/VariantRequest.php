<?php

namespace App\Requests;

use Illuminate\Support\Collection;

class VariantRequest extends PimRequest
{
    public function getVariantResponse(int $variantId): Collection
    {
        return $this->request("variants/{$variantId}");
    }

    public function getVariantAttributesResponse(int $variantId): Collection
    {
        return $this->request("variants/{$variantId}/attributevalues", 'Values');
    }

    public function getVariantAttributesReferencesResponse(int $variantId): Collection
    {
        return $this->request("variants/{$variantId}/attributevalues?globalListValueReferencesOnly=true", 'Values');
    }
}
