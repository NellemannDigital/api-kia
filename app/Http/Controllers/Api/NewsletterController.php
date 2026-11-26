<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:254',
            'campaign' => 'nullable|string|max:255',
            'user_device' => 'required|in:mobile,tablet,desktop',
            'page_location' => 'required|url:http,https',
            'model_name' => 'nullable|string|max:255',
        ]);

        $webhookUrl = config('services.power_automate.newsletter_webhook_url');

        if (! is_string($webhookUrl) || trim($webhookUrl) === '') {
            Log::error('Newsletter webhook is not configured.');

            return response()->json([
                'success' => false,
                'message' => 'Tilmeldingen kunne ikke gennemføres. Prøv igen om lidt.',
            ], 503);
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'source' => 'Website',
            'brand' => 'Kia',
            'campaign' => $data['campaign'] ?? '',
            'autopilot_session_id' => '',
            'userDevice' => $data['user_device'],
            'pageLocation' => $data['page_location'],
            'modelName' => $data['model_name'] ?? '',
        ];

        try {
            $response = Http::timeout(5)->post($webhookUrl, $payload);
        } catch (ConnectionException $e) {
            Log::warning('Newsletter webhook connection failed.');

            return response()->json([
                'success' => false,
                'message' => 'Tilmeldingen kunne ikke gennemføres. Prøv igen om lidt.',
            ], 502);
        }

        if (! $response->successful()) {
            Log::warning('Newsletter webhook returned an unsuccessful response.', [
                'status' => $response->status(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tilmeldingen kunne ikke gennemføres. Prøv igen om lidt.',
            ], 502);
        }

        return response()->json(['success' => true]);
    }
}
