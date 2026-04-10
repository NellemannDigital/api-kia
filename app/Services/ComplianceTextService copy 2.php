<?php

namespace App\Services;

use App\Models\ComplianceTextTemplate;
use Illuminate\Support\Str;

class ComplianceTextService3
{
    /**
     * Resolve a compliance template dynamically based on roots
     *
     * @param array $roots e.g. ['car' => $car, 'trim' => $trim]
     */
    public function resolve(array $roots, string $variant = 'default'): string
    {
        $template = ComplianceTextTemplate::valid()
            ->where('variant', $variant)
            ->orderByDesc('version')
            ->first();

        if (!$template) return '';

        // Flatten all roots dynamically
        $values = [];
        foreach ($roots as $key => $root) {
            $values = array_merge($values, $this->mapRootDynamic($root, $key));
        }

        // Format ranges, charging times, numbers
        $values = $this->formatSpecialValues($values);

        // Replace placeholders
        return $this->replacePlaceholders($template->template, $values);
    }

    /**
     * Dynamically map a root object/array
     */
    protected function mapRootDynamic($root, string $prefix = ''): array
    {
        $values = [];

        // Handle technical_specifications DTO/array
        if (is_object($root) && isset($root->technical_specifications)) {
            $specs = $root->technical_specifications;

            if (is_object($specs)) {
                $specs = $this->objectToArray($specs);
            }

            foreach ($specs as $key => $val) {
                $fullKey = $prefix ? $prefix.'->'.$key : $key;
                if (is_array($val) && isset($val['min'])) {
                    $values[$fullKey.'_min'] = $val['min'];
                    $values[$fullKey.'_max'] = $val['max'];
                } else {
                    $values[$fullKey] = $val;
                }
            }
        }

        // Iterate object properties
        foreach ((array)$root as $key => $val) {
            $fullKey = $prefix ? $prefix.'->'.$key : $key;

            if (is_object($val)) {
                if (isset($val->name)) $values[$fullKey.'->name'] = $val->name;
                $values = array_merge($values, $this->mapRootDynamic($val, $fullKey));
            } elseif (is_array($val) && $this->isAssoc($val)) {
                $values = array_merge($values, $this->mapRootDynamic($val, $fullKey));
            } elseif (is_array($val) && !$this->isAssoc($val)) {
                foreach ($val as $i => $item) {
                    $itemKey = $fullKey.'->'.$i;
                    if (is_object($item) && isset($item->name)) $values[$itemKey.'->name'] = $item->name;
                    if (is_array($item) && isset($item['name'])) $values[$itemKey.'->name'] = $item['name'];
                    $values = array_merge($values, $this->mapRootDynamic($item, $itemKey));
                }
            } elseif (is_scalar($val)) {
                $values[$fullKey] = $val;
            }
        }

        // Direct names for main models
        if ($root instanceof \App\Models\Car) $values['car->name'] = $root->name;
        if ($root instanceof \App\Models\Trim) $values['trim->name'] = $root->name;
        if ($root instanceof \App\Models\Powertrain) $values['powertrain->name'] = $root->engine->name ?? null;
        if ($root instanceof \App\Models\Configuration) {
            $values['car->name'] = $root->powertrain->trim->car->name ?? null;
            $values['trim->name'] = $root->powertrain->trim->name ?? null;
            $values['powertrain->name'] = $root->powertrain->engine->name ?? null;
        }

        return $values;
    }

    /**
     * Convert object (DTO) to associative array recursively
     */
    protected function objectToArray($obj): array
    {
        if (is_object($obj)) $obj = get_object_vars($obj);

        return array_map(function($val) {
            if (is_object($val)) return $this->objectToArray($val);
            if (is_array($val)) return array_map(fn($v) => is_object($v) ? $this->objectToArray($v) : $v, $val);
            return $val;
        }, $obj);
    }

    protected function formatSpecialValues(array $values): array
    {
        foreach (['consumption','electric_range','co2_emission','owner_tax','ac_charging_speed','dc_charging_speed'] as $key) {
            if (isset($values[$key.'_min'])) {
                $values[$key] = $this->formatRange($values[$key.'_min'], $values[$key.'_max'] ?? null);
            }
        }

        foreach (['ac_charging_time','dc_charging_time'] as $key) {
            if (isset($values[$key])) $values[$key] = $this->formatChargeTime($values[$key]);
            if (isset($values[$key.'_min'])) {
                $values[$key.'_min'] = $this->formatChargeTime($values[$key.'_min']);
                $values[$key.'_max'] = $this->formatChargeTime($values[$key.'_max']);
            }
        }

        foreach (['delivery_fee','owner_tax'] as $key) {
            if (isset($values[$key])) $values[$key] = $this->formatNumber($values[$key]);
        }

        return $values;
    }

    protected function replacePlaceholders(string $template, array $values): string
    {
        return preg_replace_callback('/\{([a-z0-9_\->]+)\}/i', fn($m) => $values[$m[1]] ?? '', $template);
    }

    protected function formatRange($min,$max): string
    {
        return $min===$max || $max===null ? (string)$min : $min.'–'.$max;
    }

    protected function formatChargeTime(string $time): string
    {
        if (!str_contains($time, ':')) return $time;
        [$h,$m] = explode(':',$time);
        $parts=[];
        if((int)$h>0) $parts[] = (int)$h.' t';
        if((int)$m>0) $parts[] = (int)$m.' min';
        return $parts ? implode(' ',$parts) : '0 min';
    }

    protected function formatNumber($value): string
    {
        if (!is_numeric($value)) return (string)$value;
        return number_format((float)$value,0,',','.');
    }

    protected function isAssoc(array $arr): bool
    {
        return array_keys($arr)!==range(0,count($arr)-1);
    }
}