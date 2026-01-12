<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class InteriorData extends Data
{
    public function __construct(
        public ?AssetData $image = null,
        public ?string $name = null,
        public ?string $code = null,
    ) {}
}

