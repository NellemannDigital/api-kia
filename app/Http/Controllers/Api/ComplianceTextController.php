<?php

namespace App\Http\Controllers\Api;

use App\Models\Car;
use App\Models\Trim;
use App\Models\Powertrain;
use App\Models\Configuration;
use App\Services\ComplianceTextResolver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComplianceTextController extends Controller
{
    public function show(Request $request) {
        $validated = $request->validate([
            'variant' => ['required', 'string'],
            'car_id' => ['nullable'],
            'trim_id' => ['nullable'],
            'powertrain_id' => ['nullable'],
            'configuration_id' => ['nullable'],
        ]);

        $roots = [];

        if ($request->filled('car_id')) {
            $roots['car'] = Car::findOrFail($request->query('car_id'));
        }

        if ($request->filled('trim_id')) {
            $roots['trim'] = Trim::findOrFail($request->query('trim_id'));
        }

        if ($request->filled('powertrain_id')) {
            $roots['powertrain'] = Powertrain::findOrFail($request->query('powertrain_id'));
        }

        if ($request->filled('configuration_id')) {
            $roots['configuration'] = Configuration::findOrFail($request->query('configuration_id'));
        }

        return response()->json([
            'variant' => $validated['variant'],
            'text' => ComplianceTextResolver::resolve($roots, $validated['variant']),
        ]);
    }
}