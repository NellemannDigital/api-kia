<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;

class DimensionsData extends Data
{
    public function __construct(
        public ?float $cargo_volume = null,
        public ?float $cargo_length = null,
        public ?float $cargo_width_max = null,
        public ?float $cargo_width_min = null,
        public ?float $cargo_width_wheel_housing = null,
        public ?float $cargo_height = null,
        public ?float $loading_height_rear = null,
        public ?float $loading_height_side = null,
        public ?float $chassis_length = null,
        public ?float $chassis_height = null,
        public ?float $chassis_max_overhang = null,
    ) {}
}
