<?php

namespace App\Data\Trim\Color;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class PriceData extends Data
{
    public function __construct(
        public ?float $dealer_net_price = null,
        public ?float $dealer_profit = null,
        public ?float $suggested_retail_price = null,
        public ?float $campaign_retail_price = null,
        public ?float $retail_price_ex_vat = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
    ) {}
}

