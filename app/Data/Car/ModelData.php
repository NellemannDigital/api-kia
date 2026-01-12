<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class ModelData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $brand = null,
        public ?string $code = null,
        public ?string $parent_model_code = null,
    ) {}
}

