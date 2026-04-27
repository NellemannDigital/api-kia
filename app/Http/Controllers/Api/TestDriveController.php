<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Dealer;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Jobs\ProcessTestDriveActivity;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TestDriveController extends Controller
{
    public function book(Request $request)
    {
        $data = $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'type' => 'required|string',
            'payload' => 'required|array',
        ]);

        $activity = Activity::create([
            'dealer_id' => $data['dealer_id'],
            'type' => $data['type'],
            'data' => $data['payload'],
            'status' => 'pending',
        ]);

        ProcessTestDriveActivity::dispatch($activity)->onQueue('webhooks');

        return response()->json([
            'success' => true,
            'id' => $activity->id,
        ]);
    }

    public function cars()
    {
        return Car::query()
            ->addChannels(['web_channel'])
            ->availableForTestDrive()
            ->where('variant->b2b', false)
            ->with('trims.powertrains.configuration')
            ->orderBy('name')
            ->get();
    }

    public function dealers(Request $request)
    {
        $query = Dealer::query()
            ->where('types->b2c', true)
            ->where('tools->test_drive', true);

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
            $query->orderBy('city');
        }

        return $query->get();
    }

    public function availability(Request $request, Dealer $dealer)
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

        $cursor = $start->copy()->minute(0)->second(0);

        if ($start->gt($cursor)) {
            $cursor->addHour();
        }

        $lastAllowed = $end->copy()->minute(0)->second(0);

        while ($cursor->lt($lastAllowed)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addHour();
        }

        return $slots;
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

            if (!$hours) {
                $result[$date->format('Y-m-d')] = [
                    'status' => 'closed',
                    'available' => 0,
                ];
                continue;
            }

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

            $booked = [];

            $available = array_values(array_diff($slots->toArray(), $booked));

            $count = count($available);

            $result[$date->format('Y-m-d')] = [
                'status' => $count === 0
                    ? 'full'
                    : ($count <= 3 ? 'few' : 'available'),
                'available' => $count,
            ];
        }

        return response()->json($result);
    }
}
