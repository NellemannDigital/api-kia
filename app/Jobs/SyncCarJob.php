<?php

namespace App\Jobs;

use App\Services\PimService;
use App\Models\Car;
use App\Data\CarData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncCarJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CarData $carData;

    public function __construct(
        protected int $productId
    ) {}

    public function handle(PimService $pimService): void
    {
        try {
            $this->carData = $pimService->getCar($this->productId);

            Car::withoutGlobalScopes()->updateOrCreate(
                ['struct_id' => $this->carData->struct_id],
                $this->carData->toArray()
            );

             Log::info('Car synced to database', [
                'car_id' => $this->carData->struct_id,
            ]);

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncCarJob failed', [
            'productId' => $this->productId,
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
        return ['sync', 'pim', 'car', 'id:' . $this->productId];
    }
}
