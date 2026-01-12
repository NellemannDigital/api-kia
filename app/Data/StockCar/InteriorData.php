<?php

namespace App\Data\StockCar;

use Spatie\LaravelData\Data;

class InteriorData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $code = null,
    ) {}
}

