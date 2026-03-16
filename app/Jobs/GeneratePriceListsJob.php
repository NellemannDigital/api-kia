<?php

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Throwable;

class GeneratePriceListsJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $ids = [46];

        foreach ($ids as $id) {
            Bus::batch([
                new GeneratePriceListJob($id),
            ])
                ->onQueue('price-lists')
                ->allowFailures()
                ->dispatch();
        }
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('GeneratePriceListsJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['generate', 'price-list', 'car', 'id:' . $this->carId];
    }
}
