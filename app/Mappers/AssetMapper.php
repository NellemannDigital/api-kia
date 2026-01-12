<?php

namespace App\Mappers;

use App\Data\AssetData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class AssetMapper
{
    /**
     * Map PIM asset data to AssetData
     *
     * @param Collection $assetData
     * @return AssetData
     *
     * @throws Throwable
     */
    public static function map(Collection $assetData): AssetData
    {
        try {
            return new AssetData(
                struct_id: $assetData->get('Id', ''),
                name: $assetData->get('Name', ''),
                url: $assetData->get('Url', ''),
                file_type: $assetData->get('FileType', ''),
                type: $assetData->get('Type', ''),
            );
        } catch (Throwable $e) {
            Log::error('Error mapping asset data', [
                'assetId' => $assetData->get('Id'),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'assetData' => $assetData->toArray(),
            ]);

            report($e);

            throw $e;
        }
    }
}
