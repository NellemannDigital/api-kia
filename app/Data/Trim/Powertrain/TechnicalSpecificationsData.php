<?php

namespace App\Data\Trim\Powertrain;

use Spatie\LaravelData\Data;

class TechnicalSpecificationsData extends Data
{
    public function __construct(
        public ?float $torque = null,
        public ?string $torque_rev_range = null,
        public ?float $zero_to_hundred_time = null,
        public ?float $topspeed = null,
        public ?float $net_weight = null,
        public ?float $driving_ready_weight = null,
        public ?float $maximum_total_weight = null,
        public ?float $usefull_load = null,
        public ?float $towing_capacity_braked = null,
        public ?float $towing_capacity_unbraked = null,
        public ?string $trunk_volume = null,
        public ?float $frunk_volume = null,
        public ?string $battery_type = null,
        public ?float $battery_size = null,
        public ?float $battery_voltage = null,
        public ?float $battery_weight = null,
        public ?float $ac_charging_speed = null,
        public ?string $ac_charging_percentage = null,
        public ?string $ac_charging_time = null,
        public ?float $dc_charging_speed = null,
        public ?string $dc_charging_percentage = null,
        public ?string $dc_charging_time = null,
    ) {}
}
