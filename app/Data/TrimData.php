<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\EquipmentData;
use App\Data\Trim\{
    InteriorData,
    TechnicalSpecificationsData,
    CampaignData,
    ChannelsData,
    PowertrainData,
    ColorData,
    ExtraEquipmentPackageData,
    LeasingPowertrainData,
    LeasingColorData,
    LeasingExtraEquipmentPackageData,
};

class TrimData extends Data
{
    public function __construct(
        public int $struct_id,
        public string $car_struct_id,
        public string $name,
        public ?int $sort_order = null,
        public ?float $leasing_return_fee = null,
        public bool $uses_high_tax = false,
        public ?AssetData $primary_image = null,
        public ?InteriorData $interior = null,
        public ?TechnicalSpecificationsData $technical_specifications = null,
        public ?CampaignData $campaign = null,
        public ChannelsData $channels,
        public array $accessory_mapping = [],
        public array $featured_product_details = [],
       
        #[DataCollectionOf(EquipmentData::class)]
        public array $equipment = [],

        #[DataCollectionOf(PowertrainData::class)]
        public array $powertrains = [],

        #[DataCollectionOf(ExtraEquipmentPackageData::class)]
        public array $extra_equipment_packages = [],

        #[DataCollectionOf(ColorData::class)]
        public array $colors = [],

        #[DataCollectionOf(LeasingPowertrainData::class)]
        public array $leasing_powertrains = [],

        #[DataCollectionOf(LeasingExtraEquipmentPackageData::class)]
        public array $leasing_extra_equipment_packages = [],

        #[DataCollectionOf(LeasingColorData::class)]
        public array $leasing_colors = [],
    ) {}
}
