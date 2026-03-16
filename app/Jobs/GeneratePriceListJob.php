<?php

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\Browsershot\Browsershot;
use Throwable;
use Illuminate\Support\Str;

class GeneratePriceListJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $carId
    ) {}

    public function handle(): void
    {
        try {
            $car = Car::withoutGlobalScopes()->with('trims.powertrains.configuration')->findOrFail($this->carId);

            Storage::disk('public')->makeDirectory('prislister');

            $fileName = 'prislister/' . Str::slug($car->name) . '.pdf';

            Pdf::view('price-list', ['car' => $car])
                ->format(Format::A4)
                ->margins(10, 10, 10, 10)
                ->disk('public')
                ->save($fileName);

            Log::info('Price list generated', [
                'car_id' => $car->id,
            ]);

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('GeneratePriceListJob failed', [
            'productId' => $this->carId,
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
        return ['generate', 'price-list', 'car', 'id:' . $this->carId];
    }
}
