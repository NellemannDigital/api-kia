<?php

namespace App\Data\Accessory;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class PriceData extends Data
{
    public function __construct(
        public ?float $price = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
    ) {}
}

