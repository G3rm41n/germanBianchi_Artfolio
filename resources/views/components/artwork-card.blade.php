@props(['artwork'])

<div 
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group"
    x-data
    @click="$dispatch('open-viewer', {{ json_encode([
        'id' => $artwork->id,
        'title' => $artwork->title,
        'description' => $artwork->description,
        'author_name' => $artwork->user->name,
        'author_url' => route('profile.public', $artwork->user->slug),
        'embed_html' => $artwork->embed_html,
        'thumbnail_url' => $artwork->thumbnail_url,
        'external_url' => $artwork->external_url,
        'render_type' => $artwork->render_type
    ]) }})"
>
    <!-- Thumbnail/Preview area -->
    <div class="h-56 bg-gray-100 dark:bg-gray-900 flex items-center justify-center border-b border-gray-100 dark:border-gray-800 relative overflow-hidden">
        @if($artwork->thumbnail_url)
            <img src="{{ $artwork->thumbnail_url }}" alt="{{ $artwork->title }}" {!! str_contains($artwork->thumbnail_url, 'pinimg.com') ? 'referrerpolicy="no-referrer"' : '' !!} class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
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
        
        <!-- Overlay de Hover -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
            <span class="opacity-0 group-hover:opacity-100 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2 rounded-full font-semibold text-sm shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                Ver Obra
            </span>
        </div>

        <!-- Etiqueta de proveedor -->
        <span class="absolute top-3 right-3 bg-black bg-opacity-60 backdrop-blur-sm text-white text-[10px] uppercase font-bold tracking-wider px-2 py-1 rounded shadow-sm">
            {{ $artwork->provider }}
        </span>
    </div>
    
    <div class="p-5">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" title="{{ $artwork->title }}">
            {{ $artwork->title }}
        </h3>
        
        <div class="flex items-center mt-2">
            <!-- Mini avatar placeholder -->
            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex-shrink-0"></div>
            <a href="{{ route('profile.public', $artwork->user->slug) }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors" @click.stop>
                {{ $artwork->user->name }}
            </a>
        </div>
    </div>
</div>
