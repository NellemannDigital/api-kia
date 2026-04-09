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
        $car = Car::with([
            'trims.powertrains.configuration'
        ])->find(1);

        $trim = Trim::with([
            'powertrains.configuration'
        ])->find(3);

        $powertrain = Powertrain::with([
            'configuration'
        ])->find(5);

        $roots = [
            'car' => $car,
            'trim' => $trim,
            'powertrain' => $powertrain
        ];

        $text = (new ComplianceTextService())->resolve($roots, 'configurator');

        dd($text);
    }
}
