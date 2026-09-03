<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

// ── Página de inicio (placeholder Etapa 1) ───────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Artista autenticado: Dashboard y Perfil ──────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Panel de Administración ──────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureIsAdmin::class])
    ->group(function () {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    });

// ── Rutas de Autenticación (Breeze) ─────────────────────────────────────────
require __DIR__.'/auth.php';
