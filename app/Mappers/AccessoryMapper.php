<?php

namespace App\Mappers;

use App\Models\Car;
use App\Data\AssetData;
use App\Data\AccessoryData;
use App\Data\Accessory\{
    PriceData,
};
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class AccessoryMapper
{
    public static function map(Collection $accessoryData, Collection $attributesData, Collection $variantAttributesReferencesData, callable $getAsset): ?AccessoryData
    {
        try {
            $accessoryId = Arr::get($accessoryData, 'Id', '');
            $name = Arr::get($attributesData, 'SharedAccessoryPartNameOverride') ?: Arr::get($attributesData, 'SharedAccessoryPartName', '');
            $description = Arr::get($attributesData, 'SharedAccessoryDescription', '');
            $partNumber = Arr::get($attributesData, 'SharedAccessoryPartNumber', '');
            $categoryOne = Arr::get($attributesData, 'SharedAccessoryPNCGroup1.Group', '');
            $categoryTwo = Arr::get($attributesData, 'SharedAccessoryPNCGroup2.Group', '');
            $disclaimer = Arr::get($attributesData, 'Pligttekst', '');
            $prices = self::mapPrices($attributesData->get('SharedAccessoryRetailPrice'));
            $primaryImage = self::resolveAsset($attributesData, 'PrimaryImage', $getAsset);
            $overrideImage = self::resolveAsset($attributesData, 'NellemannImage', $getAsset);
            $additional_images = self::mapAdditionalImages($attributesData->get('ExtraImages'), $getAsset);
            $accessoryMapping = self::mapAccessoryMapping($variantAttributesReferencesData->get('MobisModelMapping'));

            return new AccessoryData(
                struct_id: $accessoryId,
                name: $name,
                description: $description,
                part_number: $partNumber,
                category_one: $categoryOne,
                category_two: $categoryTwo,
                disclaimer: $disclaimer,
                prices: $prices,
                primary_image: $primaryImage,
                override_image: $overrideImage,
                additional_images: $additional_images,
                accessory_mapping: $accessoryMapping
            );

        } catch (Throwable $e) {
            Log::error('Error mapping Accessory', [
                'productId' => Arr::get($accessoryData, 'Id'),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    // --- Mapping helpers ---

    protected static function resolveAsset(Collection $attributes, string $key, callable $getAsset): ?AssetData
    {
        $id = $attributes->get($key);
        return is_numeric($id) ? $getAsset($id) : null;
    }

    protected static function mapPrices(array|Collection|null $prices): array
    {
        if (!$prices) return [];
        $data = $prices instanceof Collection ? $prices : collect($prices);

        return $data->map(fn($item) => new PriceData(
            price: Arr::get($item, 'Price'),
            valid_from: Arr::get($item, 'ValidFrom') ? substr($item['ValidFrom'], 0, 10) : null,
            valid_to: Arr::get($item, 'ValidTo') ? substr($item['ValidTo'], 0, 10) : null,
        ))->values()->all();
    }

    protected static function mapExtraImages(array|Collection|null $images): array
    {
        if (!$images) return [];
        $data = $images instanceof Collection ? $images : collect($images);

        self::resolveAsset($attributesData, 'NellemannImage', $getAsset);

    }

    protected static function mapAdditionalImages(array|Collection|null $images, callable $getAsset): array
    {
        if (!$images) return [];

        $images = $images instanceof Collection ? $images : collect($images);

        return $images->map(function ($id) use ($getAsset) {
            $attributesData = collect(['AdditionalImage' => $id]);
            return self::resolveAsset($attributesData, 'AdditionalImage', $getAsset);
        })->filter()->values()->all();
    }

    protected static function mapAccessoryMapping(array|Collection|null $mapping): array
    {
        if (!$mapping) return [];
        $data = $mapping instanceof Collection ? $mapping : collect($mapping);

        return $data->values()
                    ->all();
    }
}
