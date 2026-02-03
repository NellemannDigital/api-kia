<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Requests\UsedCarsRequest;

class SyncUsedCarsJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct() {}

    public function handle(UsedCarsRequest $usedCarsRequest): void
    {
        $usedCars = $usedCarsRequest->getUsedCars();

        $usedCars = $usedCars->filter(fn ($usedCar) =>
            $usedCar['Make'] === 'Kia'
        );

        $jobs = [];

        foreach ($usedCars->slice(0, 3) as $usedCar) {
            $jobs[] = new SyncUsedCarJob($usedCar);
        };

        Bus::batch($jobs)
            ->onQueue('bilinfo')
            ->allowFailures()
            ->dispatch();

    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncUsedCarsJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'bilinfo', 'used-cars'];
    }
}
