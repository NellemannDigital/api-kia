<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;

class TechnicalSpecificationsData extends Data
{
    public function __construct(
        public ?float $fueltank_volume = null,
        public ?float $electric_horsepower = null,
        public ?string $electric_horsepower_rev_range = null,
        public ?float $electric_torque = null,
        public ?string $electric_torque_rev_range = null,
        public ?float $total_horsepower = null,
        public ?string $total_horsepower_rev_range = null,
        public ?float $total_torque = null,
        public ?string $total_torque_rev_range = null,
    ) {}
}
