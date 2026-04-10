<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Trim;
use App\Models\Powertrain;
use App\Models\ComplianceTextTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\ComplianceTextService;

class ComplianceTextController extends Controller
{
    public function index()
    {
        $cars = Car::with(['trims.powertrains.configuration'])->get();
        $templates = ComplianceTextTemplate::valid()->get();

        return Inertia::render('compliance-text/index', [
            'cars' => $cars,
            'templates' => $templates,
        ]);
    }
}