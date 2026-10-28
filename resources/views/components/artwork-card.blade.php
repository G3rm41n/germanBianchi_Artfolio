@props(['artwork'])

@php
    $bookmarkedIds = once(function () {
        return auth()->check() 
            ? auth()->user()->bookmarks()->pluck('artwork_id')->flip() 
            : collect();
    });
    $isBookmarked = isset($bookmarkedIds[$artwork->id]);
@endphp

<div 
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group"
    x-data
    @click="$dispatch('open-viewer', {{ json_encode([
        'id' => $artwork->id,
        'title' => $artwork->title,
        'description' => $artwork->description,
        'author_name' => $artwork->user->name,
        'author_url' => route('profile.public', $artwork->user->slug),
        'author_avatar' => $artwork->user->avatar_url,
        'author_crop_x' => $artwork->user->crop_x ?? 0,
        'author_crop_y' => $artwork->user->crop_y ?? 0,
        'author_crop_zoom' => $artwork->user->crop_zoom ?? 1,
        'embed_html' => $artwork->embed_html,
        'thumbnail_url' => $artwork->preview_url ?: $artwork->thumbnail_url,
        'high_res_url' => $artwork->high_res_url ?: $artwork->external_url,
        'external_url' => $artwork->external_url,
        'render_type' => $artwork->render_type,
        'is_bookmarked' => $isBookmarked
    ]) }})"
>
    <!-- Thumbnail/Preview area -->
    <div class="h-56 bg-gray-100 dark:bg-gray-900 flex items-center justify-center border-b border-gray-100 dark:border-gray-800 relative overflow-hidden">
        @if($artwork->thumbnail_url)
            <img src="{{ $artwork->preview_url ?: $artwork->thumbnail_url }}" alt="{{ $artwork->title }}" referrerpolicy="no-referrer" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <!-- Placeholder icons based on category (since we don't have real thumbnails yet) -->
            @if($artwork->category === 'video')
                <svg class="w-16 h-16 text-gray-400 group-hover:scale-110 transition-transform duration-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @elseif($artwork->category === '3d')
                <svg class="w-16 h-16 text-gray-400 group-hover:scale-110 transition-transform duration-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            @elseif($artwork->category === 'audio')
                <svg class="w-16 h-16 text-gray-400 group-hover:scale-110 transition-transform duration-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
            @else
                <svg class="w-16 h-16 text-gray-400 group-hover:scale-110 transition-transform duration-500 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            @endif
        @endif
        
        <!-- Hover Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
            <span class="opacity-0 group-hover:opacity-100 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded-full font-semibold text-sm shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                View Artwork
            </span>
        </div>

        <!-- Provider Badge -->
        @php
            $providerBadgeClasses = match(strtolower($artwork->provider)) {
                'youtube' => 'bg-[#ff0000]/90 text-white',
                'spotify' => 'bg-[#1db954]/95 text-black font-black',
                'sketchfab' => 'bg-[#1caad9]/90 text-white',
                'deviantart' => 'bg-[#00e59b]/90 text-gray-950 font-bold',
                'pinterest' => 'bg-[#e60023]/90 text-white',
                'google_docs', 'google docs' => 'bg-[#4285f4]/90 text-white',
                'vimeo' => 'bg-[#1ab7ea]/90 text-white',
                'soundcloud' => 'bg-[#ff5500]/90 text-white',
                default => 'bg-black/60 text-white',
            };
        @endphp
        <span class="absolute top-3 right-3 backdrop-blur-sm text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full shadow-md {{ $providerBadgeClasses }}">
            {{ $artwork->provider }}
        </span>
    </div>
    
    <div class="p-5">
        <div class="flex justify-between items-start">
            <div class="w-10/12">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" title="{{ $artwork->title }}">
                    {{ $artwork->title }}
                </h3>
                
                <div class="flex items-center mt-2.5">
                    <x-user-avatar :user="$artwork->user" size="xs" class="mr-2" />
                    <a href="{{ route('profile.public', $artwork->user->slug) }}" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors truncate" @click.stop>
                        {{ $artwork->user->name }}
                    </a>
                </div>
            </div>
            
            <div class="w-2/12 flex justify-end">
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
                        " class="transition-colors focus:outline-none" :class="bookmarked ? 'text-red-500 hover:text-red-600' : 'text-gray-300 dark:text-gray-600 hover:text-red-500'" title="Save to Favorites">
                            <svg class="w-6 h-6 transition-all" :class="bookmarked ? 'fill-current scale-110' : 'scale-100'" :class="{'opacity-50': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                @else
                    <button type="button" @click.stop="$dispatch('open-guest-modal', { context: 'favorito' })" class="text-gray-300 dark:text-gray-600 hover:text-red-500 transition-colors focus:outline-none" title="Save to Favorites">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                @endauth
            </div>
        </div>
    </div>
</div>
