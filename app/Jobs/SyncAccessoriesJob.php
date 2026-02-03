<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Requests\ProductsSearchRequest;

class SyncAccessoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(ProductsSearchRequest $productsSearchRequest): void
    {
        $ids = $productsSearchRequest->getAccessoryIds('2b23c5e6-b02a-43ff-8178-0837725f92b8');

        $jobs = [];

        foreach ($ids as $id) {
            $jobs[] = new SyncAccessoryJob($id);
        };

        Bus::batch($jobs)
            ->onQueue('pim')
            ->allowFailures()
            ->dispatch();
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncAccessoriesJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'pim', 'accessories'];
    }
}
