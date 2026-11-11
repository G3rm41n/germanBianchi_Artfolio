@auth
<div x-data="{
    totalTimeoutSeconds: 10 * 60, // 10 minutes (600s)
    warningWindowSeconds: 2 * 60, // 2 minutes (120s)
    remainingSeconds: 10 * 60,
    showWarning: false,
    countdownSeconds: 120,
    intervalId: null,
    storageKey: 'artfolio_last_activity_ts',
    isExtending: false,

    init() {
        // Initialize or read shared last activity across tabs
        const existing = localStorage.getItem(this.storageKey);
        if (!existing || isNaN(parseInt(existing))) {
            this.recordActivity();
        }

        // Listen for user interactions to refresh timer silently (throttled)
        let lastThrottle = 0;
        const onUserActivity = () => {
            if (this.showWarning) return; // Freeze auto-extension while warning modal is on screen
            const now = Date.now();
            if (now - lastThrottle > 10000) { // every 10 seconds max
                lastThrottle = now;
                this.recordActivity();
            }
        };

        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(evt => {
            window.addEventListener(evt, onUserActivity, { passive: true });
        });

        // Listen to storage events from OTHER tabs for seamless multi-tab sync
        window.addEventListener('storage', (e) => {
            if (e.key === this.storageKey) {
                this.checkState();
            }
        });

        // Main check loop running every second
        this.intervalId = setInterval(() => {
            this.checkState();
        }, 1000);
    },

    recordActivity() {
        const now = Date.now();
        localStorage.setItem(this.storageKey, now.toString());
        this.remainingSeconds = this.totalTimeoutSeconds;
        this.showWarning = false;
    },

    checkState() {
        const lastActivity = parseInt(localStorage.getItem(this.storageKey) || Date.now());
        const elapsedSeconds = Math.floor((Date.now() - lastActivity) / 1000);
        this.remainingSeconds = Math.max(0, this.totalTimeoutSeconds - elapsedSeconds);

        if (this.remainingSeconds <= 0) {
            // Expired: force logout
            this.logoutExpired();
        } else if (this.remainingSeconds <= this.warningWindowSeconds) {
            // Within 2 minutes window: show interactive modal
            this.showWarning = true;
            this.countdownSeconds = this.remainingSeconds;
        } else {
            // Active window: hide modal
            this.showWarning = false;
        }
    },

    extendSession() {
        this.isExtending = true;
        fetch('{{ route('session.keepAlive') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(() => {
            this.isExtending = false;
            this.recordActivity();
        })
        .catch(() => {
            this.isExtending = false;
            this.recordActivity(); // Still reset client-side
        });
    },

    logoutNow() {
        clearInterval(this.intervalId);
        localStorage.removeItem(this.storageKey);
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('logout') }}';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    },

    logoutExpired() {
        clearInterval(this.intervalId);
        localStorage.removeItem(this.storageKey);
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('logout') }}';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
        form.appendChild(csrf);
        document.body.appendChild(form);
        // Add query parameter via hidden input or redirect
        window.location.href = '{{ route('login') }}?status=session-expired';
    },

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }
}"
x-cloak
x-show="showWarning"
class="fixed inset-0 z-[100] overflow-y-auto"
style="display: none;"
aria-labelledby="session-warning-title"
role="dialog"
aria-modal="true">

    <!-- Blurred dark backdrop -->
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-md transition-opacity"></div>

    <!-- Modal Box -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10">
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200 dark:border-gray-700">
            
            <!-- Header bar with timeout indicator -->
            <div class="p-6 bg-gradient-to-r from-amber-500 to-indigo-600 text-white relative">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-white/10 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold" id="session-warning-title">
                            Are you still there?
                        </h3>
                        <p class="text-xs text-amber-100">
                            Inactivity security notice
                        </p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    To protect your account and artwork data, your session will automatically expire due to 
                    <strong class="text-gray-900 dark:text-white">10 minutes of inactivity</strong>.
                </p>

                <!-- Timer countdown badge -->
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-xs font-semibold text-amber-800 dark:text-amber-300">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                        <span>Auto sign-out in:</span>
                    </div>
                    <div class="font-mono text-xl font-extrabold text-amber-600 dark:text-amber-400" x-text="formatTime(countdownSeconds)">
                        2:00
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-rose-500 h-full transition-all duration-1000"
                         :style="`width: ${(countdownSeconds / warningWindowSeconds) * 100}%`"></div>
                </div>

                <!-- Questions / Actions -->
                <div class="pt-2 flex flex-col sm:flex-row-reverse gap-2 sm:gap-3">
                    <button type="button" 
                            @click="extendSession()" 
                            :disabled="isExtending"
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition disabled:opacity-50">
                        <svg x-show="isExtending" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Keep me signed in (+10m)</span>
                    </button>
                    
                    <button type="button" 
                            @click="logoutNow()" 
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider rounded-xl transition">
                        Sign out now
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

