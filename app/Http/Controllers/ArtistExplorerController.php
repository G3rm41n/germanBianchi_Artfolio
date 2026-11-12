<?php

namespace App\Http\Controllers;

use App\Models\CuratedTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtistExplorerController extends Controller
{
    /**
     * Display the artists discovery and search directory (Artstation style).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', $request->input('name', '')));
        $city = trim((string) $request->input('city', ''));
        $country = trim((string) $request->input('country', ''));
        $availability = trim((string) $request->input('availability', ''));
        $viewMode = $request->input('view', 'grid');
        if (!in_array($viewMode, ['grid', 'list'])) {
            $viewMode = 'grid';
        }

        $matchMode = strtolower((string) $request->input('match', $request->input('match_mode', 'or')));
        if (!in_array($matchMode, ['or', 'all'])) {
            $matchMode = 'or';
        }

        // Process multiple skills/disciplines
        $rawSkills = $request->input('skills', []);
        if (!is_array($rawSkills)) {
            $rawSkills = $rawSkills !== '' ? [$rawSkills] : [];
        }
        if ($request->filled('skill') && !in_array($request->input('skill'), $rawSkills)) {
            $rawSkills[] = $request->input('skill');
        }
        $selectedSkills = array_values(array_unique(array_filter(array_map('trim', $rawSkills))));

        // Process multiple software/tools
        $rawTools = $request->input('tools', []);
        if (!is_array($rawTools)) {
            $rawTools = $rawTools !== '' ? [$rawTools] : [];
        }
        if ($request->filled('tool') && !in_array($request->input('tool'), $rawTools)) {
            $rawTools[] = $request->input('tool');
        }
        $selectedTools = array_values(array_unique(array_filter(array_map('trim', $rawTools))));

        $query = User::query()
            ->where('status', 'active')
            ->withCount(['artworks' => fn ($q) => $q->published()])
            ->with([
                'artworks' => fn ($q) => $q->published()->latest('created_at')->take(3)
            ]);

        // 1. Text Search (Name, Slug, Headline, Bio)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('headline', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // 2 & 3. Skills & Software Filters (Support for 'all' vs 'or' match modes)
        if (!empty($selectedSkills) || !empty($selectedTools)) {
            if ($matchMode === 'all') {
                // Must match EVERY selected skill AND EVERY selected tool
                foreach ($selectedSkills as $s) {
                    $query->where(function ($q) use ($s) {
                        $q->whereJsonContains('skills', $s)
                          ->orWhere('skills', 'like', "%{$s}%");
                    });
                }
                foreach ($selectedTools as $t) {
                    $query->where(function ($q) use ($t) {
                        $q->whereJsonContains('software', $t)
                          ->orWhere('software', 'like', "%{$t}%");
                    });
                }
            } else {
                // Match ANY of the selected skills OR tools (at least one)
                $query->where(function ($q) use ($selectedSkills, $selectedTools) {
                    foreach ($selectedSkills as $s) {
                        $q->orWhereJsonContains('skills', $s)
                          ->orWhere('skills', 'like', "%{$s}%");
                    }
                    foreach ($selectedTools as $t) {
                        $q->orWhereJsonContains('software', $t)
                          ->orWhere('software', 'like', "%{$t}%");
                    }
                });
            }
        }

        // 4. City Filter
        if ($city !== '') {
            $query->where('city', 'like', "%{$city}%");
        }

        // 5. Country Filter
        if ($country !== '') {
            $query->where('country', $country);
        }

        // 6. Availability / Hiring Filter
        if ($availability !== '') {
            if ($availability === 'commissions_open') {
                $query->where('commission_status', 'open');
            } elseif ($availability === 'commissions_consult') {
                $query->where('commission_status', 'consult');
            } elseif (in_array($availability, ['freelance', 'full-time', 'contract'])) {
                $query->where(function ($q) use ($availability) {
                    $q->whereJsonContains('hiring_types', $availability)
                      ->orWhere('hiring_types', 'like', "%{$availability}%");
                });
            }
        }

        // Priority Ordering: Verified Artists first, then artists with published works, then newest
        $query->orderByDesc('is_verified')
              ->orderByDesc('artworks_count')
              ->latest('id');

        $artists = $query->paginate(12)->withQueryString();

        // Calculate count of applied filters
        $activeFiltersCount = count($selectedSkills) + count($selectedTools);
        foreach ([$search, $city, $country, $availability] as $filterVal) {
            if ($filterVal !== '') {
                $activeFiltersCount++;
            }
        }

        // Available curated disciplines & software for modal selection
        $availableDisciplines = CuratedTag::disciplines()->orderBy('sort_order')->orderBy('name')->pluck('name')->all();
        if (empty($availableDisciplines)) {
            $availableDisciplines = [
                'Concept Art', 'Digital Artist', 'Virtual Artist', 'Character Design',
                '3D Modeling', 'Digital Painting', 'Illustration', 'Environment Art',
                'Animation', 'Sculpting', 'Digital Sculpting', 'Storyboarding',
                'Texturing', 'UI / UX Art', 'VFX', 'Matte Painting', 'Lighting',
                'Game Art', 'Motion Graphics'
            ];
        }

        $availableSoftware = CuratedTag::software()->orderBy('sort_order')->orderBy('name')->pluck('name')->all();
        if (empty($availableSoftware)) {
            $availableSoftware = [
                'Blender', 'Photoshop', 'ZBrush', 'Unreal Engine', 'Maya',
                'Substance 3D Painter', 'Procreate', 'Cinema 4D', 'After Effects',
                'Houdini', 'Figma', 'Illustrator', 'Unity', 'Marvelous Designer',
                'Clip Studio Paint', 'DaVinci Resolve'
            ];
        }

        return view('artists.index', [
            'artists'              => $artists,
            'availableDisciplines' => $availableDisciplines,
            'availableSoftware'    => $availableSoftware,
            'selectedSkills'       => $selectedSkills,
            'selectedTools'        => $selectedTools,
            'matchMode'            => $matchMode,
            'viewMode'             => $viewMode,
            'activeFiltersCount'   => $activeFiltersCount,
            'search'               => $search,
            'city'                 => $city,
            'country'              => $country,
            'availability'         => $availability,
        ]);
    }
}
