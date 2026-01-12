<?php

namespace App\Data\Trim\Powertrain;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class PriceData extends Data
{
    public function __construct(
        public ?float $dealer_net_price = null,
        public ?float $dealer_profit = null,
        public ?float $minimum_dealer_profit = null,
        public ?float $campaign_dealer_profit = null,
        public ?float $suggested_retail_price = null,
        public ?float $campaign_retail_price = null,
        public ?float $van_conversion_price = null,
        public ?float $van_price_vat = null,
        public ?float $van_price = null,
        public ?float $fleet_net_price = null,
        public ?string $valid_from = null,
        public ?string $valid_to = null,
    ) {}
}

