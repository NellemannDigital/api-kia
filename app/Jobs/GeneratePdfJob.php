<?php

namespace App\Jobs;

use App\Services\pdfService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GeneratePdfJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $carStructId
    ) {}

    public function handle(PdfService $pdfService): void
    {
        try {
            $car = $pdfService->loadCar($this->carStructId);

            $data = $pdfService->build($car);

            $pdfService->generatePdfs($data);

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    protected function handleFailure(Throwable $exception): void
    {
        report($exception);

        Log::error('GeneratePdfJob failed', [
            'car_id' => $this->carStructId,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->handleFailure($exception);
    }

    public function tags(): array
    {
        return [
            'generate',
            'pricelist',
            'car',
            'id:' . $this->carStructId,
        ];
    }
}