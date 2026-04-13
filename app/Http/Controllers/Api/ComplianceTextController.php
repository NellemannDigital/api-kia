<?php

namespace App\Http\Controllers\Api;

use App\Models\Car;
use App\Models\Configuration;
use App\Models\Powertrain;
use App\Models\Trim;
use App\Models\ComplianceTextTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ComplianceTextService;

class ComplianceTextController extends Controller
{
    public function show(Request $request)
    {

        $car = Car::with(['trims.powertrains.configuration'])->where('struct_id', $request->car_id)->firstOrFail();

        $trim = $request->trim_id
            ? Trim::with(['powertrains.configuration'])->where('struct_id', $request->trim_id)->firstOrFail()
            : null;

        $powertrain = $request->powertrain_id
            ? Powertrain::with(['configuration'])->where('configuration_id', $request->powertrain_id)->firstOrFail()
            : null;

        $configuration = null;

        $packageCodes = collect($request->input('package_codes', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($powertrain && $packageCodes->isNotEmpty()) {

            $query = Configuration::where('powertrain_id', $powertrain->id);

            foreach ($packageCodes as $code) {
                $query->whereHas('extraEquipmentPackages', function ($q) use ($code) {
                    $q->where('code', $code);
                });
            }

            $query->whereDoesntHave('extraEquipmentPackages', function ($q) use ($packageCodes) {
                $q->whereNotIn('code', $packageCodes);
            });

            $configuration = $query->first();
        }

        $template = $request->template
            ? ComplianceTextTemplate::find($request->template)
            : null;

        $roots = [
            'car' => $car,
            'trim' => $trim,
            'powertrain' => $powertrain,
            'configuration' => $configuration,
        ];

        $text = (new ComplianceTextService())->resolve($roots, $template->variant ?? 'configurator');
        

        return response()->json([
            'text' => $text
        ]);
    }
}
