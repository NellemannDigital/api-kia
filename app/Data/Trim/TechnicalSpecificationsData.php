<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;

class TechnicalSpecificationsData extends Data
{
    public function __construct(
        public ?float $length = null,
        public ?float $width = null,
        public ?float $height = null,
        public ?float $wheelbase = null,
        public ?float $minimum_groundclearance = null,
        public ?float $track_width = null,
        public ?float $front_overhang = null,
        public ?float $rear_overhang = null,
        public ?float $wading_depth = null,
        public ?float $entry_angle = null,
        public ?float $exit_angle = null,
        public ?float $cargo_bed_length = null,
        public ?float $cargo_bed_width = null,
        public ?float $cargo_bed_height = null,
        public ?float $cargo_bed_area = null,
        public ?float $maximum_load_capacity = null,
    ) {}
}
