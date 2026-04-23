<?php

namespace App\Http\Controllers\Api;

use App\Models\Activity;
use App\Jobs\ProcessTestDriveActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    public function testDrive(Request $request)
    {
        $data = $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'type' => 'required|string',
            'payload' => 'required|array',
        ]);

        $activity = Activity::create([
            'dealer_id' => $data['dealer_id'],
            'type' => $data['type'],
            'data' => $data['payload'],
            'status' => 'pending',
        ]);

        ProcessTestDriveActivity::dispatch($activity)->onQueue('webhooks');

        return response()->json([
            'success' => true,
            'id' => $activity->id,
        ]);
    }
}