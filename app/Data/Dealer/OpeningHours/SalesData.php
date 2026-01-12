<?php

namespace App\Data\Dealer\OpeningHours;
use Spatie\LaravelData\Data;

class SalesData extends Data
{
    public function __construct(
        public ?string $monday,
        public ?string $tuesday,
        public ?string $wednesday,
        public ?string $thursday,
        public ?string $friday,
        public ?string $saturday,
        public ?string $sunday,
    ) {}
}

