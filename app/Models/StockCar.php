<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Data\StockCar\{
    InteriorData,
    ExteriorData,
    EngineData,
    TransmissionData,
    TechnicalSpecificationsData
};

class StockCar extends Model
{
    protected $fillable = [
        'dynamics_id',
        'vehicle_number',
        'name',
        'struct_id',
        'vin',
        'model_code',
        'model_year',
        'exterior',
        'interior',
        'equipment',
        'configuration_id',
        'dealer_id'
    ];

    protected $casts = [
        'interior' => InteriorData::class,
        'exterior' => ExteriorData::class
    ];
}
