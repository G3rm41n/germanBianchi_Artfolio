<x-app-layout>
    <x-slot name="header">
        <!-- OpenGraph & Social Sharing Meta Tags -->
        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $artwork->title }} - por {{ $artwork->user->name }} | Artfolio" />
        <meta property="og:description" content="{{ Str::limit($artwork->description ?: 'Descubre esta obra en Artfolio, la plataforma para artistas digitales y visuales.', 160) }}" />
        <meta property="og:image" content="{{ $artwork->high_res_url ?: ($artwork->thumbnail_url ?: asset('images/og-artfolio.jpg')) }}" />
        <meta property="og:url" content="{{ route('artworks.show', $artwork) }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $artwork->title }} - por {{ $artwork->user->name }}" />
        <meta name="twitter:description" content="{{ Str::limit($artwork->description ?: 'Descubre esta obra en Artfolio.', 160) }}" />
        <meta name="twitter:image" content="{{ $artwork->high_res_url ?: ($artwork->thumbnail_url ?: asset('images/og-artfolio.jpg')) }}" />
    </x-slot>

    <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-950 min-h-screen text-gray-900 dark:text-gray-100 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Back Navigation Bar -->
            @php
                $previousUrl = url()->previous();
                $currentUrl = url()->current();
                $appHost = request()->getHost();
                $isInternalPrevious = $previousUrl 
                    && $previousUrl !== $currentUrl 
                    && (parse_url($previousUrl, PHP_URL_HOST) === $appHost || !parse_url($previousUrl, PHP_URL_HOST));

                $backRoute = $isInternalPrevious ? $previousUrl : route('home');

                // Determine smart contextual label based on the origin URL
                if ($isInternalPrevious) {
                    $prevPath = trim(parse_url($previousUrl, PHP_URL_PATH) ?? '', '/');
                    if (str_starts_with($prevPath, 'artworks') || str_starts_with($prevPath, 'obras')) {
                        $backLabel = __('Back to My Artworks');
                    } elseif (str_starts_with($prevPath, 'collections') || str_starts_with($prevPath, 'colecciones') || str_contains($prevPath, '/board/')) {
                        $backLabel = __('Back to Collection');
                    } elseif (str_starts_with($prevPath, 'artist/') || str_starts_with($prevPath, 'perfil/') || str_starts_with($prevPath, 'user/')) {
                        $backLabel = __('Back to Profile');
                    } elseif (str_starts_with($prevPath, 'artists') || str_starts_with($prevPath, 'artistas')) {
                        $backLabel = __('Back to Artists Directory');
                    } else {
                        $backLabel = __('Back');
                    }
                } else {
                    $backLabel = __('Back to Gallery');
                }
            @endphp
            <div class="mb-5 flex items-center justify-between">
                <a href="{{ $backRoute }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>{{ $backLabel }}</span>
                </a>

                <!-- Breadcrumbs -->
                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <a href="{{ route('home') }}" class="hover:underline">Explore</a>
                    <span>/</span>
                    <a href="{{ route('home', ['category' => $artwork->category]) }}" class="hover:underline capitalize">{{ $artwork->category }}</a>
                    <span>/</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-xs">{{ $artwork->title }}</span>
                </div>
            </div>

            <!-- Main Pinterest Card Container -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-200/80 dark:border-gray-800 overflow-hidden flex flex-col lg:flex-row transition-all">
                
                <!-- Left / Center: High Quality Media Presentation -->
                @php
                    $viewer = Auth::user();
                    $shouldBlurShow = $artwork->is_mature && ($viewer ? $viewer->wantsMatureBlurred() : true);
                @endphp
                <div class="w-full lg:w-3/5 bg-gray-950 flex flex-col justify-center items-center relative min-h-[380px] sm:min-h-[520px] lg:min-h-[640px] p-3 sm:p-6 overflow-hidden"
                     x-data="{
                         isBlurred: {{ $shouldBlurShow ? 'true' : 'false' }},
                         revealed: false
                     }">
                    
                    @if($artwork->render_type === 'iframe' && $artwork->embed_html)
                        <!-- Iframe Media (YouTube, Vimeo, Spotify, SoundCloud, CodePen, Bandcamp, Google Docs, Sketchfab) -->
                        <div class="w-full h-full flex flex-col justify-center items-center relative"
                             :class="(isBlurred && !revealed) ? 'filter blur-2xl scale-105 pointer-events-none' : ''">
                            <div class="w-full h-full max-w-4xl flex items-center justify-center [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:min-h-[420px] sm:[&>iframe]:min-h-[540px] [&>iframe]:rounded-2xl shadow-2xl" style="line-height:0">
                                {!! $artwork->embed_html !!}
                            </div>
                        </div>
                    @else
                        <!-- Direct / High-Res Image Media (Pinterest, DeviantArt, Imgur, ArtStation, direct uploads) -->
                        <div class="w-full h-full flex items-center justify-center relative">
                            <img src="{{ $artwork->high_res_url ?: ($artwork->thumbnail_url ?: $artwork->external_url) }}"
                                 alt="{{ $artwork->title }}"
                                 referrerpolicy="no-referrer"
                                 decoding="async"
                                 class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl transition-all duration-300"
                                 :class="(isBlurred && !revealed) ? 'filter blur-2xl scale-105' : ''">
                        </div>
                    @endif

                    <!-- Mature Content 18+ Backdrop Cover Overlay with Click to Reveal -->
                    <div x-cloak x-show="isBlurred && !revealed" class="absolute inset-0 bg-black/75 backdrop-blur-md flex flex-col items-center justify-center p-6 z-20 text-center">
                        <div class="w-14 h-14 rounded-full bg-rose-600 text-white flex items-center justify-center font-black text-lg shadow-xl mb-3">
                            18+
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white mb-1">Mature & Sensitive Content</h3>
                        <p class="text-xs sm:text-sm text-gray-300 max-w-sm mb-4 leading-relaxed">
                            This artwork contains adult, graphic, or sensitive artistic themes. Click below to confirm and reveal the media.
                        </p>
                        <button type="button"
                                @click="revealed = true"
                                class="px-5 py-2.5 rounded-full bg-white text-gray-900 font-bold text-xs shadow-lg hover:bg-gray-100 hover:scale-105 active:scale-95 transition-all">
                            View Content (Reveal)
                        </button>
                    </div>

                    <!-- Open Original Source Badge Link -->
                    @if($artwork->external_url)
                        <a href="{{ $artwork->external_url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="absolute bottom-4 right-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold backdrop-blur-md shadow-lg border border-white/10 transition-transform hover:scale-105 select-none z-10
                            {{ match(strtolower($artwork->provider)) {
                                'youtube' => 'bg-[#ff0000]/90 text-white',
                                'spotify' => 'bg-[#1db954]/95 text-black font-bold',
                                'sketchfab' => 'bg-[#1caad9]/90 text-white',
                                'deviantart' => 'bg-[#00e59b]/90 text-gray-950 font-bold',
                                'pinterest' => 'bg-[#e60023]/90 text-white',
                                'google_docs', 'google docs' => 'bg-[#4285f4]/90 text-white',
                                'vimeo' => 'bg-[#1ab7ea]/90 text-white',
                                'soundcloud' => 'bg-[#ff5500]/90 text-white',
                                default => 'bg-black/75 text-white',
                            } }}">
                            <span class="capitalize">Abrir en {{ $artwork->provider }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    @endif
                </div>

                <!-- Right: Details, Author, Action Bar, and Comments -->
                <div class="w-full lg:w-2/5 p-6 sm:p-8 flex flex-col justify-between bg-white dark:bg-gray-900 border-t lg:border-t-0 lg:border-l border-gray-100 dark:border-gray-800">
                    
                    <div class="space-y-6">
                        <!-- Top Row: Actions Bar (Likes, Favorites, Collection, Share, Report) -->
                        <div class="flex items-center justify-between pb-5 border-b border-gray-100 dark:border-gray-800 gap-2">
                            
                            <!-- Left Actions: Like & Bookmark -->
                            <div class="flex items-center gap-2">
                                <!-- Like / Appreciation Button (#9) -->
                                @auth
                                    <div x-data="{ liked: {{ $isLiked ? 'true' : 'false' }}, count: {{ (int)$artwork->likes_count }}, loading: false }"
                                         @artwork-like-updated.window="if ($event.detail.id === {{ $artwork->id }}) { liked = $event.detail.is_liked; count = $event.detail.likes_count; }">
                                        <button type="button"
                                                @click="
                                                    loading = true;
                                                    fetch('{{ route('artworks.like', $artwork->id) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                            'Accept': 'application/json'
                                                        }
                                                    }).then(res => res.json()).then(data => {
                                                        liked = data.is_liked;
                                                        count = data.likes_count;
                                                        loading = false;
                                                        window.dispatchEvent(new CustomEvent('artwork-like-updated', { detail: { id: {{ $artwork->id }}, is_liked: data.is_liked, likes_count: data.likes_count } }));
                                                    }).catch(() => loading = false)
                                                "
                                                class="flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold transition-all shadow-xs"
                                                :class="liked ? 'bg-pink-500 text-white hover:bg-pink-600 shadow-pink-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:text-pink-500 dark:hover:text-pink-400 hover:bg-pink-50 dark:hover:bg-pink-950/30'"
                                                title="Apreciar esta obra">
                                            <svg class="w-4 h-4 fill-current transition-transform" :class="liked ? 'scale-110' : ''" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                            <span x-text="count"></span>
                                        </button>
                                    </div>
                                @else
                                    <button type="button"
                                            @click="$dispatch('open-guest-modal', { context: 'aprecio' })"
                                            class="flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:text-pink-500 transition-colors shadow-xs"
                                            title="Apreciar esta obra">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        <span>{{ (int)$artwork->likes_count }}</span>
                                    </button>
                                @endauth

                                <!-- Bookmark / Favorites Button -->
                                @auth
                                    <div x-data="{ bookmarked: {{ $isBookmarked ? 'true' : 'false' }}, loading: false }"
                                         @bookmark-updated.window="if ($event.detail.id === {{ $artwork->id }}) bookmarked = $event.detail.status">
                                        <button type="button"
                                                @click="
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
                                                "
                                                class="p-2 rounded-full transition-colors focus:outline-none"
                                                :class="bookmarked ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-500 hover:text-amber-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-400 hover:text-amber-500'"
                                                title="Guardar en Favoritos">
                                            <svg class="w-5 h-5 transition-transform" :class="bookmarked ? 'fill-current scale-110' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <button type="button"
                                            @click="$dispatch('open-guest-modal', { context: 'favorito' })"
                                            class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 hover:text-amber-500 transition-colors"
                                            title="Guardar en Favoritos">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                    </button>
                                @endauth
                            </div>

                            <!-- Right Actions: Collection Picker, Share, Owner Controls -->
                            <div class="flex items-center gap-1.5">
                                <!-- Collection Picker Dropdown (Guardar en...) -->
                                <x-collection-picker :artwork-id="$artwork->id" size="md" placement="bottom" />

                                <!-- Share Button -->
                                <button type="button"
                                        @click="$dispatch('open-share-modal', { title: '{{ addslashes($artwork->title) }} por {{ addslashes($artwork->user->name) }}', url: '{{ route('artworks.show', $artwork) }}' })"
                                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                        title="Compartir obra">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                </button>

                                @auth
                                    @if(auth()->id() === $artwork->user_id)
                                        <a href="{{ route('artworks.edit', $artwork) }}"
                                           class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                           title="Editar mi obra">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <!-- Artist Identity Profile -->
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('profile.public', $artwork->user->slug) }}" class="flex items-center gap-3 group">
                                <x-user-avatar :user="$artwork->user" size="md" class="shrink-0 group-hover:scale-105 transition-transform" />
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                            {{ $artwork->user->name }}
                                        </span>
                                        @if($artwork->user->is_verified)
                                            <span class="text-blue-500 shrink-0" title="Artista Verificado">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.25 14.5l-3.5-3.5 1.41-1.41 2.09 2.09 5.67-5.67 1.41 1.41-7.08 7.08z"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 font-mono">
                                        {{ '@' . $artwork->user->slug }}
                                    </p>
                                </div>
                            </a>

                            <a href="{{ route('profile.public', $artwork->user->slug) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors shrink-0">
                                Portfolio
                            </a>
                        </div>

                        <!-- Artwork Title & Description -->
                        <div class="space-y-2">
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                                {{ $artwork->title }}
                            </h1>
                            
                            @if($artwork->description)
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ $artwork->description }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 italic">
                                    Sin descripción añadida por el autor.
                                </p>
                            @endif

                            <div class="flex items-center gap-3 text-xs text-gray-400 pt-1 font-medium">
                                <span>{{ $artwork->created_at->translatedFormat('d M, Y') }} ({{ $artwork->created_at->diffForHumans() }})</span>
                                <span>•</span>
                                <span class="flex items-center gap-1 font-mono">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ (int)$artwork->views_count }} vistas
                                </span>
                            </div>
                        </div>

                        <!-- Badges: Category, Subcategory, AI, & Software -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <!-- Category Badge -->
                            <a href="{{ route('home', ['category' => $artwork->category]) }}" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                {{ $artwork->category }}
                            </a>

                            <!-- Mature / 18+ Badge -->
                            @if($artwork->is_mature)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-100 dark:bg-rose-900/50 text-indigo-700 dark:text-indigo-300 border border-rose-200 dark:border-rose-800 flex items-center gap-1" title="Mature / 18+ Artistic Content">
                                    <span>18+</span>
                                </span>
                            @endif

                            @if($artwork->subcategory)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                    {{ $artwork->subcategory }}
                                </span>
                            @endif

                            <!-- AI Badge -->
                            @if($artwork->is_ai_generated)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-purple-100 dark:bg-purple-900/50 text-indigo-700 dark:text-indigo-300 flex items-center gap-1" title="Obra creada o asistida por Inteligencia Artificial">
                                    <svg class="w-3 h-3 text-indigo-700 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>AI</span>
                                </span>
                            @endif

                            <!-- License Badge (Hidden for reserved rights as requested, visible only for explicit CC licenses) -->
                            @if(!empty($artwork->license) && $artwork->license !== 'all_rights_reserved')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60">
                                    {{ match($artwork->license) {
                                        'cc0' => 'Dominio Público (CC0)',
                                        'cc_by' => 'Creative Commons CC BY',
                                        'cc_by_sa' => 'CC BY-SA (Compartir Igual)',
                                        'cc_by_nc' => 'CC BY-NC (No Comercial)',
                                        'cc_by_nc_nd' => 'CC BY-NC-ND (Sin Derivadas)',
                                        default => null,
                                    } }}
                                </span>
                            @endif
                        </div>

                        <!-- Software Used Badges (#10) -->
                        @if(!empty($artwork->software_used) && is_array($artwork->software_used))
                            <div class="space-y-1.5 pt-1">
                                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider">Software utilizado</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($artwork->software_used as $tool)
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60">
                                            {{ $tool }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Tags / Hashtags List -->
                        @if($artwork->tags->isNotEmpty())
                            <div class="space-y-1.5 pt-1">
                                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider">Etiquetas</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($artwork->tags as $tag)
                                        <a href="{{ route('home', ['tag' => $tag->slug]) }}"
                                           class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Comments Section (Pinterest Style) -->
                        <div id="comments"
                             class="pt-6 border-t border-gray-100 dark:border-gray-800 space-y-4"
                             x-data="{
                                 comments: {{ json_encode($artwork->comments->map(function ($c) {
                                     return [
                                         'id' => $c->id,
                                         'content' => $c->content,
                                         'created_at' => $c->created_at->diffForHumans(),
                                         'user' => [
                                             'id' => $c->user->id,
                                             'name' => $c->user->name,
                                             'slug' => $c->user->slug,
                                             'avatar_url' => $c->user->avatar_url,
                                             'is_verified' => (bool)$c->user->is_verified,
                                         ],
                                         'can_delete' => auth()->check() && (auth()->id() === $c->user_id || auth()->id() === $c->artwork->user_id || auth()->user()->is_admin),
                                     ];
                                 })) }},
                                 newComment: '',
                                 submitting: false,
                                 errorMessage: '',

                                 addComment() {
                                     if (!this.newComment.trim()) return;
                                     this.submitting = true;
                                     this.errorMessage = '';

                                     fetch('{{ route('artworks.comments.store', $artwork) }}', {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                             'Accept': 'application/json'
                                         },
                                         body: JSON.stringify({ content: this.newComment })
                                     })
                                     .then(async res => {
                                         const data = await res.json();
                                         if (!res.ok) throw new Error(data.message || 'Error al publicar comentario');
                                         return data;
                                     })
                                     .then(data => {
                                         data.comment.can_delete = true;
                                         this.comments.unshift(data.comment);
                                         this.newComment = '';
                                         this.submitting = false;
                                     })
                                     .catch(err => {
                                         this.errorMessage = err.message;
                                         this.submitting = false;
                                     });
                                 },

                                 deleteComment(id, index) {
                                     fetch('/artworks/{{ $artwork->id }}/comments/' + id, {
                                         method: 'DELETE',
                                         headers: {
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                             'Accept': 'application/json'
                                         }
                                     })
                                     .then(res => res.json())
                                     .then(data => {
                                         if (data.status === 'success') {
                                             this.comments.splice(index, 1);
                                         }
                                     });
                                 }
                             }">
                            
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <span>Comments</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold" x-text="comments.length"></span>
                                </h3>
                            </div>

                            <!-- Comment Input Form -->
                            @auth
                                <form @submit.prevent="addComment()" class="space-y-2">
                                    <div class="flex items-start gap-2.5">
                                        <x-user-avatar :user="auth()->user()" size="sm" class="shrink-0 mt-1" />
                                        <div class="flex-1">
                                            <textarea x-model="newComment"
                                                      rows="2"
                                                      maxlength="1000"
                                                      placeholder="Add a respectful comment about this artwork..."
                                                      class="w-full text-xs rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/80 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 resize-none transition-colors"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pl-10">
                                        <span class="text-[11px] text-red-500" x-text="errorMessage"></span>
                                        <button type="submit"
                                                :disabled="submitting || !newComment.trim()"
                                                class="px-4 py-1.5 text-xs font-bold rounded-full bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-xs ml-auto">
                                            <span x-show="!submitting">Publicar</span>
                                            <span x-show="submitting">Publicando...</span>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-700/80 text-center">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Inicia sesión para compartir tu opinión con el artista.</p>
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('login') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 transition">Iniciar sesión</a>
                                        <a href="{{ route('register') }}" class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 transition">Registrarse</a>
                                    </div>
                                </div>
                            @endauth

                            <!-- Comments Stream -->
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                                <template x-if="comments.length === 0">
                                    <p class="text-xs text-gray-400 py-3 text-center italic">Sé el primero en comentar esta obra.</p>
                                </template>

                                <template x-for="(comment, index) in comments" :key="comment.id">
                                    <div class="flex items-start gap-2.5 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                        <a :href="'/artist/' + comment.user.slug" class="shrink-0 mt-0.5">
                                            <template x-if="comment.user.avatar_url">
                                                <img :src="comment.user.avatar_url" class="w-7 h-7 rounded-full object-cover">
                                            </template>
                                            <template x-if="!comment.user.avatar_url">
                                                <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-[10px] font-bold" x-text="comment.user.name.substring(0, 1)"></div>
                                            </template>
                                        </a>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1.5">
                                                    <a :href="'/artist/' + comment.user.slug" class="text-xs font-bold text-gray-900 dark:text-white hover:underline truncate" x-text="comment.user.name"></a>
                                                    <template x-if="comment.user.is_verified">
                                                        <span class="text-blue-500" title="Verificado">
                                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.25 14.5l-3.5-3.5 1.41-1.41 2.09 2.09 5.67-5.67 1.41 1.41-7.08 7.08z"/></svg>
                                                        </span>
                                                    </template>
                                                    <span class="text-[10px] text-gray-400" x-text="comment.created_at"></span>
                                                </div>

                                                <template x-if="comment.can_delete">
                                                    <button type="button"
                                                            @click="deleteComment(comment.id, index)"
                                                            class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity p-1"
                                                            title="Delete comment">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </template>
                                            </div>

                                            <p class="text-xs text-gray-700 dark:text-gray-300 mt-0.5 leading-relaxed whitespace-pre-line" x-text="comment.content"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Bottom Section: Recommended / Similar Artworks -->
            <div class="mt-14 pt-10 border-t border-gray-200 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-2">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">
                            Similar Artworks
                        </h2>
                    </div>

                    <a href="{{ route('home', ['category' => $artwork->category]) }}" class="inline-flex items-center text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        <span>Explore more</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                @if($similarArtworks->isNotEmpty())
                    <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 [column-fill:_balance]">
                        @foreach($similarArtworks as $similar)
                            <div class="break-inside-avoid mb-6">
                                <x-artwork-card :artwork="$similar" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center rounded-2xl bg-white dark:bg-gray-900 border border-dashed border-gray-200 dark:border-gray-800 p-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No similar artworks found at this time.</p>
                        <a href="{{ route('home') }}" class="mt-3 inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            Explore Main Gallery
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

