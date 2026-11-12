@props(['artwork'])

@php
    $viewer = Auth::user();
    $isOwner = $viewer && $viewer->id === $artwork->user_id;

    // Performance optimization: Memoize bookmarked and liked artwork IDs per request to eliminate N+1 queries
    $bookmarkedMap = once(fn() => $viewer ? $viewer->bookmarks()->pluck('artwork_id')->flip() : collect());
    $likedMap = once(fn() => $viewer ? $viewer->likedArtworks()->pluck('artwork_id')->flip() : collect());

    $isBookmarked = isset($bookmarkedMap[$artwork->id]);
    $isLiked = isset($likedMap[$artwork->id]);

    // Determine if this artwork should be initially blurred for the viewer:
    $shouldBlur = $artwork->is_mature && ($viewer ? $viewer->wantsMatureBlurred() : true);
@endphp

<div class="artwork-card-contain group relative bg-white dark:bg-gray-800 rounded-3xl shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col border border-gray-100 dark:border-gray-700/60 hover:-translate-y-1"
     x-data="{
         isBlurred: {{ $shouldBlur ? 'true' : 'false' }},
         revealed: false,
         pickerOpen: false
     }"
     :class="pickerOpen ? 'relative z-50 ring-2 ring-indigo-500/20' : 'relative z-10'"
     @collection-picker-toggled.stop="pickerOpen = $event.detail.open">
    
    <!-- Artwork Preview Container (Dynamic Natural Aspect Ratio) -->
    <div class="relative w-full bg-gray-100 dark:bg-gray-900 rounded-t-3xl overflow-hidden group">
        
        <a href="{{ route('artworks.show', $artwork) }}" class="block w-full cursor-pointer">
            @if ($artwork->preview_url)
                <img src="{{ $artwork->preview_url }}" 
                     alt="{{ $artwork->title }}" 
                     loading="lazy" 
                     decoding="async"
                     class="w-full h-auto min-h-[140px] max-h-[540px] object-cover block transform group-hover:scale-105 transition-all duration-500 ease-out"
                     :class="(isBlurred && !revealed) ? 'filter blur-xl scale-110' : ''">
            @else
                <!-- Placeholder for direct embed media without preview image -->
                <div class="w-full aspect-[4/3] flex flex-col items-center justify-center text-gray-400 p-6 text-center"
                     :class="(isBlurred && !revealed) ? 'filter blur-xl' : ''">
                    <svg class="w-12 h-12 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-xs font-semibold">{{ $artwork->provider }}</span>
                </div>
            @endif
        </a>

        <!-- Mature +18 Blurred Click-to-Reveal Overlay (Centered) -->
        <template x-if="isBlurred && !revealed">
            <div class="absolute inset-0 bg-black/75 flex flex-col items-center justify-center p-4 z-20 text-center transition-opacity">
                <div class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center font-black text-xs shadow-lg mb-2">
                    18+
                </div>
                <h4 class="text-xs font-bold text-white mb-1">Sensitive Content</h4>
                <p class="text-[10px] text-gray-300 max-w-[180px] mb-3 leading-tight">This artwork is flagged as mature (+18).</p>
                <button type="button"
                        @click.stop="revealed = true"
                        class="px-3.5 py-1.5 rounded-full bg-white/95 hover:bg-white text-gray-900 font-bold text-[11px] shadow-md hover:scale-105 active:scale-95 transition-all">
                    Click to reveal
                </button>
            </div>
        </template>

        <!-- Floating Quick View Pill Overlay on Hover -->
        <a href="{{ route('artworks.show', $artwork) }}"
           x-show="!isBlurred || revealed"
           class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center p-4 z-10">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white font-bold text-xs shadow-md transform translate-y-2 group-hover:translate-y-0 transition-transform duration-200">
                View Artwork
            </span>
        </a>
    </div>
    
    <div class="p-5 relative z-20">
        <div class="grid grid-cols-[1fr_auto_auto] items-start gap-x-2 gap-y-2">
            <!-- Row 1, Col 1: Title -->
            <div class="min-w-0 pr-1">
                <a href="{{ route('artworks.show', $artwork) }}" class="text-base font-bold text-gray-900 dark:text-white truncate block group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-normal" title="{{ $artwork->title }}">
                    {{ $artwork->title }}
                </a>
            </div>

            <!-- Row 1, Col 2: Appreciate (Like) Button & Count -->
            <div class="flex items-center justify-start">
                @auth
                    <div x-data="{ liked: {{ $isLiked ? 'true' : 'false' }}, likesCount: {{ (int)$artwork->likes_count }}, loading: false }"
                         @artwork-like-updated.window="if ($event.detail.id === {{ $artwork->id }}) { liked = $event.detail.is_liked; likesCount = $event.detail.likes_count; }">
                        <button type="button" @click.stop="
                            loading = true;
                            fetch('{{ route('artworks.like', $artwork->id) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                }
                            }).then(res => res.json()).then(data => {
                                liked = data.is_liked;
                                likesCount = data.likes_count;
                                loading = false;
                                window.dispatchEvent(new CustomEvent('artwork-like-updated', { detail: { id: {{ $artwork->id }}, is_liked: data.is_liked, likes_count: data.likes_count } }));
                            }).catch(() => loading = false)
                        " class="flex items-center gap-1 transition-colors focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/60" :class="liked ? 'text-pink-500 hover:text-pink-600' : 'text-gray-400 dark:text-gray-500 hover:text-pink-500'" title="Appreciate artwork">
                            <svg class="w-4 h-4 transition-all" :class="liked ? 'fill-current scale-110' : ''" :class="{'opacity-50': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="text-[11px] font-mono font-semibold" x-text="likesCount"></span>
                        </button>
                    </div>
                @else
                    <button type="button" @click.stop="$dispatch('open-guest-modal', { context: 'aprecio' })" class="flex items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-pink-500 transition-colors focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/60" title="Appreciate artwork">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="text-[11px] font-mono font-semibold">{{ (int)$artwork->likes_count }}</span>
                    </button>
                @endauth
            </div>

            <!-- Row 1, Col 3: Bookmark (Favorites) Button -->
            <div class="flex items-center justify-center">
                @auth
                    <div x-data="{ bookmarked: {{ $isBookmarked ? 'true' : 'false' }}, loading: false }"
                         @bookmark-updated.window="if ($event.detail.id === {{ $artwork->id }}) bookmarked = $event.detail.status">
                        <button type="button" @click.stop="
                            loading = true;
                            fetch('{{ route('bookmarks.toggle', $artwork->id) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                }
                            }).then(res => res.json()).then(data => {
                                bookmarked = data.is_bookmarked;
                                loading = false;
                                window.dispatchEvent(new CustomEvent('bookmark-updated', { detail: { id: {{ $artwork->id }}, status: data.is_bookmarked } }));
                            }).catch(() => loading = false)
                        " class="transition-colors focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/60" :class="bookmarked ? 'text-amber-500 hover:text-amber-600' : 'text-gray-300 dark:text-gray-600 hover:text-amber-500'" title="Save to Favorites">
                            <svg class="w-4 h-4 transition-all" :class="bookmarked ? 'fill-current scale-110' : ''" :class="{'opacity-50': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </button>
                    </div>
                @else
                    <button type="button" @click.stop="$dispatch('open-guest-modal', { context: 'favorito' })" class="text-gray-300 dark:text-gray-600 hover:text-amber-500 transition-colors focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/60" title="Save to Favorites">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </button>
                @endauth
            </div>

            <!-- Row 2, Col 1: Author (Avatar + Name) and Badges beneath -->
            <div class="flex flex-col min-w-0 pr-1">
                <div class="flex items-center min-w-0">
                    <x-user-avatar :user="$artwork->user" size="xs" class="mr-2 shrink-0" />
                    <a href="{{ route('profile.public', $artwork->user->slug) }}" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors truncate" @click.stop>
                        {{ $artwork->user->name }}
                    </a>
                    @if($artwork->user->is_verified)
                        <span class="inline-flex items-center text-blue-500 ml-1 shrink-0" title="Verified Artist" aria-label="Verified Artist">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.25 14.5l-3.5-3.5 1.41-1.41 2.09 2.09 5.67-5.67 1.41 1.41-7.08 7.08z"/></svg>
                        </span>
                    @endif
                </div>

                <!-- Badges (Category/Format, AI, 18+) colocados debajo del nombre de usuario -->
                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                    <!-- Format / Category Badge -->
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60" title="Category: {{ $artwork->category }}">
                        @if($artwork->category === '3d')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>3D</span>
                        @elseif($artwork->category === 'video')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Video</span>
                        @elseif($artwork->category === 'audio')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            <span>Audio</span>
                        @elseif($artwork->category === 'document' || $artwork->category === 'doc')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Doc</span>
                        @elseif($artwork->category === 'code')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            <span>Code</span>
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Image</span>
                        @endif
                    </span>

                    <!-- AI Badge -->
                    @if($artwork->is_ai_generated)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60" title="Created with AI assistance">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>AI</span>
                        </span>
                    @endif

                    <!-- Mature / 18+ Badge -->
                    @if($artwork->is_mature)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60" title="Mature / 18+ Content">
                            <span>18+</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Row 2, Col 2: Views Count -->
            <div class="flex items-center justify-start self-start pt-0.5">
                <span class="flex items-center gap-1 text-[11px] font-mono font-semibold text-gray-400 dark:text-gray-500 p-1" title="{{ $artwork->views_count }} views">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>{{ $artwork->views_count }}</span>
                </span>
            </div>

            <!-- Row 2, Col 3: Collection Picker -->
            <div class="flex items-center justify-center self-start pt-0.5">
                <x-collection-picker :artwork-id="$artwork->id" size="sm" placement="bottom" />
            </div>
        </div>
    </div>
</div>
