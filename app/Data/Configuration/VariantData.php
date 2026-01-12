<?php

namespace App\Data\Configuration;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class VariantData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $code = null,
        public bool $b2b = false,
    ) {}
}

