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