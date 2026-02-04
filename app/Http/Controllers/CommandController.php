<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;

class CommandController extends Controller
{
    public function syncPimData(): JsonResponse
    {
        Artisan::call('nellemann:sync-pim-data'); 

        $output = Artisan::output();

        return response()->json([
            'success' => true,
            'output' => $output,
        ]);
    }
}
