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
}
