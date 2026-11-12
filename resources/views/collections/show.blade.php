<x-app-layout>
    <x-slot name="header">
        <!-- OpenGraph & Social Sharing Meta Tags -->
        <meta property="og:type" content="website" />
        <meta property="og:title" content="{{ $collection->name }} - Collection by {{ $collection->user->name }} | Artfolio" />
        <meta property="og:description" content="{{ Str::limit($collection->description ?: 'Explore this curated collection by ' . $collection->user->name . ' on Artfolio.', 160) }}" />
        <meta property="og:url" content="{{ $collection->url }}" />
        @if($artworks->first())
            <meta property="og:image" content="{{ $artworks->first()->preview_url }}" />
        @endif
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $collection->name }} - Artfolio" />
        <meta name="twitter:description" content="{{ Str::limit($collection->description ?: 'Explore this collection on Artfolio.', 160) }}" />
    </x-slot>

    <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-950 min-h-screen text-gray-900 dark:text-gray-100 transition-colors"
         x-data="{
             searchQuery: '',
             shareModalOpen: false,
             shareUrl: '{{ $collection->url }}',
             shareTitle: '{{ addslashes($collection->name) }} - Collection by {{ addslashes($collection->user->name) }}',
             artworksCount: {{ $artworks->count() }},
             
             // Cover Picker Modal State
             coverModalOpen: false,
             selectedArtworkId: null,
             selectedArtworkTitle: '',
             selectedArtworkThumb: '',
             isSavingCover: false,
             coverToast: '',

             openCoverPicker(artId, artTitle, artThumb) {
                 this.selectedArtworkId = artId;
                 this.selectedArtworkTitle = artTitle;
                 this.selectedArtworkThumb = artThumb;
                 this.coverModalOpen = true;
             },

             async setCoverSlot(slot) {
                 if (!this.selectedArtworkId || this.isSavingCover) return;
                 this.isSavingCover = true;

                 try {
                     const response = await fetch('{{ route('collections.setPreviewArtwork', $collection) }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json',
                         },
                         body: JSON.stringify({
                             artwork_id: this.selectedArtworkId,
                             slot: slot
                         })
                     });

                     const data = await response.json();
                     if (response.ok && data.success) {
                         this.coverModalOpen = false;
                         this.coverToast = data.message;
                         setTimeout(() => this.coverToast = '', 4000);
                     } else {
                         alert(data.message || 'Error setting cover artwork.');
                     }
                 } catch (e) {
                     console.error(e);
                     alert('Connection error.');
                 } finally {
                     this.isSavingCover = false;
                 }
             },

             matches(title, author, category, tags) {
                 if (!this.searchQuery || this.searchQuery.trim() === '') return true;
                 const q = this.searchQuery.toLowerCase().trim();
                 return (title && title.toLowerCase().includes(q)) ||
                        (author && author.toLowerCase().includes(q)) ||
                        (category && category.toLowerCase().includes(q)) ||
                        (tags && tags.toLowerCase().includes(q));
             }
         }">

        <!-- Toast Feedback for Cover Update -->
        <div x-cloak
             x-show="coverToast"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-2 text-sm font-semibold">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span x-text="coverToast"></span>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Top Navigation & Breadcrumbs -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                @php
                    $isBoardOwner = Auth::check() && Auth::id() === $collection->user_id;
                    $backRoute = $isBoardOwner ? route('collections.index') : route('profile.public', $collection->user->slug);
                    $backText = $isBoardOwner ? __('Back to My Boards') : __('Back to ' . $collection->user->name);
                @endphp
                <a href="{{ $backRoute }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>{{ $backText }}</span>
                </a>

                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('home') }}" class="hover:underline">Explore</a>
                    <span>/</span>
                    <a href="{{ route('profile.public', $collection->user->slug) }}" class="hover:underline">{{ $collection->user->name }}</a>
                    <span>/</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[200px]">{{ $collection->name }}</span>
                </div>
            </div>

            <!-- Pinterest Board Header Card -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-10 border border-gray-200/80 dark:border-gray-800 shadow-sm mb-8 text-center relative overflow-hidden">
                <!-- Background ambient decorative blur -->
                <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 max-w-3xl mx-auto flex flex-col items-center">
                    
                    <!-- Visibility & Count Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                        @if($collection->visibility === 'private')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Private Board
                            </span>
                        @elseif($collection->visibility === 'unlisted')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Unlisted
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Public Board
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                            {{ $artworks->count() }} {{ Str::plural('artwork', $artworks->count()) }}
                        </span>
                    </div>

                    <!-- Collection Title -->
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-3">
                        {{ $collection->name }}
                    </h1>

                    <!-- Collection Description -->
                    @if($collection->description)
                        <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed max-w-2xl">
                            {{ $collection->description }}
                        </p>
                    @endif

                    <!-- Creator Info Bar -->
                    <div class="flex items-center gap-3 py-2 px-4 rounded-full bg-gray-50 dark:bg-gray-800/60 border border-gray-200/70 dark:border-gray-700/60 mb-6">
                        <a href="{{ route('profile.public', $collection->user->slug) }}" class="flex items-center gap-2.5 group">
                            <x-user-avatar :user="$collection->user" size="sm" class="ring-2 ring-indigo-500/20 group-hover:ring-indigo-500 transition-all" />
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $collection->user->name }}
                            </span>
                        </a>
                        <span class="text-xs text-gray-400 dark:text-gray-500">• Board Creator</span>
                    </div>

                    <!-- Action Buttons Toolbar -->
                    <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-3">
                        
                        <!-- Modo Exposición (Slideshow Fullscreen) -->
                        @if($artworks->count() > 0)
                            <button type="button"
                                    x-on:click="$dispatch('open-slideshow', { items: {{ json_encode($slideshowItems) }}, startIndex: 0 })"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-semibold text-sm shadow-md hover:shadow-indigo-500/25 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Exhibition Mode</span>
                            </button>
                        @endif

                        <!-- Share Button -->
                        <button type="button"
                                x-on:click="shareModalOpen = true"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 active:scale-95 text-gray-800 dark:text-gray-200 font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            <span>Share</span>
                        </button>

                        <!-- Edit / Manage Button (Owner Only) -->
                        @can('update', $collection)
                            <a href="{{ route('collections.edit', $collection) }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 active:scale-95 text-gray-800 dark:text-gray-200 font-semibold text-sm transition-all">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>Settings</span>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar (Real-time with Alpine.js) -->
            @if($artworks->count() > 0)
                <div class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Search in this board by title, tag, category..."
                               class="w-full pl-10 pr-9 py-2.5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all shadow-xs">
                        <button type="button"
                                x-show="searchQuery.length > 0"
                                x-on:click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        @can('update', $collection)
                            <span>Hover over an artwork and click the cover icon to set it on the board mosaic.</span>
                        @else
                            <span>Showing saved artworks in chronological order</span>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Artworks Masonry / Responsive Grid -->
            @if($artworks->count() > 0)
                <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 [column-fill:_balance]">
                    @foreach($artworks as $artwork)
                        <div x-show="matches(
                                {{ json_encode($artwork->title) }},
                                {{ json_encode($artwork->user->name) }},
                                {{ json_encode($artwork->category) }},
                                {{ json_encode($artwork->tags->pluck('name')->implode(' ')) }}
                             )"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             data-artwork-item
                             class="break-inside-avoid mb-6 relative group/item">
                            
                            <x-artwork-card :artwork="$artwork" />

                            <!-- Owner Feature: Set As Collection Cover Button (Icon only, expands text on hover) -->
                            @can('update', $collection)
                                <button type="button"
                                        @click="openCoverPicker({{ $artwork->id }}, {{ json_encode($artwork->title) }}, {{ json_encode($artwork->preview_url) }})"
                                        class="absolute top-3 left-3 opacity-0 group-hover/item:opacity-100 transition-all duration-300 z-20 h-8 px-2.5 rounded-full bg-white/95 dark:bg-gray-900/95 text-gray-800 dark:text-gray-200 shadow-lg hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white text-xs font-bold flex items-center overflow-hidden max-w-[34px] hover:max-w-[170px] active:scale-95 group/coverbtn"
                                        title="Choose position of this artwork on the board cover">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="ml-1.5 whitespace-nowrap opacity-0 group-hover/coverbtn:opacity-100 transition-opacity duration-200">Set as Cover</span>
                                </button>
                            @endcan
                        </div>
                    @endforeach
                </div>

                <!-- Empty search result state -->
                <div x-cloak
                     x-show="searchQuery.trim() !== '' && $el.closest('.max-w-7xl').querySelectorAll('[data-artwork-item][style*=\'display: none\']').length === {{ $artworks->count() }}"
                     class="text-center py-16 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 my-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">No matches found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">We could not find any artwork in this board matching "<span x-text="searchQuery" class="font-semibold text-gray-800 dark:text-gray-200"></span>".</p>
                    <button type="button" x-on:click="searchQuery = ''" class="mt-4 inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Clear search
                    </button>
                </div>

            @else
                <!-- Empty Collection State -->
                <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-3xl border border-dashed border-gray-300 dark:border-gray-800 max-w-2xl mx-auto p-8 shadow-xs">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">This board is currently empty</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                        @if(Auth::check() && Auth::id() === $collection->user_id)
                            Add your favorite artworks or creations by clicking the collection folder icon on any artwork while exploring.
                        @else
                            The creator has not added artworks to this collection yet. Check back later.
                        @endif
                    </p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-all shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Explore Gallery</span>
                    </a>
                </div>
            @endif

        </div>

        <!-- Cover Position Picker Modal (For Collection Owner) -->
        <div x-cloak
             x-show="coverModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
             x-on:keydown.escape.window="coverModalOpen = false">
            <div class="relative bg-white dark:bg-gray-900 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-gray-200 dark:border-gray-800"
                 x-on:click.outside="coverModalOpen = false">
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Set Board Cover Image
                    </h3>
                    <button type="button" x-on:click="coverModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Selected Artwork Preview Bar -->
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700 mb-5">
                    <img :src="selectedArtworkThumb" class="w-12 h-12 rounded-xl object-cover ring-1 ring-black/5 shrink-0" alt="Preview">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate" x-text="selectedArtworkTitle"></p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Which mosaic slot would you like to assign this artwork to?</p>
                    </div>
                </div>

                <!-- 3 Visual Position Options (Like Pinterest Mosaic) -->
                <div class="space-y-3 mb-6">
                    
                    <!-- Slot 0: Principal (Grande) -->
                    <button type="button"
                            @click="setCoverSlot(0)"
                            :disabled="isSavingCover"
                            class="w-full flex items-center justify-between p-3.5 rounded-2xl border-2 border-indigo-200 dark:border-indigo-900/50 hover:border-indigo-500 dark:hover:border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/20 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-all text-left group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shadow-sm">
                                1
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Main Artwork (Large)
                                </h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Occupies the large left section (66% of mosaic).</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition-transform">Select →</span>
                    </button>

                    <!-- Slot 1: Secundaria Superior -->
                    <button type="button"
                            @click="setCoverSlot(1)"
                            :disabled="isSavingCover"
                            class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-gray-200 dark:border-gray-800 hover:border-indigo-500 dark:hover:border-indigo-500 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/80 transition-all text-left group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-black text-sm flex items-center justify-center shadow-xs">
                                2
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Upper Secondary (Side)
                                </h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Top thumbnail of the right column.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:translate-x-1 transition-transform">Select →</span>
                    </button>

                    <!-- Slot 2: Secundaria Inferior -->
                    <button type="button"
                            @click="setCoverSlot(2)"
                            :disabled="isSavingCover"
                            class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-gray-200 dark:border-gray-800 hover:border-indigo-500 dark:hover:border-indigo-500 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/80 transition-all text-left group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-black text-sm flex items-center justify-center shadow-xs">
                                3
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Lower Secondary (Side)
                                </h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Bottom thumbnail of the right column.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:translate-x-1 transition-transform">Select →</span>
                    </button>

                </div>

                <div class="flex justify-end">
                    <button type="button"
                            @click="coverModalOpen = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Share Modal -->
        <div x-cloak
             x-show="shareModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
             x-on:keydown.escape.window="shareModalOpen = false">
            <div class="relative bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-800"
                 x-on:click.outside="shareModalOpen = false">
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        Share Board
                    </h3>
                    <button type="button" x-on:click="shareModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Share this board directly with friends, colleagues, or on social networks.
                </p>

                <!-- Copy Link Bar -->
                <div class="flex items-center gap-2 mb-4">
                    <input type="text" readonly :value="shareUrl" class="w-full text-xs bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-gray-700 dark:text-gray-300 select-all">
                    <button type="button"
                            x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(shareUrl); copied = true; setTimeout(() => copied = false, 2000)"
                            class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied" class="text-emerald-200">Copied!</span>
                    </button>
                </div>

                <!-- Social Quick Buttons -->
                <div class="grid grid-cols-3 gap-2">
                    <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle) + '&url=' + encodeURIComponent(shareUrl)"
                       target="_blank" rel="noopener noreferrer"
                       class="flex flex-col items-center justify-center p-3 rounded-2xl bg-gray-50 dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 transition-colors">
                        <span class="font-bold">X / Twitter</span>
                    </a>
                    <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent(shareTitle + ' ' + shareUrl)"
                       target="_blank" rel="noopener noreferrer"
                       class="flex flex-col items-center justify-center p-3 rounded-2xl bg-gray-50 dark:bg-gray-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 transition-colors">
                        <span class="font-bold">WhatsApp</span>
                    </a>
                    <a :href="'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(shareUrl)"
                       target="_blank" rel="noopener noreferrer"
                       class="flex flex-col items-center justify-center p-3 rounded-2xl bg-gray-50 dark:bg-gray-800 hover:bg-sky-50 dark:hover:bg-sky-950/40 border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 transition-colors">
                        <span class="font-bold">LinkedIn</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
