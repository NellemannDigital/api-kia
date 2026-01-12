<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;

class UrlsData extends Data
{
    public function __construct(
        public ?string $website,
        public ?string $intern_website,
        public ?string $privacy_policy,
        public ?string $service_booking
    ) {}
}

