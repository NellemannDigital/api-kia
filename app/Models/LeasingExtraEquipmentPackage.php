<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeasingExtraEquipmentPackage  extends Model
{
    protected $fillable = [
        'trim_id',
        'extra_equipment_package_id',
        'code',
        'price',
        'down_payment',
    ];

    /**
     * @return BelongsTo<Trim, $this>
     */
    public function trim(): BelongsTo
    {
        return $this->belongsTo(Trim::class);
    }

     /**
     * @return BelongsTo<Color, $this>
     */
    public function extraEquipmentPackage(): BelongsTo
    {
        return $this->belongsTo(ExtraEquipmentPackage::class);
    }
}