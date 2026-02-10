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

    public function getElectricRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('pure_electric_range');
    }

    public function getConsumptionRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('consumption.number');
    }

    public function getCo2EmissionRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('co2_emission');
    }

    public function getAcChargingTimeRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('ac_charging_time');
    }

    public function getDcChargingTimeRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('dc_charging_time');
    }

    public function getAcChargingSpeedRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('ac_charging_speed');
    }

    public function getDcChargingSpeedRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('dc_charging_speed');
    }

    public function getOwnerTaxRangeAttribute()
    {
        $this->loadMissing('configuration');

        return $this->specRange('owner_tax');
    }

    public function specRange(string $key): ?array
    {
        $value = data_get($this->technical_specifications, $key);

        if ($value === null && isset($this->configuration)) {
            $value = data_get($this->configuration->technical_specifications, $key);
        }

        if ($value === null) {
            return null;
        }

        return [
            'min' => $value,
            'max' => $value,
        ];
    }

    public function formattedSpecRange(string $key, int $decimals = 0, string $unit = ''): ?string
    {
        $range = $this->specRange($key);

        if (!$range) {
            return null;
        }

        $min = number_format($range['min'], $decimals, ',', '.');
        $max = number_format($range['max'], $decimals, ',', '.');

        if ($range['min'] == $range['max']) {
            return $min . ($unit ? ' ' . $unit : '');
        }

        return $min . ' - ' . $max . ($unit ? ' ' . $unit : '');
    }
}
