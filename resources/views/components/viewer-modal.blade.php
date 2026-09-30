<div x-data="{ 
        isOpen: false, 
        artwork: null,
        
        close() { 
            this.isOpen = false; 
            setTimeout(() => { this.artwork = null; }, 300); // Wait for transition
        }
    }" 
    @open-viewer.window="artwork = $event.detail; isOpen = true; document.body.style.overflow = 'hidden';"
    @keydown.escape.window="close(); document.body.style.overflow = 'auto';"
    class="relative z-50"
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    style="display: none;"
    x-show="isOpen"
>
    <!-- Background backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-sm transition-opacity"
         @click="close(); document.body.style.overflow = 'auto';">
    </div>

    <!-- Modal panel -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="isOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all w-full max-w-6xl max-h-[90vh] flex flex-col sm:flex-row border border-gray-200 dark:border-gray-700"
                 @click.stop>
                
                <!-- Contenedor del Iframe/Media (Lado Izquierdo) -->
                <div class="w-full sm:w-2/3 bg-black flex flex-col justify-center items-center relative aspect-video sm:aspect-auto sm:min-h-[600px] overflow-auto">
                    <template x-if="artwork">
                        <div class="w-full h-full relative flex justify-center items-center" id="media-container">

                            <!-- CASO 1: iframe (YouTube, Vimeo, Sketchfab, Pinterest oEmbed) -->
                            <template x-if="artwork.render_type === 'iframe' && artwork.embed_html">
                                <div class="w-full h-full flex justify-center items-center overflow-auto bg-gray-900">
                                    <div x-html="artwork.embed_html" style="line-height:0"></div>
                                </div>
                            </template>

                            <!-- CASO 2: imagen directa (i.pinimg.com, imgur, extensión de imagen) -->
                            <template x-if="artwork.render_type === 'image' && (artwork.external_url.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) || artwork.external_url.includes('i.pinimg.com') || artwork.external_url.includes('imgur.com'))">
                                <div class="w-full h-full flex justify-center items-center p-4 bg-gray-900">
                                    <img :src="artwork.external_url" :alt="artwork.title" x-bind:referrerpolicy="artwork.external_url.includes('pinimg.com') ? 'no-referrer' : ''" class="max-w-full max-h-full object-contain shadow-lg">
                                </div>
                            </template>

                            <!-- CASO 3: Pinterest página de pin con thumbnail del oEmbed -->
                            <template x-if="artwork.render_type === 'image' && artwork.thumbnail_url && !artwork.external_url.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) && !artwork.external_url.includes('i.pinimg.com')">
                                <div class="w-full h-full flex flex-col justify-center items-center p-4 bg-gray-900 relative">
                                    <img :src="artwork.thumbnail_url" :alt="artwork.title" referrerpolicy="no-referrer" class="max-w-full max-h-[90%] object-contain shadow-lg rounded">
                                    <a :href="artwork.external_url" target="_blank" class="absolute bottom-4 right-4 text-xs text-white bg-black bg-opacity-60 px-3 py-1 rounded-full hover:bg-opacity-90 transition">
                                        Ver en Pinterest ↗
                                    </a>
                                </div>
                            </template>

                            <!-- CASO 4: Fallback — sin embed ni imagen -->
                            <template x-if="!artwork.embed_html && !artwork.thumbnail_url && !(artwork.external_url.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) || artwork.external_url.includes('i.pinimg.com') || artwork.external_url.includes('imgur.com'))">
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-900 p-8 text-center">
                                    <svg class="w-20 h-20 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    <p class="text-gray-400 mb-4">Este recurso no puede incrustarse directamente.</p>
                                    <a :href="artwork.external_url" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Abrir en nueva pestaña
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Barra lateral de información (Lado Derecho) -->
                <div class="w-full sm:w-1/3 flex flex-col h-auto sm:h-auto overflow-y-auto bg-white dark:bg-gray-900 p-6 sm:p-8">
                    <!-- Botón Cerrar (Mobile top, Desktop right) -->
                    <button @click="close(); document.body.style.overflow = 'auto';" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors bg-white dark:bg-gray-800 rounded-full p-2 shadow-sm z-10 border border-gray-100 dark:border-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <template x-if="artwork">
                        <div class="mt-4 sm:mt-0 pt-4 flex flex-col h-full">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="artwork.title"></h2>
                                
                                @auth
                                    <button type="button" @click.stop="
                                        fetch('/obras/' + artwork.id + '/bookmark', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'Accept': 'application/json'
                                            }
                                        }).then(res => res.json()).then(data => {
                                            artwork.is_bookmarked = data.is_bookmarked;
                                            $dispatch('bookmark-updated', { id: artwork.id, status: data.is_bookmarked });
                                        })
                                    " class="ml-4 flex-shrink-0 transition-colors focus:outline-none" :class="artwork.is_bookmarked ? 'text-red-500 hover:text-red-600' : 'text-gray-300 dark:text-gray-600 hover:text-red-500'" title="Guardar a Favoritos">
                                        <svg class="w-8 h-8 transition-all" :class="artwork.is_bookmarked ? 'fill-current scale-110' : 'scale-100'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" @click.stop="$dispatch('open-guest-modal')" class="ml-4 flex-shrink-0 text-gray-300 dark:text-gray-600 hover:text-red-500 transition-colors focus:outline-none" title="Guardar a Favoritos">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                @endauth
                            </div>
                            
                            <div class="flex items-center mb-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                                <!-- Avatar -->
                                <template x-if="artwork.author_avatar">
                                    <div class="w-12 h-12 rounded-full overflow-hidden mr-4 relative bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                        <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                                             :style="`background-image: url('${artwork.author_avatar}'); transform: translate(${artwork.author_crop_x}%, ${artwork.author_crop_y}%) scale(${artwork.author_crop_zoom});`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!artwork.author_avatar">
                                    <div class="w-12 h-12 rounded-full mr-4 bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xl flex-shrink-0" x-text="artwork.author_name ? artwork.author_name.substring(0, 1) : ''"></div>
                                </template>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Publicado por</p>
                                    <a :href="artwork.author_url" class="text-lg font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" x-text="artwork.author_name"></a>
                                </div>
                            </div>
                            
                            <div class="flex-grow">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-300 uppercase tracking-wider mb-2">Descripción</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm whitespace-pre-line" x-text="artwork.description || 'Sin descripción.'"></p>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
                                <a :href="artwork.author_url" class="w-full flex justify-center items-center px-4 py-3 border border-indigo-600 rounded-md shadow-sm text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-transparent hover:bg-indigo-50 dark:hover:bg-gray-800 focus:outline-none transition-colors">
                                    Ver portafolio completo
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </div>
</div>
