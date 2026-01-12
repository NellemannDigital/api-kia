<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class UsedCarData extends Data
{
    public function __construct(
        public string $vehicle_id,
        public string $mileage,
    ) {}
}
