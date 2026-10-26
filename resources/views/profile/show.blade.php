<x-app-layout>
    <!-- Artist Profile Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-8">
                <!-- Avatar -->
                @if($user->avatar_url)
                    <div class="w-32 h-32 md:w-40 md:h-40 flex-shrink-0 relative rounded-full shadow-lg border-4 border-white dark:border-gray-900 mb-6 md:mb-0 mx-auto md:mx-0 overflow-hidden bg-gray-200 dark:bg-gray-700">
                        <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                             style="background-image: url('{{ $user->avatar_url }}'); transform: translate({{ $user->crop_x ?? 0 }}%, {{ $user->crop_y ?? 0 }}%) scale({{ $user->crop_zoom ?? 1 }});">
                        </div>
                    </div>
                @else
                    <div class="w-32 h-32 md:w-40 md:h-40 flex-shrink-0 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-full shadow-lg border-4 border-white dark:border-gray-900 flex items-center justify-center text-white text-4xl font-bold mb-6 md:mb-0 mx-auto md:mx-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                
                <!-- Info -->
                <div class="text-center md:text-left flex-1">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-2">
                        {{ $user->name }}
                    </h1>
                    
                    <div class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto md:mx-0 mb-4 whitespace-pre-line">
                        {{ $user->bio ?: 'Este artista prefiere que su obra hable por sí misma.' }}
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm font-medium">
                        @if($user->commission_status === 'open')
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Comisiones Abiertas
                            </span>
                        @endif
                        
                        @if($user->price_guide)
                            <span class="text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                                {{ $user->price_guide }}
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
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white shrink-0">Portafolio</h2>
                
                @if(isset($collections) && $collections->count() > 0)
                    <!-- Tabs de Colecciones -->
                    <div class="flex-1 overflow-x-auto pb-2 -mb-2 no-scrollbar">
                        <nav class="flex space-x-2 md:justify-end min-w-max" aria-label="Tabs">
                            <a href="{{ route('profile.public', $user->slug) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ !$selectedCollectionId ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Todas
                            </a>
                            @foreach($collections as $collection)
                                <a href="{{ route('profile.public', ['slug' => $user->slug, 'collection_id' => $collection->id]) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ $selectedCollectionId == $collection->id ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                    {{ $collection->name }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                @else
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $artworks->total() }} obras publicadas</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($artworks as $artwork)
                    <x-artwork-card :artwork="$artwork" />
                @empty
                    <div class="col-span-full py-16 text-center bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-gray-500 dark:text-gray-400">Este artista aún no ha publicado obras en su portafolio.</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div class="mt-10">
                {{ $artworks->links() }}
            </div>
        </div>
    </div>

    <!-- Visor Modal Adaptativo -->
    <x-viewer-modal />
</x-app-layout>
