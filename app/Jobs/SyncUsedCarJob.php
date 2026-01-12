<?php

namespace App\Jobs;

use App\Services\BilInfoService;
use App\Models\UsedCar;
use App\Data\UsedCarData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncUsedCarJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected UsedCarData $usedCarData;

    public function __construct(
        protected array $usedCar
    ) {}

    public function handle(BilInfoService $bilInfoService): void
    {
        try {
            $this->usedCarData = $bilInfoService->getUsedCar($this->usedCar);

            UsedCar::updateOrCreate(
                ['vehicle_id' => $this->usedCarData->vehicle_id],
                $this->usedCarData->toArray()
            );

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncUsedCarJob failed', [
            'vehicleId' => $this->usedCar['Id'],
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
        return ['sync', 'azure', 'used-car', 'id:' . $this->usedCarData->vehicle_id];
    }
}
