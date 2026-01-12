<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;
use App\Data\Trim\Powertrain\{
    LeasingPriceData
};

class LeasingPowertrainData extends Data
{
    public function __construct(
        public string $configuration_id,
        public bool $leasing_active = false,

        #[DataCollectionOf(LeasingPriceData::class)]
        public array $leasing_prices = [],
    ) {}
}
