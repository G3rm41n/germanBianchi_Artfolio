@props(['artist'])

@php
    $previewArtworks = $artist->relationLoaded('artworks') ? $artist->artworks : collect();
    $artCount = $artist->artworks_count ?? $previewArtworks->count();
    $skills = is_array($artist->skills) ? $artist->skills : (json_decode($artist->skills ?? '[]', true) ?: []);
    $displaySkills = array_slice($skills, 0, 3);
    $extraSkillsCount = max(0, count($skills) - 3);
    $portfolioUrl = route('profile.public', $artist->slug);
@endphp

<div class="group relative flex flex-col bg-white dark:bg-gray-900 rounded-3xl overflow-hidden border border-gray-200/80 dark:border-gray-800 shadow-xs hover:shadow-xl transition-all duration-300">
    <!-- Top Showcase Banner (Mini Artworks Mosaic or Gradient) -->
    <a href="{{ $portfolioUrl }}" class="relative block h-32 w-full overflow-hidden bg-gray-100 dark:bg-gray-800/80">
        @if($previewArtworks->count() >= 3)
            <div class="w-full h-full flex gap-0.5">
                <div class="w-1/2 h-full overflow-hidden">
                    <img src="{{ $previewArtworks[0]->preview_url }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="w-1/4 h-full overflow-hidden">
                    <img src="{{ $previewArtworks[1]->preview_url }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="w-1/4 h-full overflow-hidden">
                    <img src="{{ $previewArtworks[2]->preview_url }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
            </div>
        @elseif($previewArtworks->count() > 0)
            <img src="{{ $previewArtworks[0]->preview_url }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @elseif($artist->banner_url)
            <img src="{{ $artist->banner_url }}" alt="" loading="lazy" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-indigo-500/15 via-purple-500/15 to-pink-500/15"></div>
        @endif
        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
    </a>

    <!-- Card Body -->
    <div class="relative px-5 pb-5 pt-0 flex-1 flex flex-col">
        <!-- Floating Avatar -->
        <div class="-mt-10 mb-3 flex items-end justify-between">
            <a href="{{ $portfolioUrl }}" class="relative inline-block">
                <x-user-avatar :user="$artist" size="lg" class="ring-4 ring-white dark:ring-gray-900 shadow-md group-hover:ring-indigo-500/40 transition-all" />
                @if($artist->is_verified)
                    <span class="absolute bottom-0 right-0 w-4 h-4 bg-indigo-600 text-white rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-900 shadow-xs" title="Verified Artist">
                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </span>
                @endif
            </a>

            <!-- Commission Availability Status Badge -->
            @if($artist->commission_status === 'open')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Available
                </span>
            @elseif($artist->commission_status === 'consult')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Upon Request
                </span>
            @endif
        </div>

        <!-- Name & Handle -->
        <div>
            <a href="{{ $portfolioUrl }}" class="font-bold text-base text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors block truncate">
                {{ $artist->name }}
            </a>
            <p class="text-xs text-gray-400 dark:text-gray-500 font-mono truncate">
                <span>@</span>{{ $artist->slug }}
            </p>
        </div>

        <!-- Headline or Bio excerpt -->
        <p class="mt-2 text-xs text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">
            {{ $artist->headline ?: ($artist->bio ?: 'Creative artist showcasing digital portfolio.') }}
        </p>

        <!-- Location (City, Country) -->
        @if($artist->city || $artist->country)
            <div class="mt-2.5 flex items-center text-xs text-gray-400 dark:text-gray-500 truncate">
                <svg class="w-3.5 h-3.5 mr-1 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="truncate">{{ implode(', ', array_filter([$artist->city, $artist->country])) }}</span>
            </div>
        @endif

        <!-- Skills Chips -->
        @if(!empty($displaySkills))
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach($displaySkills as $sk)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        {{ $sk }}
                    </span>
                @endforeach
                @if($extraSkillsCount > 0)
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold text-gray-400 dark:text-gray-500">
                        +{{ $extraSkillsCount }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Footer / Portfolio Action -->
        <div class="mt-auto pt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-800 text-xs">
            <span class="text-gray-400 dark:text-gray-500 font-medium">
                {{ $artCount }} {{ Str::plural('artwork', $artCount) }}
            </span>

            <a href="{{ $portfolioUrl }}" class="inline-flex items-center gap-1 font-bold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-0.5 transition-transform">
                <span>View Portfolio</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>

