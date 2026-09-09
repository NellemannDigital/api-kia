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
        Collection $attributesData
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

            $defaultPrice = Arr::get(
                $attributesData,
                'SharedAccessoryListPriceFO'
            );

            $prices = self::mapPrices(
                $attributesData->get('SharedAccessoryListPriceManualOverride')
            );

            $price = self::getValidPrice($prices, $defaultPrice);

            return new SparePartData(
                struct_id: $sparePartId,
                part_number: $partNumber,
                name: $name,
                group: $group,
                price: $price,
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

    protected static function mapPrices(
        array|Collection|null $prices
    ): array {
        if (!$prices) {
            return [];
        }

        $data = $prices instanceof Collection
            ? $prices
            : collect($prices);

        return $data
            ->map(fn ($item) => new PriceData(
                price: Arr::get($item, 'Price'),
                valid_from: Arr::get($item, 'ValidFrom')
                    ? substr($item['ValidFrom'], 0, 10)
                    : null,
                valid_to: Arr::get($item, 'ValidTo')
                    ? substr($item['ValidTo'], 0, 10)
                    : null,
            ))
            ->values()
            ->all();
    }

    protected static function getValidPrice(
        array $prices,
        int|float|null $defaultPrice
    ): int|float|null {
        $today = Carbon::today();

        $validPrice = collect($prices)
            ->first(function (PriceData $price) use ($today) {
                $validFrom = $price->valid_from
                    ? Carbon::parse($price->valid_from)
                    : null;

                $validTo = $price->valid_to
                    ? Carbon::parse($price->valid_to)
                    : null;

                return (!$validFrom || $today->gte($validFrom))
                    && (!$validTo || $today->lte($validTo));
            });

        return $validPrice?->price ?? $defaultPrice;
    }
}