<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\Accessory\{
    PriceData
};

class AccessoryData extends Data
{
    public function __construct(
        public int $struct_id,
        public string $name,
        public ?string $description,
        public ?string $part_number,
        public ?array $categories = [],
        public ?string $disclaimer,
        public bool $leasing_active = false,
        public ?float $leasing_price = null,
        public ?float $leasing_down_payment = null,
        public ?AssetData $primary_image = null,
        public ?AssetData $override_image = null,
        public array $accessory_mapping = [],

        #[DataCollectionOf(PriceData::class)]
        public array $prices = [],

        #[DataCollectionOf(AssetData::class)]
        public array $additional_images = [],
    ) {}
}
