<?php

namespace App\Jobs;

use App\Services\AzureService;
use App\Models\StockCar;
use App\Data\StockCarData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncStockCarJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected ?StockCarData $stockCarData;

    public function __construct(
        protected array $stockCar
    ) {}

    public function handle(AzureService $azureService): void
    {
        try {
            $this->stockCarData = $azureService->getStockCar($this->stockCar);

            if (! $this->stockCarData) {
                return;
            }

            StockCar::updateOrCreate(
                ['vehicle_number' => $this->stockCarData->vehicle_number],
                $this->stockCarData->toArray()
            );

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncStockCarJob failed', [
            'vehicleNumber' => $this->stockCar['vehicleNumber'],
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->handleFailure($exception);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'azure', 'stock-car', 'id:' . $this->stockCar->vehicle_number];
    }
}
