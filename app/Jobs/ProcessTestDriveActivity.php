<?php

namespace App\Jobs;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;

class ProcessTestDriveActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Activity $activity) {}

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function handle(): void
    {
        $activity = $this->activity;
        $dealer = $activity->dealer;
        $payload = $activity->data;

        $mapped = $this->mapPayload($activity->type, $payload, $dealer);

        $response = Http::timeout(5)->post(
            config('services.power_automate.test_drive_webhook_url'),
            $mapped
        );

        if ($response->successful()) {
            $activity->update(['status' => 'processed']);
        } else {
            throw new \Exception($response->body());
        }
    }

    private function mapPayload(string $type, array $payload, $dealer): array
    {
        return match ($type) {

            'test_drive' => [
                'dealerguid' => $dealer->dealer_guid,
                'carModelName' => $payload['car']['name'] ?? null,
                'carModelGUID' => $payload['car']['web_id'] ?? null,

                'testDriveDate' => isset($payload['date'])
                    ? Carbon::parse($payload['date'])->format('d-m-Y')
                    : null,
                'testDriveTimeOfDay' => $payload['time'] ?? null,

                'name' => $payload['name'] ?? null,
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
                
                'address' => '',
                'customerZipCode' => $payload['zip'] ?? null,
                'testdrivelocation' => 'Dealer',
                
                'userDevice' => $payload['user_device'] ?? null,
                'pageLocation' => $payload['page_location'] ?? null,
                
                'wishes' => '',
                'autopilot_session_id' => '',
                'campaign' => $payload['campaign'] ?? '',
                'newsletter' => $payload['newsletter'] ?? false,

                'previouspage' => '',
                
                'bookingCustomTimeSelction' => true,

                'comment' => '',

                'source' => 'Website',
                'brand' => 'Kia',
            ],

            default => [
                'dealerguid' => $dealer->dealer_guid,
                'type' => $type,
                'payload' => $payload,
            ],
        };
    }

    public function failed(Throwable $e): void
    {
        $this->activity->update(['status' => 'failed']);

        Log::error('Activity processing failed', [
            'activity_id' => $this->activity->id,
            'error' => $e->getMessage(),
        ]);
    }
}