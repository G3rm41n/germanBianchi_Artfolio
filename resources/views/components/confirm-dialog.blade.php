<div x-data="{
        isOpen: false,
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        isDanger: true,
        targetForm: null,

        open(detail) {
            this.title = detail.title || 'Confirm Action';
            this.message = detail.message || 'Are you sure you want to proceed?';
            this.confirmText = detail.confirmText || 'Confirm';
            this.cancelText = detail.cancelText || 'Cancel';
            this.isDanger = detail.isDanger !== undefined ? detail.isDanger : true;
            this.targetForm = detail.form || (detail.formId ? document.getElementById(detail.formId) : null);
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
            this.targetForm = null;
        },

        proceed() {
            if (this.targetForm) {
                this.targetForm.submit();
            }
            this.close();
        }
    }"
    @open-confirm.window="open($event.detail)"
    @keydown.escape.window="if (isOpen) close()"
    x-show="isOpen"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-dialog-title">

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

    <!-- Dialog Box -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-800"
             @click.stop>

            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                     :class="isDanger ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400' : 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400'">
                    <template x-if="isDanger">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </template>
                    <template x-if="!isDanger">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0">
                    <h3 id="confirm-dialog-title" class="text-base font-bold text-gray-900 dark:text-white" x-text="title"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed" x-text="message"></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="button"
                        @click="close()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <span x-text="cancelText"></span>
                </button>
                <button type="button"
                        @click="proceed()"
                        :class="isDanger ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white'"
                        class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

