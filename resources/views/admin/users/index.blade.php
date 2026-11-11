<x-admin-layout>
    <div x-data="userAdminManager()" class="space-y-6">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200 dark:border-gray-800">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">User & Artist Management</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Overview of registered accounts, account statuses, data extraction, and moderation.</p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Export List (JSON) -->
                <a href="{{ route('admin.users.exportAll', ['status' => $status, 'role' => request('role'), 'search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 shadow-sm transition-colors"
                   title="Download JSON export of all users matching current filters">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Export List (JSON)</span>
                </a>

                <!-- Import Single Artist (JSON) -->
                <button type="button"
                        @click="openArtistModal()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 transition-colors shadow-sm"
                        title="Import a single artist JSON file (with existing ID collision detection)">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Import Artist (JSON)</span>
                </button>

                <!-- Import Entire List (JSON) -->
                <button type="button"
                        @click="openListModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors"
                        title="Batch import entire list of users from JSON">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <span>Import List (JSON)</span>
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Status Tabs -->
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800/80 p-1 rounded-xl">
                <a href="{{ route('admin.users.index', ['status' => 'all', 'role' => request('role'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'all' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    All ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'active', 'role' => request('role'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'active' ? 'bg-white dark:bg-gray-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Active ({{ $counts['active'] }})
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'suspended', 'role' => request('role'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'suspended' ? 'bg-white dark:bg-gray-900 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Suspended ({{ $counts['suspended'] }})
                </a>
            </div>

            <!-- Search & Role Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-2">
                <input type="hidden" name="status" value="{{ $status }}">

                <select name="role" onchange="this.form.submit()" class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Roles</option>
                    <option value="artist" {{ request('role') === 'artist' ? 'selected' : '' }}>Artists</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrators</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or handle..." class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 pl-8 pr-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-64">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index', ['status' => $status]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">User / Artist</th>
                            <th scope="col" class="px-3 py-3.5">Role</th>
                            <th scope="col" class="px-3 py-3.5">Creations</th>
                            <th scope="col" class="px-3 py-3.5">Status</th>
                            <th scope="col" class="px-3 py-3.5">Joined Date</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <!-- User Info -->
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center space-x-3">
                                        <x-user-avatar :user="$user" size="md" />
                                        <div class="min-w-0">
                                            <span class="font-bold text-gray-900 dark:text-white block text-sm truncate">
                                                {{ $user->name }}
                                            </span>
                                            <span class="text-xs text-gray-400 block truncate">
                                                {{ $user->email }}
                                            </span>
                                            <span class="text-[11px] text-indigo-500 dark:text-indigo-400 block truncate">
                                                /artist/{{ $user->slug }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            Administrator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                            Artist
                                        </span>
                                    @endif
                                </td>

                                <!-- Creations -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $user->artworks_count }} artworks</div>
                                    <div class="text-[11px] text-gray-400">{{ $user->collections_count }} collections</div>
                                </td>

                                <!-- Status -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Suspended
                                        </span>
                                    @endif
                                </td>

                                <!-- Joined Date -->
                                <td class="px-3 py-4 whitespace-nowrap text-gray-400 text-[11px]">
                                    {{ $user->created_at->format('M d, Y') }}<br>
                                    <span class="text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <!-- View Profile -->
                                        <a href="{{ route('profile.public', $user->slug) }}" target="_blank" class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="View Public Portfolio">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>

                                        <!-- Export Individual Artist Data (JSON) -->
                                        <a href="{{ route('admin.users.exportOne', $user->id) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 border border-indigo-200/60 dark:border-indigo-800/60 transition-colors"
                                           title="Extract full JSON dossier for {{ $user->name }} (profile, artworks & collections)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            <span>Export JSON</span>
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <!-- Suspend / Activate -->
                                            @if($user->status === 'active')
                                                <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" onsubmit="return confirm('Suspend {{ $user->name }}\'s account?\nThe user will not be able to create or edit artworks while suspended.');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900 transition-colors" title="Suspend Account">
                                                        Suspend
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition-colors" title="Activate Account">
                                                        Activate
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Soft Delete -->
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Delete {{ $user->name }}\'s account?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors" title="Delete User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-400 font-medium italic">You</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    No matching users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Modal 1: Import Single Artist (JSON) with ID collision detection -->
        <div x-show="isArtistModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             @keydown.escape.window="closeArtistModal()">
            <div x-show="isArtistModalOpen" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity" 
                 @click="closeArtistModal()"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="isArtistModalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-800"
                     @click.stop>

                    <form method="POST" action="{{ route('admin.users.importOne') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="conflict_mode" :value="artistConflictMode">
                        <input type="hidden" name="json_data" :value="artistRawJson">

                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Importar Artista (JSON)</span>
                            </h3>
                            <button type="button" @click="closeArtistModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="py-4 space-y-4">
                            <!-- File Input -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Seleccionar archivo de artista (.json)
                                </label>
                                <input type="file" 
                                       name="json_file" 
                                       accept=".json,application/json" 
                                       @change="onArtistFileSelected($event)" 
                                       class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer border border-gray-200 dark:border-gray-700 rounded-xl p-2 bg-gray-50 dark:bg-gray-800">
                            </div>

                            <!-- Parse / Check Status Spinner -->
                            <div x-show="isCheckingArtist" class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 flex items-center gap-3">
                                <svg class="animate-spin w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span class="text-xs text-gray-600 dark:text-gray-300">Inspeccionando archivo y verificando ID en el sistema...</span>
                            </div>

                            <!-- Artist Preview Box -->
                            <div x-show="parsedArtist && !isCheckingArtist" class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-900 dark:text-white" x-text="parsedArtist.name || parsedArtist.account?.name || 'Artista'"></span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300" x-text="'ID #' + (parsedArtist.id || parsedArtist.account?.id || 'Nuevo')"></span>
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5">
                                    <div>Correo: <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="parsedArtist.email || parsedArtist.account?.email || 'N/A'"></span></div>
                                    <div class="flex items-center gap-3 pt-1">
                                        <span>🎨 Obras: <strong class="text-indigo-600 dark:text-indigo-400" x-text="parsedArtist.artworks?.length || 0"></strong></span>
                                        <span>📁 Colecciones: <strong class="text-purple-600 dark:text-purple-400" x-text="parsedArtist.collections?.length || 0"></strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Collision Notice: Artist Already Exists -->
                            <div x-show="artistExists && !isCheckingArtist" class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 space-y-3">
                                <div class="flex items-start gap-2.5">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">¡El artista ya existe en el sistema!</h4>
                                        <p class="text-xs text-amber-700 dark:text-amber-300/90 mt-1">
                                            Detectamos un usuario registrado con el mismo ID (<strong x-text="'#' + existingUser?.id"></strong>) o correo (<strong x-text="existingUser?.email"></strong>): <span class="font-bold underline" x-text="existingUser?.name"></span>.
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-amber-200/60 dark:border-amber-800/60">
                                    <span class="block text-xs font-bold mb-2">¿Deseas reemplazar sus datos o actualizarlos?</span>
                                    
                                    <div class="space-y-2">
                                        <!-- Option 1: Actualizar -->
                                        <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-colors"
                                               :class="artistConflictMode === 'update' ? 'bg-amber-100/70 dark:bg-amber-900/50 border-amber-400 dark:border-amber-700 font-semibold' : 'bg-white/60 dark:bg-gray-800/60 border-amber-200/50 dark:border-amber-900/40'">
                                            <input type="radio" name="_conflict_choice" value="update" x-model="artistConflictMode" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                            <div class="text-xs">
                                                <strong class="text-gray-900 dark:text-white block">Actualizar datos (Update)</strong>
                                                <span class="text-gray-600 dark:text-gray-400 text-[11px] block mt-0.5">
                                                    Actualiza su información de perfil y añade o actualiza las obras y colecciones del archivo sin borrar las obras previas que ya posea el artista.
                                                </span>
                                            </div>
                                        </label>

                                        <!-- Option 2: Reemplazar -->
                                        <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-colors"
                                               :class="artistConflictMode === 'replace' ? 'bg-rose-100/70 dark:bg-rose-950/60 border-rose-400 dark:border-rose-700 font-semibold' : 'bg-white/60 dark:bg-gray-800/60 border-amber-200/50 dark:border-amber-900/40'">
                                            <input type="radio" name="_conflict_choice" value="replace" x-model="artistConflictMode" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                                            <div class="text-xs">
                                                <strong class="text-rose-900 dark:text-rose-200 block">Reemplazar datos (Replace)</strong>
                                                <span class="text-gray-600 dark:text-gray-400 text-[11px] block mt-0.5">
                                                    Sobrescribe por completo su perfil y sustituye todas sus obras y colecciones anteriores por las contenidas en este archivo.
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Non-collision Notice: New Artist -->
                            <div x-show="parsedArtist && !artistExists && !isCheckingArtist" class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5 text-xs">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong>Nuevo Artista:</strong> Este ID y correo no están registrados. Se creará una nueva cuenta de artista con sus obras.</span>
                            </div>

                            <!-- Error Message -->
                            <div x-show="artistErrorMessage" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs" x-text="artistErrorMessage"></div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" @click="closeArtistModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-300 transition">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    :disabled="!parsedArtist || isCheckingArtist"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <span x-text="artistExists ? (artistConflictMode === 'replace' ? 'Confirmar Reemplazo' : 'Confirmar Actualización') : 'Importar Artista'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal 2: Import Entire List (JSON) with Confirmation Warning -->
        <div x-show="isListModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             @keydown.escape.window="closeListModal()">
            <div x-show="isListModalOpen" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity" 
                 @click="closeListModal()"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="isListModalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-800"
                     @click.stop>

                    <form method="POST" action="{{ route('admin.users.importAll') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="conflict_mode" :value="listConflictMode">
                        <input type="hidden" name="json_data" :value="listRawJson">

                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Importar Lista de Usuarios (JSON)</span>
                            </h3>
                            <button type="button" @click="closeListModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="py-4 space-y-4">
                            <!-- Prominent Confirmation Warning -->
                            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 space-y-2">
                                <div class="flex items-center gap-2 font-bold text-sm text-amber-800 dark:text-amber-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>¿Estás seguro de que deseas importar esta lista?</span>
                                </div>
                                <p class="text-xs text-amber-700 dark:text-amber-300/90 leading-relaxed">
                                    Esta acción procesará en lote todas las cuentas de usuarios, perfiles de artistas, obras y colecciones del archivo JSON seleccionado. Los registros coincidentes serán sincronizados en la base de datos.
                                </p>
                            </div>

                            <!-- File Input -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Seleccionar archivo de lista (.json)
                                </label>
                                <input type="file" 
                                       name="json_file" 
                                       accept=".json,application/json" 
                                       @change="onListFileSelected($event)" 
                                       class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer border border-gray-200 dark:border-gray-700 rounded-xl p-2 bg-gray-50 dark:bg-gray-800">
                            </div>

                            <!-- Detected Count Box -->
                            <div x-show="parsedListCount > 0" class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-300">Registros identificados en el archivo:</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 text-sm" x-text="parsedListCount + ' usuarios/artistas'"></span>
                            </div>

                            <!-- Conflict Mode -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Modo de sincronización para usuarios existentes
                                </label>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer"
                                           :class="listConflictMode === 'update' ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-400 dark:border-indigo-700 font-semibold text-indigo-700 dark:text-indigo-300' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'">
                                        <input type="radio" name="_list_conflict" value="update" x-model="listConflictMode" class="text-indigo-600 focus:ring-indigo-500">
                                        <span>Actualizar</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer"
                                           :class="listConflictMode === 'replace' ? 'bg-rose-50 dark:bg-rose-950/60 border-rose-400 dark:border-rose-700 font-semibold text-rose-700 dark:text-rose-300' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'">
                                        <input type="radio" name="_list_conflict" value="replace" x-model="listConflictMode" class="text-rose-600 focus:ring-rose-500">
                                        <span>Reemplazar</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Confirmation Checkbox -->
                            <div class="pt-2">
                                <label class="flex items-start gap-2.5 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700/60 cursor-pointer">
                                    <input type="checkbox" x-model="listConfirmed" class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">
                                        Sí, estoy seguro y entiendo que se modificarán o crearán registros en la base de datos.
                                    </span>
                                </label>
                            </div>

                            <!-- Error Message -->
                            <div x-show="listErrorMessage" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs" x-text="listErrorMessage"></div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" @click="closeListModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-300 transition">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    :disabled="!listConfirmed || parsedListCount === 0"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Sí, Importar Lista Completa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Manager Script -->
    <script>
    function userAdminManager() {
        return {
            // Single Artist Modal State
            isArtistModalOpen: false,
            parsedArtist: null,
            isCheckingArtist: false,
            artistExists: false,
            existingUser: null,
            artistConflictMode: 'update',
            artistRawJson: '',
            artistErrorMessage: '',

            openArtistModal() {
                this.parsedArtist = null;
                this.isCheckingArtist = false;
                this.artistExists = false;
                this.existingUser = null;
                this.artistConflictMode = 'update';
                this.artistRawJson = '';
                this.artistErrorMessage = '';
                this.isArtistModalOpen = true;
            },

            closeArtistModal() {
                this.isArtistModalOpen = false;
            },

            async onArtistFileSelected(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.artistErrorMessage = '';
                this.isCheckingArtist = true;
                this.parsedArtist = null;
                this.artistExists = false;
                this.existingUser = null;

                try {
                    const text = await file.text();
                    this.artistRawJson = text;
                    const data = JSON.parse(text);

                    const artist = data.artist || (data.users ? data.users[0] : (data.account ? data : null));
                    if (!artist) {
                        throw new Error('El archivo no contiene un perfil de artista reconocible.');
                    }

                    this.parsedArtist = artist;

                    const id = artist.account?.id || artist.id || null;
                    const email = artist.account?.email || artist.email || null;

                    const response = await fetch('{{ route('admin.users.importCheck') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id, email })
                    });

                    if (response.ok) {
                        const result = await response.json();
                        if (result.exists) {
                            this.artistExists = true;
                            this.existingUser = result.user;
                            this.artistConflictMode = 'update';
                        } else {
                            this.artistExists = false;
                            this.existingUser = null;
                        }
                    }
                } catch (err) {
                    this.artistErrorMessage = err.message || 'Error al procesar el archivo JSON.';
                } finally {
                    this.isCheckingArtist = false;
                }
            },

            // Bulk List Modal State
            isListModalOpen: false,
            parsedListCount: 0,
            listConflictMode: 'update',
            listRawJson: '',
            listConfirmed: false,
            listErrorMessage: '',

            openListModal() {
                this.parsedListCount = 0;
                this.listConflictMode = 'update';
                this.listRawJson = '';
                this.listConfirmed = false;
                this.listErrorMessage = '';
                this.isListModalOpen = true;
            },

            closeListModal() {
                this.isListModalOpen = false;
            },

            async onListFileSelected(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.listErrorMessage = '';
                this.parsedListCount = 0;

                try {
                    const text = await file.text();
                    this.listRawJson = text;
                    const data = JSON.parse(text);

                    const count = data.users?.length || (data.artist ? 1 : (Array.isArray(data) ? data.length : 0));
                    if (count === 0) {
                        throw new Error('No se encontraron registros de usuarios en el archivo.');
                    }

                    this.parsedListCount = count;
                } catch (err) {
                    this.listErrorMessage = err.message || 'Error al procesar el archivo JSON.';
                }
            }
        };
    }
    </script>
</x-admin-layout>
