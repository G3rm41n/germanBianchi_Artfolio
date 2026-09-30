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
            ->published()
            ->search($search)
            ->when($category, fn($query, $category) => $query->byCategory($category))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('explorer.index', compact('artworks', 'search', 'category'));
    }
}
