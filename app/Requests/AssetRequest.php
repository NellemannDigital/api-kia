<?php

namespace App\Requests;

use Illuminate\Support\Collection;

class AssetRequest extends PimRequest
{
    public function getAssetResponse(int|string $assetId): Collection
    {
        return $this->request("assets/{$assetId}");
    }
}
