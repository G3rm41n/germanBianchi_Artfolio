<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mis Colecciones') }}
            </h2>
            <a href="{{ route('collections.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Colección
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
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate" title="{{ $collection->name }}">
                                            {{ $collection->name }}
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                            {{ $collection->artworks_count }} {{ Str::plural('obra', $collection->artworks_count) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 h-10">
                                        {{ $collection->description ?: 'Sin descripción.' }}
                                    </p>
                                    
                                    <div class="flex justify-end space-x-2 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <a href="{{ route('collections.edit', $collection) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('collections.destroy', $collection) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta colección? Las obras no se borrarán.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                Eliminar
                                            </button>
                                        </form>
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
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">Sin colecciones</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando tu primera colección para agrupar tus obras.</p>
                            <div class="mt-6">
                                <a href="{{ route('collections.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    Nueva Colección
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
