<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\Car\{
    ModelData,
    VariantData,
    TechnicalSpecificationsData,
    DimensionsData,
    CampaignData,
    UrlsData,
    ChannelsData,
    PriceListData,
    InsuranceRateData,
    FileData
};

class CarData extends Data
{
    public function __construct(
        public int $struct_id,
        public ?string $web_id,
        public string $name,
        public ?string $year,
        public ?string $delivery_year,
        public ?string $disclaimer = null,
        public ?ModelData $model = null,
        public ?VariantData $variant = null,
        public ?AssetData $primary_image = null,
        public ?TechnicalSpecificationsData $technical_specifications = null,
        public ?DimensionsData $dimensions = null,
        public ?CampaignData $campaign = null,
        public ?UrlsData $urls = null,
        public ChannelsData $channels,
        public ?PriceListData $price_list = null,
        public ?array $categories = [],

        #[DataCollectionOf(FileData::class)]
        public array $files = [],

        #[DataCollectionOf(InsuranceRateData::class)]
        public array $insurance_rates = [],
    ) {}
}
