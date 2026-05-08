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
use Illuminate\Support\Facades\Storage;
use Throwable;

class GeneratePdfsJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $cars = Car::addChannels(['web_channel', 'price_channel'])->get();

        Storage::disk('public')->deleteDirectory('prislister');

        foreach ($cars as $car) {
            Bus::batch([
                new GeneratePdfJob($car->struct_id),
            ])
                ->onQueue('pdfs')
                ->allowFailures()
                ->dispatch();
        }
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        Log::error('GeneratePdfsJob failed', [
            'exception'  => $exception->getMessage(),
            'trace'      => $exception->getTraceAsString(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function tags(): array
    {
        return ['generate', 'pdfs', 'cars'];
    }
}
