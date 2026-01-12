<?php

namespace App\Data\Trim\Powertrain;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class LeasingPriceData extends Data
{
    public function __construct(
        public ?float $down_payment = null,
        public ?float $mp_10000 = null,
        public ?float $mp_15000 = null,
        public ?float $mp_20000 = null,
        public ?float $mp_25000 = null,
        public ?float $mp_30000 = null,
        public ?float $mp_35000 = null,
        public ?float $mp_40000 = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
    ) {}
}

