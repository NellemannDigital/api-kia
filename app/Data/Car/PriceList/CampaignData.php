<?php

namespace App\Data\Car\PriceList;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class CampaignData extends Data
{
    public function __construct(
        public ?AssetData $image = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
    ) {}
}

