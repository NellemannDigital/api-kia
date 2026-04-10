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
        $car = Car::with(['trims.powertrains.configuration'])->findOrFail($request->car_id);

        $trim = $request->trim_id
            ? Trim::with(['powertrains.configuration'])->find($request->trim_id)
            : null;

        $powertrain = $request->powertrain_id
            ? Powertrain::with(['configuration'])->find($request->powertrain_id)
            : null;

        $roots = [
            'car' => $car,
            'trim' => $trim,
            'powertrain' => $powertrain
        ];

        $text = (new ComplianceTextService())->resolve($roots, 'configurator');
        

        return response()->json([
            'text' => $text
        ]);
    }
}
