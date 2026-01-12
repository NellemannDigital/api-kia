<?php

namespace App\Services;

use App\Data\UsedCarData;
use App\Mappers\UsedCarMapper;
use Illuminate\Support\Facades\Log;
use Throwable;

class BilInfoService
{
    public function __construct(
        protected UsedCarMapper $usedCarMapper,
    ) {}

    public function getUsedCar(array $usedCar)
    {
        return $this->safeCall(fn() => $this->buildUsedCarData($usedCar), $usedCar);
    }

    protected function buildUsedCarData(array $usedCar): UsedCarData
    {
        return $this->usedCarMapper->map(
            $usedCar
        );
    }

    protected function safeCall(callable $callback)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            Log::error('Failed to fetch data from BilInfor', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            report($e);

            return null;
        }
    }
}
