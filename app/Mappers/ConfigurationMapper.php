<?php

namespace App\Mappers;

use App\Models\Car;
use App\Data\ConfigurationData;
use App\Data\Configuration\{
    ModelData,
    VariantData,
    TechnicalSpecificationsData,
    EngineData,
    TransmissionData,
    Engine\ServiceIntervalData,
    TechnicalSpecifications\ConsumptionData
};
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConfigurationMapper
{
    public static function map(Collection $configurationData, Collection $attributesData, callable $getAsset): ?ConfigurationData
    {
        try {
            $model = self::mapModel($attributesData->get('KiaModel'));
            $variant = self::mapVariant($attributesData->get('KiaVariant'));
            $engine = self::mapEngine($attributesData->get('KiaEngine'));
            $transmission = self::mapTransmission($attributesData->get('Transmission'));

            $configurationId = Arr::get($configurationData, 'Id', '');
            $modelCode = Arr::get($attributesData, 'ModelCode', '');
            $grade = Arr::get($attributesData, 'Grade.Code', '');
            $ocn = Arr::get($attributesData, 'OCN', '');
            $isStandardConfiguration = Arr::get($attributesData, 'SharedIsStandardConfig', '') ?? false;
            $year = Arr::get($attributesData, 'KiaModelYear.ModelYear', '');
            $trimName = Arr::get($attributesData, 'KiaEquipmentVariant', '');
            $modelChangeCode = Arr::get($attributesData, 'KiaModelChangeCode.Code', '');
            $originalModelChangeCode = Arr::get($attributesData, 'KiaOriginalModelChangeCode', '');
            $referencedFoundationCarId = Arr::get($attributesData, 'SharedStructFoundationReferenceProductId', '');
            $referencedFoundationTrimId = Arr::get($attributesData, 'SharedStructFoundationReferenceVariantId', '');

            $technicalSpecifications = self::mapTechnicalSpecifications($attributesData);

            $car = Car::withoutGlobalScopes()
                ->where('struct_id', $referencedFoundationCarId)
                ->first();

            if (! $car) {
                $car = Car::withoutGlobalScopes()
                    ->where('model->code', $model->code)
                    ->where('model->name', $model->name)
                    ->where('year', $year)
                    ->where('variant->code', $variant->code)
                    ->first();
            }

            if (! $car) {
                Log::warning('Error mapping Configuration: Couldn\'t find a Car.', [
                    'productId' => $configurationId
                ]);

                return null;
            }

            $trim = $car->trims()
                ->where('struct_id', $referencedFoundationTrimId)
                ->first();

            if (! $trim) {
                $trim = $car->trims()
                    ->where('name', $trimName)
                    ->first();
            }

            if (! $trim) {
                Log::warning('Error mapping Configuration: Couldn\'t find a Trim.', [
                    'productId' => $configurationId
                ]);

                return null;
            }

            $powertrain = $trim->powertrains()
                ->where('configuration_id', $configurationId)
                ->first();

            if (! $powertrain) {
                $powertrain = $trim->powertrains()
                    ->where('engine->name', $engine->name)
                    ->where('engine->code', $engine->code)
                    ->where('engine->horse_power', $engine->horse_power)
                    ->where('engine->horsepower_rev_range', $engine->horsepower_rev_range)
                    ->first();
            }

            if (! $powertrain) {
                Log::warning('Error mapping Configuration: Couldn\'t find a Powertrain.', [
                    'productId' => $configurationId
                ]);

                return null;
            }

            return new ConfigurationData(
                struct_id: $configurationId,
                car_id: $car->id,
                trim_id: $trim->id,
                powertrain_id: $powertrain->id,
                model_code: $modelCode,
                grade: $grade,
                ocn: $ocn,
                is_standard_configuration: $isStandardConfiguration,
                year: $year,
                trim: $trimName,
                technical_specifications: $technicalSpecifications,
                model_change_code: $modelChangeCode,
                original_model_change_code: $originalModelChangeCode,
                referenced_foundation_car_id: $referencedFoundationCarId,
                referenced_foundation_trim_id: $referencedFoundationTrimId,
                model: $model,
                variant: $variant,
                engine: $engine,
                transmission: $transmission,
            );

        } catch (Throwable $e) {
            Log::error('Error mapping Configuration', [
                'productId' => Arr::get($configurationData, 'Id'),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    // --- Mapping helpers ---

    protected static function mapModel(array|Collection|null $attributes): ?ModelData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'Name', '');
        $brand = Arr::get($attributes, 'Brand', '');
        $code = Arr::get($attributes, 'Code', '');
        $parentModelCode = Arr::get($attributes, 'ParentModelCode', '');

        if (!$name && !$brand && !$code && !$parentModelCode) return null;

        return new ModelData(
            name: $name,
            brand: $brand,
            code: $code,
            parent_model_code: $parentModelCode
        );
    }

    protected static function mapVariant(array|Collection|null $attributes): ?VariantData
    {
        if (!$attributes) return null;

        $name = Arr::get($attributes, 'Name', '');
        $code = Arr::get($attributes, 'Code', '');
        $b2bRaw = Arr::get($attributes, 'PBVSpecific', '');
        $b2b = $b2bRaw ? true : false;

        if (!$name && !$brand && !$b2b) return null;

        return new VariantData(
            name: $name,
            code: $code,
            b2b: $b2b
        );
    }

    protected static function mapTechnicalSpecifications(array|Collection|null $attributes): ?TechnicalSpecificationsData
    {
        if (!$attributes) return null;

        $values = [
            'pure_electric_range' => Arr::get($attributes, 'SharedPureElectricRange'),
            'co2_emission' => Arr::get($attributes, 'SharedCO2Emission'),
            'kilometers_per_litre' => Arr::get($attributes, 'SharedKilometersPerLitre'),
            'consumption' => self::mapConsumption(Arr::get($attributes, 'SharedConsumption')),
            'pure_electric_consumption' => Arr::get($attributes, 'SharedPureElectricConsumption'),
            'owner_tax' => Arr::get($attributes, 'SharedOwnerTax'),
            'energy_label' => Arr::get($attributes, 'SharedEnergyLabel'),
            'sound_level_stationary_db' => Arr::get($attributes, 'SharedSoundLevelStationarydB'),
            'sound_level_stationary_rpm' => Arr::get($attributes, 'SoundLevelStationaryRPM'),
            'sound_level_drive_by' => Arr::get($attributes, 'SoundLevelDriveBy'),
            'battery_size' => Arr::get($attributes, 'SharedBatterySize'),
            'total_horsepower' => Arr::get($attributes, 'SharedTotalHorsepower'),
            'has_coc_data_from_dataverse' => Arr::get($attributes, 'SharedHasCocDataFromDataverse') ?? false,
        ];

        if (!array_filter($values)) return null;

        return new TechnicalSpecificationsData(...$values);
    }

    protected static function mapConsumption(array|Collection|null $attributes): ?ConsumptionData
    {
        if (!$attributes) return null;

        $data = [
            'number' => Arr::get($attributes, 'Consumption'),
            'unit' => Arr::get($attributes, 'Unit.Name'),
        ];

        if (!$data['number'] || !$data['unit']) {
            return null;
        }

        return new ConsumptionData(...$data);
    }

    protected static function mapEngine(array|Collection|null $attributes): ?EngineData
    {
        if (!$attributes) return null;

        $data = [
            'name' => Arr::get($attributes, 'Name'),
            'code' => Arr::get($attributes, 'Code'),
            'horse_power' => Arr::get($attributes, 'HorsePower'),
            'amount_of_cylinders' => Arr::get($attributes, 'AmountOfCylinders'),
            'amount_of_valves' => Arr::get($attributes, 'AmountOfValves'),
            'volume' => Arr::get($attributes, 'Volume'),
            'horsepower_rev_range' => Arr::get($attributes, 'HorsepowerRevRange'),
            'fuel_type' => Arr::get($attributes, 'FuelType'),
            'service_interval' => self::mapEngineServiceInterval(Arr::get($attributes, 'ServiceInterval')),
        ];

        if (!$data['name'] && !$data['code'] && !$data['fuelType']) {
            return null;
        }

        return new EngineData(...$data);
    }

    protected static function mapEngineServiceInterval(array|Collection|null $attributes): ?ServiceIntervalData
    {
        if (!$attributes) return null;

        $data = [
            'months' => Arr::get($attributes, 'ServiceIntervalMonths', ''),
            'kilometers' => Arr::get($attributes, 'ServiceIntervalKilometers', ''),
            'oil_change_months' => Arr::get($attributes, 'OilChangeMonths', ''),
            'oil_change_kilometers' => Arr::get($attributes, 'OilChangeKilometers', '')
        ];

        if (!$data['months'] && !$data['kilometers'] && !$data['oil_change_months'] && !$data['oil_change_kilometers']) {
            return null;
        }

        return new ServiceIntervalData(...$data);
    }

    protected static function mapTransmission(array|Collection|null $attributes): ?TransmissionData
    {
        if (!$attributes) return null;

        $data = [
            'name' => Arr::get($attributes, 'Name'),
            'code' => Arr::get($attributes, 'Code'),
            'number_of_gears' => Arr::get($attributes, 'NumberOfGears'),
            'charge_plug_type' => Arr::get($attributes, 'ChargePlugType'),
        ];

        if (!$data['name'] && !$data['code']) {
            return null;
        }

        return new TransmissionData(...$data);
    }
}
