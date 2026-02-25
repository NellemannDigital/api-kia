<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Data\AssetData;
use App\Data\Trim\{
    InteriorData,
    TechnicalSpecificationsData,
    CampaignData,
    ChannelsData
};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Scopes\OpenChannels;

class Trim extends Model
{
    protected $appends = ['electric_range', 'consumption_range', 'co2_emission_range', 'ac_charging_time_range', 'dc_charging_time_range', 'ac_charging_speed_range', 'dc_charging_speed_range', 'owner_tax_range'];

    protected $fillable = [
        'struct_id',
        'car_id',
        'name',
        'sort_order',
        'leasing_return_fee',
        'primary_image',
        'interior',
        'technical_specifications',
        'campaign',
        'channels',
        'accessory_mapping'
    ];

    protected $casts = [
        'primary_image' => AssetData::class,
        'interior' => InteriorData::class,
        'technical_specifications' => TechnicalSpecificationsData::class,
        'campaign' => CampaignData::class,
        'channels' => ChannelsData::class,
        'accessory_mapping' => 'array'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new OpenChannels(['channels->master_channel']));

        static::addGlobalScope('hasPowertrains', function ($builder) {
            $builder->has('powertrains');
        });

        static::addGlobalScope('sort_order', function (Builder $builder) {
            $builder->orderBy('sort_order');
        });
    }

    /**
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * @return HasMany<Powertrain, $this>
     */
    public function powertrains(): HasMany
    {
        return $this->hasMany(Powertrain::class);
    }

    /**
     * @return HasMany<Color, $this>
     */
    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }

    /**
     * @return BelongsToMany<Accessory, $this>
     */
    public function accessories()
    {
        return $this->belongsToMany(Accessory::class, 'accessory_trim');
    }

      /**
     * @return BelongsToMany<Equipment, $this>
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'equipment_trim');
    }

      /**
     * @return HasMeny<ExtraEquipmentPackage, $this>
     */
    public function extraEquipmentPackages()
    {
        return $this->hasMany(ExtraEquipmentPackage::class);
    }

    public function getElectricRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('pure_electric_range');
        }

        return null;
    }

    public function getConsumptionRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('consumption.number');
        }

        return null;
    }

    public function getCo2EmissionRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('co2_emission');
        }

        return null;
    }

    public function getAcChargingTimeRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('ac_charging_time');
        }

        return null;
    }

    public function getDcChargingTimeRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('dc_charging_time');
        }

        return null;
    }

    public function getAcChargingSpeedRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('ac_charging_speed');
        }

        return null;
    }

    public function getDcChargingSpeedRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('dc_charging_speed');
        }

        return null;
    }

    public function getOwnerTaxRangeAttribute()
    {
        if ($this->relationLoaded('powertrains')) {
            return $this->specRange('owner_tax');
        }

        return null;
    }

    public function specRange(string $key): ?array
    {
        $values = $this->powertrains
            ->map(function ($powertrain) use ($key) {
                $configValue = data_get($powertrain, "configuration.technical_specifications.$key");

                return $configValue ?? data_get($powertrain->technical_specifications, $key);
            })
            ->filter(fn($v) => $v !== null)
            ->values();

        if ($values->isEmpty()) {
            return null;
        }

        return [
            'min' => $values->min(),
            'max' => $values->max(),
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
