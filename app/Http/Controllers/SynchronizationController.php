<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SynchronizationController extends Controller
{
    /**
     * Display the synchronization page.
     */
    public function index()
    {
        return Inertia::render('synchronization/index');
    }

    /**
     * Start PIM synchronization.
     */
    public function syncPim(Request $request)
    {
        $request->validate([
            'jobs' => 'sometimes|array',
            'jobs.*' => 'string|in:cars,configurations,accessories',
        ]);

        $jobs = $request->input('jobs', []);

        Artisan::call('nellemann:sync-pim-data', [
            '--jobs' => $jobs,
        ]);

        return redirect()->back()->with('success', 'PIM sync started!');
    }

    /**
     * Start dealer synchronization.
     */
    public function syncDealers()
    {
        Artisan::call('nellemann:sync-dealers');

        return redirect()->back()->with('success', 'Dealer sync started!');
    }

    /**
     * Start used cars synchronization.
     */
    public function syncUsedCars()
    {
        Artisan::call('nellemann:sync-used-cars');

        return redirect()->back()->with('success', 'Used cars sync started!');
    }

    /**
     * Start stock cars synchronization.
     */
    public function syncStockCars()
    {
        Artisan::call('nellemann:sync-stock-cars');

        return redirect()->back()->with('success', 'Stock cars sync started!');
    }

    /**
     * Genereate Pdfs
     */
    public function generatePdfs()
    {
        Artisan::call('nellemann:generate-pdfs');

        return redirect()->back()->with('success', 'Generate PDFs started!');
    }
}