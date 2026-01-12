<?php

namespace App\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Throwable;

abstract class PimRequest
{
    /**
     * Perform a GET request to the Nellemann PIM API.
     *
     * @param  string  $endpoint
     * @param  string|null  $jsonKey
     * @return \Illuminate\Support\Collection
     *
     * @throws \Throwable
     */
    protected function request(string $endpoint, ?string $jsonKey = null): Collection
    {
        $startTime = microtime(true);

        try {
            $response = Http::nellemannPIM()
                ->timeout(10)
                ->retry(3, 200)
                ->get($endpoint);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->failed()) {
                throw new \Exception("PIM request failed [{$response->status()}] for endpoint: {$endpoint}");
            }

            $data = $jsonKey
                ? $response->json($jsonKey, [])
                : $response->json();

            return collect($data);

        } catch (RequestException | Throwable $e) {
            Log::error('PIM request failed', [
                'endpoint'  => $endpoint,
                'json_key'  => $jsonKey,
                'error'     => $e->getMessage(),
                'exception' => class_basename($e),
            ]);

            report($e);
            throw $e;
        }
    }
}
