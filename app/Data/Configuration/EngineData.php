<?php

namespace App\Data\Configuration;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use App\Data\Configuration\Engine\ServiceIntervalData;

class EngineData extends Data
{
    public function __construct(
        public string $name,
        public string $code,
        public int $horse_power,
        public string $fuel_type,
        public ?int $amount_of_cylinders,
        public ?int $amount_of_valves,
        public ?float $volume,
        public ?string $horsepower_rev_range,
        public ?ServiceIntervalData $service_interval = null
    ) {}
}

