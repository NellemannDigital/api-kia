<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;

class DeliveryData extends Data
{
    public function __construct(
        public ?string $vehicle_type = null,
        public ?string $year = null,
        public ?string $fee = null,
        public ?string $ev_tax_threshold = null,
        public ?string $tax_fase = null,
        public ?string $total_tax_ev = null,
    ) {}
}
