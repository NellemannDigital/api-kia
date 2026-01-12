<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;
use App\Data\Trim\Powertrain\{
    EngineData,
    TransmissionData,
    TechnicalSpecificationsData,
    PriceData
};

class PowertrainData extends Data
{
    public function __construct(
        public ?string $ocn,
        public int $configuration_id,
        public EngineData $engine,
        public TransmissionData $transmission,
        public ?TechnicalSpecificationsData $technical_specifications = null,

        #[DataCollectionOf(PriceData::class)]
        public array $prices = [],
    ) {}
}
