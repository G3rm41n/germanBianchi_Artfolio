<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QueueController extends Controller
{
    /**
     * Monitor de trabajos en cola y fallos del sistema (RNF-01).
     */
    public function index(): View
    {
        $pendingJobs = DB::table('jobs')
            ->orderBy('id', 'desc')
            ->take(30)
            ->get();

        $failedJobs = DB::table('failed_jobs')
            ->orderBy('id', 'desc')
            ->take(30)
            ->get();

        $counts = [
            'pending' => DB::table('jobs')->count(),
            'failed'  => DB::table('failed_jobs')->count(),
        ];

        return view('admin.queues.index', compact('pendingJobs', 'failedJobs', 'counts'));
    }

    /**
     * Reintenta un trabajo fallido específico.
     */
    public function retry(string|int $id): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => [(string) $id]]);

        return back()->with('success', "Se ha ordenado el reintento del trabajo #{$id}.");
    }

    /**
     * Descarta y elimina un fallo específico.
     */
    public function forget(string|int $id): RedirectResponse
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return back()->with('success', "El trabajo fallido #{$id} ha sido descartado.");
    }

    /**
     * Limpia todos los registros de trabajos fallidos.
     */
    public function flush(): RedirectResponse
    {
        Artisan::call('queue:flush');

        return back()->with('success', 'Se ha vaciado el registro completo de trabajos fallidos.');
    }
}
