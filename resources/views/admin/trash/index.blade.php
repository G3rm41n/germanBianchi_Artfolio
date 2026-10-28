<x-admin-layout>
    <div class="space-y-6">
        <!-- Encabezado con Botón de Purga Global -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800 gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Papelera Global del Sistema</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Auditoría y control de todas las obras y colecciones eliminadas en la plataforma (RF-11, RN-03, RN-04).</p>
            </div>
            
            <!-- Botón Forzar Purga de 30 Días -->
            <form method="POST" action="{{ route('admin.trash.prune') }}" onsubmit="return confirm('¿Ejecutar la purga de 30 días ahora?\nSe eliminarán definitivamente todos los registros con más de 30 días en papelera (model:prune).');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-700 text-white shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Ejecutar Purga de 30 Días (RN-04)
                </button>
            </form>
        </div>

        <!-- Pestañas y Buscador -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Pestañas -->
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800/80 p-1 rounded-xl">
                <a href="{{ route('admin.trash.index', ['tab' => 'artworks', 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $tab === 'artworks' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Obras Eliminadas ({{ $counts['artworks'] }})
                </a>
                <a href="{{ route('admin.trash.index', ['tab' => 'collections', 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-all {{ $tab === 'collections' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Colecciones Eliminadas ({{ $counts['collections'] }})
                </a>
            </div>

            <!-- Buscador -->
            <form method="GET" action="{{ route('admin.trash.index') }}" class="flex gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por título o artista..." class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 pl-8 pr-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-64">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if(request('search'))
                    <a href="{{ route('admin.trash.index', ['tab' => $tab]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 flex items-center justify-center">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabla según Pestaña -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            @if($tab === 'artworks')
                <!-- OBRAS ELIMINADAS -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                        <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3">Obra</th>
                                <th scope="col" class="px-3 py-3.5">Propietario</th>
                                <th scope="col" class="px-3 py-3.5">Eliminada</th>
                                <th scope="col" class="px-3 py-3.5">Gracia de Retención (RN-04)</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            @forelse($artworks as $art)
                                @php
                                    $diasRestantes = max(0, 30 - (int) $art->deleted_at->diffInDays(now()));
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-4 pl-6 pr-3 min-w-[220px]">
                                        <div class="flex items-center space-x-3">
                                            @if($art->thumbnail_url)
                                                <img src="{{ $art->thumbnail_url }}" alt="{{ $art->title }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-gray-100 dark:bg-gray-800">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <span class="font-bold text-gray-900 dark:text-white block truncate text-sm">
                                                    {{ $art->title }}
                                                </span>
                                                <span class="text-[11px] text-gray-400 uppercase">
                                                    {{ $art->provider }} ({{ $art->category }})
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 whitespace-nowrap">
                                        @if($art->user)
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $art->user->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $art->user->email }}</div>
                                        @else
                                            <span class="text-gray-400 italic">Desconocido</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 whitespace-nowrap text-gray-400 text-[11px]">
                                        {{ $art->deleted_at->format('d/m/Y H:i') }}<br>
                                        <span class="text-gray-500">{{ $art->deleted_at->diffForHumans() }}</span>
                                    </td>

                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $diasRestantes <= 5 ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400' : ($diasRestantes <= 14 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400') }}">
                                            {{ $diasRestantes }} días restantes
                                        </span>
                                    </td>

                                    <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center space-x-2">
                                            <!-- Restaurar (RN-03) -->
                                            <form method="POST" action="{{ route('admin.trash.artworks.restore', $art->id) }}">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition-colors">
                                                    Restaurar
                                                </button>
                                            </form>

                                            <!-- Forzar Eliminación -->
                                            <form method="POST" action="{{ route('admin.trash.artworks.destroy', $art->id) }}" onsubmit="return confirm('¿Eliminar DEFINITIVAMENTE la obra &quot;{{ $art->title }}&quot;?\nEsta acción es irreversible.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors" title="Eliminar definitivamente">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                        No hay obras en la papelera.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($artworks->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                        {{ $artworks->links() }}
                    </div>
                @endif
            @else
                <!-- COLECCIONES ELIMINADAS -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                        <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3">Colección</th>
                                <th scope="col" class="px-3 py-3.5">Propietario</th>
                                <th scope="col" class="px-3 py-3.5">Eliminada</th>
                                <th scope="col" class="px-3 py-3.5">Gracia de Retención (RN-04)</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            @forelse($collections as $col)
                                @php
                                    $diasRestantes = max(0, 30 - (int) $col->deleted_at->diffInDays(now()));
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="py-4 pl-6 pr-3 min-w-[200px]">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 font-bold">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-gray-900 dark:text-white block text-sm truncate">
                                                    {{ $col->name }}
                                                </span>
                                                <span class="text-xs text-gray-400 block truncate">
                                                    {{ $col->description ?: 'Sin descripción' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-4 whitespace-nowrap">
                                        @if($col->user)
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $col->user->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $col->user->email }}</div>
                                        @else
                                            <span class="text-gray-400 italic">Desconocido</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 whitespace-nowrap text-gray-400 text-[11px]">
                                        {{ $col->deleted_at->format('d/m/Y H:i') }}<br>
                                        <span class="text-gray-500">{{ $col->deleted_at->diffForHumans() }}</span>
                                    </td>

                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $diasRestantes <= 5 ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400' : ($diasRestantes <= 14 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400') }}">
                                            {{ $diasRestantes }} días restantes
                                        </span>
                                    </td>

                                    <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center space-x-2">
                                            <!-- Restaurar -->
                                            <form method="POST" action="{{ route('admin.trash.collections.restore', $col->id) }}">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900 transition-colors">
                                                    Restaurar
                                                </button>
                                            </form>

                                            <!-- Forzar Eliminación -->
                                            <form method="POST" action="{{ route('admin.trash.collections.destroy', $col->id) }}" onsubmit="return confirm('¿Eliminar DEFINITIVAMENTE la colección &quot;{{ $col->name }}&quot;?\nEsta acción es irreversible.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors" title="Eliminar definitivamente">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                        No hay colecciones en la papelera.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($collections->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                        {{ $collections->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-admin-layout>
