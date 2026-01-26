<?php

namespace App\Data\Trim\ExtraEquipmentPackage;

use Spatie\LaravelData\Data;

class ExcludesStandardEquipmentData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $category = null,
    ) {}
}

