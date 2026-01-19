<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\AssetData;

class EquipmentData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $category,

        #[DataCollectionOf(AssetData::class)]
        public array $images = [],
    ) {}
}
