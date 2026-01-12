<?php

namespace App\Jobs;

use App\Services\DynamicsService;
use App\Models\Dealer;
use App\Data\DealerData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncDealerJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected DealerData $dealerData;

    public function __construct(
        protected array $dealer
    ) {}

    public function handle(DynamicsService $dynamicsService): void
    {
        try {
            $this->dealerData = $dynamicsService->getDealer($this->dealer);

            Dealer::updateOrCreate(
                ['dynamics_id' => $this->dealerData->dynamics_id],
                $this->dealerData->toArray()
            );

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncDealerJob failed', [
            'dealerGuid' => $this->dealer['pin_dealerguid'],
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
        return ['sync', 'dynamics', 'dealer', 'id:' . $this->dealer['pin_dealerguid']];
    }
}
