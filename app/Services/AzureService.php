<?php

namespace App\Services;

use App\Data\StockCarData;
use App\Mappers\StockCarMapper;
use Illuminate\Support\Facades\Log;
use Throwable;

class AzureService
{
    public function __construct(
        protected StockCarMapper $stockCarMapper,
    ) {}

    public function getStockCar(array $stockCar): ?StockCarData
    {
        return $this->safeCall(fn() => $this->buildStockCarData($stockCar), $stockCar);
    }

    protected function buildStockCarData(array $stockCar): ?StockCarData
    {
        return $this->stockCarMapper->map(
            $stockCar
        );
    }

    protected function safeCall(callable $callback)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            Log::error('Failed to fetch data from Azure', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            report($e);

            return null;
        }
    }
}
