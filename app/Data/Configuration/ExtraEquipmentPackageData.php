<?php

namespace App\Data\Configuration;

use Spatie\LaravelData\Data;

class ExtraEquipmentPackageData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $category = null,
    ) {}
}
