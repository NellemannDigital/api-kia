<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Trim;
use App\Models\Powertrain;
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

    protected function mapFromPowertrain(Powertrain $powertrain): array
    {
        return [
            'car' => $powertrain->trim->car->name,
            'trim' => $powertrain->trim->name,
            'powertrain' => $powertrain->engine->name,
            'consumption_min' => $powertrain->consumption_range['min'],
            'consumption_max' => $powertrain->consumption_range['max'],
            'electric_range_min' => $powertrain->electric_range['min'],
            'electric_range_max' => $powertrain->electric_range['max'],
            'co2_emission_min' => $powertrain->co2_emission_range['min'],
            'ac_charging_time_min' => $this->formatChargeTime($powertrain->ac_charging_time_range['min']),
            'ac_charging_time_max' => $this->formatChargeTime($powertrain->ac_charging_time_range['max']),
            'dc_charging_time_min' => $this->formatChargeTime($powertrain->dc_charging_time_range['min']),
            'dc_charging_time_max' => $this->formatChargeTime($powertrain->dc_charging_time_range['max']),
            'ac_charging_time_min' => $this->formatChargeTime($powertrain->ac_charging_time_range['min']),
            'ac_charging_time_max' => $this->formatChargeTime($powertrain->ac_charging_time_range['max']),
            'dc_charging_time_min' => $this->formatChargeTime($powertrain->dc_charging_time_range['min']),
            'dc_charging_time_max' => $this->formatChargeTime($powertrain->dc_charging_time_range['max']),
            'ac_charging_speed_min' => $powertrain->ac_charging_speed_range['min'],
            'ac_charging_speed_max' => $powertrain->ac_charging_speed_range['max'],
            'dc_charging_speed_min' => $powertrain->dc_charging_speed_range['min'],
            'dc_charging_speed_max' => $powertrain->dc_charging_speed_range['max'],
            'owner_tax_min' => $powertrain->owner_tax_range['min'],
            'owner_tax_max' => $powertrain->owner_tax_range['max'],
        ];
    }

    protected function mapFromTrim(Trim $trim): array
    {
        return [
            'car' => $trim->car->name,
            'trim' => $trim->name,
            'consumption_min' => $trim->consumption_range['min'],
            'consumption_max' => $trim->consumption_range['max'],
            'electric_range_min' => $trim->electric_range['min'],
            'electric_range_max' => $trim->electric_range['max'],
            'co2_emission_min' => $trim->co2_emission_range['min'],
            'ac_charging_time_min' => $this->formatChargeTime($trim->ac_charging_time_range['min']),
            'ac_charging_time_max' => $this->formatChargeTime($trim->ac_charging_time_range['max']),
            'dc_charging_time_min' => $this->formatChargeTime($trim->dc_charging_time_range['min']),
            'dc_charging_time_max' => $this->formatChargeTime($trim->dc_charging_time_range['max']),
            'ac_charging_time_min' => $this->formatChargeTime($trim->ac_charging_time_range['min']),
            'ac_charging_time_max' => $this->formatChargeTime($trim->ac_charging_time_range['max']),
            'dc_charging_time_min' => $this->formatChargeTime($trim->dc_charging_time_range['min']),
            'dc_charging_time_max' => $this->formatChargeTime($trim->dc_charging_time_range['max']),
            'ac_charging_speed_min' => $trim->ac_charging_speed_range['min'],
            'ac_charging_speed_max' => $trim->ac_charging_speed_range['max'],
            'dc_charging_speed_min' => $trim->dc_charging_speed_range['min'],
            'dc_charging_speed_max' => $trim->dc_charging_speed_range['max'],
            'owner_tax_min' => $trim->owner_tax_range['min'],
            'owner_tax_max' => $trim->owner_tax_range['max'],
        ];
    }

    protected function mapFromCar(Car $car): array
    {
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
