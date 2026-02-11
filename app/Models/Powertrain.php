<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Data\Trim\Powertrain\{
    EngineData,
    TransmissionData,
    TechnicalSpecificationsData
};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Powertrain extends Model
{
    protected $appends = ['electric_range', 'consumption_range', 'co2_emission_range', 'ac_charging_time_range', 'dc_charging_time_range', 'ac_charging_speed_range', 'dc_charging_speed_range', 'owner_tax_range'];

    protected $fillable = [
        'trim_id',
        'configuration_id',
        'ocn',
        'leasing_active',
        'engine',
        'transmission',
        'technical_specifications',
    ];

    protected $casts = [
        'engine' => EngineData::class,
        'transmission' => TransmissionData::class,
        'technical_specifications' => TechnicalSpecificationsData::class,
    ];

    protected static function booted()
    {
        static::addGlobalScope('hasAnyPrices', function ($query) {
            $query->where(function ($q) {
                $q->whereHas('prices')
                ->orWhereHas('leasingPrices');
            });
        });
    }

    public function scopeHasPrices($query)
    {
        return $query->whereHas('prices');
    }

    public function scopeHasLeasingPrices($query)
    {
        return $query->whereHas('leasingPrices');
    }

    public function getHasPricesAttribute()
    {
        if ($this->relationLoaded('prices')) {
            return $this->prices->isNotEmpty();
        }

        return $this->prices()->exists();
    }

    public function getHasLeasingAttribute()
    {
        if ($this->relationLoaded('leasingPrices')) {
            return $this->leasingPrices->isNotEmpty();
        }

        return $this->leasingPrices()->exists();
    }


    /**
     * @return BelongsTo<Trim, $this>
     */
    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class);
    }

    /**
     * @return HasMany<Price, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * @return HasMany<LeasingPrice, $this>
     */
    public function leasingPrices(): HasMany
    {
        return $this->hasMany(LeasingPrice::class);
    }

     /**
     * @return HasOne<Configuration, $this>
     */
    public function configuration(): HasOne
    {
        return $this->hasOne(Configuration::class, 'struct_id', 'configuration_id');
    }
}
