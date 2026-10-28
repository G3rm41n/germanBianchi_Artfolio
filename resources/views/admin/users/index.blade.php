<x-admin-layout>
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gestión de Artistas y Usuarios</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Supervisión de cuentas registradas, control de estados, suspensión y auditoría (RF-11).</p>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Pestañas de Estado -->
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800/80 p-1 rounded-xl">
                <a href="{{ route('admin.users.index', ['status' => 'all', 'role' => request('role'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'all' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Todos ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'active', 'role' => request('role'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'active' ? 'bg-white dark:bg-gray-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Activos ({{ $counts['active'] }})
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'suspended', 'role' => request('role'), 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $status === 'suspended' ? 'bg-white dark:bg-gray-900 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Suspendidos ({{ $counts['suspended'] }})
                </a>
            </div>

            <!-- Formulario de Búsqueda -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-2">
                <input type="hidden" name="status" value="{{ $status }}">

                <select name="role" onchange="this.form.submit()" class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los roles</option>
                    <option value="artist" {{ request('role') === 'artist' ? 'selected' : '' }}>Artistas</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administradores</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, email o slug..." class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 pl-8 pr-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-64">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index', ['status' => $status]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 flex items-center justify-center">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">Usuario / Artista</th>
                            <th scope="col" class="px-3 py-3.5">Rol</th>
                            <th scope="col" class="px-3 py-3.5">Creaciones</th>
                            <th scope="col" class="px-3 py-3.5">Estado</th>
                            <th scope="col" class="px-3 py-3.5">Fecha Registro</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <!-- Usuario -->
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
                                                /artista/{{ $user->slug }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Rol -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            Administrador
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                            Artista
                                        </span>
                                    @endif
                                </td>

                                <!-- Creaciones -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $user->artworks_count }} obras</div>
                                    <div class="text-[11px] text-gray-400">{{ $user->collections_count }} colecciones</div>
                                </td>

                                <!-- Estado -->
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Suspendido
                                        </span>
                                    @endif
                                </td>

                                <!-- Fecha Registro -->
                                <td class="px-3 py-4 whitespace-nowrap text-gray-400 text-[11px]">
                                    {{ $user->created_at->format('d/m/Y') }}<br>
                                    <span class="text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                                </td>

                                <!-- Acciones -->
                                <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <!-- Ver Perfil -->
                                        <a href="{{ route('profile.public', $user->slug) }}" target="_blank" class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Ver Portafolio Público">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <!-- Suspender / Reactivar -->
                                            @if($user->status === 'active')
                                                <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" onsubmit="return confirm('¿Suspender la cuenta de {{ $user->name }}?\nEl usuario no podrá crear ni editar obras mientras permanezca suspendido.');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900 transition-colors" title="Suspender Cuenta">
                                                        Suspender
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition-colors" title="Reactivar Cuenta">
                                                        Activar
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Eliminar (SoftDelete) -->
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('¿Eliminar la cuenta de {{ $user->name }}?\nSe aplicará un borrado lógico (Soft Delete).');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors" title="Eliminar Usuario">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-400 font-medium italic">Tú</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron usuarios coincidentes.
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
    </div>
</x-admin-layout>
