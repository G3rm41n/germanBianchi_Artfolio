<x-app-layout>
    <!-- Artist Profile Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-8">
                <!-- Avatar -->
                <div class="w-32 h-32 md:w-40 md:h-40 flex-shrink-0 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-full shadow-lg border-4 border-white dark:border-gray-900 flex items-center justify-center text-white text-4xl font-bold mb-6 md:mb-0 mx-auto md:mx-0">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
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
            
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Portafolio</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $artworks->total() }} obras publicadas</span>
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
