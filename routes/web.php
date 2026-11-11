<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;

// Public Gallery / Home
Route::get('/', [App\Http\Controllers\ExplorerController::class, 'index'])->name('home');

// Artist Public Profile
Route::get('/artista/{slug}', [App\Http\Controllers\PublicProfileController::class, 'show'])->name('profile.public');

// Global Countries API Endpoint (Cached proxy for onboarding & location)
Route::get('/api/countries', function () {
    return \Illuminate\Support\Facades\Cache::remember('global_countries_list_v1', 86400 * 30, function () {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            if ($response->successful()) {
                return collect($response->json())
                    ->pluck('name.common')
                    ->filter()
                    ->sort()
                    ->values();
            }
        } catch (\Throwable $e) {}

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get('https://restcountries.com/v3.1/all?fields=name');
            if ($response->successful()) {
                return collect($response->json())
                    ->pluck('name.common')
                    ->filter()
                    ->sort()
                    ->values();
            }
        } catch (\Throwable $e) {}

        return [
            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia',
            'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados',
            'Belarus', 'Belgium', 'Belize', 'Benin', 'Bolivia', 'Bosnia and Herzegovina',
            'Botswana', 'Brazil', 'Bulgaria', 'Canada', 'Chile', 'China', 'Colombia',
            'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czechia', 'Denmark', 'Dominican Republic',
            'Ecuador', 'Egypt', 'El Salvador', 'Estonia', 'Finland', 'France', 'Georgia',
            'Germany', 'Greece', 'Guatemala', 'Honduras', 'Hungary', 'Iceland', 'India',
            'Indonesia', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan',
            'Kazakhstan', 'Kenya', 'Kuwait', 'Latvia', 'Lebanon', 'Lithuania', 'Luxembourg',
            'Malaysia', 'Malta', 'Mexico', 'Monaco', 'Morocco', 'Netherlands', 'New Zealand',
            'Nicaragua', 'Nigeria', 'Norway', 'Panama', 'Paraguay', 'Peru', 'Philippines',
            'Poland', 'Portugal', 'Qatar', 'Romania', 'Saudi Arabia', 'Serbia', 'Singapore',
            'Slovakia', 'Slovenia', 'South Africa', 'South Korea', 'Spain', 'Sweden',
            'Switzerland', 'Taiwan', 'Thailand', 'Turkey', 'Ukraine', 'United Arab Emirates',
            'United Kingdom', 'United States', 'Uruguay', 'Venezuela', 'Vietnam', 'Other'
        ];
    });
})->name('api.countries');

// Authenticated Artist Routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Onboarding Wizard
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/step/{step}', [App\Http\Controllers\OnboardingController::class, 'step'])->name('step');
        Route::post('/step/1', [App\Http\Controllers\OnboardingController::class, 'saveStep1'])->name('step1.save');
        Route::post('/step/2', [App\Http\Controllers\OnboardingController::class, 'saveStep2'])->name('step2.save');
        Route::post('/complete', [App\Http\Controllers\OnboardingController::class, 'complete'])->name('complete');
        Route::get('/check-username', [App\Http\Controllers\OnboardingController::class, 'checkUsername'])->name('checkUsername');
    });

    // Session Keep-Alive Endpoint
    Route::post('/session/keep-alive', function () {
        session()->put('last_activity', now()->timestamp);
        return response()->json([
            'status' => 'success',
            'message' => 'Session extended successfully',
            'lifetime_minutes' => config('session.lifetime', 10),
        ]);
    })->name('session.keepAlive');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/blocking', [App\Http\Controllers\UserBlockController::class, 'store'])->name('profile.blocking.store');
    Route::delete('/profile/blocking/{blockedUser}', [App\Http\Controllers\UserBlockController::class, 'destroy'])->name('profile.blocking.destroy');

    // Protected actions requiring email verification
    Route::middleware('verified')->group(function () {
        // Artworks & Collections
        Route::post('artworks/preview-url', [App\Http\Controllers\ArtworkController::class, 'previewUrl'])->name('artworks.preview');
        Route::resource('artworks', App\Http\Controllers\ArtworkController::class);
        Route::resource('collections', App\Http\Controllers\CollectionController::class);
        Route::get('/collections/artwork-status/{artwork}', [App\Http\Controllers\CollectionController::class, 'artworkStatus'])->name('collections.artworkStatus');
        Route::post('/collections/{collection}/toggle-artwork', [App\Http\Controllers\CollectionController::class, 'toggleArtwork'])->name('collections.toggleArtwork');
        Route::post('/collections/quick-create-with-artwork', [App\Http\Controllers\CollectionController::class, 'quickCreateWithArtwork'])->name('collections.quickCreateWithArtwork');

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

        // Artwork Reports
        Route::post('/obras/{id}/report', [App\Http\Controllers\ReportController::class, 'store'])->name('artworks.report');
    });
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
