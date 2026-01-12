<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    protected $fillable = [
        'powertrain_id',
        'dealer_net_price',
        'dealer_profit',
        'minimum_dealer_profit',
        'campaign_dealer_profit',
        'suggested_retail_price',
        'campaign_retail_price',
        'van_conversion_price',
        'van_price_vat',
        'van_price',
        'fleet_net_price',
        'valid_from',
        'valid_to',
    ];

    protected static function booted()
    {
        static::addGlobalScope('valid', function (Builder $builder) {
            $today = Carbon::today()->toDateString();

            $builder->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })->where(function ($q) use ($today) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            });
        });
    }

    /**
     * @return BelongsTo<Powertrain, $this>
     */
    public function powertrain(): BelongsTo
    {
        return $this->belongsTo(Powertrain::class);
    }
}
