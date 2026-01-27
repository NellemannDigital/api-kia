<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Data\Trim\ExtraEquipmentPackage\{
    InteriorOverrideToData,
    ModelChangeCodeData,
    TransmissionRequiredData,
    ColorRequiredData,
    RequiresData,
    ExcludesData,
    ExcludesStandardEquipmentData,
    EngineRequiredData
};

class ExtraEquipmentPackage extends Model
{
    protected $fillable = [
        'trim_id',
        'code',
        'name',
        'category',
        'image',
        'interior_override_to',
        'model_change_code',
        'transmission_required',
        'color_required',
        'requires',
        'excludes',
        'excludes_standard_equipment',
        'engine_required'
    ];

    protected $casts = [
        'image' => AssetData::class,
        'interior_override_to' => InteriorOverrideToData::class,
        'model_change_code' => ModelChangeCodeData::class,
        'transmission_required' => TransmissionRequiredData::class,
        'engine_required' => 'array',
        'color_required' => 'array',
        'requires' => 'array',
        'excludes' => 'array',
        'excludes_standard_equipment'=> 'array',
    ];

    /**
     * @return BelongsToMany<Equipment, $this>
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'extra_equipment_package_equipment');
    }

    /**
     * @return HasMany<ExtraEquipmentPackagePrice, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ExtraEquipmentPackagePrice::class);
    }
}