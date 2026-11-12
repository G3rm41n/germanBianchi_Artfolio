<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\CuratedTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExplorerController extends Controller
{
    /**
     * Display the artworks discovery and filter gallery (Artstation / Pinterest style).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $sort = strtolower(trim((string) $request->input('sort', 'latest')));
        if (!in_array($sort, ['latest', 'likes', 'views', 'oldest'])) {
            $sort = 'latest';
        }

        $matchMode = strtolower(trim((string) $request->input('match', $request->input('match_mode', 'or'))));
        if (!in_array($matchMode, ['or', 'all'])) {
            $matchMode = 'or';
        }

        // Process multiple tags / disciplines
        $rawTags = $request->input('tags', []);
        if (!is_array($rawTags)) {
            $rawTags = $rawTags !== '' ? [$rawTags] : [];
        }
        if ($request->filled('tag') && !in_array($request->input('tag'), $rawTags)) {
            $rawTags[] = $request->input('tag');
        }
        $selectedTags = array_values(array_unique(array_filter(array_map('trim', $rawTags))));

        // Process multiple software / tools
        $rawTools = $request->input('tools', []);
        if (!is_array($rawTools)) {
            $rawTools = $rawTools !== '' ? [$rawTools] : [];
        }
        if ($request->filled('tool') && !in_array($request->input('tool'), $rawTools)) {
            $rawTools[] = $request->input('tool');
        }
        $selectedTools = array_values(array_unique(array_filter(array_map('trim', $rawTools))));

        $query = Artwork::query()
            ->with(['user', 'tags'])
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->published();

        // 1. Text Search (title, description, author name)
        if ($search !== '') {
            $query->search($search);
        }

        // 2. Category / Media Type Filter
        if ($category !== '' && in_array($category, ['image', 'video', '3d', 'audio', 'doc'])) {
            $query->byCategory($category);
        }

        // 3. User Profile AI Content Preference (decided in user settings)
        if ($request->user()?->hide_ai_content) {
            $query->notAi();
        }

        // 4. Mature Content Preferences (18+)
        if ($request->user()?->wantsMatureHidden()) {
            $query->notMature();
        }

        // 5. Blocked Users Filter
        if ($request->user()) {
            $blockedIds = $request->user()->blockedUsers()->pluck('blocked_user_id')
                ->merge($request->user()->blockedByUsers()->pluck('user_id'))
                ->unique();

            if ($blockedIds->isNotEmpty()) {
                $query->whereNotIn('user_id', $blockedIds);
            }
        }

        // 6. Multiple Tags & Software Filters (Support for 'all' vs 'or' match modes)
        if (!empty($selectedTags) || !empty($selectedTools)) {
            $applyTagCondition = function ($subQuery, $tag) {
                $slug = Str::slug($tag);
                $subQuery->where(function ($q) use ($tag, $slug) {
                    $q->whereHas('tags', fn($tq) => $tq->where('name', $tag)->orWhere('slug', $slug))
                      ->orWhere('subcategory', $slug)
                      ->orWhere('subcategory', $tag)
                      ->orWhereHas('user', function ($uq) use ($tag) {
                          $uq->whereJsonContains('skills', $tag)
                             ->orWhere('skills', 'like', "%{$tag}%");
                      });
                });
            };

            $applyToolCondition = function ($subQuery, $tool) {
                $subQuery->where(function ($q) use ($tool) {
                    $q->whereJsonContains('software_used', $tool)
                      ->orWhere('software_used', 'like', "%{$tool}%")
                      ->orWhereHas('user', function ($uq) use ($tool) {
                          $uq->whereJsonContains('software', $tool)
                             ->orWhere('software', 'like', "%{$tool}%");
                      });
                });
            };

            if ($matchMode === 'all') {
                // Must match EVERY selected tag AND EVERY selected software tool
                foreach ($selectedTags as $tag) {
                    $applyTagCondition($query, $tag);
                }
                foreach ($selectedTools as $tool) {
                    $applyToolCondition($query, $tool);
                }
            } else {
                // Matches ANY of the selected tags OR tools (at least one)
                $query->where(function ($orQuery) use ($selectedTags, $selectedTools, $applyTagCondition, $applyToolCondition) {
                    foreach ($selectedTags as $tag) {
                        $orQuery->orWhere(function ($q) use ($applyTagCondition, $tag) {
                            $applyTagCondition($q, $tag);
                        });
                    }
                    foreach ($selectedTools as $tool) {
                        $orQuery->orWhere(function ($q) use ($applyToolCondition, $tool) {
                            $applyToolCondition($q, $tool);
                        });
                    }
                });
            }
        }

        // 7. Sort Order
        switch ($sort) {
            case 'likes':
                $query->orderByDesc('likes_count')->latest('id');
                break;
            case 'views':
                $query->orderByDesc('views_count')->latest('id');
                break;
            case 'oldest':
                $query->oldest('id');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $artworks = $query->paginate(12)->withQueryString();

        // Calculate count of applied filters
        $activeFiltersCount = count($selectedTags) + count($selectedTools);
        if ($search !== '') $activeFiltersCount++;
        if ($category !== '') $activeFiltersCount++;
        if ($sort !== 'latest') $activeFiltersCount++;

        // Available curated disciplines & software for modal selection
        $availableDisciplines = CuratedTag::disciplines()->orderBy('sort_order')->orderBy('name')->pluck('name')->all();
        if (empty($availableDisciplines)) {
            $availableDisciplines = [
                'Concept Art', 'Digital Artist', 'Virtual Artist', 'Character Design',
                '3D Modeling', 'Digital Painting', 'Illustration', 'Environment Art',
                'Animation', 'Sculpting', 'Digital Sculpting', 'Storyboarding',
                'Texturing', 'UI / UX Art', 'VFX', 'Matte Painting', 'Lighting & LookDev',
                'Game Art', 'Motion Graphics', 'Pixel Art', 'Sound Design'
            ];
        }

        $availableSoftware = CuratedTag::software()->orderBy('sort_order')->orderBy('name')->pluck('name')->all();
        if (empty($availableSoftware)) {
            $availableSoftware = [
                'Blender', 'Photoshop', 'ZBrush', 'Unreal Engine', 'Maya',
                'Substance 3D Painter', 'Procreate', 'Cinema 4D', 'After Effects',
                'Houdini', 'Figma', 'Illustrator', 'Unity', 'Marvelous Designer',
                'Clip Studio Paint', 'DaVinci Resolve', 'Godot', '3ds Max'
            ];
        }

        return view('explorer.index', [
            'artworks'             => $artworks,
            'availableDisciplines' => $availableDisciplines,
            'availableSoftware'    => $availableSoftware,
            'selectedTags'         => $selectedTags,
            'selectedTools'        => $selectedTools,
            'search'               => $search,
            'category'             => $category,
            'sort'                 => $sort,
            'matchMode'            => $matchMode,
            'activeFiltersCount'   => $activeFiltersCount,
        ]);
    }
}
