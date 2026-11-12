<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Artworks') }}
            </h2>
            <a href="{{ route('artworks.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out shadow-sm">
                + Add New Artwork
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($artworks as $artwork)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition duration-300">
                        <!-- Thumbnail/Preview area -->
                        <a href="{{ route('artworks.show', $artwork) }}" class="block h-48 bg-gray-100 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 relative overflow-hidden group cursor-pointer" title="{{ __('View Artwork') }}: {{ $artwork->title }}">
                            @if($artwork->thumbnail_url)
                                <img src="{{ $artwork->thumbnail_url }}" alt="{{ $artwork->title }}" loading="lazy" decoding="async" {!! str_contains($artwork->thumbnail_url, 'pinimg.com') ? 'referrerpolicy="no-referrer"' : '' !!} class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    @if($artwork->category === 'video')
                                        <svg class="w-12 h-12 text-gray-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif($artwork->category === '3d')
                                        <svg class="w-12 h-12 text-gray-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    @elseif($artwork->category === 'audio')
                                        <svg class="w-12 h-12 text-gray-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                                    @else
                                        <svg class="w-12 h-12 text-gray-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                            @endif

                            @if($artwork->is_pinned)
                                <span class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded shadow z-10">
                                    📌 Pinned
                                </span>
                            @endif

                            <!-- Floating Quick View Pill Overlay on Hover -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center p-4 z-10">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white font-bold text-xs shadow-md transform translate-y-2 group-hover:translate-y-0 transition-transform duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ __('View Artwork') }}
                                </span>
                            </div>
                        </a>
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1 truncate pr-2">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="{{ $artwork->title }}">
                                        {{ $artwork->title }}
                                    </a>
                                </h3>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[11px] text-gray-400 flex items-center gap-1 font-mono mr-1" title="{{ $artwork->views_count }} views">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ $artwork->views_count }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $artwork->status === 'published' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                                        {{ $artwork->status === 'published' ? 'Public' : 'Hidden' }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-4 line-clamp-2 h-10">
                                {{ $artwork->description ?: 'No description' }}
                            </p>
                            
                            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <!-- Pin / Unpin Button -->
                                <form action="{{ route('artworks.toggle-pin', $artwork) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-xs font-semibold flex items-center transition-colors {{ $artwork->is_pinned ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-400 hover:text-amber-600' }}"
                                            title="{{ $artwork->is_pinned ? 'Unpin from Top of Portfolio' : 'Pin to Top of Portfolio (Max 3)' }}">
                                        <svg class="w-4 h-4 mr-1 {{ $artwork->is_pinned ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        {{ $artwork->is_pinned ? 'Pinned' : 'Pin' }}
                                    </button>
                                </form>

                                <div class="flex items-center gap-3">
                                    <a href="{{ route('artworks.edit', $artwork) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-semibold flex items-center transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </a>
                                    
                                    <form action="{{ route('artworks.destroy', $artwork) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                @click.prevent="$dispatch('open-confirm', {
                                                    title: 'Move Artwork to Trash',
                                                    message: 'Are you sure you want to move \'{{ addslashes($artwork->title) }}\' to the recycle bin? It will be retained for 30 days before permanent deletion.',
                                                    form: $el.closest('form'),
                                                    confirmText: 'Move to Trash',
                                                    isDanger: true
                                                })"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-semibold flex items-center transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Trash
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-12 text-center border border-dashed border-gray-300 dark:border-gray-700">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">No artworks published yet</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Start sharing your art by adding a link from YouTube, Sketchfab, ArtStation, Vimeo, or any supported provider.</p>
                        <div class="mt-6">
                            <a href="{{ route('artworks.create') }}" class="inline-flex items-center px-5 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Publish My First Artwork
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($artworks->hasPages())
                <div class="mt-8">
                    {{ $artworks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
