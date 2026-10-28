<x-admin-layout>
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800 gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Monitor de Colas y Sistema</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Supervisión de trabajos en cola asíncronos y gestión de fallos en base de datos (RNF-01).</p>
            </div>

            @if($counts['failed'] > 0)
                <form method="POST" action="{{ route('admin.queues.flush') }}" onsubmit="return confirm('¿Vaciar y eliminar todos los registros de fallos?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-700 text-white shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Limpiar Registro de Fallos
                    </button>
                </form>
            @endif
        </div>

        <!-- Indicadores de Salud de la Cola -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Trabajos en Cola</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $counts['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border {{ $counts['failed'] > 0 ? 'border-red-300 dark:border-red-800 bg-red-50/20' : 'border-gray-200 dark:border-gray-800' }} shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-3 rounded-xl {{ $counts['failed'] > 0 ? 'bg-red-100 text-red-600' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Trabajos Fallidos</p>
                        <p class="text-2xl font-black {{ $counts['failed'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $counts['failed'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Driver de Colas</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">MySQL Database</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trabajos Fallidos (failed_jobs) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Historial de Trabajos Fallidos (failed_jobs)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Errores capturados durante el procesamiento asíncrono de correos y tareas.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $counts['failed'] > 0 ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                    {{ $counts['failed'] }} registrados
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">ID / Cola</th>
                            <th scope="col" class="px-3 py-3.5">Excepción / Causa</th>
                            <th scope="col" class="px-3 py-3.5">Fecha de Fallo</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($failedJobs as $failed)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="py-4 pl-6 pr-3 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 dark:text-white">#{{ $failed->id }}</div>
                                    <div class="text-[11px] text-indigo-500">{{ $failed->queue }}</div>
                                </td>

                                <td class="px-3 py-4 max-w-md">
                                    <div class="font-mono text-[11px] text-red-600 dark:text-red-400 line-clamp-2 bg-red-50/60 dark:bg-red-950/30 p-2 rounded-lg border border-red-100 dark:border-red-900">
                                        {{ Str::limit($failed->exception, 220) }}
                                    </div>
                                </td>

                                <td class="px-3 py-4 whitespace-nowrap text-gray-400 text-[11px]">
                                    {{ $failed->failed_at }}
                                </td>

                                <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Reintentar -->
                                        <form method="POST" action="{{ route('admin.queues.retry', $failed->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                                                Reintentar
                                            </button>
                                        </form>

                                        <!-- Descartar -->
                                        <form method="POST" action="{{ route('admin.queues.forget', $failed->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors" title="Descartar fallo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">Sin trabajos fallidos</p>
                                    <p class="text-[11px] text-gray-400">Todos los correos y tareas asíncronas se están procesando correctamente.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trabajos Pendientes en Cola (jobs) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Trabajos en Cola de Espera (jobs)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tareas listas para ser ejecutadas por el worker (`queue:work`).</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">ID</th>
                            <th scope="col" class="px-3 py-3.5">Cola</th>
                            <th scope="col" class="px-3 py-3.5">Intentos</th>
                            <th scope="col" class="px-3 py-3.5">Encolado el</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($pendingJobs as $job)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="py-3 pl-6 pr-3 font-mono font-bold text-gray-900 dark:text-white">
                                    #{{ $job->id }}
                                </td>
                                <td class="px-3 py-3 text-indigo-600 dark:text-indigo-400 font-semibold">
                                    {{ $job->queue }}
                                </td>
                                <td class="px-3 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $job->attempts }}
                                </td>
                                <td class="px-3 py-3 text-gray-400 text-[11px]">
                                    {{ date('d/m/Y H:i:s', $job->created_at) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400 text-xs">
                                    La cola está vacía. No hay trabajos en espera.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
