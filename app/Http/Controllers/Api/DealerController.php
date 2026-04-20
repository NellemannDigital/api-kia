<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\Availability\AvailabilityEngine;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dealer::query();

        if ($request->has('zip')) {
            $query->whereJsonContains('postal_codes->b2c', $request->zip)->orderBy('name');
        }

        if ($request->has(['lat', 'lng'])) {
            $lat = $request->lat;
            $lng = $request->lng;

            $query->selectRaw("
                dealers.*,
                (6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->orderBy('distance')
            ->paginate(6);
        }

        else {
            $query->orderBy('name');
        }

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Dealer $dealer)
    {
        $date = Carbon::parse($request->date);
        $weekday = strtolower($date->format('l'));

        $hours = $dealer->opening_hours->sales->$weekday ?? null;

        if (!$hours) {
            return response()->json([
                'timeSlots' => [],
                'unavailableSlots' => [],
            ]);
        }

        [$start, $end] = explode('-', $hours);

        $startTime = Carbon::createFromFormat('H.i', trim($start));
        $endTime   = Carbon::createFromFormat('H.i', trim($end));

        $allSlots = $this->generateHourlySlots($startTime, $endTime);

        /*$booked = Booking::where('dealer_id', $dealer->id)
            ->whereDate('date', $date)
            ->pluck('time')
            ->toArray();*/

        return response()->json([
            'timeSlots' => $allSlots,
            'unavailableSlots' => [],
        ]);
    }

    private function generateHourlySlots(Carbon $start, Carbon $end): array
    {
        $slots = [];

        // 🎯 rund op til næste hele time
        $cursor = $start->copy()->minute(0)->second(0);

        if ($start->gt($cursor)) {
            $cursor->addHour();
        }

        // ⛔ stop før closing hour
        $lastAllowed = $end->copy()->minute(0)->second(0);

        while ($cursor->lt($lastAllowed)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addHour();
        }

        return $slots;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function calendarAvailability(Request $request, Dealer $dealer)
    {
        $month = Carbon::parse($request->month);

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $result = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {

            $weekday = strtolower($date->format('l'));

            $hours = data_get($dealer->opening_hours, "sales.$weekday");

            // 🔴 closed
            if (!$hours) {
                $result[$date->format('Y-m-d')] = [
                    'status' => 'closed',
                    'available' => 0,
                ];
                continue;
            }

            // generate slots (1h)
            [$startT, $endT] = explode('-', $hours);

            $startTime = Carbon::createFromFormat('H.i', trim($startT));
            $endTime   = Carbon::createFromFormat('H.i', trim($endT));

            $slots = collect();
            $cursor = $startTime->copy()->minute(0);

            if ($startTime->gt($cursor)) {
                $cursor->addHour();
            }

            $last = $endTime->copy()->minute(0);

            while ($cursor->lt($last)) {
                $slots->push($cursor->format('H:i'));
                $cursor->addHour();
            }

            // booked
            $booked = [];

            $available = array_values(array_diff($slots->toArray(), $booked));

            $result[$date->format('Y-m-d')] = [
                'status' => count($available) ? 'available' : 'full',
                'available' => count($available),
            ];
        }

        return response()->json($result);
    }


    public function slots(Request $request, Dealer $dealer)
    {
        return response()->json([
            'data' => app(AvailabilityEngine::class)
                ->getDaySlots($dealer, Carbon::parse($request->date))
        ]);
    }

    public function availability(Request $request, Dealer $dealer)
    {
        $from = Carbon::parse($request->query('from', now()));
        $to = Carbon::parse($request->query('to', now()->addMonths(3)));

        $engine = app(AvailabilityEngine::class);

        return response()->json([
            'availability' => $engine->getRangeOverview($dealer, $from, $to),
            'opening_hours' => $this->normalizeOpeningHours($dealer),
        ]);
    }

    public function nextAvailable(Dealer $dealer)
    {
        return response()->json([
            'data' => app(AvailabilityEngine::class)
                ->getNextAvailableDate($dealer)
        ]);
    }

    private function normalizeOpeningHours($dealer): array
    {
        $sales = $dealer->opening_hours->sales ?? [];

        return collect($sales)
            ->mapWithKeys(fn ($value, $key) => [
                strtolower($key) => $value
            ])
            ->toArray();
    }

}
