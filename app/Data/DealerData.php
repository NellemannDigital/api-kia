<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\Dealer\{
    ChannelsData,
    EmailsData,
    UrlsData,
    TypesData,
    OpeningHoursData,
    PostalCodesData
};

class DealerData extends Data
{
    public function __construct(
        public string $dynamics_id,
        public string $account_number,
        public ?string $company_id,
        public ?string $crm_id,
        public ?string $dealerbridge_id,
        public ?string $bilinfo_id,
        public ?string $autouncle_department_id,
        public ?string $rooftop_id,
        public ?string $dealer_guid,
        public ?string $owner_guid,
        public ?ChannelsData $channels = null,
        public string $name,
        public ?string $display_name,
        public ?int $cvr_number,
        public ?string $group,
        public ?string $street_name,
        public ?string $street_number,
        public ?string $city,
        public ?int $zip_code,
        public ?string $country,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $phone,
        public ?EmailsData $emails = null,
        public ?UrlsData $urls = null,
        public ?TypesData $types = null,
        public ?OpeningHoursData $opening_hours = null,
        public ?PostalCodesData $postal_codes = null,
    ) {}
}
