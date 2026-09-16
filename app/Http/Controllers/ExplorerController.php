<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ExplorerController extends Controller
{
    public function index()
    {
        $artworks = Artwork::with('user')
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('explorer.index', compact('artworks'));
    }
}
