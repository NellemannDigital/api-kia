<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class CampaignData extends Data
{
    public function __construct(
        public ?AssetData $image = null,
        public ?string $placement = null,
    ) {}
}

