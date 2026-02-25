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
use Illuminate\Database\Eloquent\Relations\HasOne;

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

     /**
     * @return HasOne<Configuration, $this>
     */
    public function configuration(): HasOne
    {
        return $this->hasOne(Configuration::class, 'id', 'configuration_id');
    }

    
     /**
     * @return HasOne<Dealer, $this>
     */
    public function dealer(): HasOne
    {
        return $this->hasOne(Dealer::class, 'id', 'dealer_id');
    }
}
