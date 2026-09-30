<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

// ── Página de inicio (Galería Pública) ───────────────────────────────────
Route::get('/', [App\Http\Controllers\ExplorerController::class, 'index'])->name('home');

// ── Perfil Público de Artista ────────────────────────────────────────────────
Route::get('/artista/{slug}', [App\Http\Controllers\PublicProfileController::class, 'show'])->name('profile.public');

// ── Artista autenticado: Dashboard y Perfil ──────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Gestión de Obras (Artista)
    Route::resource('artworks', App\Http\Controllers\ArtworkController::class);

    // Favoritos
    Route::get('/favoritos', [App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/obras/{id}/bookmark', [App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Papelera
    Route::get('/papelera', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    Route::post('/papelera/{id}/restore', [App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/papelera/{id}', [App\Http\Controllers\TrashController::class, 'destroy'])->name('trash.destroy');
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
