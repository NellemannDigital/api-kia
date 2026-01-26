<?php

namespace App\Data\Trim\ExtraEquipmentPackage;

use Spatie\LaravelData\Data;

class RequiresData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $category = null,
    ) {}
}

