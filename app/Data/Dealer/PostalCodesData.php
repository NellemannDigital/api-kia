<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;
use App\Data\Configuration\OpeningHours\{
    B2bData,
    B2cData
};

class PostalCodesData extends Data
{
    public function __construct(
        public array $b2b = [],
        public array $b2c = [],
    ) {}
}

