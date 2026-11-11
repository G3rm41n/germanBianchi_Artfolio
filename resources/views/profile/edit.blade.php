<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white font-['Outfit'] tracking-tight">
                    Settings & Profile Hub
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Manage your portfolio showcase, artistic resume, content browsing filters, and account security.
                </p>
            </div>

            <a href="{{ route('profile.public', $user->slug) }}" target="_blank" class="inline-flex items-center self-start sm:self-auto px-4 py-2 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition shadow-sm border border-indigo-200/60 dark:border-indigo-800">
                <span>View Public Portfolio</span>
                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10 bg-gray-50/50 dark:bg-gray-900/50 min-h-[calc(100vh-140px)]"
         x-data="{
             tab: '{{ session('active_tab', request('tab', ($errors->updatePassword->isNotEmpty() ? 'password' : ($errors->userDeletion->isNotEmpty() ? 'account' : ($errors->has('username') ? 'blocking' : 'profile'))))) }}',
             setTab(newTab) {
                 this.tab = newTab;
                 if (window.history.pushState) {
                     const url = new URL(window.location);
                     url.searchParams.set('tab', newTab);
                     window.history.pushState({}, '', url);
                 }
             }
         }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- ── Left Sidebar: Artist Mini-Card & Tabs Navigation ──────────────── -->
                <aside class="lg:col-span-4 xl:col-span-3 space-y-6">
                    <!-- User Identity Mini Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center space-x-4">
                        <x-user-avatar :user="$user" size="lg" class="border-2 border-indigo-500 shadow-md flex-shrink-0" />
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                {{ $user->name }}
                            </h2>
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-mono truncate">
                                {{ '@' . $user->slug }}
                            </p>
                            @if($user->headline)
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                    {{ $user->headline }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Vertical Navigation Tabs (ArtStation Style) -->
                    <nav class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden p-2 space-y-1">
                        <!-- 1. Profile -->
                        <button type="button"
                                @click="setTab('profile')"
                                :class="tab === 'profile' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent'"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-xl transition text-left">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Profile</span>
                        </button>

                        <!-- 2. Resume -->
                        <button type="button"
                                @click="setTab('resume')"
                                :class="tab === 'resume' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent'"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-xl transition text-left">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Resume</span>
                        </button>

                        <!-- 3. Account -->
                        <button type="button"
                                @click="setTab('account')"
                                :class="tab === 'account' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent'"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-xl transition text-left">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            <span>Account</span>
                        </button>

                        <!-- 4. Notifications -->
                        <button type="button"
                                @click="setTab('notifications')"
                                :class="tab === 'notifications' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent'"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-xl transition text-left">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span>Notifications</span>
                        </button>

                        <!-- 5. Password -->
                        <button type="button"
                                @click="setTab('password')"
                                :class="tab === 'password' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent'"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-xl transition text-left">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Password</span>
                        </button>

                        <!-- 6. Blocking -->
                        <button type="button"
                                @click="setTab('blocking')"
                                :class="tab === 'blocking' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40 hover:text-gray-900 dark:hover:text-gray-200 border-l-4 border-transparent'"
                                class="w-full flex items-center px-4 py-3 text-sm rounded-xl transition text-left">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            <span>Blocking</span>
                            @if(isset($blockedUsers) && $blockedUsers->isNotEmpty())
                                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400">
                                    {{ $blockedUsers->count() }}
                                </span>
                            @endif
                        </button>
                    </nav>
                </aside>

                <!-- ── Right Content Area: Active Tab Panel ───────────────────────── -->
                <main class="lg:col-span-8 xl:col-span-9">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-sm p-6 sm:p-8 lg:p-10 transition-all">
                        <!-- Tab 1: Profile -->
                        <div x-show="tab === 'profile'" x-cloak>
                            @include('profile.partials.tab-profile')
                        </div>

                        <!-- Tab 2: Resume -->
                        <div x-show="tab === 'resume'" x-cloak>
                            @include('profile.partials.tab-resume')
                        </div>

                        <!-- Tab 3: Account -->
                        <div x-show="tab === 'account'" x-cloak>
                            @include('profile.partials.tab-account')
                        </div>

                        <!-- Tab 4: Notifications -->
                        <div x-show="tab === 'notifications'" x-cloak>
                            @include('profile.partials.tab-notifications')
                        </div>

                        <!-- Tab 5: Password -->
                        <div x-show="tab === 'password'" x-cloak>
                            @include('profile.partials.tab-password')
                        </div>

                        <!-- Tab 6: Blocking -->
                        <div x-show="tab === 'blocking'" x-cloak>
                            @include('profile.partials.tab-blocking')
                        </div>
                    </div>
                </main>

            </div>
        </div>
    </div>
</x-app-layout>
