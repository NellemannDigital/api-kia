<?php

namespace App\Jobs;

use App\Services\PimService;
use App\Models\Configuration;
use App\Data\ConfigurationData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncConfigurationJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected ?ConfigurationData $configurationData = null;

    public function __construct(
        protected int $productId
    ) {}

    public function handle(PimService $pimService): void
    {
        try {
            $this->configurationData = $pimService->getConfiguration($this->productId);

            if (! $this->configurationData) {
                return;
            }

              Log::info('Configuration synced to database', [
                'configuration_id' => $this->productId,
            ]);

            Configuration::withoutGlobalScopes()->updateOrCreate(
                ['struct_id' => $this->configurationData->struct_id],
                $this->configurationData->toArray()
            );

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncConfigurationJob failed', [
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
        return ['sync', 'pim', 'configuration', 'id:' . $this->productId];
    }
}
