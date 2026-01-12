<?php

namespace App\Jobs;

use App\Services\PimService;
use App\Models\Accessory;
use App\Models\Trim;
use App\Data\AccessoryData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncAccessoryJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected ?AccessoryData $accessoryData = null;

    public function __construct(
        protected int $productId
    ) {}

    public function handle(PimService $pimService): void
    {
        try {

            DB::transaction(function () use ($pimService) {

                $this->accessoryData = $pimService->getAccessory($this->productId);

                if (! $this->accessoryData) {
                    return;
                }

                Log::info('Accessory synced to database', [
                    'accessory_id' => $this->productId,
                ]);

                $accessory = Accessory::withoutGlobalScopes()->updateOrCreate(
                    ['struct_id' => $this->accessoryData->struct_id],
                    $this->accessoryData->toArray()
                );

                $trims = Trim::whereJsonContains('accessory_mapping', $this->accessoryData->accessory_mapping)->get();

                foreach ($trims as $trim) {
                    $trim->accessories()->syncWithoutDetaching([$accessory->id]);
                }
            });

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncAccessoryJob failed', [
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
        return ['sync', 'pim', 'accessory', 'id:' . $this->productId];
    }
}
