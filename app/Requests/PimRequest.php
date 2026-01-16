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

    /**
     * Perform a POST request to the Nellemann PIM API.
     *
     * @param string $endpoint
     * @param array $payload
     * @param string|null $jsonKey
     * @return Collection
     *
     * @throws Throwable
     */
    protected function postRequest(string $endpoint, array $payload, ?string $jsonKey = null): Collection
    {
        $startTime = microtime(true);

        try {
            if (empty($payload)) {
                return collect();
            }

            $payload = array_map('strval', $payload);

            $response = Http::nellemannPIM()
                ->timeout(10)
                ->retry(3, 200)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withBody(json_encode($payload), 'application/json')
                ->post($endpoint);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->failed()) {
                throw new \Exception("PIM POST request failed [{$response->status()}] for endpoint: {$endpoint}");
            }

            $data = $jsonKey
                ? $response->json($jsonKey, [])
                : $response->json();

            return collect($data);

        } catch (RequestException | Throwable $e) {
            Log::error('PIM POST request failed', [
                'endpoint' => $endpoint,
                'payload'  => $payload,
                'json_key' => $jsonKey,
                'error'    => $e->getMessage(),
                'exception'=> class_basename($e),
            ]);

            report($e);
            throw $e;
        }
    }
}
