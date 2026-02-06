<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;

/*
|--------------------------------------------------------------------------
| API Routes (consumed by Next.js frontend / other modules)
|--------------------------------------------------------------------------
|
| Base URL: /api (e.g. http://localhost:8201/api)
|
*/

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'backend' => 'Laravel',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('api.status');

// Current user (for "Recommended for your role") – uses web session; call with credentials: 'include'
Route::get('/user', [UserApiController::class, 'me'])->middleware('web')->name('api.user.me');

Route::get('/tools', [App\Http\Controllers\Api\ToolApiController::class, 'index']);
Route::post('/tools', [App\Http\Controllers\Api\ToolApiController::class, 'store'])
    ->middleware(['web', 'auth']);
