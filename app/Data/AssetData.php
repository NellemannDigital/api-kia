<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Data;

#[MapOutputName(SnakeCaseMapper::class)]
class AssetData extends Data
{
    public function __construct(
        public string $struct_id,
        public string $name,
        public string $url,
        public string $file_type,
        public string $type,
    ) {}
}
