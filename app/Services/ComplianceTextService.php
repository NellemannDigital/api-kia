<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Trim;
use App\Models\Powertrain;
use App\Models\Configuration;
use App\Models\ComplianceTextTemplate;
use Illuminate\Support\Str;

class ComplianceTextService
{
    public function getForCar(Car $car, string $variant = 'default'): string
    {
        return $this->renderForScope([
            ['scope' => 'car', 'id' => $car->id],
        ], $car, $variant);
    }

    public function getForTrim(Trim $trim, string $variant = 'default'): string
    {
        return $this->renderForScope([
            ['scope' => 'trim', 'id' => $trim->id],
            ['scope' => 'car', 'id' => $trim->car_id],
        ], $trim, $variant);
    }

    public function getForPowertrain(Powertrain $powertrain, string $variant = 'default'): string
    {
        return $this->renderForScope([
            ['scope' => 'powertrain', 'id' => $powertrain->id],
            ['scope' => 'trim', 'id' => $powertrain->trim_id],
            ['scope' => 'car', 'id' => $powertrain->car_id],
        ], $powertrain, $variant);
    }

    public function getForConfiguration(Configuration $configuration, string $variant = 'default'): string
    {
        return $this->renderForScope([
            ['scope' => 'configuration', 'id' => $configuration->id],
            ['scope' => 'powertrain', 'id' => $configuration->powertrain_id],
            ['scope' => 'trim', 'id' => $configuration->trim_id],
            ['scope' => 'car', 'id' => $configuration->car_id],
        ], $configuration, $variant);
    }

    protected function renderForScope(array $scopes, $context, string $variant): string
    {
        $template = $this->resolveTemplate($scopes, $variant);
        return $this->renderTemplate($template->template, $context);
    }

    protected function resolveTemplate(array $scopes, string $variant): ComplianceTextTemplate
    {
        return ComplianceTextTemplate::active()
            ->where('variant', $variant)
            ->where(function ($q) use ($scopes) {
                foreach ($scopes as $scope) {
                    $q->orWhere(fn ($q) =>
                        $q->where('scope', $scope['scope'])
                          ->where(function ($q) use ($scope) {
                              $q->where('scope_id', $scope['id'])
                                ->orWhereNull('scope_id'); // fallback til generisk
                          })
                    );
                }
            })
            ->orderByRaw("
                CASE scope
                    WHEN 'configuration' THEN 1
                    WHEN 'powertrain' THEN 2
                    WHEN 'trim' THEN 3
                    WHEN 'car' THEN 4
                END
            ")
            ->firstOrFail();
    }

    protected function renderTemplate(string $template, $context): string
    {
        $values = $this->buildValueMap($context);

        foreach (['consumption', 'electric_range', 'co2_emission', 'owner_tax'] as $key) {
            if (isset($values[$key . '_min'])) {
                $values[$key] = $this->formatRange($values[$key . '_min'], $values[$key . '_max'] ?? null);
            }
        }

        return Str::of($template)->replaceMatches('/\{(\w+)\}/', fn($m) => $values[$m[1]] ?? '');
    }

    protected function formatRange($min, $max)
    {
        return $min === $max || is_null($max) ? $min : $min . '–' . $max;
    }

    protected function buildValueMap($context): array
    {
        if ($context instanceof Configuration) {
            return $this->mapFromConfiguration($context);
        }

        if ($context instanceof Powertrain) {
            return $this->mapFromPowertrain($context);
        }

        if ($context instanceof Trim) {
            return $this->mapFromTrim($context);
        }

        if ($context instanceof Car) {
            return $this->mapFromCar($context);
        }

        return [];
    }

    protected function mapFromConfiguration(Configuration $config): array
    {
        return [
            'car' => $config->car->name,
            'trim' => $config->trim->name,
            'powertrain' => $config->powertrain->name,
            'consumption' => $config->consumption_wh_per_km,
            'range' => $config->range_km,
            'dc_power' => $config->dc_power_kw,
            'ac_charge_time' => $config->ac_charge_time_hours,
            'dc_charge_time' => $config->dc_charge_time_minutes,
            'green_tax_half_year' => $config->green_tax_half_year,
            'co2_emission' => $config->co2_emission,
        ];
    }

    protected function mapFromPowertrain(Powertrain $powertrain): array
    {
        $configs = $powertrain->configurations;

        return [
            'car' => $powertrain->car->name,
            'trim' => $powertrain->trim->name,
            'powertrain' => $powertrain->name,
            'consumption_min' => $configs->min('consumption_wh_per_km'),
            'consumption_max' => $configs->max('consumption_wh_per_km'),
            'range_min' => $configs->min('range_km'),
            'range_max' => $configs->max('range_km'),
            'dc_power_min' => $configs->min('dc_power_kw'),
            'dc_power_max' => $configs->max('dc_power_kw'),
            'co2_emission_min' => $configs->min('co2_emission'),
            'co2_emission_max' => $configs->max('co2_emission'),
        ];
    }

    protected function mapFromTrim(Trim $trim): array
    {
        $configs = $trim->configurations;

        return [
            'car' => $trim->car->name,
            'trim' => $trim->name,
            'consumption_min' => $configs->min('consumption_wh_per_km'),
            'consumption_max' => $configs->max('consumption_wh_per_km'),
            'range_min' => $configs->min('range_km'),
            'range_max' => $configs->max('range_km'),
            'co2_emission_min' => $configs->min('co2_emission'),
            'co2_emission_max' => $configs->max('co2_emission'),
        ];
    }

    protected function mapFromCar(Car $car): array
    {
        $configs = $car->configurations;

        return [
            'car' => $car->name,
            'consumption_min' => $car->consumption_range['min'],
            'consumption_max' => $car->consumption_range['max'],
            'electric_range_min' => $car->electric_range['min'],
            'electric_range_max' => $car->electric_range['max'],
            'co2_emission_min' => $car->co2_emission_range['min'],
            'ac_charging_time_min' => $this->formatChargeTime($car->ac_charging_time_range['min']),
            'ac_charging_time_max' => $this->formatChargeTime($car->ac_charging_time_range['max']),
            'dc_charging_time_min' => $this->formatChargeTime($car->dc_charging_time_range['min']),
            'dc_charging_time_max' => $this->formatChargeTime($car->dc_charging_time_range['max']),
            'ac_charging_time_min' => $this->formatChargeTime($car->ac_charging_time_range['min']),
            'ac_charging_time_max' => $this->formatChargeTime($car->ac_charging_time_range['max']),
            'dc_charging_time_min' => $this->formatChargeTime($car->dc_charging_time_range['min']),
            'dc_charging_time_max' => $this->formatChargeTime($car->dc_charging_time_range['max']),
            'ac_charging_speed_min' => $car->ac_charging_speed_range['min'],
            'ac_charging_speed_max' => $car->ac_charging_speed_range['max'],
            'dc_charging_speed_min' => $car->dc_charging_speed_range['min'],
            'dc_charging_speed_max' => $car->dc_charging_speed_range['max'],
            'owner_tax_min' => $car->owner_tax_range['min'],
            'owner_tax_max' => $car->owner_tax_range['max'],
        ];
    }

    protected function formatChargeTime(string $time): string
    {
        [$hours, $minutes] = explode(':', $time);

        $hours = (int) $hours;
        $minutes = (int) $minutes;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' t';
        }

        if ($minutes > 0) {
            $parts[] = $minutes . ' min';
        }

        return $parts ? implode(' ', $parts) : '0 min';
    }

}
