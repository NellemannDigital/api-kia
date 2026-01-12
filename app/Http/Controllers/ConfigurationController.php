<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use Inertia\Inertia;

class ConfigurationController extends Controller
{
    public function index()
    {
        return Inertia::render('Cars/Index', [
            'cars' => Car::query()
                ->select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
