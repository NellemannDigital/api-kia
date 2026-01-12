<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsedCar extends Model
{
    protected $fillable = [
        'vehicle_id',
        'mileage',
    ];
}
