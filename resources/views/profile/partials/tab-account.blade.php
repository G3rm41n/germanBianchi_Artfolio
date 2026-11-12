<section class="space-y-10">
    <div>
        <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Account & Content Preferences
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Configure your browsing filters, content censorship settings, and primary account credentials.
            </p>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
            @csrf
            @method('patch')
            <input type="hidden" name="active_tab" value="account">

            <!-- Primary Account Email -->
            <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    Primary Account Email
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    This email is used for sign-in, account security notifications, and system alerts.
                </p>

                <div>
                    <input type="email"
                           id="account_email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           class="block w-full sm:w-2/3 text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300">
                            <span>Your email address is unverified.</span>
                            <button form="send-verification-account" class="underline font-semibold ml-2 hover:text-amber-950 dark:hover:text-white">
                                Click here to resend the verification email.
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Browsing & QoL Preferences -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    Content Censorship & Browsing Filters
                </h3>

                <!-- Filter 1: Mature Content (+18) Three-State Selector -->
                <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60"
                     x-data="{ maturePref: '{{ old('mature_filter_preference', $user->mature_filter_preference ?? 'blur') }}' }">
                    <div class="mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                18+
                            </span>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                Mature & Sensitive Content Display Mode
                            </h4>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Choose how artworks containing adult, graphic, or sensitive artistic themes appear across your exploration feeds, searches, and artist portfolios.
                        </p>
                    </div>

                    <input type="hidden" name="mature_filter_preference" :value="maturePref">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Option 1: Blurred (Default / Recommended) -->
                        <button type="button"
                                @click="maturePref = 'blur'"
                                :class="maturePref === 'blur' ? 'border-indigo-500 ring-2 ring-indigo-500/20 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300'"
                                class="p-4 rounded-xl border text-left transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Blurred
                                </span>
                                <span x-show="maturePref === 'blur'" class="text-indigo-600 font-bold">✓</span>
                            </div>
                            <p class="text-[11px] text-gray-600 dark:text-gray-400">
                                Previews are blurred with a sensitive content warning. Click any card to reveal immediately.
                            </p>
                        </button>

                        <!-- Option 2: Hide (No mostrar) -->
                        <button type="button"
                                @click="maturePref = 'hide'"
                                :class="maturePref === 'hide' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/50 dark:bg-rose-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300'"
                                class="p-4 rounded-xl border text-left transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                    Do Not Show
                                </span>
                                <span x-show="maturePref === 'hide'" class="text-rose-600 font-bold">✓</span>
                            </div>
                            <p class="text-[11px] text-gray-600 dark:text-gray-400">
                                Completely filter out and hide all mature 18+ artworks from all feeds and searches.
                            </p>
                        </button>

                        <!-- Option 3: Show (Mostrar) -->
                        <button type="button"
                                @click="maturePref = 'show'"
                                :class="maturePref === 'show' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300'"
                                class="p-4 rounded-xl border text-left transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Always Show
                                </span>
                                <span x-show="maturePref === 'show'" class="text-emerald-600 font-bold">✓</span>
                            </div>
                            <p class="text-[11px] text-gray-600 dark:text-gray-400">
                                Display all mature artworks directly in full clarity without any blurring or warning screens.
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Filter 2: Hide AI-Generated Works -->
                <div class="flex items-start justify-between p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60">
                    <div class="pr-6">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200 dark:border-purple-900">
                                AI FILTER
                            </span>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                Do Not Show AI-Generated Works
                            </h4>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            Automatically filter out and hide artworks tagged as created with or assisted by generative AI from your exploration feeds and searches.
                        </p>
                    </div>

                    <input type="hidden" name="hide_ai_content" value="0">
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" name="hide_ai_content" value="1" {{ old('hide_ai_content', $user->hide_ai_content) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                <x-primary-button class="rounded-xl px-6 py-2.5">
                    {{ __('Save Account Settings') }}
                </x-primary-button>

                @if (session('status') === 'profile-updated' && session('active_tab') === 'account')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Preferences updated.') }}
                    </p>
                @endif
            </div>
        </form>

        <form id="send-verification-account" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    </div>

    <!-- Permanent Account Deletion -->
    <div class="pt-8 border-t border-gray-200 dark:border-gray-800">
        <div>
            <x-danger-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="rounded-xl px-5 py-2.5">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                    {{ __('Please enter your current account password to confirm that you want to permanently delete your Artfolio account.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="delete_password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="delete_password"
                        name="password"
                        type="password"
                        class="mt-1 block w-3/4 rounded-xl text-sm"
                        placeholder="{{ __('Current Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="rounded-xl">
                        {{ __('Confirm Deletion') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</section>

