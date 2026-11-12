@php
    $alerts = [];
    if (session('success')) {
        $alerts[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $alerts[] = ['type' => 'error', 'message' => session('error')];
    }
    if (session('warning')) {
        $alerts[] = ['type' => 'warning', 'message' => session('warning')];
    }
    if (session('info')) {
        $alerts[] = ['type' => 'info', 'message' => session('info')];
    }
    if (session('status') && session('status') !== 'profile-updated') {
        $alerts[] = ['type' => 'info', 'message' => session('status')];
    }
@endphp

@if(!empty($alerts))
    <div class="fixed top-20 right-4 z-50 flex flex-col gap-3 max-w-sm sm:max-w-md w-full pointer-events-none select-none"
         role="status"
         aria-live="polite">
        @foreach($alerts as $alert)
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 4500)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="pointer-events-auto rounded-2xl p-4 shadow-xl border backdrop-blur-md flex items-start gap-3 transition-all
                    @if($alert['type'] === 'success') bg-emerald-50/95 dark:bg-emerald-950/90 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-100
                    @elseif($alert['type'] === 'error') bg-rose-50/95 dark:bg-rose-950/90 border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-100
                    @elseif($alert['type'] === 'warning') bg-amber-50/95 dark:bg-amber-950/90 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-100
                    @else bg-indigo-50/95 dark:bg-indigo-950/90 border-indigo-200 dark:border-indigo-800 text-indigo-900 dark:text-indigo-100
                    @endif">

                <!-- Icon -->
                <div class="shrink-0 mt-0.5">
                    @if($alert['type'] === 'success')
                        <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    @elseif($alert['type'] === 'error')
                        <div class="w-7 h-7 rounded-full bg-rose-100 dark:bg-rose-900/60 flex items-center justify-center text-rose-600 dark:text-rose-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                    @elseif($alert['type'] === 'warning')
                        <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    @else
                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    @endif
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pr-1">
                    <p class="text-xs font-semibold leading-relaxed">
                        {{ $alert['message'] }}
                    </p>
                </div>

                <!-- Close Button -->
                <button type="button"
                        @click="show = false"
                        class="shrink-0 p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                        aria-label="Cerrar notificación">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endforeach
    </div>
@endif

