<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;

class EmailsData extends Data
{
    public function __construct(
        public ?string $contact,
        public ?string $sales,
        public ?string $marketing,
        public ?string $workshop,
        public ?string $spare_parts,
        public ?string $private_leasing
    ) {}
}

