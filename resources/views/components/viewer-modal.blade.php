<div x-data="{ 
        isOpen: false, 
        artwork: null,
        isReportOpen: false,
        reportReason: 'Broken or inaccessible link',
        reportDetails: '',
        reportLoading: false,
        reportSuccess: false,
        reportError: '',
        
        close() { 
            this.isOpen = false; 
            this.isReportOpen = false;
            this.reportSuccess = false;
            this.reportError = '';
            setTimeout(() => { this.artwork = null; }, 300); // Wait for transition
        },

        openReport() {
            this.isReportOpen = true;
            this.reportSuccess = false;
            this.reportError = '';
            this.reportReason = 'Broken or inaccessible link';
            this.reportDetails = '';
        },

        closeReport() {
            this.isReportOpen = false;
            this.reportError = '';
            this.reportSuccess = false;
        },

        submitReport() {
            if (!this.artwork) return;
            this.reportLoading = true;
            this.reportError = '';
            
            fetch('/obras/' + this.artwork.id + '/report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reason: this.reportReason,
                    details: this.reportDetails
                })
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Error processing report.');
                }
                return data;
            })
            .then((data) => {
                this.reportLoading = false;
                this.reportSuccess = true;
                setTimeout(() => {
                    this.isReportOpen = false;
                    this.reportSuccess = false;
                    this.reportDetails = '';
                }, 2200);
            })
            .catch((err) => {
                this.reportLoading = false;
                this.reportError = err.message;
            });
        },

        recordView() {
            if (!this.artwork || !this.artwork.id) return;
            fetch('/artworks/' + this.artwork.id + '/view', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.views_count !== undefined) {
                    this.artwork.views_count = data.views_count;
                }
            })
            .catch(() => {});
        },

        togglePin() {
            if (!this.artwork || !this.artwork.id) return;
            fetch('/artworks/' + this.artwork.id + '/toggle-pin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.artwork.is_pinned = data.is_pinned;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: data.message } }));
                } else if (data.message) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: data.message } }));
                }
            })
            .catch(() => {
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: 'Error updating pinned status.' } }));
            });
        }
    }" 
    @open-viewer.window="artwork = $event.detail; isOpen = true; document.body.style.overflow = 'hidden'; recordView();"
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

                            <!-- CASO 1: iframe (YouTube, Vimeo, Sketchfab, Spotify, Google Docs) -->
                            <template x-if="artwork.render_type === 'iframe' && artwork.embed_html">
                                <div class="w-full h-full flex flex-col justify-center items-center overflow-auto bg-gray-950 relative p-3 sm:p-5">
                                    <!-- Contenedor adaptativo según el tipo de recurso -->
                                    <div class="w-full h-full flex items-center justify-center" 
                                         :class="artwork.provider === 'spotify' ? 'max-w-xl' : (artwork.provider === 'youtube' ? 'w-full max-w-4xl aspect-video' : 'w-full h-full')">
                                        <div x-html="artwork.embed_html" class="w-full h-full flex items-center justify-center [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:rounded-xl" style="line-height:0"></div>
                                    </div>

                                    <!-- Attribution / External link -->
                                    <a :href="artwork.external_url" target="_blank" rel="noopener noreferrer" 
                                       class="absolute bottom-4 right-4 inline-flex items-center space-x-1.5 text-xs backdrop-blur-md px-3.5 py-1.5 rounded-full font-medium shadow-lg transition-all duration-300 border border-white/10 group z-10"
                                       :class="artwork.provider === 'youtube' ? 'bg-[#ff0000]/90 hover:bg-[#cc0000] text-white font-medium' : 
                                              (artwork.provider === 'spotify' ? 'bg-[#1DB954]/90 hover:bg-[#1ed760] text-black font-semibold' : 
                                              (artwork.provider === 'google_docs' ? 'bg-[#4285F4]/90 hover:bg-[#3367d6] text-white font-medium' : 
                                              (artwork.provider === 'sketchfab' ? 'bg-[#1caad9] hover:bg-[#1594be] text-white font-medium' : 'bg-black/70 hover:bg-indigo-600 text-white')))">
                                        <span x-text="artwork.provider === 'youtube' ? 'Watch on YouTube' : 
                                                     (artwork.provider === 'spotify' ? 'Listen on Spotify' : 
                                                     (artwork.provider === 'google_docs' ? 'Open in Google Docs' : 
                                                     (artwork.provider === 'sketchfab' ? 'View on Sketchfab' : 'Open Original')))"></span>
                                        <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </template>

                            <!-- CASO 2: imagen directa -->
                            <template x-if="artwork.render_type === 'image' && (artwork.external_url.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) || artwork.external_url.includes('i.pinimg.com') || artwork.external_url.includes('imgur.com'))">
                                <div class="w-full h-full flex justify-center items-center p-4 bg-gray-900">
                                    <img :src="artwork.high_res_url || artwork.external_url" :alt="artwork.title" x-bind:referrerpolicy="artwork.external_url.includes('pinimg.com') ? 'no-referrer' : ''" class="max-w-full max-h-full object-contain shadow-lg">
                                </div>
                            </template>

                            <!-- CASO 3: Pinterest / DeviantArt -->
                            <template x-if="artwork.render_type === 'image' && artwork.thumbnail_url && !artwork.external_url.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) && !artwork.external_url.includes('i.pinimg.com')">
                                <div class="w-full h-full flex flex-col justify-center items-center p-4 bg-gray-950 relative">
                                    <img :src="artwork.high_res_url || artwork.thumbnail_url" :alt="artwork.title" referrerpolicy="no-referrer" class="max-w-full max-h-full object-contain shadow-2xl rounded-lg">
                                    <a :href="artwork.external_url" target="_blank" rel="noopener noreferrer" 
                                       class="absolute bottom-4 right-4 inline-flex items-center space-x-1.5 text-xs backdrop-blur-md px-3.5 py-1.5 rounded-full font-medium shadow-lg transition-all duration-300 border border-white/10 group"
                                       :class="artwork.provider === 'deviantart' ? 'bg-[#00e59b]/90 hover:bg-[#00e59b] text-gray-950 font-semibold' : (artwork.provider === 'sketchfab' ? 'bg-[#1caad9] hover:bg-[#1594be] text-white font-medium' : 'bg-black/70 hover:bg-[#e60023] text-white')">
                                        <span x-text="artwork.provider === 'deviantart' ? 'View on DeviantArt' : (artwork.provider === 'sketchfab' ? 'View 3D Model on Sketchfab' : (artwork.provider === 'pinterest' ? 'View Pin on Pinterest' : 'View Original'))"></span>
                                        <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </template>

                            <!-- CASO 4: Fallback -->
                            <template x-if="!artwork.embed_html && !artwork.thumbnail_url && !(artwork.external_url.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) || artwork.external_url.includes('i.pinimg.com') || artwork.external_url.includes('imgur.com'))">
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-900 p-8 text-center">
                                    <svg class="w-20 h-20 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    <p class="text-gray-400 mb-4">This resource cannot be embedded directly.</p>
                                    <a :href="artwork.external_url" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Open in new tab
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Info Sidebar (Right Side) -->
                <div class="w-full sm:w-1/3 flex flex-col h-auto sm:h-auto overflow-y-auto bg-white dark:bg-gray-900 p-6 sm:p-8">
                    <!-- Close button -->
                    <button @click="close(); document.body.style.overflow = 'auto';" aria-label="Close media viewer" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors bg-white dark:bg-gray-800 rounded-full p-2 shadow-sm z-10 border border-gray-100 dark:border-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <template x-if="artwork">
                        <div class="mt-4 sm:mt-0 pt-4 flex flex-col h-full">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <!-- Badges: Category, Subcategory, AI, Views -->
                                    <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                        <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400" x-text="artwork.category"></span>
                                        <template x-if="artwork.subcategory">
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300" x-text="artwork.subcategory"></span>
                                        </template>
                                        <template x-if="artwork.is_ai_generated">
                                            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 flex items-center space-x-1" title="Created with AI assistance">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                <span>AI Generated</span>
                                            </span>
                                        </template>
                                        <template x-if="artwork.is_pinned">
                                            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 flex items-center space-x-1" title="Pinned Artwork">
                                                <span>📌 Pinned</span>
                                            </span>
                                        </template>
                                        <!-- Views Count -->
                                        <span class="text-[11px] font-mono px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center gap-1" title="Views">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span x-text="artwork.views_count || 0"></span>
                                        </span>

                                        <!-- License Badge (#14) -->
                                        <template x-if="artwork.license">
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60" x-text="
                                                artwork.license === 'cc0' ? 'Public Domain (CC0)' :
                                                (artwork.license === 'cc_by' ? 'CC BY' :
                                                (artwork.license === 'cc_by_sa' ? 'CC BY-SA' :
                                                (artwork.license === 'cc_by_nc' ? 'CC BY-NC' :
                                                (artwork.license === 'cc_by_nc_nd' ? 'CC BY-NC-ND' : 'All Rights Reserved'))))
                                            "></span>
                                        </template>
                                    </div>
                                    <h2 id="modal-title" class="text-2xl font-bold text-gray-900 dark:text-white" x-text="artwork.title"></h2>
                                </div>
                                
                                <div class="flex items-center space-x-1.5 ml-4 flex-shrink-0">
                                    <!-- Like / Appreciation Button (#9) -->
                                    @auth
                                        <button type="button"
                                                @click.stop="
                                                    fetch('/artworks/' + artwork.id + '/like', {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                            'Accept': 'application/json'
                                                        }
                                                    }).then(res => res.json()).then(data => {
                                                        artwork.is_liked = data.is_liked;
                                                        artwork.likes_count = data.likes_count;
                                                        window.dispatchEvent(new CustomEvent('artwork-like-updated', { detail: { id: artwork.id, is_liked: data.is_liked, likes_count: data.likes_count } }));
                                                    })
                                                "
                                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all focus:outline-none"
                                                :class="artwork.is_liked ? 'bg-pink-500 text-white shadow-sm hover:bg-pink-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:text-pink-500 dark:hover:text-pink-400'"
                                                title="Appreciate this artwork">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                            <span x-text="artwork.likes_count || 0"></span>
                                        </button>
                                    @else
                                        <button type="button"
                                                @click.stop="$dispatch('open-guest-modal', { context: 'aprecio' })"
                                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:text-pink-500 transition-colors focus:outline-none"
                                                title="Appreciate this artwork">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                            <span x-text="artwork.likes_count || 0"></span>
                                        </button>
                                    @endauth

                                    <!-- Share Artwork Button -->
                                    <button type="button" 
                                            @click.stop="$dispatch('open-share-modal', { title: artwork.title + ' by ' + artwork.author_name, url: artwork.author_url })" 
                                            class="p-2 rounded-full text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none" 
                                            title="Share Artwork" 
                                            aria-label="Share Artwork">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                    </button>

                                    <!-- Pin/Unpin (Only if owner) -->
                                    <template x-if="artwork.is_owner">
                                        <button type="button" 
                                                @click.stop="togglePin()" 
                                                :class="artwork.is_pinned ? 'text-amber-500 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100' : 'text-gray-400 hover:text-amber-500 hover:bg-gray-100 dark:hover:bg-gray-800'" 
                                                class="p-2 rounded-full transition-colors focus:outline-none" 
                                                :title="artwork.is_pinned ? 'Unpin from Top of Portfolio' : 'Pin to Top of Portfolio'" 
                                                aria-label="Pin artwork">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/>
                                            </svg>
                                        </button>
                                    </template>

                                    <!-- Add to Collection Picker Button -->
                                    <x-collection-picker artwork-id-expr="artwork ? artwork.id : null" size="lg" placement="bottom" align="modal" />

                                    <!-- Report Artwork Button -->
                                    @auth
                                        <button type="button" @click.stop="openReport()" aria-label="Report Artwork" class="p-2 rounded-full text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none" title="Report Artwork">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" @click.stop="$dispatch('open-guest-modal', { context: 'reporte' })" aria-label="Report Artwork" class="p-2 rounded-full text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none" title="Report Artwork">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                            </svg>
                                        </button>
                                    @endauth

                                    <!-- Favorite Button -->
                                    @auth
                                        <button type="button" :aria-label="artwork && artwork.is_bookmarked ? 'Remove from favorites' : 'Save to favorites'" @click.stop="
                                            fetch('/obras/' + artwork.id + '/bookmark', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json'
                                                }
                                            }).then(res => res.json()).then(data => {
                                                artwork.is_bookmarked = data.is_bookmarked;
                                                window.dispatchEvent(new CustomEvent('bookmark-updated', { detail: { id: artwork.id, status: data.is_bookmarked } }));
                                            })
                                        " class="p-1 transition-colors focus:outline-none" :class="artwork.is_bookmarked ? 'text-red-500 hover:text-red-600' : 'text-gray-300 dark:text-gray-600 hover:text-red-500'" title="Save to Favorites">
                                            <svg class="w-8 h-8 transition-all" :class="artwork.is_bookmarked ? 'fill-current scale-110' : 'scale-100'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" aria-label="Save to favorites" @click.stop="$dispatch('open-guest-modal', { context: 'favorito' })" class="p-1 text-gray-300 dark:text-gray-600 hover:text-red-500 transition-colors focus:outline-none" title="Save to Favorites">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    @endauth
                                </div>
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
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Published by</p>
                                    <div class="flex items-center gap-1.5">
                                        <a :href="artwork.author_url" class="text-lg font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" x-text="artwork.author_name"></a>
                                        <template x-if="artwork.author_is_verified">
                                            <span class="inline-flex items-center text-blue-500 shrink-0" title="Verified Artist" aria-label="Verified Artist">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.25 14.5l-3.5-3.5 1.41-1.41 2.09 2.09 5.67-5.67 1.41 1.41-7.08 7.08z"/></svg>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex-grow">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-300 uppercase tracking-wider mb-2">Description</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm whitespace-pre-line" x-text="artwork.description || 'No description provided.'"></p>

                                <!-- Tags / Hashtags List -->
                                <template x-if="artwork.tags && artwork.tags.length">
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                                        <h5 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Tags</h5>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="tag in artwork.tags" :key="tag.slug">
                                                <a :href="'/?tag=' + tag.slug" 
                                                   class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 font-medium transition" 
                                                   x-text="'#' + tag.name"></a>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Software / Tools Used List (#10) -->
                                <template x-if="artwork.software_used && artwork.software_used.length">
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                                        <h5 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Software Used</h5>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="tool in artwork.software_used" :key="tool">
                                                <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60 font-semibold"
                                                      x-text="tool"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
                                <a :href="artwork.author_url" class="w-full flex justify-center items-center px-4 py-3 border border-indigo-600 rounded-md shadow-sm text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-transparent hover:bg-indigo-50 dark:hover:bg-gray-800 focus:outline-none transition-colors">
                                    View Full Portfolio
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Artwork Report Sub-modal -->
                <div x-show="isReportOpen" 
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 z-30 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4"
                     style="display: none;"
                     @click.stop>
                    
                    <div @click.stop 
                         role="dialog"
                         aria-modal="true"
                         aria-labelledby="report-modal-title"
                         class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 overflow-hidden transform transition-all">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-2">
                                <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                    </svg>
                                </div>
                                <h3 id="report-modal-title" class="text-lg font-bold text-gray-900 dark:text-white">Report Artwork</h3>
                            </div>
                            <button @click="closeReport()" type="button" aria-label="Close report dialog" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Success state -->
                        <template x-if="reportSuccess">
                            <div class="py-8 text-center">
                                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Report Submitted!</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Thank you for your feedback. Our moderation team will review this artwork shortly.</p>
                            </div>
                        </template>

                        <!-- Report form -->
                        <template x-if="!reportSuccess">
                            <form @submit.prevent="submitReport()" class="mt-4 space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800 text-xs text-gray-600 dark:text-gray-300">
                                    <span class="font-semibold text-gray-700 dark:text-gray-200">Artwork:</span> <span class="font-medium" x-text="artwork ? artwork.title : ''"></span>
                                </div>

                                <div>
                                    <label for="report_reason" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                        Reason for Report <span class="text-red-500">*</span>
                                    </label>
                                    <select id="report_reason" x-model="reportReason" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 dark:focus:ring-amber-900/40">
                                        <option value="Broken or inaccessible link">Broken or inaccessible link (Recommended)</option>
                                        <option value="Inappropriate or explicit content">Inappropriate or explicit content</option>
                                        <option value="Copyright infringement">Copyright infringement</option>
                                        <option value="Spam or misleading content">Spam or misleading content</option>
                                        <option value="Other reason">Other reason</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="report_details" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                        Additional Details <span class="text-gray-400 font-normal lowercase">(optional)</span>
                                    </label>
                                    <textarea id="report_details" x-model="reportDetails" rows="3" maxlength="1000" placeholder="Briefly describe the issue..." class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 dark:focus:ring-amber-900/40 placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                                </div>

                                <!-- Error alert -->
                                <div x-show="reportError" class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-xs text-red-600 dark:text-red-400" x-text="reportError"></div>

                                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <button type="button" @click="closeReport()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" :disabled="reportLoading" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <template x-if="reportLoading">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="reportLoading ? 'Submitting...' : 'Submit Report'"></span>
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
