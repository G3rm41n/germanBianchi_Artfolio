<x-admin-layout>
    <div x-data="{ 
            detailsOpen: false, 
            activeLog: null,
            openDetails(log) {
                this.activeLog = log;
                this.detailsOpen = true;
            }
         }" 
         class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800 gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Audit Logs</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Chronological trail of administrative operations and user activity.</p>
            </div>
            
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                Total events recorded: <span class="font-bold text-gray-900 dark:text-white">{{ number_format($counts['total']) }}</span>
            </div>
        </div>

        <!-- Actor Separation Tabs (Admin vs User) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex space-x-2 bg-gray-100 dark:bg-gray-800/80 p-1.5 rounded-2xl w-fit">
                <!-- Administrator Actions Tab -->
                <a href="{{ route('admin.audit-logs.index', ['actor' => 'admin']) }}" 
                   class="flex items-center px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $actor === 'admin' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Administrator Activity
                    <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] {{ $actor === 'admin' ? 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                        {{ $counts['admin'] }}
                    </span>
                </a>

                <!-- User Actions Tab -->
                <a href="{{ route('admin.audit-logs.index', ['actor' => 'user']) }}" 
                   class="flex items-center px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $actor === 'user' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    User / Artist Activity
                    <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] {{ $actor === 'user' ? 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                        {{ $counts['user'] }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <input type="hidden" name="actor" value="{{ $actor }}">

                <!-- Search Field -->
                <div class="relative lg:col-span-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by user, target, IP, or details..." 
                           class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white pl-8 pr-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Action Filter -->
                <div>
                    <select name="action" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Actions</option>
                        @foreach($availableActions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ str_replace(['admin.', 'user.'], '', $act) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}" 
                           title="From date"
                           class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Submit & Clear -->
                <div class="flex items-center space-x-2">
                    <button type="submit" class="w-full px-3 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'action', 'date_from', 'date_to']))
                        <a href="{{ route('admin.audit-logs.index', ['actor' => $actor]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Audit Log Table -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">Timestamp</th>
                            <th scope="col" class="px-3 py-3.5">Actor</th>
                            <th scope="col" class="px-3 py-3.5">Action</th>
                            <th scope="col" class="px-3 py-3.5">Target</th>
                            <th scope="col" class="px-3 py-3.5">Description</th>
                            <th scope="col" class="px-3 py-3.5">IP / Device</th>
                            <th scope="col" class="py-3.5 pl-3 pr-6 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <!-- Timestamp -->
                                <td class="py-3.5 pl-6 pr-3 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $log->created_at?->format('M d, Y H:i:s') ?? 'N/A' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        {{ $log->created_at?->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- Actor -->
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center space-x-2">
                                            <x-user-avatar :user="$log->user" size="xs" />
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white leading-tight">
                                                    {{ $log->user->name }}
                                                </div>
                                                <div class="text-[10px] text-gray-400">
                                                    {{ $log->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">System / Deleted</span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    @php
                                        $act = $log->action;
                                        $badgeColor = 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700';

                                        if (str_contains($act, 'created') || str_contains($act, 'activated') || str_contains($act, 'restored') || str_contains($act, 'verified')) {
                                            $badgeColor = 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800';
                                        } elseif (str_contains($act, 'suspended') || str_contains($act, 'updated') || str_contains($act, 'renamed') || str_contains($act, 'resolved')) {
                                            $badgeColor = 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800';
                                        } elseif (str_contains($act, 'deleted') || str_contains($act, 'purged') || str_contains($act, 'unverified') || str_contains($act, 'dismissed')) {
                                            $badgeColor = 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-medium border {{ $badgeColor }}">
                                        {{ $act }}
                                    </span>
                                </td>

                                <!-- Target -->
                                <td class="px-3 py-3.5 min-w-[160px]">
                                    @if($log->target_type)
                                        <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">
                                            {{ $log->target_type }} #{{ $log->target_id }}
                                        </div>
                                    @endif
                                    <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-xs" title="{{ $log->target_label }}">
                                        {{ $log->target_label ?? '—' }}
                                    </div>
                                </td>

                                <!-- Description -->
                                <td class="px-3 py-3.5 max-w-sm">
                                    <span class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2" title="{{ $log->description }}">
                                        {{ $log->description ?? '—' }}
                                    </span>
                                </td>

                                <!-- IP / Device -->
                                <td class="px-3 py-3.5 whitespace-nowrap text-[11px] text-gray-500 font-mono">
                                    <div>{{ $log->ip_address ?? '—' }}</div>
                                    @if($log->user_agent)
                                        <div class="text-[10px] text-gray-400 truncate max-w-[140px]" title="{{ $log->user_agent }}">
                                            {{ Str::limit($log->user_agent, 20) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Details Button -->
                                <td class="py-3.5 pl-3 pr-6 text-right whitespace-nowrap">
                                    @if(!empty($log->changes))
                                        <button @click="openDetails({{ json_encode([
                                            'id'          => $log->id,
                                            'action'      => $log->action,
                                            'actor'       => $log->user?->name ?? 'System',
                                            'target'      => $log->target_label,
                                            'description' => $log->description,
                                            'changes'     => $log->changes,
                                            'created_at'  => $log->created_at?->format('Y-m-d H:i:s'),
                                            'ip'          => $log->ip_address,
                                            'user_agent'  => $log->user_agent,
                                        ]) }})" 
                                                type="button" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors">
                                            View Diff
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-[11px]">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-9 w-9 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-sm font-medium">No audit records found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or date range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        <!-- Details / Changes Diff Modal -->
        <div x-show="detailsOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                <div x-show="detailsOpen" 
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="detailsOpen = false" 
                     class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

                <div x-show="detailsOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-800">
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Audit Event Details</h3>
                                <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'Event #' + (activeLog ? activeLog.id : '') + ' • ' + (activeLog ? activeLog.action : '')"></p>
                            </div>
                            <button @click="detailsOpen = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="mt-4 space-y-3 text-xs">
                            <div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl">
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Actor</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200" x-text="activeLog ? activeLog.actor : '—'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Target Entity</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200" x-text="activeLog ? activeLog.target : '—'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Date & Time</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200" x-text="activeLog ? activeLog.created_at : '—'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">IP Address</span>
                                    <span class="font-mono text-gray-800 dark:text-gray-200" x-text="activeLog ? activeLog.ip : '—'"></span>
                                </div>
                            </div>

                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold mb-1">Description</span>
                                <p class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl" x-text="activeLog ? activeLog.description : '—'"></p>
                            </div>

                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold mb-1">Payload / Changes (JSON)</span>
                                <pre class="font-mono text-[11px] bg-gray-950 text-gray-100 p-4 rounded-xl overflow-x-auto max-h-64 leading-relaxed" x-text="activeLog && activeLog.changes ? JSON.stringify(activeLog.changes, null, 2) : 'No changes payload'"></pre>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3 flex justify-end border-t border-gray-100 dark:border-gray-800">
                        <button @click="detailsOpen = false" type="button" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

