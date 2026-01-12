<?php

namespace App\Data\Configuration\TechnicalSpecifications;

use Spatie\LaravelData\Data;

class ConsumptionData extends Data
{
    public function __construct(
        public ?float $number = null,
        public ?string $unit = null,
    ) {}
}
