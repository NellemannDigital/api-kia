<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
use App\Data\Accessory\{
    PriceData
};
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accessory extends Model
{
    protected $fillable = [
        'struct_id',
        'name',
        'description',
        'part_number',
        'categories',
        'disclaimer',
        'primary_image',
        'override_image',
        'additional_images'
    ];

    protected $casts = [
        'categories' => 'array',
        'primary_image' => AssetData::class,
        'override_image' => AssetData::class,
        'additional_images' => DataCollection::class . ':' . AssetData::class,
    ];

    protected static function booted()
    {
        static::addGlobalScope('hasPrices', function ($builder) {
            $builder->has('prices');
        });

        static::addGlobalScope('hasImage', function ($builder) {
            $builder->whereNotNull('primary_image');
        });
    }

    /**
     * @return BelongsToMany<Trim, $this>
     */
    public function trims()
    {
        return $this->belongsToMany(Trim::class, 'accessory_trim');
    }

    /**
     * @return HasMany<AccessoryPrice, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(AccessoryPrice::class);
    }
}
