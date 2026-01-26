<?php

namespace App\Data\Trim\ExtraEquipmentPackage;

use Spatie\LaravelData\Data;

class ColorRequiredData extends Data
{
    public function __construct(
        public ?string $code = null,
        public ?string $primary_color = null,
        public ?string $secondary_color = null,
    ) {}
}
