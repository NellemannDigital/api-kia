<?php

namespace App\Data\Trim\Color;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class LeasingPriceData extends Data
{
    public function __construct(
        public ?float $monthly_price = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
    ) {}
}

