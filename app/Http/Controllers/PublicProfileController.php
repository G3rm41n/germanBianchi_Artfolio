<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show($slug)
    {
        $user = User::where('slug', $slug)->firstOrFail();

        $collections = $user->collections()->has('artworks')->get();
        $selectedCollectionId = request('collection_id');

        $query = $user->artworks()->where('status', 'published')->latest();

        if ($selectedCollectionId) {
            $query->whereHas('collections', function($q) use ($selectedCollectionId) {
                $q->where('collections.id', $selectedCollectionId);
            });
        }

        $artworks = $query->paginate(12)->withQueryString();

        return view('profile.show', compact('user', 'artworks', 'collections', 'selectedCollectionId'));
    }
}
