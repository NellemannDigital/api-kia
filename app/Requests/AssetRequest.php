<?php

namespace App\Requests;

use Illuminate\Support\Collection;

class AssetRequest extends PimRequest
{
    public function getAssetResponse(int|string $assetId): Collection
    {
        return $this->request("assets/{$assetId}");
    }

    public function getAssetsResponse(array $assetIds): Collection
    {
        $assetIds = array_filter($assetIds, fn($id) => !is_null($id) && $id !== '');
        if (empty($assetIds)) {
            return collect();
        }

        return $this->postRequest('assets/batch', $assetIds);
    }
}
