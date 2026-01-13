<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApiTokenController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $tokens = $user->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'created_at' => $token->created_at->format('Y-m-d H:i'),
                'last_used_at' => $token->last_used_at?->format('Y-m-d H:i'),
            ];
        });

        return Inertia::render('settings/api-tokens', [
            'tokens' => $tokens,
            'flash' => [
                'token' => session('token'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $user = $request->user();

        $user->tokens()->where('name', $request->name)->delete();

        $token = $user->createToken($request->name)->plainTextToken;

        return back()->with('token', $token);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $user->tokens()->where('id', $id)->delete();

        return back()->with('success', 'Token revoked.');
    }
}
