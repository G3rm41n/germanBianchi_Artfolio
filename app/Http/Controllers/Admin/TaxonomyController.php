<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuratedTag;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    /**
     * Display a listing of curated tags (disciplines & software).
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'discipline');
        if (!in_array($type, ['discipline', 'software', 'all'])) {
            $type = 'discipline';
        }

        $query = CuratedTag::query();

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->orderBy('name')->paginate(20)->withQueryString();

        $counts = [
            'all'         => CuratedTag::count(),
            'discipline'  => CuratedTag::disciplines()->count(),
            'software'    => CuratedTag::software()->count(),
        ];

        return view('admin.tags.index', compact('tags', 'type', 'counts'));
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:60'],
            'type'         => ['required', 'in:discipline,software'],
            'is_suggested' => ['nullable', 'boolean'],
        ]);

        $name = trim($validated['name']);
        $type = $validated['type'];

        // Case-insensitive duplicate check within same type
        $exists = CuratedTag::where('type', $type)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "A tag with name \"{$name}\" already exists in " . ($type === 'software' ? 'Software & Tools' : 'Artistic Disciplines') . ".");
        }

        $tag = CuratedTag::create([
            'name'         => $name,
            'type'         => $type,
            'slug'         => Str::slug($name),
            'is_suggested' => $request->boolean('is_suggested', true),
        ]);

        AuditLogger::log(
            'admin.tag_created',
            $tag,
            $tag->name,
            ['type' => $tag->type, 'is_suggested' => $tag->is_suggested],
            "Admin created {$tag->type} tag '{$tag->name}'"
        );

        return back()->with('success', "Tag \"{$tag->name}\" added successfully to " . ($type === 'software' ? 'Software & Tools' : 'Artistic Disciplines') . ".");
    }

    /**
     * Update the specified tag in storage with cascading sync to user profiles.
     */
    public function update(Request $request, CuratedTag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:60'],
            'is_suggested' => ['nullable', 'boolean'],
        ]);

        $newName = trim($validated['name']);
        $oldName = $tag->name;
        $isSuggested = $request->boolean('is_suggested', true);

        // Check duplicate name within same type
        $duplicate = CuratedTag::where('type', $tag->type)
            ->where('id', '!=', $tag->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($newName)])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', "Another tag named \"{$newName}\" already exists in this category.");
        }

        $affectedUsers = 0;
        if (strcasecmp($oldName, $newName) !== 0) {
            $affectedUsers = CuratedTag::syncCascadeRename($tag->type, $oldName, $newName);
        }

        $tag->update([
            'name'         => $newName,
            'slug'         => Str::slug($newName),
            'is_suggested' => $isSuggested,
        ]);

        AuditLogger::log(
            'admin.tag_renamed',
            $tag,
            $newName,
            [
                'old_name'       => $oldName,
                'new_name'       => $newName,
                'type'           => $tag->type,
                'affected_users' => $affectedUsers,
                'is_suggested'   => $isSuggested,
            ],
            "Admin renamed {$tag->type} tag from '{$oldName}' to '{$newName}' (cascaded to {$affectedUsers} user profiles)"
        );

        $msg = "Tag \"{$newName}\" updated.";
        if ($affectedUsers > 0) {
            $msg .= " Automatically updated in {$affectedUsers} user profile(s).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Remove the specified tag from storage and cascade purge from user profiles.
     */
    public function destroy(CuratedTag $tag): RedirectResponse
    {
        $tagName = $tag->name;
        $tagType = $tag->type;

        // Cascade remove from all users
        $affectedUsers = CuratedTag::syncCascadeRemove($tagType, $tagName);

        $tag->delete();

        AuditLogger::log(
            'admin.tag_deleted',
            null,
            $tagName,
            [
                'type'           => $tagType,
                'affected_users' => $affectedUsers,
            ],
            "Admin deleted {$tagType} tag '{$tagName}' (purged from {$affectedUsers} user profiles)"
        );

        $msg = "Tag \"{$tagName}\" was deleted.";
        if ($affectedUsers > 0) {
            $msg .= " Removed from {$affectedUsers} user profile(s).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Toggle whether a tag is suggested in user autocomplete dropdowns.
     */
    public function toggleSuggested(CuratedTag $tag): RedirectResponse
    {
        $tag->update([
            'is_suggested' => ! $tag->is_suggested,
        ]);

        AuditLogger::log(
            'admin.tag_toggle_suggested',
            $tag,
            $tag->name,
            ['is_suggested' => $tag->is_suggested],
            "Admin changed suggested status for '{$tag->name}' to " . ($tag->is_suggested ? 'true' : 'false')
        );

        $statusText = $tag->is_suggested ? 'now marked as suggested' : 'no longer suggested';
        return back()->with('success', "Tag \"{$tag->name}\" is {$statusText}.");
    }
}

