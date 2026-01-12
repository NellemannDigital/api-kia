<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Requests\StockCarsRequest;

class SyncStockCarsJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        protected string $stockStatuses,
        protected ?Carbon $modifiedFrom = null
    ) {}

    public function handle(StockCarsRequest $stockCarsRequest): void
    {
        $stockCars = $stockCarsRequest->getStockCars(
            [$this->stockStatuses],
            $this->modifiedFrom
        );

        $stockCars = $stockCars->reject(fn ($stockCar) =>
            $stockCar['hideOnPortal'] === true ||
            $stockCar['standardPriceNumber'] !== null
        );

        $jobs = [];

        foreach ($stockCars->slice(0, 3) as $stockCar) {
            $jobs[] = new SyncStockCarJob($stockCar);
        };

        Bus::batch($jobs)
            ->onQueue('azure-sync')
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
