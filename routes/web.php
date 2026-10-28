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
    
    // Gestión de Obras y Colecciones (Artista)
    Route::post('artworks/preview-url', [App\Http\Controllers\ArtworkController::class, 'previewUrl'])->name('artworks.preview');
    Route::resource('artworks', App\Http\Controllers\ArtworkController::class);
    Route::resource('collections', App\Http\Controllers\CollectionController::class);

    // Favoritos
    Route::get('/favoritos', [App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/obras/{id}/bookmark', [App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Papelera
    Route::get('/papelera', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    Route::post('/papelera/obras/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreArtwork'])->name('trash.artworks.restore');
    Route::delete('/papelera/obras/{id}', [App\Http\Controllers\TrashController::class, 'destroyArtwork'])->name('trash.artworks.destroy');
    Route::post('/papelera/colecciones/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreCollection'])->name('trash.collections.restore');
    Route::delete('/papelera/colecciones/{id}', [App\Http\Controllers\TrashController::class, 'destroyCollection'])->name('trash.collections.destroy');
    Route::post('/papelera/vaciar', [App\Http\Controllers\TrashController::class, 'emptyTrash'])->name('trash.empty');
    // Rutas heredadas / aliases de conveniencia
    Route::post('/papelera/{id}/restore', [App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/papelera/{id}', [App\Http\Controllers\TrashController::class, 'destroy'])->name('trash.destroy');

    // Contacto Seguro hacia el Artista (RF-06, RN-05)
    Route::post('/artista/{slug}/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('artist.contact');

    // Reporte de Obras (RF-09)
    Route::post('/obras/{id}/report', [App\Http\Controllers\ReportController::class, 'store'])->name('artworks.report');
});

// ── Panel de Administración ──────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureIsAdmin::class])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        // Moderación de Reportes (RF-09, RN-02)
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
        Route::patch('/reports/{id}/note', [App\Http\Controllers\Admin\ReportController::class, 'updateNote'])->name('reports.updateNote');
        Route::post('/reports/{id}/resolve', [App\Http\Controllers\Admin\ReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/reports/{id}/dismiss', [App\Http\Controllers\Admin\ReportController::class, 'dismiss'])->name('reports.dismiss');
        Route::post('/reports/{id}/link-down-alert', [App\Http\Controllers\Admin\ReportController::class, 'sendLinkDownAlert'])->name('reports.linkDownAlert');

        // Gestión de Usuarios y Artistas (RF-11)
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/suspend', [App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{id}/activate', [App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Papelera Global (RF-11, RN-03, RN-04)
        Route::get('/trash', [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/artworks/{id}/restore', [App\Http\Controllers\Admin\TrashController::class, 'restoreArtwork'])->name('trash.artworks.restore');
        Route::delete('/trash/artworks/{id}', [App\Http\Controllers\Admin\TrashController::class, 'destroyArtwork'])->name('trash.artworks.destroy');
        Route::post('/trash/collections/{id}/restore', [App\Http\Controllers\Admin\TrashController::class, 'restoreCollection'])->name('trash.collections.restore');
        Route::delete('/trash/collections/{id}', [App\Http\Controllers\Admin\TrashController::class, 'destroyCollection'])->name('trash.collections.destroy');
        Route::post('/trash/prune', [App\Http\Controllers\Admin\TrashController::class, 'pruneNow'])->name('trash.prune');

        // Monitor de Colas y Fallos (RNF-01)
        Route::get('/queues', [App\Http\Controllers\Admin\QueueController::class, 'index'])->name('queues.index');
        Route::post('/queues/{id}/retry', [App\Http\Controllers\Admin\QueueController::class, 'retry'])->name('queues.retry');
        Route::delete('/queues/{id}', [App\Http\Controllers\Admin\QueueController::class, 'forget'])->name('queues.forget');
        Route::post('/queues/flush', [App\Http\Controllers\Admin\QueueController::class, 'flush'])->name('queues.flush');
    });

// ── Rutas de Autenticación (Breeze) ─────────────────────────────────────────
require __DIR__.'/auth.php';
