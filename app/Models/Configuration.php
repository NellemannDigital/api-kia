<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
use App\Data\Configuration\{
    ModelData,
    VariantData,
    EngineData,
    TransmissionData,
    TechnicalSpecificationsData
};
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMeny;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuration extends Model
{
    protected $fillable = [
        'struct_id',
        'car_id',
        'trim_id',
        'powertrain_id',
        'model_code',
        'grade',
        'ocn',
        'is_standard_configuration',
        'model',
        'year',
        'variant',
        'trim',
        'engine',
        'transmission',
        'technical_specifications',
        'model_change_code',
        'original_model_change_code',
        'referenced_foundation_car_id',
        'referenced_foundation_trim_id'
    ];

    protected $casts = [
        'model' => ModelData::class,
        'variant' => VariantData::class,
        'engine' => EngineData::class,
        'transmission' => TransmissionData::class,
        'technical_specifications' => TechnicalSpecificationsData::class
    ];

    /**
     * @return BelongsTo<Powertrain, $this>
     */
    public function powertrain(): BelongsTo
    {
        return $this->belongsTo(Powertrain::class);
    }

    /**
     * @return BelongsToMeny<ExtraEquipmentPackage, $this>
     */
    public function extraEquipmentPackages()
    {
        return $this->belongsToMany(ExtraEquipmentPackage::class);
    }

}
