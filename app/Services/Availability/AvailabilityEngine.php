<?php

namespace App\Services\Availability;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Booking;

class AvailabilityEngine
{
    public function __construct(
        private OpeningHoursParser $parser,
        private SlotGenerator $slots
    ) {}

    public function getDaySlots($dealer, Carbon $date): array
    {
        if ($date->startOfDay()->lt(now()->startOfDay())) {
            return [];
        }

        $dayKey = strtolower($date->format('l'));

        $range = data_get($dealer->opening_hours, "sales.$dayKey");

        $parsed = $this->parser->parseRange($range);

        if (!$parsed) return [];

        [$start, $end] = $parsed;

        $slots = $this->slots->generate($start, $end);

        $bookedData = $this->dummyBookings();

        $booked = $bookedData[$dealer->id][$date->format('Y-m-d')] ?? [];

        return array_values(array_diff($slots, $booked));
    }

    public function getRangeOverview($dealer, Carbon $from, Carbon $to): array
    {
        $now = now()->startOfDay();

        $from = $from->lt($now) ? $now : $from;

        $max = $now->copy()->addMonths(3)->endOfDay();

        if ($to->gt($max)) {
            $to = $max;
        }

        $period = CarbonPeriod::create($from, $to);

        $result = [];

        foreach ($period as $date) {
            $result[$date->format('Y-m-d')] =
                count($this->getDaySlots($dealer, $date));
        }

        return $result;
    }

    public function getNextAvailableDate($dealer): ?string
    {
        $date = now();

        for ($i = 0; $i < 60; $i++) {
            $slots = $this->getDaySlots($dealer, $date);

            if (count($slots) > 0) {
                return $date->format('Y-m-d');
            }

            $date->addDay();
        }

        return null;
    }

    private function dummyBookings(): array
    {
        return [
            13 => [
                '2026-04-15' => ['09:00', '11:00'],
                '2026-04-16' => ['10:00', '14:00'],
            ],
            7 => [
                '2026-04-15' => ['10:00'],
            ],
        ];
    }
}