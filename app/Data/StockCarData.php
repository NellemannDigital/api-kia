<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\StockCar\{
    ExteriorData,
    InteriorData
};

class StockCarData extends Data
{
    public function __construct(
        public string $vehicle_number,
        public string $dynamics_id,
        public string $name,
        public string $struct_id,
        public ?string $vin,
        public ?string $model_code,
        public ?string $model_year,
        public ?string $equipment,
        public ?ExteriorData $exterior = null,
        public ?InteriorData $interior = null,
        public ?int $configuration_id,
        public ?int $dealer_id
    ) {}
}
