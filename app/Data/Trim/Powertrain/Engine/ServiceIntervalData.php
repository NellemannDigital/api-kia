<?php

namespace App\Data\Trim\Powertrain\Engine;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class ServiceIntervalData extends Data
{
    public function __construct(
        public ?int $months,
        public ?int $kilometers,
        public ?int $oil_change_months,
        public ?int $oil_change_kilometers,
    ) {}
}

