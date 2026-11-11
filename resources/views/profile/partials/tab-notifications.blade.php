<section>
    <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Notification Settings
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Control which platform notifications and activity updates you want to receive on Artfolio.
        </p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')
        <input type="hidden" name="active_tab" value="notifications">

        <div class="space-y-4">
            <!-- 1. Followed Artists Updates -->
            <div class="flex items-start justify-between p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60">
                <div class="pr-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            Followed Artists Uploads
                        </h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                        Get alerted when artists you follow publish new artworks or release updated project showcases.
                    </p>
                </div>

                <input type="hidden" name="notify_new_artworks" value="0">
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                    <input type="checkbox" name="notify_new_artworks" value="1" {{ old('notify_new_artworks', $user->notify_new_artworks) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <!-- 2. Messages & Commission Inquiries -->
            <div class="flex items-start justify-between p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60">
                <div class="pr-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            Direct Messages & Commission Inquiries
                        </h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                        Receive instant notifications when other users or studios reach out for commission requests or direct conversations.
                    </p>
                </div>

                <input type="hidden" name="notify_messages" value="0">
                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                    <input type="checkbox" name="notify_messages" value="1" {{ old('notify_messages', $user->notify_messages) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/60">
            <x-primary-button class="rounded-xl px-6 py-2.5">
                {{ __('Save Notification Preferences') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated' && session('active_tab') === 'notifications')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Preferences saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>

