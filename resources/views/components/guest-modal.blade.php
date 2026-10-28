<div 
    x-data="{ 
        open: false,
        title: 'Join the Artfolio Community',
        message: 'To interact with artworks, save favorites, and explore creative portfolios, please log in or create a free account.',
        context: 'general',

        handleOpen(e) {
            const detail = (e && e.detail) ? e.detail : {};
            this.context = detail.context || 'general';
            
            if (detail.title) {
                this.title = detail.title;
            } else if (this.context === 'favorito') {
                this.title = 'Save Your Favorite Artworks';
            } else if (this.context === 'reporte') {
                this.title = 'Help Us Moderate Artfolio';
            } else {
                this.title = 'Join the Artfolio Community';
            }

            if (detail.message) {
                this.message = detail.message;
            } else if (this.context === 'favorito') {
                this.message = 'Create your private bookmarks and keep your favorite multimedia works at your fingertips with a single click.';
            } else if (this.context === 'reporte') {
                this.message = 'Log in to report a broken link or inappropriate content to help maintain community quality.';
            } else {
                this.message = 'To interact with artworks and organize private lists, log in or create your free account.';
            }

            this.open = true;
        }
    }" 
    @open-guest-modal.window="handleOpen($event)" 
    @keydown.escape.window="open = false"
    class="relative z-50" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    x-show="open"
    style="display: none;"
    x-cloak
>
    <!-- Background backdrop -->
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/75 transition-opacity backdrop-blur-sm"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal panel -->
            <div 
                x-show="open"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200 dark:border-gray-800"
            >
                <!-- Close Button -->
                <button @click="open = false" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="p-6">
                    <div class="sm:flex sm:items-start">
                        <!-- Context Icon -->
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl sm:mx-0 shadow-sm"
                             :class="{
                                 'bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400': context === 'favorito',
                                 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400': context === 'contacto' || context === 'general',
                                 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400': context === 'reporte'
                             }">
                            
                            <!-- Favorite Icon -->
                            <template x-if="context === 'favorito'">
                                <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </template>

                            <!-- Contact Icon -->
                            <template x-if="context === 'contacto'">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </template>

                            <!-- Report Icon -->
                            <template x-if="context === 'reporte'">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                </svg>
                            </template>

                            <!-- General Icon -->
                            <template x-if="context === 'general'">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            </template>
                        </div>

                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left min-w-0">
                            <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white" id="modal-title" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50/80 dark:bg-gray-800/50 px-6 py-4 sm:flex sm:flex-row-reverse sm:items-center gap-2.5 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="inline-flex w-full justify-center items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 active:bg-indigo-800 sm:w-auto transition-colors">
                        Log In
                    </a>
                    <a href="{{ route('register', ['redirect' => url()->current()]) }}" class="mt-2 sm:mt-0 inline-flex w-full justify-center items-center rounded-xl bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-800 dark:text-gray-200 shadow-sm border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 sm:w-auto transition-colors">
                        Sign Up Free
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
