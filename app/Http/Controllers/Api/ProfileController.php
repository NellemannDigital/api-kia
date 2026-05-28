<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
    public function __invoke(string $profileId)
    {
        $response = Http::post(
            config('services.power_automate.profile_lookup_url'),
            [
                'contactId' => $profileId,
                'brand' => 'Kia',
            ]
        );

        if (! $response->successful()) {
            return response()->json([], 404);
        }

        $data = $response->json();

        Log::info('Data', [
            'data' => $data,
        ]);

        return response()->json([
            'name' => trim(
                ($data['Firstname'] ?? '') . ' ' .
                ($data['Lastname'] ?? '')
            ),

            'email' => $data['Email'] ?? null,
            'phone' => $data['MobilePhone'] ?? null,
            'zip' => $data['PostalCode'] ?? null
        ]);
    }
}