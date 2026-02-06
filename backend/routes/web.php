<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', '2fa', 'disable.back.cache'])->name('dashboard');

Route::middleware(['auth', 'verified', '2fa', 'disable.back.cache'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::resource('tools', ToolController::class);
    Route::post('/tools/{tool}/comments', [CommentController::class, 'store'])->name('tools.comments.store');
    Route::delete('/tools/{tool}/comments/{comment}', [CommentController::class, 'destroy'])->name('tools.comments.destroy');
});

// Admin area: both Owner and Admin (admin middleware = canAccessAdminArea)
Route::middleware(['auth', 'verified', '2fa', 'admin', 'disable.back.cache'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    // Tool approval: single list for Owner and Admin
    Route::get('/tools/pending', [AdminController::class, 'pendingTools'])->name('tools.pending');
    Route::post('/tools/{tool}/toggle-status', [AdminController::class, 'toggleStatus'])->name('tools.toggle-status');
    // User management: Owner only
    Route::middleware('owner')->group(function () {
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
