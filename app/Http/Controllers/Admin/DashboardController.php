<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Collection;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'        => User::where('is_admin', false)->count(),
            'suspended_users'    => User::where('status', 'suspended')->count(),
            'total_admins'       => User::where('is_admin', true)->count(),
            'published_artworks' => Artwork::where('status', 'published')->count(),
            'hidden_artworks'    => Artwork::where('status', 'hidden')->count(),
            'trashed_artworks'   => Artwork::onlyTrashed()->count(),
            'trashed_collections'=> Collection::onlyTrashed()->count(),
            'pending_reports'    => Report::where('status', 'pending')->count(),
            'resolved_reports'   => Report::where('status', 'resolved')->count(),
        ];

        $recentReports = Report::with(['user', 'artwork.user'])
            ->latest()
            ->take(6)
            ->get();

        $recentUsers = User::where('is_admin', false)
            ->withCount('artworks')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports', 'recentUsers'));
    }
}
