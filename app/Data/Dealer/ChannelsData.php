<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;

class ChannelsData extends Data
{
    public function __construct(
        public bool $web = false,
        public bool $test_drive = false,
        public bool $webshop = false,
        public bool $sales_advisor = false,
        public bool $pickup_location = false,
        public bool $insurance_calculator = false,
        public bool $pdf_list = false,
    ) {}
}

