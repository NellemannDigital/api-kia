<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ComplianceTextResolver;
use Illuminate\Http\Request;

class ComplianceTextController extends Controller
{
    public function show(Request $request)
    {
        $variant = $request->query('variant');

        if (!$variant) {
            return response()->json([
                'message' => 'variant is required'
            ], 422);
        }

        $roots = $request->query('roots');

        if (is_string($roots)) {
            $decoded = json_decode($roots, true);
            $roots = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($roots)) {
            $roots = [];
        }

        $text = ComplianceTextResolver::resolve($roots, $variant);

        return response()->json([
            'variant' => $variant,
            'text' => $text,
        ]);
    }
}