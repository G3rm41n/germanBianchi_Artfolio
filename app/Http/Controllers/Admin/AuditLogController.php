<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs separated by actor (admin vs user).
     */
    public function index(Request $request): View
    {
        $actor = $request->get('actor', 'admin');
        if (!in_array($actor, ['admin', 'user'])) {
            $actor = 'admin';
        }

        $query = AuditLog::with('user')->where('actor_type', $actor)->latest('id');

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('target_label', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(25)->withQueryString();

        $counts = [
            'admin' => AuditLog::where('actor_type', 'admin')->count(),
            'user'  => AuditLog::where('actor_type', 'user')->count(),
            'total' => AuditLog::count(),
        ];

        // Available actions for quick filtering in the current actor view
        $availableActions = AuditLog::where('actor_type', $actor)
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit_logs.index', compact('logs', 'actor', 'counts', 'availableActions'));
    }
}

