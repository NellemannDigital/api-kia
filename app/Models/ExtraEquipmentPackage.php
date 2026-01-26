<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
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
        'color_required' => ColorRequiredData::class,
        'requires' => DataCollection::class . ':' . RequiresData::class,
        'excludes' => DataCollection::class . ':' . ExcludesData::class,
        'excludes_standard_equipment' => DataCollection::class . ':' . ExcludesStandardEquipmentData::class,
        'engine_required' => DataCollection::class . ':' . EngineRequiredData::class,
    ];

    /**
     * @return BelongsToMany<Equipment, $this>
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'extra_equipment_package_equipment');
    }
}