<?php

namespace App\Data\Trim\Powertrain;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class TransmissionData extends Data
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $charge_plug_type,
        public ?int $number_of_gears
    ) {}
}

