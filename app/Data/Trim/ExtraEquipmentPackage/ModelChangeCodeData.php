<?php

namespace App\Data\Trim\ExtraEquipmentPackage;

use Spatie\LaravelData\Data;

class ModelChangeCodeData extends Data
{
    public function __construct(
        public string $code,
        public string $name
    ) {}
}

