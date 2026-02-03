<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Requests\ProductRequest;

class SyncTrimsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected ?int $productId = null,
    ) {}

    public function handle(ProductRequest $productRequest): void
    {
        $ids = $productRequest->getProductVariantsIdsByProductId($this->productId);
        $jobs = [];

        foreach ($ids as $id) {
            $jobs[] = new SyncTrimJob($id);
        }

        Bus::batch($jobs)
            ->onQueue('pim')
            ->allowFailures()
            ->dispatch();
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncTrimsJob failed', [
            'productId' => $this->productId,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }


    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'pim', 'trims', 'productId:' . $this->productId];
    }
}
