<div x-data="{
    open: false,
    items: [],
    currentIndex: 0,
    isPlaying: false,
    autoPlayTimer: null,
    intervalMs: 5000,
    progress: 0,
    progressTimer: null,
    isFullscreen: false,

    init() {
        window.addEventListener('open-slideshow', (event) => {
            if (event.detail && event.detail.items && event.detail.items.length > 0) {
                this.items = event.detail.items;
                this.currentIndex = event.detail.startIndex || 0;
                this.open = true;
                this.isPlaying = false;
                this.progress = 0;
                document.body.style.overflow = 'hidden';
            }
        });
    },

    get currentItem() {
        return this.items[this.currentIndex] || null;
    },

    next() {
        if (this.items.length === 0) return;
        this.currentIndex = (this.currentIndex + 1) % this.items.length;
        this.resetProgress();
    },

    prev() {
        if (this.items.length === 0) return;
        this.currentIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
        this.resetProgress();
    },

    togglePlay() {
        this.isPlaying = !this.isPlaying;
        if (this.isPlaying) {
            this.startAutoPlay();
        } else {
            this.stopAutoPlay();
        }
    },

    startAutoPlay() {
        this.resetProgress();
        clearInterval(this.autoPlayTimer);
        clearInterval(this.progressTimer);

        const step = 50;
        this.progressTimer = setInterval(() => {
            this.progress += (step / this.intervalMs) * 100;
            if (this.progress >= 100) {
                this.next();
                this.progress = 0;
            }
        }, step);
    },

    stopAutoPlay() {
        clearInterval(this.autoPlayTimer);
        clearInterval(this.progressTimer);
        this.progress = 0;
    },

    resetProgress() {
        this.progress = 0;
    },

    toggleFullscreen() {
        const elem = this.$refs.slideshowContainer;
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            }
            this.isFullscreen = true;
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            this.isFullscreen = false;
        }
    },

    close() {
        this.stopAutoPlay();
        if (document.fullscreenElement && document.exitFullscreen) {
            document.exitFullscreen().catch(() => {});
        }
        this.isFullscreen = false;
        this.open = false;
        document.body.style.overflow = '';
    }
}"
x-show="open"
x-cloak
@keydown.window.escape="close()"
@keydown.window.arrow-left="if (open) prev()"
@keydown.window.arrow-right="if (open) next()"
@keydown.window.space.prevent="if (open) togglePlay()"
class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 select-none"
style="display: none;">

    <div x-ref="slideshowContainer" class="relative w-full h-full flex flex-col justify-between bg-black text-white">
        <!-- Top Toolbar -->
        <div class="relative z-20 flex items-center justify-between px-6 py-4 bg-gradient-to-b from-black/80 to-transparent">
            <!-- Slide Counter & Controls -->
            <div class="flex items-center gap-4">
                <span class="text-xs font-mono font-bold tracking-wider px-2.5 py-1 rounded-md bg-white/10 backdrop-blur-md">
                    <span x-text="currentIndex + 1"></span> / <span x-text="items.length"></span>
                </span>

                <!-- Play / Pause Button -->
                <button @click="togglePlay()"
                        type="button"
                        class="flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-lg transition-colors"
                        :class="isPlaying ? 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30' : 'bg-white/10 text-white hover:bg-white/20'">
                    <template x-if="isPlaying">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/></svg>
                    </template>
                    <template x-if="!isPlaying">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </template>
                    <span x-text="isPlaying ? 'Pause' : 'Play Exhibition'"></span>
                </button>
            </div>

            <!-- Right Controls: Fullscreen & Close -->
            <div class="flex items-center gap-2">
                <button @click="toggleFullscreen()"
                        type="button"
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-gray-200 hover:text-white transition"
                        title="Toggle Fullscreen">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                </button>
                <button @click="close()"
                        type="button"
                        class="p-2 rounded-xl bg-white/10 hover:bg-red-500/30 text-gray-200 hover:text-red-300 transition"
                        title="Close Exhibition (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Progress Bar (Auto-Play) -->
        <div x-show="isPlaying" class="absolute top-0 left-0 w-full h-1 bg-white/10 z-30">
            <div class="h-full bg-indigo-500 transition-all duration-75" :style="`width: ${progress}%`"></div>
        </div>

        <!-- Center Slide Display -->
        <div class="relative flex-1 flex items-center justify-center p-4 sm:p-8 overflow-hidden">
            <!-- Navigation Arrow Left -->
            <button @click="prev()"
                    type="button"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-black/40 hover:bg-white/20 text-white backdrop-blur-md transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <!-- Media Content -->
            <template x-if="currentItem">
                <div class="max-w-full max-h-full flex items-center justify-center transition-all duration-300">
                    <template x-if="currentItem.render_type === 'image'">
                        <img :src="currentItem.high_res_url || currentItem.preview_url"
                             :alt="currentItem.title"
                             class="max-w-full max-h-[82vh] object-contain rounded-lg shadow-2xl">
                    </template>
                    <template x-if="currentItem.render_type === 'iframe'">
                        <div class="w-[90vw] max-w-5xl h-[70vh] rounded-xl overflow-hidden shadow-2xl bg-gray-900" x-html="currentItem.embed_html"></div>
                    </template>
                </div>
            </template>

            <!-- Navigation Arrow Right -->
            <button @click="next()"
                    type="button"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-black/40 hover:bg-white/20 text-white backdrop-blur-md transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <!-- Bottom Details Caption -->
        <div class="relative z-20 px-8 py-5 bg-gradient-to-t from-black/90 via-black/70 to-transparent flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <template x-if="currentItem">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-white tracking-wide truncate max-w-2xl" x-text="currentItem.title"></h3>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-300">
                        <span class="px-2 py-0.5 rounded bg-white/10 uppercase text-[10px] font-bold" x-text="currentItem.provider"></span>
                        <span class="px-2 py-0.5 rounded bg-white/10 uppercase text-[10px] font-bold" x-text="currentItem.category"></span>

                        <!-- License Badge -->
                        <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-semibold text-[10px]" x-text="
                            currentItem.license === 'cc0' ? 'Public Domain (CC0)' :
                            (currentItem.license === 'cc_by' ? 'Creative Commons BY' :
                            (currentItem.license === 'cc_by_sa' ? 'CC BY-SA' :
                            (currentItem.license === 'cc_by_nc' ? 'CC BY-NC' :
                            (currentItem.license === 'cc_by_nc_nd' ? 'CC BY-NC-ND' : 'All Rights Reserved'))))
                        "></span>

                        <!-- Likes -->
                        <span class="inline-flex items-center gap-1 text-pink-400 font-semibold">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            <span x-text="currentItem.likes_count || 0"></span>
                        </span>
                    </div>

                    <!-- Software Badges -->
                    <template x-if="currentItem.software_used && currentItem.software_used.length > 0">
                        <div class="flex flex-wrap items-center gap-1 pt-1">
                            <template x-for="tool in currentItem.software_used" :key="tool">
                                <span class="px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 text-[10px] font-semibold" x-text="tool"></span>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Instructions -->
            <div class="hidden md:flex items-center gap-3 text-[11px] text-gray-400 font-mono">
                <span>[◄ / ►] Nav</span>
                <span>[Space] Auto</span>
                <span>[Esc] Close</span>
            </div>
        </div>
    </div>
</div>

