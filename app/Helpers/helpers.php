<?php

use Carbon\Carbon;

if (! function_exists('formatTimeString')) {
    
    function formatTimeString($time) {
        try {
            $carbonTime = Carbon::createFromFormat('H:i', $time);
            $hours = (int) $carbonTime->format('G');
            $minutes = (int) $carbonTime->format('i');

            $parts = [];
            if ($hours > 0) $parts[] = "{$hours} t";
            if ($minutes > 0) $parts[] = "{$minutes} min";

            return implode(' ', $parts) ?: '0 min';
        } catch (\Exception $e) {
            return '-';
        }
    }
}

if (! function_exists('formatNumber')) {

    function formatNumber($number, $decimals = 0, $decimalSeparator = ',', $thousandSeparator = '.') {
        try {
            if (!is_numeric($number)) {
                return '-';
            }

            return number_format($number, $decimals, $decimalSeparator, $thousandSeparator);
        } catch (\Exception $e) {
            return '-';
        }
    }
}

if (!function_exists('compliance_text_for')) {

    function compliance_text_for(array $roots, string $variant)
    {
        return \App\Services\ComplianceTextResolver::resolve($roots, $variant);
    }
}