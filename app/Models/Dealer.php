<?php

namespace App\Models;

use App\Data\Dealer\ChannelsData;
use App\Data\Dealer\EmailsData;
use App\Data\Dealer\OpeningHoursData;
use App\Data\Dealer\PostalCodesData;
use App\Data\Dealer\TypesData;
use App\Data\Dealer\UrlsData;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $fillable = [
        'dynamics_id',
        'account_number',
        'company_id',
        'crm_id',
        'dealerbridge_id',
        'bilinfo_id',
        'autouncle_department_id',
        'rooftop_id',
        'dealer_guid',
        'owner_guid',
        'channels',
        'name',
        'display_name',
        'cvr_number',
        'group',
        'street_name',
        'street_number',
        'city',
        'zip_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'emails',
        'urls',
        'types',
        'opening_hours',
        'postal_codes',
        'synced_at',
    ];

    protected $casts = [
        'channels' => ChannelsData::class,
        'emails' => EmailsData::class,
        'urls' => UrlsData::class,
        'types' => TypesData::class,
        'opening_hours' => OpeningHoursData::class,
        'postal_codes' => PostalCodesData::class,
    ];
}
