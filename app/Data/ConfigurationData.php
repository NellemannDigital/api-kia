<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\Configuration\{
    ModelData,
    VariantData,
    EngineData,
    TransmissionData,
    TechnicalSpecificationsData
};

class ConfigurationData extends Data
{
    public function __construct(
        public int $struct_id,
        public int $car_id,
        public int $trim_id,
        public int $powertrain_id,
        public string $model_code,
        public string $grade,
        public ?string $ocn,
        public bool $is_standard_configuration = false,
        public ?ModelData $model = null,
        public ?string $year,
        public ?VariantData $variant = null,
        public ?string $trim,
        public EngineData $engine,
        public TransmissionData $transmission,
        public ?TechnicalSpecificationsData $technical_specifications = null,

        public ?string $model_change_code,
        public ?string $original_model_change_code,
        public ?string $referenced_foundation_car_id,
        public ?string $referenced_foundation_trim_id,
    ) {}
}
