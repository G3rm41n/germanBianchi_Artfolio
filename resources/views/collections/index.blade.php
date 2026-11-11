<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Collections') }}
            </h2>
            <a href="{{ route('collections.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Collection
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($collections->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($collections as $collection)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-all flex flex-col justify-between"
                                     x-data="{ copied: false, shareUrl: '{{ route('profile.public', ['slug' => Auth::user()->slug, 'collection_id' => $collection->id]) }}' }">
                                    <div>
                                        <div class="flex justify-between items-start gap-2 mb-2">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate" title="{{ $collection->name }}">
                                                {{ $collection->name }}
                                            </h3>
                                            
                                            <!-- Visibility Badge -->
                                            @if($collection->visibility === 'public')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                    Public
                                                </span>
                                            @elseif($collection->visibility === 'unlisted')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800 shrink-0" title="Hidden from portfolio. Accessible via direct link only.">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                    Unlisted
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800 shrink-0" title="Private collection. Only you can view this.">
                                                    <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    Private
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                                {{ $collection->artworks_count }} {{ Str::plural('artwork', $collection->artworks_count) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                            {{ $collection->description ?: 'No description provided.' }}
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-600 text-xs">
                                        <!-- Copy direct link button (for public & unlisted) -->
                                        @if($collection->visibility !== 'private')
                                            <button type="button"
                                                    @click="navigator.clipboard.writeText(shareUrl); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="inline-flex items-center font-semibold text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition"
                                                    title="Copy link to this collection">
                                                <svg x-show="!copied" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                <svg x-show="copied" class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span x-text="copied ? 'Copied Link!' : 'Copy Link'"></span>
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-[11px] italic">Private link</span>
                                        @endif

                                        <div class="flex items-center space-x-3">
                                            <!-- Edit / Settings -->
                                            <a href="{{ route('collections.edit', $collection) }}"
                                               class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1"
                                               title="Edit name, description, and visibility settings">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Settings
                                            </a>

                                            <!-- Delete -->
                                            <form method="POST" action="{{ route('collections.destroy', $collection) }}" onsubmit="return confirm('Are you sure you want to delete this collection? Artworks will not be deleted.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600 dark:text-red-400 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $collections->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">No collections yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start by creating your first collection to organize your artworks.</p>
                            <div class="mt-6">
                                <a href="{{ route('collections.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    New Collection
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
