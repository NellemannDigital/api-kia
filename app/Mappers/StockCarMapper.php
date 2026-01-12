<?php

namespace App\Mappers;

use App\Models\Dealer;
use App\Models\Configuration;
use App\Data\StockCarData;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use App\Data\StockCar\{
    InteriorData,
    ExteriorData
};
use Illuminate\Support\Facades\Log;
use Throwable;

class StockCarMapper
{
    public static function map(array $stockCarData): ?StockCarData
    {

        try {
            $dealerAccountNumber = $stockCarData['dealerNumber'];

            $vehicleNumber = $stockCarData['vehicleNumber'];
            $dynamicsId = $stockCarData['carId'];
            $name = $stockCarData['webType'];
            $struct_id = $stockCarData['structId'];
            $vin = $stockCarData['vin'];
            $modelCode = $stockCarData['vehicleModelCode'];
            $modelYear = $stockCarData['modelYear'];
            $equipment = $stockCarData['equipment'];

            $exterior = self::mapExterior($stockCarData);
            $interior = self::mapInterior($stockCarData);

            $dealerAccountNumber = $stockCarData['dealerNumber'];
            $dealerAccountNumber = (string) $dealerAccountNumber;
            $structId = $stockCarData['structId'];

            $dealer = Dealer::withoutGlobalScopes()
                ->where('account_number', $dealerAccountNumber)
                ->first();

            if (! $dealer) {
                Log::warning('Error mapping StockCar: Couldn\'t find a Dealer.', [
                    'vehicleNumber' => $vehicleNumber,
                    'dealerAccountNumber' => $dealerAccountNumber ?? ''
                ]);

                return null;
            }

            $configuration = Configuration::withoutGlobalScopes()
                ->where('struct_id', $structId)
                ->first();

            if (! $configuration) {
                Log::warning('Error mapping StockCar: Couldn\'t find a Configuration.', [
                    'configurationId' => $structId
                ]);

                return null;
            }

            return new StockCarData(
                vehicle_number: $vehicleNumber,
                dynamics_id: $dynamicsId,
                name: $name,
                struct_id: $structId,
                vin: $vin,
                model_code: $modelCode,
                model_year: $modelYear,
                equipment: $equipment,
                exterior: $exterior,
                interior: $interior,
                configuration_id: $configuration->id,
                dealer_id: $dealer->id
            );

        } catch (Throwable $e) {
            Log::error('Error mapping StockCar', [
                'vehicleNumber' => $stockCarData['vehicleNumber'],
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected static function mapExterior(array|Collection|null $attributes): ?ExteriorData
    {
        if (!$attributes) return null;

        $name = $attributes['exteriorColorName'];
        $code = $attributes['exteriorColorCode'];

        if (!$name && !$code) return null;

        return new ExteriorData(
            name: $name,
            code: $code
        );
    }

    protected static function mapInterior(array|Collection|null $attributes): ?InteriorData
    {
        if (!$attributes) return null;

        $name = $attributes['interiorColorName'];
        $code = $attributes['interiorColorCode'];

        if (!$name && !$code) return null;

        return new InteriorData(
            name: $name,
            code: $code
        );
    }
}
