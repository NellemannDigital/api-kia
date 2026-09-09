<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\SparePart\{
    PriceData
};

class SparePartData extends Data
{
    public function __construct(
        public int $struct_id,
        public string $name,
        public string $part_number,
        public ?float $price = null,
        public string $group,
        public ?float $weight = null,
        public ?float $width = null,
        public ?float $length = null,
        public ?float $height = null,
    ) {}
}
