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

class SyncConfigurationsJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(ProductsSearchRequest $productsSearchRequest): void
    {
        $ids = $productsSearchRequest->getProductIds('944096c2-c7af-4396-ab32-058276a495a2');
        $jobs = [];

        foreach ($ids as $id) {
            $jobs[] = new SyncConfigurationJob($id);
        };

        Bus::batch($jobs)
            ->onQueue('pim')
            ->allowFailures()
            ->dispatch();
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncConfigurationJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'pim', 'configurations'];
    }
}
