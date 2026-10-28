<x-admin-layout>
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Admin Overview</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time overview of artists, publications, moderation reports, and recycle bin.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                    <span class="w-2 h-2 mr-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    System Operational
                </span>
            </div>
        </div>

        <!-- Metric Cards (KPIs) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- KPI 1: Artists -->
            <a href="{{ route('admin.users.index') }}" class="group bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all">
                <div class="flex items-center justify-between">
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 flex items-center">
                        Manage <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registered Artists</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @if($stats['suspended_users'] > 0)
                            <span class="text-amber-600 dark:text-amber-400 font-semibold">{{ $stats['suspended_users'] }} suspended</span>
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">All active</span>
                        @endif
                    </p>
                </div>
            </a>

            <!-- KPI 2: Public Artworks -->
            <a href="{{ route('home') }}" target="_blank" class="group bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all">
                <div class="flex items-center justify-between">
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 flex items-center">
                        View Gallery <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Public Artworks</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['published_artworks']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $stats['hidden_artworks'] }} hidden</span> • <span class="text-gray-400">{{ $stats['trashed_artworks'] }} in trash</span>
                    </p>
                </div>
            </a>

            <!-- KPI 3: Pending Reports -->
            <a href="{{ route('admin.reports.index') }}" class="group bg-white dark:bg-gray-900 p-5 rounded-2xl border {{ $stats['pending_reports'] > 0 ? 'border-amber-300 dark:border-amber-800 bg-amber-50/30 dark:bg-amber-950/20' : 'border-gray-200 dark:border-gray-800' }} shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div class="p-3 rounded-xl {{ $stats['pending_reports'] > 0 ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }} group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium {{ $stats['pending_reports'] > 0 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-400' }} flex items-center">
                        Inbox <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending Reports</p>
                    <p class="text-2xl font-black {{ $stats['pending_reports'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }} mt-1">
                        {{ number_format($stats['pending_reports']) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $stats['resolved_reports'] }} resolved reports
                    </p>
                </div>
            </a>

            <!-- KPI 4: Recycle Bin -->
            <a href="{{ route('admin.trash.index') }}" class="group bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-400 group-hover:text-rose-600 dark:group-hover:text-rose-400 flex items-center">
                        Inspect <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recycle Bin</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($stats['trashed_artworks'] + $stats['trashed_collections']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $stats['trashed_artworks'] }} artworks • {{ $stats['trashed_collections'] }} collections
                    </p>
                </div>
            </a>
        </div>

        <!-- Recent Activity Panels -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3): Recent Reports -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Latest Reports Received</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Moderation feed and link maintenance</p>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View all
                    </a>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentReports as $report)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <div class="flex items-start space-x-3 min-w-0 mb-3 sm:mb-0">
                                <div class="p-2 rounded-lg {{ $report->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400' }} flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            {{ $report->artwork ? $report->artwork->title : 'Deleted artwork' }}
                                        </span>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $report->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                                            {{ $report->status === 'pending' ? 'Pending' : 'Resolved' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $report->reason }}</span>
                                        @if($report->user)
                                            • Reported by {{ $report->user->name }}
                                        @endif
                                        • {{ $report->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0 self-end sm:self-center">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-950 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    Review
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Inbox is clean!</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">There are no pending reports to review right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column (1/3): Recent Artists -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">New Artists</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Latest accounts registered</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View all
                    </a>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentUsers as $user)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <div class="flex items-center min-w-0 space-x-3">
                                <x-user-avatar :user="$user" size="sm" />
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $user->artworks_count }} artworks</span>
                                <p class="text-[10px] text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-gray-400">
                            No registered artists yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
