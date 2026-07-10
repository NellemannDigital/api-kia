<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Requests\DealersRequest;

class SyncDealersJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct() {}

    public function handle(DealersRequest $dealersRequest): void
    {
        $dealers = $dealersRequest->getDealers();
        $generalSpecialOpeningHours = $dealersRequest->getGeneralSpecialOpeningHours()
            ->values()
            ->all();

        $jobs = [];

        foreach ($dealers as $dealer) {
            $dealer['general_special_opening_hours'] = $generalSpecialOpeningHours;
            $jobs[] = new SyncDealerJob($dealer);
        };

        Bus::batch($jobs)
            ->onQueue('dynamics')
            ->allowFailures()
            ->dispatch();

    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncStockCarsJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'azure', 'stock-cars'];
    }
}
