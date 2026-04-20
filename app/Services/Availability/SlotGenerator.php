<?php

namespace App\Services\Availability;

use Carbon\Carbon;

class SlotGenerator
{
    public function generate(string $start, string $end): array
    {
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        // round up start
        if ($startTime->minute > 0) {
            $startTime->addHour()->minute(0);
        }

        // round down end
        $endTime->minute(0);

        $slots = [];

        while ($startTime < $endTime) {
            $slots[] = $startTime->format('H:i');
            $startTime->addHour();
        }

        return $slots;
    }
}