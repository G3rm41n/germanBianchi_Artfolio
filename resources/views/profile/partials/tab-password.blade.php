<section>
    <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Password & Security
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Ensure your account is using a strong, unique password to protect your creative assets and account settings.
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
        @csrf
        @method('put')
        <input type="hidden" name="active_tab" value="password">

        <div>
            <label for="update_password_current_password" class="block text-sm font-bold text-gray-900 dark:text-white">
                {{ __('Current Password') }}
            </label>
            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   autocomplete="current-password"
                   required
                   class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-bold text-gray-900 dark:text-white">
                {{ __('New Password') }}
            </label>
            <input id="update_password_password"
                   name="password"
                   type="password"
                   autocomplete="new-password"
                   required
                   class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-gray-900 dark:text-white">
                {{ __('Confirm New Password') }}
            </label>
            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   autocomplete="new-password"
                   required
                   class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/60">
            <x-primary-button class="rounded-xl px-6 py-2.5">
                {{ __('Update Password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Password updated successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>

