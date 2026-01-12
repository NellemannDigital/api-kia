<?php

namespace App\Mappers;

use App\Data\UsedCarData;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class UsedCarMapper
{
    public static function map(array $usedCarData): UsedCarData
    {

        try {
            $vehicleId = $usedCarData['Id'];
            $mileage = $usedCarData['Mileage'];

            return new UsedCarData(
                vehicle_id: $vehicleId,
                mileage: $mileage
            );

        } catch (Throwable $e) {
            Log::error('Error mapping UsedCar', [
                'vehicleId' => $usedCarData['Id'],
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
