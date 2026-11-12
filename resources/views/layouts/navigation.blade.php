<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Gallery') }}
                    </x-nav-link>
                    <x-nav-link :href="route('artists.index')" :active="request()->routeIs('artists.*')">
                        {{ __('Artists') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(Auth::check() && Auth::user()->is_admin)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            {{ __('Admin Panel') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Global Header Search Bar with Dropdown (Artstation style Ref3) -->
            <div class="hidden md:flex flex-1 max-w-sm lg:max-w-md mx-6 items-center"
                 x-data="{
                     searchQuery: '{{ request('search') ?? request('name') ?? '' }}',
                     dropdownOpen: false,
                     submitSearch(scope) {
                         this.dropdownOpen = false;
                         const trimmed = this.searchQuery.trim();
                         if (scope === 'artists') {
                             window.location.href = '{{ route('artists.index') }}' + (trimmed ? '?search=' + encodeURIComponent(trimmed) : '');
                         } else {
                             window.location.href = '{{ route('home') }}' + (trimmed ? '?search=' + encodeURIComponent(trimmed) : '');
                         }
                     }
                 }"
                 @click.outside="dropdownOpen = false">
                
                <div class="relative w-full">
                    <form @submit.prevent="submitSearch('{{ request()->routeIs('artists.*') ? 'artists' : 'artworks' }}')">
                        <div class="relative flex items-center">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text"
                                   x-model="searchQuery"
                                   @focus="dropdownOpen = true"
                                   placeholder="Search..."
                                   class="w-full pl-10 pr-4 py-2 text-xs rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-xs">
                        </div>
                    </form>

                    <!-- Contextual Dropdown (Artstation style Ref3) -->
                    <div x-cloak
                         x-show="dropdownOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 py-1.5 z-50 overflow-hidden">
                        
                        <!-- Search Artworks Option -->
                        <button type="button"
                                @click="submitSearch('artworks')"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs text-left font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-indigo-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Search Artworks</span>
                                <template x-if="searchQuery.trim()">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold" x-text="'“' + searchQuery + '”'"></span>
                                </template>
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </button>

                        <!-- Search Artists Option -->
                        <button type="button"
                                @click="submitSearch('artists')"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs text-left font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-purple-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>Search Artists</span>
                                <template x-if="searchQuery.trim()">
                                    <span class="text-purple-600 dark:text-purple-400 font-semibold" x-text="'“' + searchQuery + '”'"></span>
                                </template>
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-2">
                <x-theme-toggle />

                @auth
                    <!-- In-App Notifications Bell Dropdown (#15) -->
                    <div x-data="{
                        open: false,
                        unreadCount: {{ Auth::user()->unreadNotifications()->count() }},
                        notifications: [],
                        loading: false,

                        loadNotifications() {
                            this.loading = true;
                            fetch('{{ route('notifications.index') }}', {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.notifications = data.notifications;
                                this.unreadCount = data.unread_count;
                                this.loading = false;
                            })
                            .catch(() => this.loading = false);
                        },

                        toggle() {
                            this.open = !this.open;
                            if (this.open) {
                                this.loadNotifications();
                            }
                        },

                        markAsRead(id, url) {
                            fetch('/notifications/' + id + '/read', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.unreadCount = data.unread_count;
                                const notif = this.notifications.find(n => n.id === id);
                                if (notif) notif.is_read = true;
                                if (url) window.location.href = url;
                            });
                        },

                        markAllRead() {
                            fetch('{{ route('notifications.readAll') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.unreadCount = 0;
                                this.notifications.forEach(n => n.is_read = true);
                            });
                        }
                    }"
                    @click.outside="open = false"
                    class="relative">
                        <!-- Bell Icon Button -->
                        <button @click="toggle()"
                                type="button"
                                class="relative p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none"
                                aria-label="Notifications"
                                title="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadCount > 0"
                                  class="absolute top-1 right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-extrabold text-white bg-red-500 rounded-full shadow-xs"
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                             style="display: none;">
                            <!-- Header -->
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/60 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Notifications</span>
                                <button type="button"
                                        x-show="unreadCount > 0"
                                        @click="markAllRead()"
                                        class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Mark all as read
                                </button>
                            </div>

                            <!-- List -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/50">
                                <template x-if="loading && notifications.length === 0">
                                    <div class="p-6 text-center text-xs text-gray-400">Loading notifications...</div>
                                </template>

                                <template x-if="!loading && notifications.length === 0">
                                    <div class="p-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                        <p class="font-medium text-gray-700 dark:text-gray-300">All caught up!</p>
                                        <p class="mt-1">You will receive notifications when other artists appreciate or bookmark your artwork.</p>
                                    </div>
                                </template>

                                <template x-for="notif in notifications" :key="notif.id">
                                    <div @click="markAsRead(notif.id, notif.url)"
                                         class="p-3.5 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer transition-colors"
                                         :class="!notif.is_read ? 'bg-indigo-50/40 dark:bg-indigo-950/20' : ''">
                                        <!-- Icon / Avatar -->
                                        <div class="relative shrink-0 mt-0.5">
                                            <template x-if="notif.actor_avatar">
                                                <img :src="notif.actor_avatar" class="w-9 h-9 rounded-full object-cover">
                                            </template>
                                            <template x-if="!notif.actor_avatar">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-xs"
                                                     x-text="notif.actor_name ? notif.actor_name.substring(0, 1) : '★'"></div>
                                            </template>
                                            <!-- Mini type badge -->
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-[9px]"
                                                  :class="notif.type === 'like' ? 'bg-pink-500 text-white' : 'bg-amber-500 text-white'">
                                                <template x-if="notif.type === 'like'">
                                                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                                </template>
                                                <template x-if="notif.type !== 'like'">
                                                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                                                </template>
                                            </span>
                                        </div>

                                        <!-- Message & Details -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-snug" x-text="notif.message"></p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 font-medium" x-text="notif.time_ago"></p>
                                        </div>

                                        <!-- Unread Dot -->
                                        <template x-if="!notif.is_read">
                                            <span class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400 shrink-0 mt-1.5"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <x-user-avatar :user="Auth::user()" size="sm" class="mr-2" />
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(Auth::user()->is_admin)
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('Admin Panel') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('collections.index')">
                                {{ __('My Collections') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('bookmarks.index')">
                                {{ __('My Favorites') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('trash.index')">
                                {{ __('Trash') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors mr-4">Log in</a>
                    <a href="{{ route('register') }}" class="text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-xl transition-colors">Register</a>
                @endauth
            </div>

            <!-- Hamburger & Theme Toggle -->
            <div class="-me-2 flex items-center space-x-1 sm:hidden">
                <x-theme-toggle />
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Mobile Search Input -->
        <div class="px-4 pt-2 pb-1"
             x-data="{
                 mobileQuery: '{{ request('search') ?? '' }}',
                 submitMobile(scope) {
                     const trimmed = this.mobileQuery.trim();
                     if (scope === 'artists') {
                         window.location.href = '{{ route('artists.index') }}' + (trimmed ? '?search=' + encodeURIComponent(trimmed) : '');
                     } else {
                         window.location.href = '{{ route('home') }}' + (trimmed ? '?search=' + encodeURIComponent(trimmed) : '');
                     }
                 }
             }">
            <div class="relative">
                <input type="text"
                       x-model="mobileQuery"
                       placeholder="Search..."
                       @keydown.enter.prevent="submitMobile('{{ request()->routeIs('artists.*') ? 'artists' : 'artworks' }}')"
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
            </div>
            <div class="flex items-center gap-2 mt-1.5 text-[11px]">
                <button type="button" @click="submitMobile('artworks')" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                    In Artworks
                </button>
                <span class="text-gray-400">•</span>
                <button type="button" @click="submitMobile('artists')" class="text-purple-600 dark:text-purple-400 font-semibold hover:underline">
                    In Artists
                </button>
            </div>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Gallery') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('artists.index')" :active="request()->routeIs('artists.*')">
                {{ __('Artists') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(Auth::check() && Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    {{ __('Admin Panel') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <x-user-avatar :user="Auth::user()" size="md" class="mr-3" />
                        <div>
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <x-theme-toggle />
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('collections.index')">
                        {{ __('My Collections') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('bookmarks.index')">
                        {{ __('My Favorites') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('trash.index')">
                        {{ __('Trash') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
