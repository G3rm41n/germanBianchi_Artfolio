<x-app-layout>
    <div>
        <!-- Artist Profile Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center md:space-x-8">
                    <!-- Avatar -->
                    <x-user-avatar :user="$user" size="2xl" class="border-4 border-white dark:border-gray-900 shadow-xl mb-6 md:mb-0 mx-auto md:mx-0" />
                    
                    <!-- Info -->
                    <div class="text-center md:text-left flex-1">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </h1>

                            <!-- Actions -->
                            <div class="flex justify-center md:justify-end">
                                @if(auth()->check() && auth()->id() === $user->id)
                                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit My Profile
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto md:mx-0 mb-4 whitespace-pre-line text-sm leading-relaxed">
                            {{ $user->bio ?: 'This artist prefers to let their artwork speak for itself.' }}
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-xs font-semibold">
                            @if($user->commission_status === 'open')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                    Commissions Open
                                </span>
                            @endif
                            
                            @if($user->price_guide)
                                <span class="text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/60 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                                    Rates: {{ $user->price_guide }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artist's Gallery Section -->
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white shrink-0">Portfolio</h2>
                    
                    @if(isset($collections) && $collections->count() > 0)
                        <!-- Collection Tabs -->
                        <div class="flex-1 overflow-x-auto pb-2 -mb-2 no-scrollbar">
                            <nav class="flex space-x-2 md:justify-end min-w-max" aria-label="Tabs">
                                <a href="{{ route('profile.public', $user->slug) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ !$selectedCollectionId ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">
                                    All
                                </a>
                                @foreach($collections as $collection)
                                    <a href="{{ route('profile.public', ['slug' => $user->slug, 'collection_id' => $collection->id]) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ $selectedCollectionId == $collection->id ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">
                                        {{ $collection->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $artworks->total() }} published {{ Str::plural('artwork', $artworks->total()) }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($artworks as $artwork)
                        <x-artwork-card :artwork="$artwork" />
                    @empty
                        <div class="col-span-full py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 shadow-sm">
                            <p class="text-gray-500 dark:text-gray-400">This artist has not published any artworks in this section yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $artworks->links() }}
                </div>
            </div>
        </div>

    </div>

    <!-- Visor Modal Adaptativo -->
    <x-viewer-modal />
</x-app-layout>
