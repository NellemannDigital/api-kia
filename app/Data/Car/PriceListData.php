<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\Car\PriceList\CampaignData;

class PriceListData extends Data
{
    public function __construct(
        public ?AssetData $primary_image = null,
        public ?CampaignData $campaign = null,
    ) {}
}
