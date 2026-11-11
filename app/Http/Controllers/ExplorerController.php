<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ExplorerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $artworks = Artwork::with('user')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->published()
            ->search($search)
            ->when($category, fn($query, $category) => $query->byCategory($category))
            ->when($request->user()?->hide_ai_content, fn($query) => $query->notAi())
            ->when($request->user(), function ($query, $user) {
                $blockedIds = $user->blockedUsers()->pluck('blocked_user_id')
                    ->merge($user->blockedByUsers()->pluck('user_id'))
                    ->unique();

                if ($blockedIds->isNotEmpty()) {
                    $query->whereNotIn('user_id', $blockedIds);
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('explorer.index', compact('artworks', 'search', 'category'));
    }
}
