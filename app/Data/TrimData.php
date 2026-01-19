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
    ColorData,
    PowertrainData,
    LeasingPowertrainData
};

class TrimData extends Data
{
    public function __construct(
        public int $struct_id,
        public string $car_struct_id,
        public string $name,
        public ?int $sort_order = null,
        public ?float $leasing_return_fee = null,
        public ?AssetData $primary_image = null,
        public ?InteriorData $interior = null,
        public ?TechnicalSpecificationsData $technical_specifications = null,
        public ?CampaignData $campaign = null,
        public ChannelsData $channels,
        public array $accessory_mapping = [],
       
        #[DataCollectionOf(EquipmentData::class)]
        public array $equipment = [],

        #[DataCollectionOf(ColorData::class)]
        public array $colors = [],

        #[DataCollectionOf(PowertrainData::class)]
        public array $powertrains = [],

        #[DataCollectionOf(LeasingPowertrainData::class)]
        public array $leasing_powertrains = [],
    ) {}
}
