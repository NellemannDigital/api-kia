<?php

namespace App\Data\Trim\ExtraEquipmentPackage;

use Spatie\LaravelData\Data;

class ColorRequiredData extends Data
{
    public function __construct(
        public string $code,
        public string $primary_color,
    ) {}
}
