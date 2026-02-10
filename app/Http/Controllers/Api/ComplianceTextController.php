<?php

namespace App\Http\Controllers\Api;

use App\Models\Car;
use App\Models\Configuration;
use App\Models\Powertrain;
use App\Models\Trim;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ComplianceTextService;

class ComplianceTextController extends Controller
{
    public function show(Request $request)
    {
        $variant = $request->get('variant', 'default');

        if ($request->has('configuration_id')) {
            $config = Configuration::findOrFail($request->configuration_id);
            $text = app(ComplianceTextService::class)->getForConfiguration($config, $variant);
        } elseif ($request->has('powertrain_id')) {
            $powertrain = Powertrain::findOrFail($request->powertrain_id);
            $text = app(ComplianceTextService::class)->getForPowertrain($powertrain, $variant);
        } elseif ($request->has('trim_id')) {
            $trim = Trim::findOrFail($request->trim_id);
            $text = app(ComplianceTextService::class)->getForTrim($trim, $variant);
        } elseif ($request->has('car_id')) {
            $car = Car::findOrFail($request->car_id);
            $text = app(ComplianceTextService::class)->getForCar($car, $variant);
        } else {
            abort(422, 'No valid scope provided.');
        }

        return response()->json([
            'text' => $text,
            'variant' => $variant,
        ]);
    }
}
