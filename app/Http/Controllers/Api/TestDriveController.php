<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Dealer;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Jobs\ProcessTestDriveActivity;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Log;

class TestDriveController extends Controller
{
    public function book(Request $request)
    {
        $data = $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'type' => 'required|string',
            'date' => 'required',
            'time' => 'required',
            'payload' => 'required|array',
            'campaign' => 'nullable|string',
            'newsletter' => 'boolean',
            'page_location' => 'nullable|string',
            'user_device' => 'nullable|string',
        ]);

        $date = Carbon::parse($data['date']);

        $activity = Activity::create([
            'dealer_id' => $data['dealer_id'],
            'type' => $data['type'],
            'date' => $date,
            'time' => $data['payload']['time'],
            'data' => [
                ...$data['payload'],
                'campaign' => $data['campaign'] ?? null,
                'newsletter' => $data['newsletter'] ?? null,
                'page_location' => $data['page_location'] ?? null,
                'user_device' => $data['user_device'] ?? null,
            ],
            'status' => 'pending',
        ]);

        ProcessTestDriveActivity::dispatch($activity)->onQueue('webhooks');

        return response()->json([
            'success' => true,
            'id' => $activity->id,
        ]);
    }

    public function car($id)
    {
        return Car::query()
            ->addChannels(['web_channel'])
            ->availableForTestDrive()
            ->where('web_id', $id)
            ->firstOrFail();
    }

    public function cars()
    {
        $ids = request('ids');
        $b2b = request('b2b');

        return Car::query()
            ->addChannels(['web_channel'])
            ->availableForTestDrive()
            ->where('variant->b2b', $b2b)

            ->when($ids, function ($query) use ($ids) {
                $query->whereIn(
                    'web_id',
                    explode(',', $ids)
                );
            })

            ->with('trims.powertrains.configuration')
            ->orderBy('name')
            ->get();
    }

    public function dealer($id)
    {
        return Dealer::query()
            ->where('tools->test_drive', true)
            ->where('dealer_guid', $id)
            ->firstOrFail();
    }

    public function dealers(Request $request, GeocodingService $geocodingService)
    {
        $b2b = request()->boolean('b2b');
        
        $query = Dealer::query()
            ->where('types->b2c', true)
            ->where('tools->test_drive', true)
            ->when(
                $b2b,
                function ($query) {
                    $query->where('types->b2b', true);
                }
            )
            ->when(
                $request->filled('ids'),
                function ($query) use ($request) {
                    $query->whereIn(
                        'dealer_guid',
                        explode(',', $request->ids)
                    );
                }
            );

        $lat = null;
        $lng = null;

        if ($request->filled('zip')) {
            $geoData = $geocodingService->fromZip($request->zip);

            if ($geoData) {
                $lat = $geoData['lat'];
                $lng = $geoData['lng'];
            }
        }

        if ($request->filled(['lat', 'lng'])) {
            $lat = $request->lat;
            $lng = $request->lng;
        }

        if ($lat && $lng) {
            $query
                ->selectRaw("
                    dealers.*,
                    (
                        6371 * acos(
                            cos(radians(?))
                            * cos(radians(latitude))
                            * cos(radians(longitude) - radians(?))
                            + sin(radians(?))
                            * sin(radians(latitude))
                        )
                    ) AS distance
                ", [$lat, $lng, $lat])
                ->orderBy('distance');

            return $query->paginate(6);
        }

        return $query
            ->orderBy('city')
            ->get();
    }

    public function postalCodes(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        return PostalCode::query()
            ->where('postal_code', 'like', "{$query}%")
            ->orWhere('city', 'like', "%{$query}%")
            ->orderByRaw("
                CASE 
                    WHEN postal_code LIKE ? THEN 1
                    WHEN city LIKE ? THEN 2
                    ELSE 3
                END
            ", ["{$query}%", "%{$query}%"])
            ->limit(10)
            ->get(['postal_code', 'city']);
    }

    public function availability(Request $request, Dealer $dealer)
    {
        $date = Carbon::parse($request->date);
        $car = $this->resolveCarFromRequest($request);

        if ($this->isAfterBookingEnd($car, $date)) {
            return response()->json([
                'timeSlots' => [],
                'unavailableSlots' => [],
            ]);
        }

        $hours = $this->resolveSalesOpeningHours($dealer, $date);

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

        /*
        VI TESTER AT FOLK KAN BOOKE FLERE SAMTIDIG
        $booked = Activity::where('dealer_id', $dealer->id)
            ->where('type', 'test_drive')
            ->where('date', $date)
            ->pluck('time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->values();
            */

        return response()->json([
            'timeSlots' => $allSlots,
            'unavailableSlots' => [] // $booked
        ]);
    }

    private function resolveCarFromRequest(Request $request): ?Car
    {
        $identifier = $request->input('car_id')
            ?? $request->input('car')
            ?? $request->input('web_id')
            ?? $request->input('car_web_id');

        if (is_array($identifier)) {
            $identifier = $identifier['id']
                ?? $identifier['struct_id']
                ?? $identifier['web_id']
                ?? null;
        }

        if (!$identifier) {
            return null;
        }

        return Car::withoutGlobalScopes()
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query
                        ->whereKey($identifier)
                        ->orWhere('struct_id', (int) $identifier);
                }

                $query->orWhere('web_id', $identifier);
            })
            ->firstOrFail();
    }

    private function isAfterBookingEnd(?Car $car, Carbon $date): bool
    {
        $bookingEnd = data_get($car, 'channels.test_drive_channel.booking_end');

        if (!$bookingEnd) {
            return false;
        }

        return $date->copy()
            ->startOfDay()
            ->gt(Carbon::parse($bookingEnd)->startOfDay());
    }

    private function resolveSalesOpeningHours(Dealer $dealer, Carbon $date): ?string
    {
        $specialOpeningHour = $this->specialOpeningHourForDate($dealer, $date);

        if ($specialOpeningHour) {
            if (data_get($specialOpeningHour, 'closed') === true) {
                return null;
            }

            $openingTime = data_get($specialOpeningHour, 'opening_time');
            $closingTime = data_get($specialOpeningHour, 'closing_time');

            if ($openingTime && $closingTime) {
                return trim($openingTime) . '-' . trim($closingTime);
            }
        }

        $weekday = strtolower($date->format('l'));

        return data_get($dealer->opening_hours, "sales.$weekday");
    }

    private function specialOpeningHourForDate(Dealer $dealer, Carbon $date): mixed
    {
        $specialOpeningHours = $dealer->special_opening_hours ?? [];

        if (is_object($specialOpeningHours) && method_exists($specialOpeningHours, 'toArray')) {
            $specialOpeningHours = $specialOpeningHours->toArray();
        }

        return collect($specialOpeningHours)
            ->first(fn($specialOpeningHour) => data_get($specialOpeningHour, 'date') === $date->toDateString());
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
        $car = $this->resolveCarFromRequest($request);

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $result = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {

            if ($this->isAfterBookingEnd($car, $date)) {
                $result[$date->format('Y-m-d')] = [
                    'status' => 'closed',
                    'available' => 0,
                ];
                continue;
            }

            $hours = $this->resolveSalesOpeningHours($dealer, $date);

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

            $booked = Activity::where('dealer_id', $dealer->id)
                ->where('type', 'test_drive')
                ->where('date', $date)
                ->pluck('time')
                ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
                ->all();

            $available = array_values(array_diff($slots->toArray(), $booked));

            $count = count($available);

            $result[$date->format('Y-m-d')] = [
                'status' => $count === 0
                    ? 'full'
                    : ($count <= 2 ? 'few' : 'available'),
                'available' => $count,
            ];
        }

        return response()->json($result);
    }
}
