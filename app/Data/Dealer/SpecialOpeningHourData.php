<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;

class SpecialOpeningHourData extends Data
{
    public function __construct(
        public ?string $date,
        public ?string $opening_time = null,
        public ?string $closing_time = null,
        public ?bool $closed = null,
        public ?string $display_name = null,
    ) {}
}
