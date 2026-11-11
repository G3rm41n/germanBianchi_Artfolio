<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white font-['Outfit'] tracking-tight">
                    Artist Dashboard
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Overview of your portfolio activity, quick actions, and artistic metrics.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('profile.public', Auth::user()->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition border border-indigo-200/60 dark:border-indigo-800 shadow-sm">
                    <span>View Public Portfolio</span>
                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $user = Auth::user();
        $artworksCount = $user->artworks()->where('status', 'published')->count();
        $collectionsCount = $user->collections()->count();
        $bookmarksCount = $user->bookmarks()->count();
    @endphp

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner & Artist KPIs Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-gray-700/60 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center space-x-4">
                        <x-user-avatar :user="$user" size="lg" class="border-2 border-indigo-500 shadow-md shrink-0" />
                        <div>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white font-['Outfit']">
                                Welcome back, {{ $user->name }}!
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Public URL: <a href="{{ route('profile.public', $user->slug) }}" target="_blank" class="font-mono text-indigo-600 dark:text-indigo-400 hover:underline">{{ url('/artista/' . $user->slug) }}</a>
                            </p>
                        </div>
                    </div>

                    <!-- Commission Status Badge -->
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Commissions:</span>
                        @if($user->commission_status === 'open')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                Open for Work
                            </span>
                        @elseif($user->commission_status === 'consult')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                Upon Request
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                                Closed
                            </span>
                        @endif
                    </div>
                </div>

                <!-- KPI Metric Counters -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6">
                    <!-- KPI 1 -->
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Published Artworks</span>
                        <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $artworksCount }}</div>
                    </div>

                    <!-- KPI 2 -->
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Collections</span>
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $collectionsCount }}</div>
                    </div>

                    <!-- KPI 3 -->
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Saved Favorites</span>
                        <div class="text-2xl font-black text-rose-500 mt-1">{{ $bookmarksCount }}</div>
                    </div>

                    <!-- KPI 4 -->
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Account Status</span>
                        <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 mt-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Verified Artist
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 3 Balanced Action Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: My Artworks -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col justify-between hover:shadow-lg transition">
                    <div>
                        <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-2xl mb-4 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">My Artworks</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                            Publish new artwork embeds, manage descriptions, tag AI generation, and catalog subcategories.
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <a href="{{ route('artworks.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition">
                            + Publish New Artwork
                        </a>
                        <a href="{{ route('artworks.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-50 dark:bg-gray-900/60 hover:bg-gray-100 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition">
                            Manage All Artworks ({{ $artworksCount }})
                        </a>
                    </div>
                </div>

                <!-- Card 2: My Collections & Favorites (3rd missing card now present!) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col justify-between hover:shadow-lg transition">
                    <div>
                        <div class="flex items-center justify-center w-12 h-12 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-2xl mb-4 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Collections & Favorites</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                            Group your artworks into thematic showcases with custom privacy (Public, Unlisted, Private) and explore favorites.
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <a href="{{ route('collections.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition">
                            Manage Collections ({{ $collectionsCount }})
                        </a>
                        <a href="{{ route('bookmarks.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-50 dark:bg-gray-900/60 hover:bg-gray-100 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition">
                            Saved Favorites ({{ $bookmarksCount }})
                        </a>
                    </div>
                </div>

                <!-- Card 3: Profile & Settings Hub -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-gray-700/60 shadow-sm flex flex-col justify-between hover:shadow-lg transition">
                    <div>
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 rounded-2xl mb-4 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Settings & Profile Hub</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                            Customize your avatar, cover banner, artist resume, software skills, AI/mature filters, and user blocking.
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <a href="{{ route('profile.edit') }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition">
                            Open Settings Hub
                        </a>
                        <a href="{{ route('profile.public', $user->slug) }}" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-50 dark:bg-gray-900/60 hover:bg-gray-100 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition">
                            Preview Public Profile ↗
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
