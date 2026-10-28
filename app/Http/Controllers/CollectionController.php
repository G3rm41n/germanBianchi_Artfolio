<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collections = Auth::user()->collections()->withCount('artworks')->latest()->paginate(10);
        return view('collections.index', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('collections.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Auth::user()->collections()->create($validated);

        return redirect()->route('collections.index')->with('success', 'Collection created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        return view('collections.form', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $collection->update($validated);

        return redirect()->route('collections.index')->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $collection->delete();

        return redirect()->route('collections.index')->with('success', 'Collection moved to trash.');
    }
}
