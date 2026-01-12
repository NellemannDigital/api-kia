<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class PriceListData extends Data
{
    public function __construct(
        public ?AssetData $primary_image = null,
        public ?AssetData $campaign_image = null,
    ) {}
}
