<?php

namespace App\Jobs;

use App\Services\PimService;
use App\Models\Car;
use App\Models\Trim;
use App\Models\Equipment;
use App\Data\TrimData;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncTrimJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TrimData $trimData;

    public function __construct(
        protected int $variantId
    ) {}

    public function handle(PimService $pimService): void
    {
        try {

            DB::transaction(function () use ($pimService) {

                $this->trimData = $pimService->getTrim($this->variantId);

                $car = Car::withoutGlobalScopes()
                    ->where('struct_id', $this->trimData->car_struct_id)
                    ->firstOrFail();

                $trim = Trim::withoutGlobalScopes()->updateOrCreate(
                    ['struct_id' => $this->trimData->struct_id],
                    array_merge(
                        ['car_id' => $car->id],
                        $this->trimData->toArray()
                    )
                );

                $this->mapColors($this->trimData, $trim);
                $this->mapPowertrains($this->trimData, $trim);
                $this->mapLeasingPowertrains($this->trimData, $trim);
                $this->mapEquipment($this->trimData, $trim);
            });

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('SyncTrimJob failed', [
            'variantId' => $this->variantId,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->handleFailure($exception);
    }

    private function mapColors(TrimData $trimData, Trim $trim): void
    {
        $existingColorIds = collect();

        foreach ($trimData->colors as $c) {

            $color = $trim->colors()->updateOrCreate(
                ['code' => $c->code],
                $c->toArray()
            );

            Log::info('Color synced to database', [
                'code' => $c->code,
            ]);

            $existingColorIds->push($color->id);

            $existingPriceIds = collect();

            foreach ($c->prices as $p) {
                $price = $color->prices()->updateOrCreate(
                    $p->toArray()
                );

                Log::info('Color price synced to database');

                $existingPriceIds->push($price->id);
            }

            $color->prices()->whereNotIn('id', $existingPriceIds)->delete();

        }

        $trim->colors()->whereNotIn('id', $existingColorIds)->delete();
    }

    private function mapPowertrains(TrimData $trimData, Trim $trim): void
    {
        $existingPowertrainIds = collect();

        foreach ($trimData->powertrains as $pt) {

            $powertrain = $trim->powertrains()->updateOrCreate(
                ['configuration_id' => $pt->configuration_id],
                $pt->toArray()
            );

            Log::info('Powertrain synced to database', [
                'configuration_id' => $pt->configuration_id,
            ]);

            $existingPowertrainIds->push($powertrain->id);

            $existingPriceIds = collect();

            foreach ($pt->prices as $p) {
                $price = $powertrain->prices()->updateOrCreate(
                    $p->toArray()
                );

                $existingPriceIds->push($price->id);
            }

            $powertrain->prices()->whereNotIn('id', $existingPriceIds)->delete();

        }

        $trim->powertrains()->whereNotIn('id', $existingPowertrainIds)->delete();
    }

    private function mapLeasingPowertrains(TrimData $trimData, Trim $trim): void
    {
        foreach ($trimData->leasing_powertrains as $lpt) {

            $powertrain = $trim->powertrains()
                ->where('configuration_id', $lpt->configuration_id)
                ->first();

            if (!$powertrain) {
                Log::warning("Powertrain not found for leasing: {$lpt->configuration_id}");
                continue;
            }

            $powertrain->update([
                'leasing_active' => $lpt->leasing_active
            ]);

            if ($lpt->leasing_active) {
                $existingPriceIds = collect();

                foreach ($lpt->leasing_prices as $lp) {
                    $price = $powertrain->leasingPrices()->updateOrCreate(
                        $lp->toArray()
                    );

                    $existingPriceIds->push($price->id);
                }

                $powertrain->leasingPrices()->whereNotIn('id', $existingPriceIds)->delete();
            }

        }
    }

    private function mapEquipment(TrimData $trimData, Trim $trim): void
    {
        $equipmentIds = collect();

        foreach ($trimData->equipment as $e) {

            $equipment = Equipment::withoutGlobalScopes()->updateOrCreate(
                ['code' => $e->code],
                $e->toArray()
            );

            Log::info('Equipment synced to database', [
                'code' => $e->code,
            ]);
            
            $equipmentIds->push($equipment->id);
        }

        $trim->equipment()->sync($equipmentIds->all());
    }


    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['sync', 'pim', 'trim', 'id:' . $this->variantId];
    }
}
