<button type="button" 
        x-data="{
            darkMode: document.documentElement.classList.contains('dark'),
            toggle() {
                this.darkMode = !this.darkMode;
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                }
            }
        }"
        @click="toggle()"
        {{ $attributes->merge(['class' => 'p-2 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none flex items-center justify-center']) }}
        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        aria-label="Toggle Theme Mode">
    <!-- Sun icon (shown in dark mode) -->
    <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    <!-- Moon icon (shown in light mode) -->
    <svg x-show="!darkMode" class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>
