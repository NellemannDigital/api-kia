<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;

class WarrantyData extends Data
{
    public function __construct(
        public ?string $registration_purpose = null,
        public ?string $base_warranty = null,
        public ?string $unlimited_period = null,
        public ?string $hv_battery_warranty = null,
        public ?string $paint_warranty = null,
        public ?string $corrosion_warranty = null,
        public ?string $vehicle_type = null,
    ) {}
}