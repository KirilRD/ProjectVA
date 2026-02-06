<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserApiController extends Controller
{
    /**
     * Return the current authenticated user (id, name, role) for SPA.
     * Uses web session; call with credentials: 'include' from the frontend.
     */
    public function me(): JsonResponse
    {
        $user = Auth::guard('web')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
}
