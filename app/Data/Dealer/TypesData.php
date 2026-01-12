<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;

class TypesData extends Data
{
    public function __construct(
        public bool $b2c = false,
        public bool $b2b = false,
        public bool $service = false,
    ) {}
}

