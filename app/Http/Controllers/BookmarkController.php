<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = auth()->user()->bookmarks()->with('user')->paginate(12);
        return view('bookmarks.index', compact('bookmarks'));
    }

    public function toggle(Request $request, $artwork_id)
    {
        $user = auth()->user();
        
        $toggled = $user->bookmarks()->toggle($artwork_id);
        
        $isBookmarked = count($toggled['attached']) > 0;
        
        return response()->json([
            'status' => 'success',
            'is_bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to favorites' : 'Removed from favorites'
        ]);
    }
}

