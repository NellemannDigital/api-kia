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

    public function resolve(array $roots, string $variant): string
    {
        $template = ComplianceTextTemplate::valid()
            ->where('variant', $variant)
            ->orderByDesc('version')
            ->first();

        if (!$template) {
            return '';
        }

        $values = $this->buildValueMapFromRoots($roots);

        return $this->renderTemplate($template->template, $roots, $values);
    }

    protected function buildValueMapFromRoots(array $roots): array
    {
        $map = [];

        foreach ($roots as $key => $object) {
            if ($object instanceof Configuration) {
                $map = array_merge($map, $this->mapFromConfiguration($object));
            } elseif ($object instanceof Powertrain) {
                $map = array_merge($map, $this->mapFromPowertrain($object));
            } elseif ($object instanceof Trim) {
                $map = array_merge($map, $this->mapFromTrim($object));
            } elseif ($object instanceof Car) {
                $map = array_merge($map, $this->mapFromCar($object));
            }
        }

        return $map;
    }

    protected function renderTemplate(string $template, array $roots, array $values): string
    {
        $text = preg_replace_callback('/\{([a-z0-9_]+(?:->[\w_]+)*)\}/i', function ($matches) use ($roots, $values) {
            $key = $matches[1];

            if (array_key_exists($key, $values) && $values[$key] !== null && $values[$key] !== '') {
                return $values[$key];
            }

            $path = explode('->', $key);
            $rootKey = array_shift($path);
            $value = $roots[$rootKey] ?? null;

            foreach ($path as $segment) {
                if ($value === null) return null; 
                if ($value instanceof \Illuminate\Support\Collection) {
                    $value = $value->pluck('name')->implode(', ');
                } else {
                    $value = $value->{$segment} ?? null;
                }
            }

            return $value; 
        }, $template);

        $text = preg_replace('/\s{2,}/', ' ', $text); 
        $text = preg_replace('/\s+([.,:;])/u', '$1', $text); 
        $text = trim($text);

        return $text;
    }

    protected function mapFromConfiguration(Configuration $configuration): array
    {
        return [
            'consumption'       => $configuration->technical_specifications->consumption->number,
            'electric_range'    => $configuration->technical_specifications->pure_electric_range,
            'co2_emission'      => $configuration->technical_specifications->co2_emission,
            'ac_charging_time'  => $this->formatChargeTime($configuration->powertrain->technical_specifications->ac_charging_time),
            'dc_charging_time'  => $this->formatChargeTime($configuration->powertrain->technical_specifications->dc_charging_time),
            'ac_charging_speed' => $configuration->powertrain->technical_specifications->ac_charging_speed,
            'dc_charging_speed' => $configuration->powertrain->technical_specifications->dc_charging_speed,
            'owner_tax'         => $configuration->technical_specifications->owner_tax,
        ];
    }

    protected function mapFromPowertrain(Powertrain $powertrain): array
    {
        return [
            'consumption'       => $powertrain->configuration->technical_specifications->consumption->number,
            'electric_range'    => $powertrain->configuration->technical_specifications->pure_electric_range,
            'co2_emission'      => $powertrain->configuration->technical_specifications->co2_emission,
            'ac_charging_time'  => $this->formatChargeTime($powertrain->technical_specifications->ac_charging_time),
            'dc_charging_time'  => $this->formatChargeTime($powertrain->technical_specifications->dc_charging_time),
            'ac_charging_speed' => $powertrain->technical_specifications->ac_charging_speed,
            'dc_charging_speed' => $powertrain->technical_specifications->dc_charging_speed,
            'owner_tax'         => $powertrain->configuration->technical_specifications->owner_tax,
        ];
    }

    protected function mapFromTrim(Trim $trim): array
    {
        return [
            'consumption'       => $this->formatOptionalRange($trim->consumption_range),
            'electric_range'    => $this->formatOptionalRange($trim->electric_range),
            'co2_emission'      => $trim->co2_emission_range['min'],
            'ac_charging_time'  => 'Ned til ' . $this->formatChargeTime($trim->ac_charging_time_range['min']),
            'dc_charging_time'  => 'Ned til ' . $this->formatChargeTime($trim->dc_charging_time_range['min']),
            'ac_charging_speed' => $trim->ac_charging_speed_range['max'],
            'dc_charging_speed' => $trim->dc_charging_speed_range['max'],
            'owner_tax'         => $this->formatOptionalRange($trim->owner_tax_range),
        ];
    }

    protected function mapFromCar(Car $car): array
    {
        return [
            'consumption'       => $this->formatOptionalRange($car->consumption_range),
            'electric_range'    => $this->formatOptionalRange($car->electric_range),
            'co2_emission'      => $car->co2_emission_range['min'],
            'ac_charging_time'  => 'Ned til ' . $this->formatChargeTime($car->ac_charging_time_range['min']),
            'dc_charging_time'  => 'Ned til ' . $this->formatChargeTime($car->dc_charging_time_range['min']),
            'ac_charging_speed' => $car->ac_charging_speed_range['max'],
            'dc_charging_speed' => $car->dc_charging_speed_range['max'],
            'owner_tax'         => $this->formatOptionalRange($car->owner_tax_range),
        ];
    }

    protected function formatOptionalRange(array $range, ?callable $formatter = null): string
    {
        $min = $range['min'];
        $max = $range['max'];

        if ($formatter) {
            $min = $formatter($min);
            $max = $formatter($max);
        }

        return $min === $max ? (string) $min : "{$min}-{$max}";
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

    protected function formatNumber(string $value): string
    {
        $number = (float) str_replace(',', '.', $value);

        return number_format($number, 0, ',', '.');
    }
}