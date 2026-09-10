<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Obra') }}: {{ $artwork->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    
                    <form method="POST" action="{{ route('artworks.update', $artwork) }}">
                        @csrf
                        @method('PUT')

                        <!-- Título -->
                        <div class="mb-6">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Título de la Obra <span class="text-red-500">*</span></label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="title" value="{{ old('title', $artwork->title) }}" required autofocus />
                            @error('title')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL Externa (Solo Lectura) -->
                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Enlace Multimedia (No editable)</label>
                            <input class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-500 cursor-not-allowed" type="text" value="{{ $artwork->external_url }}" disabled />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Para cambiar la URL multimedia, debes crear una nueva obra. 
                                Tipo actual: <span class="font-bold uppercase">{{ $artwork->provider }}</span> ({{ $artwork->category }}).
                            </p>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción (Opcional)</label>
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $artwork->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Precio / Disponibilidad -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="price_hint" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Indicación de Precio (Opcional)</label>
                                <input id="price_hint" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="price_hint" value="{{ old('price_hint', $artwork->price_hint) }}" placeholder="Ej: $150 USD o 'No a la venta'" />
                                @error('price_hint')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Estado de Publicación</label>
                                <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="public" {{ old('status', $artwork->status) == 'public' ? 'selected' : '' }}>Público (Visible en la Galería)</option>
                                    <option value="hidden" {{ old('status', $artwork->status) == 'hidden' ? 'selected' : '' }}>Oculto (Sólo visible para ti)</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <!-- Enlace a la previsualización directa al original (solo para que el usuario verifique si funciona el enlace externo) -->
                            <a href="{{ $artwork->external_url }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Probar Enlace Multimedia Original
                            </a>
                            
                            <div class="flex items-center">
                                <a href="{{ route('artworks.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition mr-4">
                                    Cancelar
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Actualizar Obra
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
