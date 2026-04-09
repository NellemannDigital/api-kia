<?php

namespace App\Http\Controllers;

use App\Models\ComplianceTextTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplianceTextTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('compliance-text-templates/index', [
            'complianceTextTemplates' => ComplianceTextTemplate::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('compliance-text-templates/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'variant' => 'required|string',
            'template' => 'required|string',
            'version' => 'required|string',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
        ]);

        ComplianceTextTemplate::create($data);

        return redirect()->route('compliance-text-templates.index');
    }

    public function edit($id)
    {
        $complianceTextTemplate = ComplianceTextTemplate::findOrFail($id);
        
        return Inertia::render('compliance-text-templates/edit', [
            'template' => $complianceTextTemplate
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComplianceTextTemplate $complianceTextTemplate)
    {
        $data = $request->validate([
            'variant' => 'required|string',
            'template' => 'required|string',
            'version' => 'required|string',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
        ]);

        $complianceTextTemplate->update($data);

        return redirect()->route('compliance-text-templates.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $complianceTextTemplate = ComplianceTextTemplate::findOrFail($id);
        
        $complianceTextTemplate->delete();
    }
}