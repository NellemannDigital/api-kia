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

            $powertrain = Powertrain::where('configuration_id', $request->configuration_id)->firstOrFail();

            $query = Configuration::query()
                ->where('powertrain_id', $powertrain->id);

            if ($request->filled('package_codes')) {
                $packageCodes = collect(explode(',', $request->package_codes))
                    ->map(fn ($c) => trim($c))
                    ->filter()
                    ->values();

                foreach ($packageCodes as $code) {
                    $query->whereHas('extraEquipmentPackages', function ($q) use ($code) {
                        $q->where('code', $code);
                    });
                }

                $query->whereDoesntHave('extraEquipmentPackages', function ($q) use ($packageCodes) {
                    $q->whereNotIn('code', $packageCodes);
                });
            }

            $config = $query->firstOrFail();

            $text = app(ComplianceTextService::class)->getForConfiguration($config, $variant);
        } elseif ($request->has('powertrain_id')) {
            $powertrain = Powertrain::where('configuration_id', $request->powertrain_id)->firstOrFail();
            $text = app(ComplianceTextService::class)->getForPowertrain($powertrain, $variant);
        } elseif ($request->has('trim_id')) {
            $trim = Trim::where('struct_id', $request->trim_id)->firstOrFail();
            $text = app(ComplianceTextService::class)->getForTrim($trim, $variant);
        } elseif ($request->has('car_id')) {
            $car = Car::where('struct_id', $request->car_id)->firstOrFail();
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
