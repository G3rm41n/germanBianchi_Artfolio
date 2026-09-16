<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show($slug)
    {
        $user = User::where('slug', $slug)->firstOrFail();

        $artworks = $user->artworks()
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('profile.show', compact('user', 'artworks'));
    }
}
