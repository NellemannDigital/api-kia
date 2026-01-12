<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Requests\ProductsSearchRequest;

class SyncCarsJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct() {}

    public function handle(ProductsSearchRequest $productsSearchRequest): void
    {
        $ids = $productsSearchRequest->getProductIds('f81c8095-1c6c-410b-93fc-24c33cda9567');

        foreach ($ids as $id) {
            Bus::batch([
                new SyncCarJob($id),
                new SyncTrimsJob($id),
            ])
                ->onQueue('pim-sync')
                ->allowFailures()
                ->dispatch();
        }
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncCarsJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'pim', 'cars'];
    }
}
