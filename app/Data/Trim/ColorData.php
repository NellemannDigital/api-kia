<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\Trim\Color\{
    PriceData
};

class ColorData extends Data
{
    public function __construct(
        public string $code,
        public string $primary_color,
        public ?string $secondary_color,
        public ?string $type,
        public ?AssetData $color_image = null,
        public ?string $ocn_change_code,
        
        #[DataCollectionOf(AssetData::class)]
        public array $turntable_images = [],

        #[DataCollectionOf(PriceData::class)]
        public array $prices = [],
    ) {}
}
