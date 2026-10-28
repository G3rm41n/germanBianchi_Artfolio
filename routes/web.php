<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

// Public Gallery / Home
Route::get('/', [App\Http\Controllers\ExplorerController::class, 'index'])->name('home');

// Artist Public Profile
Route::get('/artista/{slug}', [App\Http\Controllers\PublicProfileController::class, 'show'])->name('profile.public');

// Authenticated Artist Routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Artworks & Collections
    Route::post('artworks/preview-url', [App\Http\Controllers\ArtworkController::class, 'previewUrl'])->name('artworks.preview');
    Route::resource('artworks', App\Http\Controllers\ArtworkController::class);
    Route::resource('collections', App\Http\Controllers\CollectionController::class);

    // Bookmarks / Favorites
    Route::get('/favoritos', [App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/obras/{id}/bookmark', [App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Trash
    Route::get('/papelera', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    Route::post('/papelera/obras/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreArtwork'])->name('trash.artworks.restore');
    Route::delete('/papelera/obras/{id}', [App\Http\Controllers\TrashController::class, 'destroyArtwork'])->name('trash.artworks.destroy');
    Route::post('/papelera/colecciones/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreCollection'])->name('trash.collections.restore');
    Route::delete('/papelera/colecciones/{id}', [App\Http\Controllers\TrashController::class, 'destroyCollection'])->name('trash.collections.destroy');
    Route::post('/papelera/vaciar', [App\Http\Controllers\TrashController::class, 'emptyTrash'])->name('trash.empty');
    Route::post('/papelera/{id}/restore', [App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/papelera/{id}', [App\Http\Controllers\TrashController::class, 'destroy'])->name('trash.destroy');

    // Artist Contact
    Route::post('/artista/{slug}/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('artist.contact');

    // Artwork Reports
    Route::post('/obras/{id}/report', [App\Http\Controllers\ReportController::class, 'store'])->name('artworks.report');
});

// Admin Panel Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureIsAdmin::class])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        // Reports Moderation
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
        Route::patch('/reports/{id}/note', [App\Http\Controllers\Admin\ReportController::class, 'updateNote'])->name('reports.updateNote');
        Route::post('/reports/{id}/resolve', [App\Http\Controllers\Admin\ReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/reports/{id}/dismiss', [App\Http\Controllers\Admin\ReportController::class, 'dismiss'])->name('reports.dismiss');
        Route::post('/reports/{id}/link-down-alert', [App\Http\Controllers\Admin\ReportController::class, 'sendLinkDownAlert'])->name('reports.linkDownAlert');

        // Users & Artists Management
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/suspend', [App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{id}/activate', [App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Global Trash
        Route::get('/trash', [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');
        Route::post('/trash/artworks/{id}/restore', [App\Http\Controllers\Admin\TrashController::class, 'restoreArtwork'])->name('trash.artworks.restore');
        Route::delete('/trash/artworks/{id}', [App\Http\Controllers\Admin\TrashController::class, 'destroyArtwork'])->name('trash.artworks.destroy');
        Route::post('/trash/collections/{id}/restore', [App\Http\Controllers\Admin\TrashController::class, 'restoreCollection'])->name('trash.collections.restore');
        Route::delete('/trash/collections/{id}', [App\Http\Controllers\Admin\TrashController::class, 'destroyCollection'])->name('trash.collections.destroy');
        Route::post('/trash/prune', [App\Http\Controllers\Admin\TrashController::class, 'pruneNow'])->name('trash.prune');
    });

// ── Rutas de Autenticación (Breeze) ─────────────────────────────────────────
require __DIR__.'/auth.php';
