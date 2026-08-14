<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;

class LeasingExtraEquipmentPackageData extends Data
{
    public function __construct(
        public string $code,
        public ?float $price = null,
        public ?float $down_payment = null,
    ) {}
}
