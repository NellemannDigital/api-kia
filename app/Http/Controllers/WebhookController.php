<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\PimService;
use App\Jobs\{SyncCarJob, SyncConfigurationJob, SyncTrimJob};
use App\Exceptions\Webhooks\{
    MissingEventKeyException,
    MissingPayloadKeyException,
    UnknownEventKeyException
};

class WebhookController extends Controller
{
    protected array $handlers = [
        'variants:created' => 'handleVariants',
        'variants:updated' => 'handleVariants',
        'products:created' => 'handleProducts',
        'products:updated' => 'handleProducts',
    ];

    public function handle(Request $request, PimService $pimService)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        try {
            $eventKey = $headers['x-event-key'][0] ?? $payload['event'] ?? null;

            if (!$eventKey) {
                throw new MissingEventKeyException('Webhook received without event key');
            }

            $handler = $this->handlers[$eventKey] ?? null;
            if (!$handler) {
                throw new UnknownEventKeyException("Unhandled event key: {$eventKey}");
            }

            $this->$handler($payload, $pimService);

        } catch (\Throwable $e) {
            report($e);

            Log::debug('Webhook processing failed', [
                'eventKey' => $eventKey ?? null,
                'payload'  => $payload,
                'exception'=> $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleVariants(array $payload, PimService $pimService)
    {
        $variantIds = $payload['VariantIds'] ?? [];
        if (empty($variantIds)) {
            throw new MissingPayloadKeyException('VariantIds');
        }

        foreach ($variantIds as $id) {
            dispatch(new SyncTrimJob($id))->onQueue('pim-sync');
        }
    }

    protected function handleProducts(array $payload, PimService $pimService)
    {
        $productIds = $payload['ProductIds'] ?? [];
        if (empty($productIds)) {
            throw new MissingPayloadKeyException('ProductIds');
        }

        foreach ($productIds as $productId) {
            $structureId = $pimService->getProductStructureId($productId);

            match ($structureId) {
                'f81c8095-1c6c-410b-93fc-24c33cda9567' => dispatch(new SyncCarJob($productId))->onQueue('pim-sync'),
                '944096c2-c7af-4396-ab32-058276a495a2' => dispatch(new SyncConfigurationJob($productId))->onQueue('pim-sync'),
                default => Log::warning("Unknown product structure ID: {$structureId} for product {$productId}"),
            };
        }
    }
}
