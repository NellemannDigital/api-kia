<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'images'
    ];

    protected $casts = [
        'images' => DataCollection::class . ':' . AssetData::class,
    ];

    /**
     * @return BelongsToMany<Trim, $this>
     */
    public function trims()
    {
        return $this->belongsToMany(Trim::class, 'equipment_trim');
    }
}
