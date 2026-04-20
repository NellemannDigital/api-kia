<?php

namespace App\Services\Availability;

class OpeningHoursParser
{
    public function normalize(string $time): string
    {
        return preg_replace([
            '/-/',
            '/\.(?=\d{2})/'
        ], [':', ':'], trim($time));
    }

    public function parseRange(?string $range): ?array
    {
        if (!$range) return null;

        $parts = explode('-', $range);

        if (count($parts) === 2) {
            [$start, $end] = $parts;
        } elseif (count($parts) === 4) {
            $start = $parts[0] . '-' . $parts[1];
            $end = $parts[2] . '-' . $parts[3];
        } else {
            return null;
        }

        return [
            $this->normalize($start),
            $this->normalize($end),
        ];
    }
}