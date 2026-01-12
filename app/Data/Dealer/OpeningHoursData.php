<?php

namespace App\Data\Dealer;

use Spatie\LaravelData\Data;
use App\Data\Dealer\OpeningHours\{
    SalesData,
    WorkshopData
};

class OpeningHoursData extends Data
{
    public function __construct(
        public ?SalesData $sales = null,
        public ?WorkshopData $workshop = null,
    ) {}
}

