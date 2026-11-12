<div x-data="{
        isOpen: false,
        title: '',
        url: '',
        copied: false,

        open(detail) {
            this.title = detail.title || document.title;
            this.url = detail.url || window.location.href;
            this.copied = false;
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
            this.copied = false;
        },

        copy() {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(this.url).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2500);
                });
            }
        },

        shareWhatsapp() {
            const shareUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(this.title + ' ' + this.url);
            window.open(shareUrl, '_blank', 'noopener,noreferrer');
        },

        shareTwitter() {
            const shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(this.title) + '&url=' + encodeURIComponent(this.url);
            window.open(shareUrl, '_blank', 'noopener,noreferrer');
        },

        shareLinkedIn() {
            const shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(this.url);
            window.open(shareUrl, '_blank', 'noopener,noreferrer');
        }
    }"
    @open-share-modal.window="open($event.detail)"
    @keydown.escape.window="if (isOpen) close()"
    x-show="isOpen"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-labelledby="share-modal-title">

    <!-- Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity"
         @click="close()"></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative rounded-2xl bg-white dark:bg-gray-900 p-6 sm:p-7 shadow-2xl w-full max-w-md border border-gray-100 dark:border-gray-800"
             @click.stop>

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </div>
                    <div>
                        <h3 id="share-modal-title" class="text-base font-bold text-gray-900 dark:text-white">Share with Community</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Share direct link or post to social channels</p>
                    </div>
                </div>

                <button type="button" @click="close()" aria-label="Close share dialog" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Content -->
            <div class="py-5 space-y-5">
                <div class="text-left">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Direct URL</label>
                    <!-- Copy Link Bar -->
                    <div class="flex items-center gap-2 p-1.5 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <input type="text"
                               readonly
                               :value="url"
                               class="w-full text-xs bg-transparent border-0 text-gray-700 dark:text-gray-300 focus:ring-0 truncate px-2 select-all">
                        <button type="button"
                                @click="copy()"
                                :class="copied ? 'bg-emerald-600 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white'"
                                class="shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                            <template x-if="!copied">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </template>
                            <template x-if="copied">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </template>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>

                <!-- Quick Share Social Icons -->
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 block mb-2.5 text-center">Quick Share</span>
                    <div class="flex items-center justify-center gap-3">
                        <!-- WhatsApp -->
                        <button type="button" @click="shareWhatsapp()" class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 hover:scale-105 transition shadow-sm border border-emerald-200/60 dark:border-emerald-800" title="Share to WhatsApp">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 01-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
                        </button>

                        <!-- X / Twitter -->
                        <button type="button" @click="shareTwitter()" class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 hover:scale-105 transition shadow-sm border border-gray-200 dark:border-gray-700" title="Share to X">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </button>

                        <!-- LinkedIn -->
                        <button type="button" @click="shareLinkedIn()" class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 hover:scale-105 transition shadow-sm border border-blue-200/60 dark:border-blue-800" title="Share to LinkedIn">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.72a1.63 1.63 0 0 0-1.63 1.63c0 .9.73 1.63 1.63 1.63s1.63-.73 1.63-1.63c0-.9-.73-1.63-1.63-1.63z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

