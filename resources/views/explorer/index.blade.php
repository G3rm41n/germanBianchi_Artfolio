<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gray-900 text-white py-20 relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-1/2 -right-1/4 w-full h-full bg-gradient-to-b from-indigo-500 to-purple-600 opacity-20 transform rotate-12 filter blur-3xl"></div>
            <div class="absolute -bottom-1/2 -left-1/4 w-full h-full bg-gradient-to-t from-blue-500 to-teal-400 opacity-20 transform -rotate-12 filter blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-4">
                Discover incredible <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">artworks</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-300">
                Explore portfolios from talented creators around the world. Animation, 3D models, illustrations, and music in one place.
            </p>
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Search & Filters -->
            <div class="mb-10">
                <form action="{{ route('home') }}" method="GET" class="flex flex-col lg:flex-row gap-4 justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    
                    <div class="relative w-full lg:w-1/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search artworks or artists..." class="block w-full pl-10 pr-3 py-3 border border-gray-200 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        @if(isset($category) && $category !== '')
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                    </div>

                    <div class="w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0 scrollbar-hide">
                        <div class="flex space-x-2">
                            @php
                                $categories = [
                                    '' => 'All',
                                    'image' => '2D Digital',
                                    'video' => 'Animation',
                                    '3d' => '3D Models',
                                    'audio' => 'Music & Audio',
                                    'doc' => 'Documents'
                                ];
                            @endphp
                            
                            @foreach($categories as $val => $label)
                                <a href="{{ route('home', array_merge(request()->query(), ['category' => $val, 'page' => null])) }}" 
                                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors {{ (isset($category) && $category === $val) || (empty($category) && empty($val)) ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="hidden"></button>
                </form>
            </div>

            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    @if(!empty($search))
                        Results for "{{ $search }}"
                    @elseif(!empty($category))
                        Exploring {{ $categories[$category] ?? 'Category' }}
                    @else
                        Recent Artworks
                    @endif
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($artworks as $artwork)
                    <x-artwork-card :artwork="$artwork" />
                @empty
                    <div class="col-span-full py-20 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">The gallery is empty</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">No artworks found matching your criteria.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $artworks->links() }}
            </div>
        </div>
    </div>

    <!-- Visor Modal Adaptativo (Oculto por defecto) -->
    <x-viewer-modal />
</x-app-layout>
