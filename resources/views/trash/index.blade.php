<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Recycle Bin') }}
            </h2>

            @if($artworks->count() > 0 || $collections->count() > 0)
                <div x-data="{ showConfirmModal: false }">
                    <button @click="showConfirmModal = true" type="button" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold uppercase tracking-wider rounded-xl shadow-sm transition-all focus:outline-none">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Empty Trash
                    </button>

                    <!-- Confirmation modal to empty trash -->
                    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
                        <div class="flex min-h-full items-center justify-center p-4 text-center">
                            <div @click.away="showConfirmModal = false" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 text-left shadow-2xl transition-all sm:max-w-lg w-full border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center space-x-3 text-rose-600 dark:text-rose-400">
                                    <div class="p-3 bg-rose-100 dark:bg-rose-950/60 rounded-xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Empty entire recycle bin?</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                    This action will permanently delete <strong>all your artworks and collections</strong> in the recycle bin. This content cannot be recovered.
                                </p>
                                <div class="mt-6 flex justify-end space-x-3">
                                    <button @click="showConfirmModal = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition">
                                        Cancel
                                    </button>
                                    <form action="{{ route('trash.empty') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition">
                                            Yes, empty permanently
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen" x-data="{ activeTab: 'artworks' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Retention policy banner -->
            <div class="mb-8 p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 flex items-start space-x-3 text-indigo-900 dark:text-indigo-200">
                <svg class="w-5 h-5 mt-0.5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm leading-relaxed">
                    <strong>Retention Policy:</strong> Items sent to the recycle bin are kept for <strong>30 days</strong>. After this period, they are permanently deleted automatically.
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex items-center space-x-2 border-b border-gray-200 dark:border-gray-700 mb-6 pb-2">
                <button @click="activeTab = 'artworks'" 
                        :class="activeTab === 'artworks' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800 shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                        class="px-5 py-2.5 rounded-xl border text-sm transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Artworks</span>
                    <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full" 
                          :class="activeTab === 'artworks' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'">
                        {{ $artworks->count() }}
                    </span>
                </button>

                <button @click="activeTab = 'collections'" 
                        :class="activeTab === 'collections' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800 shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                        class="px-5 py-2.5 rounded-xl border text-sm transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Collections</span>
                    <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full" 
                          :class="activeTab === 'collections' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'">
                        {{ $collections->count() }}
                    </span>
                </button>
            </div>

            <!-- TAB CONTENT: ARTWORKS -->
            <div x-show="activeTab === 'artworks'">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($artworks as $artwork)
                            @php
                                $daysRemaining = max(0, 30 - (int) $artwork->deleted_at->diffInDays(now()));
                            @endphp
                            <li class="p-4 sm:p-5 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 h-16 w-16 bg-gray-900 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-700 shadow-sm relative">
                                            @if($artwork->thumbnail_url)
                                                <img class="h-full w-full object-cover" src="{{ $artwork->thumbnail_url }}" alt="{{ $artwork->title }}" referrerpolicy="no-referrer">
                                            @else
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                            <span class="absolute bottom-1 right-1 px-1 py-0.2 rounded text-[8px] font-bold uppercase tracking-wider bg-black/80 text-white">
                                                {{ $artwork->category }}
                                            </span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                                    {{ $artwork->title }}
                                                </h3>
                                                <span class="text-xs uppercase font-mono px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px]">
                                                    {{ $artwork->provider }}
                                                </span>
                                            </div>

                                            <div class="mt-2 flex flex-wrap items-center gap-2.5 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    Deleted on {{ $artwork->deleted_at->format('M d, Y H:i') }}
                                                </span>

                                                <!-- Remaining Days Counter -->
                                                @if($daysRemaining >= 15)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $daysRemaining }} days remaining
                                                    </span>
                                                @elseif($daysRemaining >= 6)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $daysRemaining }} days remaining
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 animate-pulse">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        {{ $daysRemaining }} days remaining (imminent purge)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2.5 self-end sm:self-center">
                                        <!-- Restore -->
                                        <form action="{{ route('trash.artworks.restore', $artwork->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3.5 py-2 border border-indigo-200 dark:border-indigo-800 text-xs font-semibold rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 dark:hover:bg-indigo-900/50 transition">
                                                <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Restore
                                            </button>
                                        </form>
                                        
                                        <!-- Delete permanently -->
                                        <form action="{{ route('trash.artworks.destroy', $artwork->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this artwork? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3.5 py-2 border border-rose-200 dark:border-rose-900 text-xs font-semibold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition">
                                                <svg class="w-4 h-4 mr-1 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Delete Permanently
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">No artworks in the recycle bin</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                    When you delete artworks from your portfolio, they will be kept here for 30 days before being permanently deleted.
                                </p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- TAB CONTENT: COLLECTIONS -->
            <div x-show="activeTab === 'collections'" style="display: none;">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($collections as $collection)
                            @php
                                $colDaysRemaining = max(0, 30 - (int) $collection->deleted_at->diffInDays(now()));
                            @endphp
                            <li class="p-4 sm:p-5 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 h-14 w-14 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/60 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                                {{ $collection->name }}
                                            </h3>
                                            @if($collection->description)
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                                                    {{ $collection->description }}
                                                </p>
                                            @endif

                                            <div class="mt-2 flex flex-wrap items-center gap-2.5 text-xs text-gray-500 dark:text-gray-400">
                                                <span>Deleted on {{ $collection->deleted_at->format('M d, Y H:i') }}</span>

                                                <!-- Remaining Days Counter -->
                                                @if($colDaysRemaining >= 15)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                        {{ $colDaysRemaining }} days remaining
                                                    </span>
                                                @elseif($colDaysRemaining >= 6)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                        {{ $colDaysRemaining }} days remaining
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 animate-pulse">
                                                        {{ $colDaysRemaining }} days remaining
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2.5 self-end sm:self-center">
                                        <!-- Restore Collection -->
                                        <form action="{{ route('trash.collections.restore', $collection->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3.5 py-2 border border-indigo-200 dark:border-indigo-800 text-xs font-semibold rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 dark:hover:bg-indigo-900/50 transition">
                                                <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Restore
                                            </button>
                                        </form>
                                        
                                        <!-- Delete permanently -->
                                        <form action="{{ route('trash.collections.destroy', $collection->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this collection? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3.5 py-2 border border-rose-200 dark:border-rose-900 text-xs font-semibold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition">
                                                <svg class="w-4 h-4 mr-1 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Delete Permanently
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">No collections in the recycle bin</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                    Collections you delete will remain here temporarily before permanent deletion.
                                </p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
