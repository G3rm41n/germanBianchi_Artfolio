<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'   => User::where('is_admin', false)->count(),
            'total_admins'  => User::where('is_admin', true)->count(),
            'suspended'     => User::where('status', 'suspended')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
