<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Data\Dealer\{
    ChannelsData,
    EmailsData,
    UrlsData,
    TypesData,
    OpeningHoursData,
    PostalCodesData
};

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
        'postal_codes'
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
