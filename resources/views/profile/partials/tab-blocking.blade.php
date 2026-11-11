<section>
    <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            User Blocking & Moderation
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Block users to prevent them from contacting you. Their artworks and comments will also be hidden from your feed and explorer.
        </p>
    </div>

    <!-- Block User Input Form -->
    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60 mb-8">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">
            Block a User by Handle or Name
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
            Type the user's handle or name (e.g. <span class="font-mono text-indigo-500">@username</span> or <span class="font-mono text-indigo-500">username</span>) to add them to your blocklist.
        </p>

        <form method="post" action="{{ route('profile.blocking.store') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-mono text-sm">
                    @
                </span>
                <input type="text"
                       name="username"
                       required
                       placeholder="handle_or_name"
                       class="block w-full pl-8 pr-4 py-2.5 text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm flex items-center justify-center gap-1.5 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                <span>Block User</span>
            </button>
        </form>

        <x-input-error :messages="$errors->get('username')" class="mt-2" />

        @if (session('status') && session('active_tab') === 'blocking')
            <p class="mt-3 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('status') }}
            </p>
        @endif

        @if (session('info') && session('active_tab') === 'blocking')
            <p class="mt-3 text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('info') }}
            </p>
        @endif
    </div>

    <!-- Currently Blocked Users List -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                Blocked Accounts ({{ $blockedUsers->count() }})
            </h3>
        </div>

        @if($blockedUsers->isEmpty())
            <div class="p-8 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">No Blocked Users</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                    You have not blocked anyone. If you block an artist or user, they will appear here and you can unblock them at any time.
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
                @foreach($blockedUsers as $blockedUser)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                        <div class="flex items-center space-x-3.5">
                            <x-user-avatar :user="$blockedUser" size="sm" />
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $blockedUser->name }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ '@' . $blockedUser->slug }}
                                    @if($blockedUser->pivot?->created_at)
                                        <span class="text-[11px] text-gray-400 font-sans ml-1">· Blocked {{ $blockedUser->pivot->created_at->diffForHumans() }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.blocking.destroy', $blockedUser->id) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                Unblock
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

