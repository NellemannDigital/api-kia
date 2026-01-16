<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Data\AssetData;

class Color extends Model
{
    protected $fillable = [
        'trim_id',
        'code',
        'primary_color',
        'secondary_color',
        'type',
        'color_image',
        'ocn_change_code',
        'turntable_images',
    ];

    protected $casts = [
        'color_image' => AssetData::class
    ];

    protected static function booted()
    {
        static::addGlobalScope('hasPrices', function (Builder $builder) {
            $builder->has('prices');
        });
    }

    /**
     * @return BelongsTo<Trim, $this>
     */
    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class);
    }

        /**
     * @return HasMany<ColorPrice, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ColorPrice::class);
    }
}