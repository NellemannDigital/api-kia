<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;
use App\Data\AssetData;

class FileData extends Data
{
    public function __construct(
        public ?AssetData $file = null,
        public ?string $document_type = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
        public ?string $url = null,
    ) {}
}
