<?php

namespace App\Mappers;

use App\Data\CarData;
use App\Data\AssetData;
use App\Data\Car\{
    ModelData,
    VariantData,
    TechnicalSpecificationsData,
    DimensionsData,
    CampaignData,
    UrlsData,
    FileData,
    ChannelsData,
    PriceListData,
    InsuranceRateData
};
use App\Mappers\Car\ChannelsMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class CarMapper
{
    public static function map(Collection $productData, Collection $attributesData, callable $getAsset): CarData
    {
        try {
            $model = self::mapModel($attributesData);
            $variant = self::mapVariant($attributesData);

            $structId = Arr::get($productData, 'Id', '');
            $webId = Arr::get($attributesData, 'ModelWebID', '');
            $name = Arr::get($attributesData, 'ModelWebName') ?? $model->name;
            $year = Arr::get($attributesData, 'KiaModelYear.ModelYear', '');
            $deliveryYear = Arr::get($attributesData, 'DeliveryYear.DeliveryYear', '');
            $disclaimer = Arr::get($attributesData, 'CustomPligttekst', '');

            $technicalSpecifications = self::mapTechnicalSpecifications($attributesData);
            $dimensions = self::mapDimensions($attributesData->get('Dimmensions'));
            $urls = self::mapUrls($attributesData);
            $campaign = self::mapCampaign($attributesData->get('CampaignImage'), $getAsset);
            $files = self::mapFiles($attributesData->get('Files'), $getAsset);
            $insuranceRates = self::mapInsuranceRates($attributesData->get('Insurance'));
            $categories = self::mapCategories($attributesData->get('FamilyGroups'));
            $primaryImage = self::resolveAsset($attributesData, 'PrimaryImage', $getAsset);
            $channels = ChannelsMapper::map($attributesData);
            $priceList = self::mapPriceList($attributesData, $getAsset);

            return new CarData(
                struct_id: $structId,
                web_id: $webId,
                name: $name,
                year: $year,
                delivery_year: $deliveryYear,
                disclaimer: $disclaimer,
                model: $model,
                variant: $variant,
                technical_specifications: $technicalSpecifications,
                dimensions: $dimensions,
                primary_image: $primaryImage,
                campaign: $campaign,
                files: $files,
                insurance_rates: $insuranceRates,
                categories: $categories,
                urls: $urls,
                channels: $channels,
                price_list: $priceList
            );

        } catch (Throwable $e) {
            Log::error('Error mapping Car', [
                'productId' => Arr::get($productData, 'Id'),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected static function resolveAsset(Collection $attributes, string $key, callable $getAsset): ?AssetData
    {
        $id = $attributes->get($key);
        return is_numeric($id) ? $getAsset($id) : null;
    }

    // --- Mapping helpers ---

    protected static function mapModel(array|Collection|null $attributes): ?ModelData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'KiaModel.Name', '');
        $brand = Arr::get($attributes, 'KiaModel.Brand', '');
        $code = Arr::get($attributes, 'KiaModel.Code', '');
        $parentModelCode = Arr::get($attributes, 'KiaModel.ParentModelCode', '');

        if (!$name && !$brand && !$code && !$parentModelCode) return null;

        return new ModelData(
            name: $name,
            brand: $brand,
            code: $code,
            parent_model_code: $parentModelCode
        );
    }

    protected static function mapVariant(array|Collection|null $attributes): ?VariantData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'KiaVariant.Name', '');
        $code = Arr::get($attributes, 'KiaVariant.Code', '');
        $b2bRaw = Arr::get($attributes, 'KiaVariant.PBVSpecific', '');
        $b2b = $b2bRaw ? true : false;

        if (!$name && !$brand && !$b2b) return null;

        return new VariantData(
            name: $name,
            code: $code,
            b2b: $b2b
        );
    }

    protected static function mapTechnicalSpecifications(array|Collection|null $attributes): ?TechnicalSpecificationsData
    {
        if (!$attributes) return null;

        $values = [
            'fueltank_volume' => Arr::get($attributes, 'SharedFueltankVolume'),
            'electric_horsepower' => Arr::get($attributes, 'SharedElectricHorsepower'),
            'electric_horsepower_rev_range' => Arr::get($attributes, 'SharedElectricHorsepowerRevRange'),
            'electric_torque' => Arr::get($attributes, 'SharedElectricTorque'),
            'electric_torque_rev_range' => Arr::get($attributes, 'SharedElectricTorqueRevRange'),
            'total_horsepower' => Arr::get($attributes, 'SharedTotalHorsepower'),
            'total_horsepower_rev_range' => Arr::get($attributes, 'SharedTotalHorsepowerRevRange'),
            'total_torque' => Arr::get($attributes, 'SharedTotalTorque'),
            'total_torque_rev_range' => Arr::get($attributes, 'SharedTotalTorqueRevRange'),
        ];

        if (!array_filter($values)) return null;

        return new TechnicalSpecificationsData(...$values);
    }

    protected static function mapDimensions(array|Collection|null $attributes): ?DimensionsData
    {
        if (!$attributes) return null;

        $values = [
            'cargo_volume' => Arr::get($attributes, 'CargoVolume'),
            'cargo_length' => Arr::get($attributes, 'CargoLength'),
            'cargo_width_max' => Arr::get($attributes, 'CargoWidthMax'),
            'cargo_width_min' => Arr::get($attributes, 'CargoWidthMin'),
            'cargo_width_wheel_housing' => Arr::get($attributes, 'CargoWidthWheel'),
            'cargo_height' => Arr::get($attributes, 'CargoHeight'),
            'loading_height_rear' => Arr::get($attributes, 'LoadingHeightRear'),
            'loading_height_side' => Arr::get($attributes, 'LoadingHeightSide'),
            'chassis_length' => Arr::get($attributes, 'ChassisLenght'),
            'chassis_height' => Arr::get($attributes, 'ChassisHeight'),
            'chassis_max_overhang' => Arr::get($attributes, 'ChassisMaxOverhang'),
        ];

        if (!array_filter($values)) return null;

        return new DimensionsData(...$values);
    }

    protected static function mapUrls(array|Collection|null $attributes): ?UrlsData
    {
        if (!$attributes) return null;

        $website = Arr::get($attributes, 'WebsiteUrl', '');
        $buildConfigurator = Arr::get($attributes, 'BuildURL', '');
        $leasingConfigurator = Arr::get($attributes, 'LeasingUrl', '');
        $technicalSpecifications = Arr::get($attributes, 'SpecificationsSite', '');
        $testDrive = Arr::get($attributes, 'TestdriveUrl', '');

        if (!$website && !$buildConfigurator && !$leasingConfigurator && !$technicalSpecifications && !$testDrive) return null;

        return new UrlsData(
            website: $website,
            build_configurator: $buildConfigurator,
            leasing_configurator: $leasingConfigurator,
            technical_specifications: $technicalSpecifications,
            test_drive: $testDrive
        );
    }

    protected static function mapCampaign(array|Collection|null $attributes, callable $getAsset): ?CampaignData
    {
        if (!$attributes) return null;

        $imageId = Arr::get($attributes, 'CampaignImageBadge');
        $image = $imageId ? $getAsset($imageId) : null;
        $placement = Arr::get($attributes, 'CampaignImagePlacement.PlacementCode');

        if (!$image) return null;

        return new CampaignData(image: $image, placement: $placement);
    }

    protected static function mapFiles(array|Collection|null $files, callable $getAsset): array
    {
        if (!$files) return [];

        $data = $files instanceof Collection ? $files : collect($files);

        return $data->map(function ($item) use ($getAsset) {
            $fileAsset = $itemId = Arr::get($item, 'File') ? $getAsset($item['File']) : null;
            return new FileData(
                file: $fileAsset,
                document_type: Arr::get($item, 'DocumentTypes'),
                valid_from: Arr::get($item, 'ValidFrom') ? substr($item['ValidFrom'], 0, 10) : null,
                valid_to: Arr::get($item, 'ValidTo') ? substr($item['ValidTo'], 0, 10) : null,
                url: Arr::get($item, 'Url')
            );
        })->filter(fn($f) => $f->file || $f->url)->values()->all();
    }

    protected static function mapInsuranceRates(array|Collection|null $rates): array
    {
        if (!$rates) return [];
        $data = $rates instanceof Collection ? $rates : collect($rates);

        return $data->map(fn($item) => new InsuranceRateData(
            age_from: Arr::get($item, 'AgeFrom'),
            age_to: Arr::get($item, 'AgeTo'),
            annual_price: Arr::get($item, 'AnnualPrice'),
            excess: Arr::get($item, 'Excess')
        ))->values()->all();
    }

    protected static function mapCategories(array|Collection|null $categories): array
    {
        if (!$categories) return [];
        $data = $categories instanceof Collection ? $categories : collect($categories);

        return $data->values()
                    ->all();
    }

    protected static function mapPriceList(array|Collection|null $attributes, callable $getAsset): ?PriceListData
    {
        if (!$attributes) return null;

        $primaryImageId = Arr::get($attributes, 'PrimaryImagePricelist');
        $campaignImageId = Arr::get($attributes, 'CampaignImagePricelist');

        $primaryImage = is_numeric($primaryImageId) ? $getAsset($primaryImageId) : null;
        $campaignImage = is_numeric($campaignImageId) ? $getAsset($campaignImageId) : null;

        if (!$primaryImage && !$campaignImage) return null;

        return new PriceListData(primary_image: $primaryImage, campaign_image: $campaignImage);
    }
}
