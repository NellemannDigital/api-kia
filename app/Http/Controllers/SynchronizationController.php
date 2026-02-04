<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SynchronizationController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('synchronization/index');
    }
    
    public function run(Request $request)
    {
        $request->validate([
            'jobs' => 'sometimes|array',
            'jobs.*' => 'string|in:cars,configurations,accessories',
        ]);

        $jobs = $request->input('jobs', []); // default: []

        $jobsOption = empty($jobs) ? [] : $jobs;

        $exitCode = Artisan::call('nellemann:sync-pim-data', [
            '--jobs' => $jobsOption
        ]);

        return response()->json([
            'success' => $exitCode === 0,
            'message' => 'Jobs dispatched',
            'output' => Artisan::output(),
        ]);
    }
}
