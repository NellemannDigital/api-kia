<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;

class UrlsData extends Data
{
    public function __construct(
        public ?string $website = null,
        public ?string $build_configurator = null,
        public ?string $leasing_configurator = null,
        public ?string $technical_specifications = null,
        public ?string $test_drive = null,
    ) {}
}
