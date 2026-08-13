<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;

class LeasingColorData extends Data
{
    public function __construct(
        public string $code,
        public ?float $price = null,
    ) {}
}
