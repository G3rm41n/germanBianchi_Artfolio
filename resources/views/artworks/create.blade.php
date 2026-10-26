<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Publicar Nueva Obra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    
                    <form method="POST" action="{{ route('artworks.store') }}" x-data="{
                        url: '{{ old('external_url', '') }}',
                        title: '{{ old('title', '') }}',
                        category: '{{ old('category', '') }}',
                        loading: false,
                        preview: null,
                        errorMessage: '',

                        checkUrl() {
                            const trimmed = (this.url || '').trim();
                            if (!trimmed || !trimmed.startsWith('http')) {
                                this.preview = null;
                                this.errorMessage = '';
                                return;
                            }
                            this.loading = true;
                            this.errorMessage = '';

                            fetch('{{ route('artworks.preview') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ url: trimmed })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.loading = false;
                                if (data.success) {
                                    this.preview = data;
                                    if (data.category) {
                                        this.category = data.category;
                                    }
                                    if (data.title && (!this.title || this.title.trim() === '')) {
                                        this.title = data.title;
                                    }
                                } else {
                                    this.preview = null;
                                    this.errorMessage = data.message || 'No se pudo procesar la URL.';
                                }
                            })
                            .catch(() => {
                                this.loading = false;
                            });
                        }
                    }" x-init="if (url) checkUrl()">
                        @csrf

                        <!-- Título -->
                        <div class="mb-6">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Título de la Obra <span class="text-red-500">*</span></label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="title" x-model="title" required autofocus />
                            @error('title')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL Externa -->
                        <div class="mb-6">
                            <label for="external_url" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Enlace Multimedia (URL) <span class="text-red-500">*</span></label>
                            <input id="external_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="url" name="external_url" x-model="url" @input.debounce.600ms="checkUrl()" @paste="$nextTick(() => checkUrl())" placeholder="https://ar.pinterest.com/pin/... o https://youtube.com/..." required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <strong>Sitios permitidos:</strong> Pinterest, YouTube, Vimeo, Sketchfab, ArtStation, SoundCloud, Spotify, DeviantArt, Google Drive, Imgur.
                            </p>
                            @error('external_url')
                                <p class="text-red-500 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror

                            <!-- Feedback de Carga / Detección -->
                            <div x-show="loading" x-cloak class="mt-3 p-3.5 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600 flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-300 animate-pulse">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Analizando enlace y extrayendo imagen en alta resolución...</span>
                            </div>

                            <!-- Previsualización de Imagen Detectada -->
                            <div x-show="preview && preview.thumbnail_url" x-cloak class="mt-4 p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 shadow-sm transition-all">
                                <div class="flex items-start space-x-4">
                                    <div class="relative w-24 h-24 rounded-lg overflow-hidden flex-shrink-0 bg-gray-900 border border-indigo-200 dark:border-indigo-700 shadow-sm">
                                        <img :src="preview.thumbnail_url" alt="Previsualización" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-black/75 text-white" x-text="preview.provider"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300">
                                                <svg class="w-3.5 h-3.5 mr-1 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Imagen vinculada con éxito
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="
                                                preview.provider === 'pinterest' ? 'Máxima calidad (Originals)' : 
                                                (preview.provider === 'deviantart' ? 'Calidad HD (DeviantArt)' : 
                                                (preview.provider === 'sketchfab' ? 'Render 3D (Sketchfab)' : 
                                                (preview.provider === 'youtube' ? 'Video HD (YouTube)' : 
                                                (preview.provider === 'spotify' ? 'Audio Oficial (Spotify)' : 
                                                (preview.provider === 'google_docs' ? 'Documento (Google Docs)' : preview.provider)))))"></span>
                                        </div>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="preview.title || 'Recurso listo para publicar'"></p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-all truncate font-mono" x-text="preview.thumbnail_url"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Error de validación al vuelo -->
                            <div x-show="errorMessage" x-cloak class="mt-3 p-3 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/40 text-xs text-red-600 dark:text-red-400 flex items-center space-x-2">
                                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="errorMessage"></span>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción (Opcional)</label>
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Precio / Categoría / Disponibilidad -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="category" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Categoría <span class="text-red-500">*</span></label>
                                <select id="category" name="category" x-model="category" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="" disabled selected>Selecciona una categoría</option>
                                    <option value="image" {{ old('category') == 'image' ? 'selected' : '' }}>Ilustración / 2D</option>
                                    <option value="video" {{ old('category') == 'video' ? 'selected' : '' }}>Animación / Cine</option>
                                    <option value="3d" {{ old('category') == '3d' ? 'selected' : '' }}>Modelado 3D</option>
                                    <option value="audio" {{ old('category') == 'audio' ? 'selected' : '' }}>Música / Podcast</option>
                                    <option value="doc" {{ old('category') == 'doc' ? 'selected' : '' }}>Documento / Cómic</option>
                                </select>
                                @error('category')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price_hint" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Indicación de Precio</label>
                                <input id="price_hint" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="price_hint" value="{{ old('price_hint') }}" placeholder="Ej: $150 USD" />
                                @error('price_hint')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Estado</label>
                                <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Público (Galería)</option>
                                    <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Oculto (Privado)</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Colecciones -->
                        @if(isset($collections) && $collections->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-3">Añadir a Colecciones</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($collections as $collection)
                                        <label class="inline-flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <input type="checkbox" name="collections[]" value="{{ $collection->id }}" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                                {{ (is_array(old('collections')) && in_array($collection->id, old('collections'))) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $collection->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('artworks.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition mr-4">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Obra
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
