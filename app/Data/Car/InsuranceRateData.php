<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;

class InsuranceRateData extends Data
{
    public function __construct(
        public ?int $age_from = null,
        public ?int $age_to = null,
        public ?float $annual_price = null,
        public ?float $excess = null,
    ) {}
}
