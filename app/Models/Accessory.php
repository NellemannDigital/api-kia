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

class Accessory extends Model
{
    protected $fillable = [
        'struct_id',
        'name',
        'description',
        'part_number',
        'category_one',
        'category_two',
        'disclaimer',
        'primary_image',
        'override_image',
        'prices',
        'additional_images'
    ];

    protected $casts = [
        'primary_image' => AssetData::class,
        'override_image' => AssetData::class,
        'prices' => DataCollection::class . ':' . PriceData::class,
        'additional_images' => DataCollection::class . ':' . AssetData::class,
    ];

    /**
     * @return BelongsToMany<Trim, $this>
     */
    public function trims()
    {
        return $this->belongsToMany(Trim::class, 'accessory_trim');
    }
}
