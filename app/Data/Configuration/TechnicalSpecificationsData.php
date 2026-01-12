<?php

namespace App\Data\Configuration;

use Spatie\LaravelData\Data;
use App\Data\Configuration\TechnicalSpecifications\{
    ConsumptionData
};

class TechnicalSpecificationsData extends Data
{
    public function __construct(
        public ?float $pure_electric_range = null,
        public ?float $co2_emission = null,
        public ?float $kilometers_per_litre = null,
        public ?ConsumptionData $consumption = null,
        public ?float $pure_electric_consumption = null,
        public ?float $owner_tax = null,
        public ?string $energy_label = null,
        public ?float $sound_level_stationary_db = null,
        public ?float $sound_level_stationary_rpm = null,
        public ?float $sound_level_drive_by = null,
        public ?float $battery_size = null,
        public ?float $total_horsepower = null,
        public bool $has_coc_data_from_dataverse = false,
    ) {}
}
