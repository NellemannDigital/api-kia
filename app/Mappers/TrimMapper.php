<?php

namespace App\Mappers;

use App\Data\TrimData;
use App\Data\AssetData;
use App\Data\EquipmentData;
use App\Data\Trim\{
    InteriorData,
    TechnicalSpecificationsData,
    CampaignData,
    ChannelsData,
    PowertrainData,
    ExtraEquipmentPackageData,
    LeasingPowertrainData,
    ColorData,
    Color\PriceData as ColorPriceData,
    Powertrain\EngineData,
    Powertrain\TransmissionData,
    Powertrain\TechnicalSpecificationsData as PowertrainTechnicalSpecificationsData,
    Powertrain\PriceData,
    Powertrain\LeasingPriceData,
    Powertrain\Engine\ServiceIntervalData,
    ExtraEquipmentPackage\InteriorOverrideToData,
    ExtraEquipmentPackage\ModelChangeCodeData,
    ExtraEquipmentPackage\TransmissionRequiredData,
    ExtraEquipmentPackage\ColorRequiredData,
};
use App\Mappers\Trim\ChannelsMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrimMapper
{
    public static function map(Collection $variantData, Collection $attributesData, Collection $variantAttributesReferencesData, callable $getAsset, callable $getAssets): TrimData
    {
        try {
            $structId = Arr::get($variantData, 'Id', '');
            $carStructId = Arr::get($variantData, 'ProductId', '');
            $sortOrder = is_numeric($value = Arr::get($attributesData, 'MarketingSortOrderKia')) ? (int) $value : null;
            $name = Arr::get($attributesData, 'KiaEquipmentVariant', '');
            $leasingReturnFee = Arr::get($attributesData, 'LeasingReturnFee', '');

            $interior = self::mapInterior($attributesData, $getAsset);
            $technicalSpecifications = self::mapTechnicalSpecifications($attributesData);
            $campaign = self::mapCampaign($attributesData->get('CampaignImage'), $getAsset);
            $primaryImage = self::resolveAsset($attributesData, 'PrimaryImage', $getAsset);
            $channels = ChannelsMapper::map($attributesData);
            $powertrains = self::mapPowertrains($attributesData->get('KiaEngineAndTransmissions'));
            $equipment = self::mapEquipment($attributesData->get('KiaStandardEquipment'), $getAssets);
            $extraEquipmentPackages = self::mapExtraEquipmentPackages($attributesData->get('KiaExtraEquipmentFoundation'), $getAsset, $getAssets);
            $colors = self::mapColors($attributesData->get('KiaColors'), $getAsset, $getAssets);
            $leasingPowertrains = self::mapLeasingPowertrains($attributesData->get('KiaLeasingEngineAndTransmission'));
            $accessoryMapping = self::mapAccessoryMapping($variantAttributesReferencesData->get('MobisModelMapping'));

            return new TrimData(
                struct_id: $structId,
                car_struct_id: $carStructId,
                name: $name,
                sort_order: $sortOrder,
                leasing_return_fee: $leasingReturnFee,
                interior: $interior,
                technical_specifications: $technicalSpecifications,
                campaign: $campaign,
                primary_image: $primaryImage,
                channels: $channels,
                powertrains: $powertrains,
                leasing_powertrains: $leasingPowertrains,
                accessory_mapping: $accessoryMapping,
                colors: $colors,
                equipment: $equipment,
                extra_equipment_packages: $extraEquipmentPackages
            );

        } catch (Throwable $e) {
            Log::error('Error mapping Trim', [
                'variantId' => Arr::get($variantData, 'Id'),
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
    protected static function mapTechnicalSpecifications(array|Collection|null $attributes): ?TechnicalSpecificationsData
    {
        if (!$attributes) return null;

        $data = [
            'length' => Arr::get($attributes, 'SharedLength'),
            'width' => Arr::get($attributes, 'SharedWidth'),
            'height' => Arr::get($attributes, 'SharedHeight'),
            'wheelbase' => Arr::get($attributes, 'SharedWheelbase'),
            'minimum_groundclearance' => Arr::get($attributes, 'SharedMinimumGroundclearance'),
            'track_width' => Arr::get($attributes, 'SharedTrackWidth'),
            'front_overhang' => Arr::get($attributes, 'SharedFrontOverhang'),
            'rear_overhang' => Arr::get($attributes, 'SharedRearOverhang'),
            'wading_depth' => Arr::get($attributes, 'SharedWadingDepth'),
            'entry_angle' => Arr::get($attributes, 'SharedEntryAngle'),
            'exit_angle' => Arr::get($attributes, 'SharedExitAngle'),
            'cargo_bed_length' => Arr::get($attributes, 'SharedCargoBedLength'),
            'cargo_bed_width' => Arr::get($attributes, 'SharedCargoBedWidth'),
            'cargo_bed_height' => Arr::get($attributes, 'SharedCargoBedHeight'),
            'cargo_bed_area' => Arr::get($attributes, 'SharedCargoBedArea'),
            'maximum_load_capacity' => Arr::get($attributes, 'SharedMaximumLoadCapacity')
        ];

        if (!array_filter($data)) return null;

        return new TechnicalSpecificationsData(...$data);
    }

    protected static function mapInterior(array|Collection|null $attributes, callable $getAsset): ?InteriorData
    {
        if (!$attributes) return null;

        $imageId = Arr::get($attributes, 'InteriorImage');
        $image = $imageId ? $getAsset($imageId) : null;
        $name = Arr::get($attributes, 'KiaInterior.Name');
        $code = Arr::get($attributes, 'KiaInterior.Code');

        if (!$name || !$code) return null;

        return new InteriorData(image: $image, name: $name, code: $code);
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

    protected static function mapPowertrains(array|Collection|null $powertrains): array
    {
        if (!$powertrains) return [];

        $data = $powertrains instanceof Collection ? $powertrains : collect($powertrains);

        return $data
            ->map(function ($item) {
                $engineData = Arr::get($item, 'Engine');
                $transmissionData = Arr::get($item, 'Transmission');
                $technicalSpecificationsData = Arr::get($item, 'TechnicalSpecifications');
                $pricesData = Arr::get($item, 'Prices');
                $ocn = Arr::get($item, 'OCN');

                $configurationId = Arr::get($item, 'Configuration');
                $engine = $engineData ? self::mapPowertrainEngine($engineData) : null;
                $transmission = $transmissionData ? self::mapPowertrainTransmission($transmissionData) : null;
                $technicalSpecifications = $technicalSpecificationsData ? self::mapPowertrainTechnicalSpecifications($technicalSpecificationsData) : null;
                $prices = self::mapPowertrainPrices($pricesData);

                if (!$engine || !$transmission || !$ocn || !$configurationId) {
                    return null;
                }

                return new PowertrainData(
                    ocn: $ocn,
                    configuration_id: $configurationId,
                    engine: $engine,
                    transmission: $transmission,
                    technical_specifications: $technicalSpecifications,
                    prices: $prices
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function mapEquipment(array|Collection|null $equipment, callable $getAssets): array
    {
        if (!$equipment) return [];

        $data = $equipment instanceof Collection ? $equipment : collect($equipment);

        return $data
            ->map(function ($item) use ($getAssets)  {
                $code = Arr::get($item, 'Code');
                
                $name = collect(Arr::get($item, 'Name', []))
                ->firstWhere('CultureCode', 'da-DK')['Data'] ?? '';

                $category = Arr::get($item, 'Category.Name', '');
                $imagesData = Arr::get($item, 'Images');
                $images = self::mapEquipmentImages($imagesData, $getAssets);

                if (!$code || !$name) {
                    return null;
                }

                return new EquipmentData(
                    code: $code,
                    name: $name,
                    category: $category,
                    images: $images
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function mapEquipmentImages(array|Collection|null $images, callable $getAssets): array
    {
        if (!$images) return [];

        $data = $images instanceof Collection ? $images : collect($images);

        $ids = $data
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (string) $id)
            ->values()
            ->all();

        $assets = $ids ? $getAssets($ids) : collect();

        return $assets->all();
    }

    protected static function mapExtraEquipmentPackages(array|Collection|null $packages, callable $getAsset, callable $getAssets): array
    {
        if (!$packages) return [];

        $data = $packages instanceof Collection ? $packages : collect($packages);

        return $data
            ->map(function ($item) use ($getAsset, $getAssets)  {
                $code = Arr::get($item, 'Package.Code');
                $name = Arr::get($item, 'Package.Name');
                $category = Arr::get($item, 'Package.Category.Name', '');
                
                $imageId = Arr::get($item, 'Image');
                $image = $imageId ? $getAsset($imageId) : null;

                $interiorOverrideTo = self::mapInteriorOverrideTo(Arr::get($item, 'InteriorOverrideTo'));
                $modelChangeCode = self::mapModelChangeCode(Arr::get($item, 'ModelChangeCode'));
                $transmissionRequired = self::mapTransmissionRequired(Arr::get($item, 'TransmissionRequired'));
                
                $colorRequiredData = Arr::get($item, 'ColorRequired');
                $colorRequired = self::mapColorRequired($colorRequiredData);

                $equipmentData = Arr::get($item, 'Equipment');
                $equipment = self::mapEquipment($equipmentData, $getAssets);

                if (!$code || !$name) {
                    return null;
                }

                return new ExtraEquipmentPackageData(
                    code: $code,
                    name: $name,
                    category: $category,
                    image: $image,
                    interior_override_to: $interiorOverrideTo,
                    model_change_code: $modelChangeCode,
                    transmission_required: $transmissionRequired,
                    color_required: $colorRequired,
                    equipment: $equipment
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function mapInteriorOverrideTo(array|Collection|null $attributes): ?InteriorOverrideToData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'Name');
        $code = Arr::get($attributes, 'Code');

        if (!$name || !$code) return null;

        return new InteriorOverrideToData(name: $name, code: $code);
    }

    protected static function mapModelChangeCode(array|Collection|null $attributes): ?ModelChangeCodeData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'Name');
        $code = Arr::get($attributes, 'Code');

        if (!$name || !$code) return null;

        return new ModelChangeCodeData(name: $name, code: $code);
    }

    protected static function mapTransmissionRequired(array|Collection|null $attributes): ?TransmissionRequiredData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'Name');
        $code = Arr::get($attributes, 'Code');

        if (!$name || !$code) return null;

        return new TransmissionRequiredData(name: $name, code: $code);
    }

    protected static function mapColorRequired(array|Collection|null $reguiredColors): array
    {
        if (!$reguiredColors) return [];
        $data = $reguiredColors instanceof Collection ? $reguiredColors : collect($reguiredColors);

        return $data->map(fn($item) => new ColorRequiredData(
            code: Arr::get($item, 'Code'),
            primary_color: Arr::get($item, 'PrimaryColor'),
            secondary_color: Arr::get($item, 'SecondaryColor'),
        ))->values()->all();
    }

    protected static function mapColors(array|Collection|null $colors, callable $getAsset, callable $getAssets): array
    {
        if (!$colors) return [];

        $data = $colors instanceof Collection ? $colors : collect($colors);

        return $data
            ->map(function ($item) use ($getAsset, $getAssets) {
                $code = Arr::get($item, 'Color.Code');
                $primaryColor = Arr::get($item, 'Color.PrimaryColor');
                $secondaryColor = Arr::get($item, 'Color.SecondaryColor');
                $type = Arr::get($item, 'Color.Type.Name');
                $colorImageId = Arr::get($item, 'Color.Image');
                $colorImage = $colorImageId ? $getAsset($colorImageId) : null;
                $ocnChangeCode = Arr::get($item, 'OCNChangeCode');

                $turntableImagesData = Arr::get($item, 'Image');
                $turntableImages = self::mapColorTurntableImages($turntableImagesData, $getAssets);

                $pricesData = Arr::get($item, 'Prices');
                $prices = self::mapColorPrices($pricesData);

                

                if (!$code || !$primaryColor) {
                    return null;
                }

                return new ColorData(
                    code: $code,
                    primary_color: $primaryColor,
                    secondary_color: $secondaryColor,
                    type: $type,
                    color_image: $colorImage,
                    ocn_change_code: $ocnChangeCode,
                    turntable_images: $turntableImages,
                    prices: $prices
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function mapColorPrices(array|Collection|null $prices): array
    {
        if (!$prices) return [];
        $data = $prices instanceof Collection ? $prices : collect($prices);

        return $data->map(fn($item) => new ColorPriceData(
            dealer_net_price: Arr::get($item, 'DealerNetPrice'),
            dealer_profit: Arr::get($item, 'DealerProfit'),
            suggested_retail_price: Arr::get($item, 'SuggestedRetailPrice'),
            campaign_retail_price: Arr::get($item, 'CampaignRetailPrice'),
            retail_price_ex_vat: Arr::get($item, 'RetailPriceExVAT'),
            valid_from: Arr::get($item, 'ValidFrom') ? substr($item['ValidFrom'], 0, 10) : null,
            valid_to: Arr::get($item, 'ValidTo') ? substr($item['ValidTo'], 0, 10) : null,
        ))->values()->all();
    }

    protected static function mapColorTurntableImages(array|Collection|null $images, callable $getAssets): array
    {
        if (!$images) return [];

        $data = $images instanceof Collection ? $images : collect($images);

        $ids = $data
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (string) $id)
            ->values()
            ->all();

        $assets = $ids ? $getAssets($ids) : collect();

        return $assets->all();
    }

    protected static function mapLeasingPowertrains(array|Collection|null $powertrains): array
    {
        if (!$powertrains) return [];

        $data = $powertrains instanceof Collection ? $powertrains : collect($powertrains);

        return $data
            ->map(function ($item) {
                $pricesData = Arr::get($item, 'Payment');

                $configurationId = Arr::get($item, 'StandardConfiguration');
                $leasingActive = Arr::get($item, 'PLActive') ?? false;
                $prices = self::mapLeasingPowertrainPrices($pricesData);

                if (!$configurationId) {
                    return null;
                }

                return new LeasingPowertrainData(
                    configuration_id: $configurationId,
                    leasing_active: $leasingActive,
                    leasing_prices: $prices
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function mapPowertrainEngine(array|Collection|null $attributes): ?EngineData
    {
        if (!$attributes) return null;

        $data = [
            'name' => Arr::get($attributes, 'Name'),
            'code' => Arr::get($attributes, 'Code'),
            'horse_power' => Arr::get($attributes, 'HorsePower'),
            'amount_of_cylinders' => Arr::get($attributes, 'AmountOfCylinders'),
            'amount_of_valves' => Arr::get($attributes, 'AmountOfValves'),
            'volume' => Arr::get($attributes, 'Volume'),
            'horsepower_rev_range' => Arr::get($attributes, 'HorsepowerRevRange'),
            'fuel_type' => Arr::get($attributes, 'FuelType'),
            'service_interval' => self::mapPowertrainEngineServiceInterval(Arr::get($attributes, 'ServiceInterval')),
        ];

        if (!$data['name'] && !$data['code'] && !$data['fuelType']) {
            return null;
        }

        return new EngineData(...$data);
    }

    protected static function mapPowertrainEngineServiceInterval(array|Collection|null $attributes): ?ServiceIntervalData
    {
        if (!$attributes) return null;

        $data = [
            'months' => Arr::get($attributes, 'ServiceIntervalMonths', ''),
            'kilometers' => Arr::get($attributes, 'ServiceIntervalKilometers', ''),
            'oil_change_months' => Arr::get($attributes, 'OilChangeMonths', ''),
            'oil_change_kilometers' => Arr::get($attributes, 'OilChangeKilometers', '')
        ];

        if (!$data['months'] && !$data['kilometers'] && !$data['oil_change_months'] && !$data['oil_change_kilometers']) {
            return null;
        }

        return new ServiceIntervalData(...$data);
    }

    protected static function mapPowertrainTransmission(array|Collection|null $attributes): ?TransmissionData
    {
        if (!$attributes) return null;

        $data = [
            'name' => Arr::get($attributes, 'Name'),
            'code' => Arr::get($attributes, 'Code'),
            'number_of_gears' => Arr::get($attributes, 'NumberOfGears'),
            'charge_plug_type' => Arr::get($attributes, 'ChargePlugType'),
        ];

        if (!$data['name'] && !$data['code']) {
            return null;
        }

        return new TransmissionData(...$data);
    }

    protected static function mapPowertrainTechnicalSpecifications(array|Collection|null $attributes): ?PowertrainTechnicalSpecificationsData
    {
        if (!$attributes) return null;

        $data = [
            'torque' => Arr::get($attributes, 'Torque'),
            'torque_rev_range' => Arr::get($attributes, 'TorqueRevRange'),
            'zero_to_hundred_time' => Arr::get($attributes, 'ZeroToHundredTime'),
            'topspeed' => Arr::get($attributes, 'Topspeed'),
            'net_weight' => Arr::get($attributes, 'NetWeight'),
            'driving_ready_weight' => Arr::get($attributes, 'DrivingReadyWeight'),
            'maximum_total_weight' => Arr::get($attributes, 'MaximumTotalWeight'),
            'usefull_load' => Arr::get($attributes, 'UsefullLoad'),
            'towing_capacity_braked' => Arr::get($attributes, 'TowingCapacityBraked'),
            'towing_capacity_unbraked' => Arr::get($attributes, 'TowingCapacityUnbraked'),
            'trunk_volume' => Arr::get($attributes, 'TrunkVolume'),
            'frunk_volume' => Arr::get($attributes, 'FrunkVolume'),
            'battery_type' => Arr::get($attributes, 'BatteryType'),
            'battery_size' => Arr::get($attributes, 'BatterySize'),
            'battery_voltage' => Arr::get($attributes, 'BatteryVoltage'),
            'battery_weight' => Arr::get($attributes, 'BatteryWeight'),
            'ac_charging_speed' => Arr::get($attributes, 'ACChargingSpeed'),
            'ac_charging_percentage' => Arr::get($attributes, 'ACChargingPercentage'),
            'ac_charging_time' => Arr::get($attributes, 'ACChargingTime'),
            'dc_charging_speed' => Arr::get($attributes, 'DCChargingSpeed'),
            'dc_charging_percentage' => Arr::get($attributes, 'DCChargingPercentage'),
            'dc_charging_time' => Arr::get($attributes, 'DCChargingTime'),
        ];

        if (!array_filter($data)) return null;

        return new PowertrainTechnicalSpecificationsData(...$data);
    }

    protected static function mapPowertrainPrices(array|Collection|null $prices): array
    {
        if (!$prices) return [];
        $data = $prices instanceof Collection ? $prices : collect($prices);

        return $data->map(fn($item) => new PriceData(
            dealer_net_price: Arr::get($item, 'DealerNetPrice'),
            dealer_profit: Arr::get($item, 'DealerProfit'),
            minimum_dealer_profit: Arr::get($item, 'MinimumDealerProfit'),
            campaign_dealer_profit: Arr::get($item, 'CampaignDealerProfit'),
            suggested_retail_price: Arr::get($item, 'SuggestedRetailPrice'),
            campaign_retail_price: Arr::get($item, 'CampaignRetailPrice'),
            van_conversion_price: Arr::get($item, 'VanConversionPrice'),
            van_price_vat: Arr::get($item, 'VanPriceVAT'),
            van_price: Arr::get($item, 'VanPrice'),
            fleet_net_price: Arr::get($item, 'FleetNetPrice'),
            valid_from: Arr::get($item, 'ValidFrom') ? substr($item['ValidFrom'], 0, 10) : null,
            valid_to: Arr::get($item, 'ValidTo') ? substr($item['ValidTo'], 0, 10) : null,
        ))->values()->all();
    }

    protected static function mapLeasingPowertrainPrices(array|Collection|null $prices): array
    {
        if (!$prices) return [];
        $data = $prices instanceof Collection ? $prices : collect($prices);

        return $data->map(fn($item) => new LeasingPriceData(
            down_payment: Arr::get($item, 'DownPayment'),
            mp_10000: Arr::get($item, 'MP10000'),
            mp_15000: Arr::get($item, 'MP15000'),
            mp_20000: Arr::get($item, 'MP20000'),
            mp_25000: Arr::get($item, 'MP25000'),
            mp_30000: Arr::get($item, 'MP30000'),
            mp_35000: Arr::get($item, 'MP35000'),
            mp_40000: Arr::get($item, 'MP40000'),
            valid_from: Arr::get($item, 'ValidFrom') ? substr($item['ValidFrom'], 0, 10) : null,
            valid_to: Arr::get($item, 'ValidTo') ? substr($item['ValidTo'], 0, 10) : null,
        ))->values()->all();
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

    protected static function mapFamilyGroups(array|Collection|null $groups): array
    {
        if (!$groups) return [];
        $data = $groups instanceof Collection ? $groups : collect($groups);

        return $data->map(fn($group) => new FamilyGroupData(name: $group))
                    ->values()
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

        return new PriceListData(primary_image: $primaryImage, campaign_mage: $campaignImage);
    }

    protected static function mapAccessoryMapping(array|Collection|null $mapping): array
    {
        if (!$mapping) return [];
        $data = $mapping instanceof Collection ? $mapping : collect($mapping);

        return $data->values()
                    ->all();
    }
}
