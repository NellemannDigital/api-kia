<?php

namespace App\Mappers;

use App\Data\SparePartData;
use App\Data\SparePart\PriceData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class SparePartMapper
{
    public static function map(
        Collection $sparePartData,
        Collection $attributesData,
        ?array $foData = null,
        int|float $stock = 0
    ): ?SparePartData {
        try {
            $sparePartId = Arr::get($sparePartData, 'Id', '');
            $name = Arr::get($attributesData, 'SharedAccessoryPartName', '');
            $partNumber = Arr::get($attributesData, 'SharedAccessoryPartNumber', '');

            $weight = Arr::get($attributesData, 'SharedAccessoryUnitWeightManual')
                ?? Arr::get($attributesData, 'KiaSparePartsUnitWeight')
                ?? null;

            $width = Arr::get($attributesData, 'SharedAccessoryWidthManual')
                ?? Arr::get($attributesData, 'KiaSparePartsWidth')
                ?? null;

            $length = Arr::get($attributesData, 'SharedAccessoryLengthManual')
                ?? Arr::get($attributesData, 'KiaSparePartsLength')
                ?? null;

            $height = Arr::get($attributesData, 'SharedAccessoryHeightManual')
                ?? Arr::get($attributesData, 'KiaSparePartsHeight')
                ?? null;
            
            $group = Arr::get($attributesData, 'SharedAccessoryPNCGroup2.Id', '');

            $euHsCode = Arr::get($attributesData, 'KiaSparePartsTariffNumberManualOverride')
                ?? Arr::get($attributesData, 'KiaSparePartsTariffNumberShortened')
                ?? null;

            $price = Arr::get($foData, 'SalesPrice');

            $discountCode = Arr::get(
                $foData,
                'SalesLineDiscountProductGroupCode'
            );

            return new SparePartData(
                struct_id: $sparePartId,
                part_number: $partNumber,
                name: $name,
                eu_hs_code: $euHsCode,
                group: $group,
                price: $price,
                discount_code: $discountCode,
                stock: $stock,
                weight: $weight,
                width: $width,
                length: $length,
                height: $height
            );

        } catch (Throwable $e) {
            Log::error('Error mapping SparePart', [
                'productId' => Arr::get($sparePartData, 'Id'),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}