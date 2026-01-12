<?php

namespace App\Services;

use App\Data\DealerData;
use App\Mappers\DealerMapper;
use Illuminate\Support\Facades\Log;
use Throwable;

class DynamicsService
{
    public function __construct(
        protected DealerMapper $dealerMapper,
    ) {}

    public function getDealer(array $dealer)
    {
        return $this->safeCall(fn() => $this->buildDealerData($dealer), $dealer);
    }

    protected function buildDealerData(array $dealer): DealerData
    {
        return $this->dealerMapper->map(
            $dealer
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
