<x-admin-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Report Moderation</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review reported artworks, broken links, and content inquiries.</p>
            </div>
        </div>

        <!-- Filters and Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Status Tabs -->
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800/80 p-1 rounded-xl">
                <a href="{{ route('admin.reports.index', ['status' => 'pending', 'reason' => request('reason'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'pending' ? 'bg-white dark:bg-gray-900 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Pending ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'resolved', 'reason' => request('reason'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'resolved' ? 'bg-white dark:bg-gray-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Resolved ({{ $counts['resolved'] }})
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'all', 'reason' => request('reason'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'all' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    All ({{ $counts['all'] }})
                </a>
            </div>

            <!-- Search and Reason Filter -->
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                
                <select name="reason" onchange="this.form.submit()" class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All reasons</option>
                    <option value="Broken or inaccessible link" {{ str_contains(request('reason', ''), 'Broken') || str_contains(request('reason', ''), 'caído') ? 'selected' : '' }}>Broken link</option>
                    <option value="Inappropriate or explicit content" {{ str_contains(request('reason', ''), 'Inappropriate') || str_contains(request('reason', ''), 'inapropiado') ? 'selected' : '' }}>Inappropriate content</option>
                    <option value="Copyright infringement" {{ str_contains(request('reason', ''), 'Copyright') ? 'selected' : '' }}>Copyright</option>
                    <option value="Spam or misleading content" {{ str_contains(request('reason', ''), 'Spam') ? 'selected' : '' }}>Spam</option>
                    <option value="Other reason" {{ str_contains(request('reason', ''), 'Other') || str_contains(request('reason', ''), 'Otro') ? 'selected' : '' }}>Other reason</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or user..." class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 pl-8 pr-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-64">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if(request('search') || request('reason'))
                    <a href="{{ route('admin.reports.index', ['status' => $status]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Reports Table -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">Reported Artwork</th>
                            <th scope="col" class="px-3 py-3.5">Artist / Author</th>
                            <th scope="col" class="px-3 py-3.5">Reason</th>
                            <th scope="col" class="px-3 py-3.5">Reported By</th>
                            <th scope="col" class="px-3 py-3.5">Status</th>
                            <th scope="col" class="px-3 py-3.5">Date</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <!-- Artwork -->
                                <td class="py-4 pl-6 pr-3 min-w-[240px]">
                                    <div class="flex items-center space-x-3">
                                        @if($report->artwork && $report->artwork->thumbnail_url)
                                            <img src="{{ $report->artwork->thumbnail_url }}" alt="{{ $report->artwork->title }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 flex-shrink-0 border border-gray-200 dark:border-gray-700">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            @if($report->artwork)
                                                <span class="font-bold text-gray-900 dark:text-white truncate block text-sm">
                                                    {{ $report->artwork->title }}
                                                </span>
                                                <div class="flex items-center space-x-1.5 mt-0.5">
                                                    <span class="uppercase text-[10px] font-bold px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400">
                                                        {{ $report->artwork->provider }}
                                                    </span>
                                                    <span class="text-[11px] text-gray-400">
                                                        Visibility: {{ $report->artwork->status }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-sm italic text-gray-400">[Deleted artwork]</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Artist -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($report->artwork && $report->artwork->user)
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $report->artwork->user->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $report->artwork->user->email }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Unknown</span>
                                    @endif
                                </td>

                                <!-- Reason -->
                                <td class="px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ (str_contains(strtolower($report->reason), 'caído') || str_contains(strtolower($report->reason), 'broken')) ? 'bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200' }}">
                                        {{ $report->reason }}
                                    </span>
                                    @if($report->details)
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-1 italic max-w-xs">
                                            "{{ $report->details }}"
                                        </p>
                                    @endif
                                </td>

                                <!-- Reporter -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($report->user)
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $report->user->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $report->user->email }}</div>
                                    @else
                                        <span class="text-gray-400 italic">Deleted user</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300' }}">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $report->status === 'pending' ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                        {{ $report->status === 'pending' ? 'Pending' : 'Resolved' }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="px-3 py-4 whitespace-nowrap text-gray-400 text-[11px]">
                                    {{ $report->created_at->format('M d, Y H:i') }}<br>
                                    <span class="text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                                </td>

                                <!-- Action -->
                                <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                                        Review
                                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="font-semibold text-gray-700 dark:text-gray-300">No reports found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
