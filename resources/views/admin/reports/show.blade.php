<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Barra de navegación superior -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.reports.index') }}" class="p-2 rounded-xl text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ticket de Reporte #{{ $report->id }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $report->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300' }}">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $report->status === 'pending' ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                            {{ $report->status === 'pending' ? 'Pendiente' : 'Resuelto' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Recibido el {{ $report->created_at->format('d/m/Y \a \l\a\s H:i') }} ({{ $report->created_at->diffForHumans() }})</p>
                </div>
            </div>

            <!-- Visibilidad actual de la obra -->
            @if($report->artwork)
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Estado de la Obra:</span>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $report->artwork->status === 'published' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800' : ($report->artwork->status === 'hidden' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-300 dark:border-amber-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400') }}">
                        {{ $report->artwork->status === 'published' ? 'Pública' : ($report->artwork->status === 'hidden' ? 'Oculta' : $report->artwork->status) }}
                    </span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Izquierda (2/3): Obra, Motivo del Reporte y Notas -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tarjeta de la Obra -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Información de la Obra</h3>

                    @if($report->artwork)
                        <div class="flex flex-col sm:flex-row gap-5">
                            <!-- Miniatura / Media -->
                            <div class="w-full sm:w-48 h-36 bg-gray-950 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center border border-gray-200 dark:border-gray-800">
                                @if($report->artwork->thumbnail_url)
                                    <img src="{{ $report->artwork->thumbnail_url }}" alt="{{ $report->artwork->title }}" class="w-full h-full object-cover">
                                @elseif($report->artwork->render_type === 'image')
                                    <img src="{{ $report->artwork->external_url }}" alt="{{ $report->artwork->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-3 text-gray-500">
                                        <svg class="w-8 h-8 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <span class="text-[10px] uppercase font-bold">{{ $report->artwork->provider }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Metadatos de la Obra -->
                            <div class="min-w-0 flex-1">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $report->artwork->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-3">
                                    {{ $report->artwork->description ?: 'Sin descripción.' }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-2 items-center text-xs">
                                    <span class="px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-semibold uppercase text-[11px]">
                                        {{ $report->artwork->provider }} ({{ $report->artwork->category }})
                                    </span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-500 dark:text-gray-400">Creada {{ $report->artwork->created_at->format('d/m/Y') }}</span>
                                </div>

                                <!-- Botón para comprobar el enlace externo -->
                                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                                    <a href="{{ $report->artwork->external_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        Comprobar URL original en nueva pestaña
                                        <span class="text-gray-400 font-normal ml-1">({{ Str::limit($report->artwork->external_url, 45) }})</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-6 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-xl text-red-700 dark:text-red-400 text-sm">
                            Esta obra ha sido eliminada permanentemente de la base de datos.
                        </div>
                    @endif
                </div>

                <!-- Detalle del Reporte del Usuario -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Detalle de la Denuncia</h3>

                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Motivo Declarado</span>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ str_contains(strtolower($report->reason), 'caído') ? 'bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' }}">
                                    {{ $report->reason }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Descripción / Detalles</span>
                            <div class="mt-1 p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 text-sm text-gray-800 dark:text-gray-200">
                                {{ $report->details ?: 'El usuario no proporcionó detalles adicionales.' }}
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 text-xs text-gray-500 pt-2">
                            <span>Denunciado por:</span>
                            @if($report->user)
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $report->user->name }}</span>
                                <span class="text-gray-400">({{ $report->user->email }})</span>
                            @else
                                <span class="italic text-gray-400">Usuario anónimo o dado de baja</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notas Internas del Administrador -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">Notas Internas de Auditoría</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Estas notas son visibles exclusivamente para los administradores y quedan registradas en el ticket.</p>

                    <form method="POST" action="{{ route('admin.reports.updateNote', $report->id) }}">
                        @csrf
                        @method('PATCH')

                        <textarea name="admin_note" rows="3" placeholder="Añade observaciones, fecha de verificación externa o contexto de la resolución..." class="w-full text-sm rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 text-gray-900 dark:text-white p-3 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('admin_note', $report->admin_note) }}</textarea>

                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-800 dark:bg-gray-700 text-white hover:bg-gray-900 dark:hover:bg-gray-600 transition-colors shadow-sm">
                                Guardar Nota Interna
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna Derecha (1/3): Acciones de Moderación y Autor -->
            <div class="space-y-6">
                <!-- Panel de Acciones de Moderación (RN-02) -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Acciones de Moderación</h3>

                    <div class="space-y-3">
                        <!-- ACCIÓN PRINCIPAL: Alerta Enlace Caído (RN-02) -->
                        @if($report->artwork)
                            <form method="POST" action="{{ route('admin.reports.linkDownAlert', $report->id) }}" onsubmit="return confirm('¿Confirmas que el enlace está caído?\n\n1. La obra pasará a estado OCULTO (hidden).\n2. El reporte se marcará como RESUELTO.\n3. Se enviará un correo informativo al artista autor con el enlace de edición.');">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition-colors group">
                                    <svg class="w-4 h-4 mr-2 text-amber-200 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Enviar Alerta de Enlace Caído (RN-02)
                                </button>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5 text-center">
                                    Oculta la obra y notifica al autor por correo asíncrono.
                                </p>
                            </form>
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-800 my-3"></div>

                        <!-- Marcar como Resuelto sin Ocultar -->
                        <form method="POST" action="{{ route('admin.reports.resolve', $report->id) }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Marcar como Resuelto (Sin Ocultar)
                            </button>
                        </form>

                        <!-- Desestimar Reporte -->
                        <form method="POST" action="{{ route('admin.reports.dismiss', $report->id) }}" onsubmit="return confirm('¿Desestimar este reporte como infundado o spam?');">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Desestimar Denuncia
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Datos del Artista Autor -->
                @if($report->artwork && $report->artwork->user)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Artista Propietario</h3>

                        <div class="flex items-center space-x-3 mb-3">
                            <x-user-avatar :user="$report->artwork->user" size="md" />
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $report->artwork->user->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $report->artwork->user->email }}</p>
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Registrado:</span> {{ $report->artwork->user->created_at->format('d/m/Y') }}</div>
                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span> {{ $report->artwork->user->status }}</div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('profile.public', $report->artwork->user->slug) }}" target="_blank" class="block text-center px-3 py-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                Ver Portafolio Público
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Otros reportes sobre esta misma obra -->
                @if($otherReports->isNotEmpty())
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                            Otros Reportes de esta Obra ({{ $otherReports->count() }})
                        </h3>
                        <div class="space-y-2">
                            @foreach($otherReports as $other)
                                <a href="{{ route('admin.reports.show', $other->id) }}" class="block p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/40 transition-colors text-xs">
                                    <div class="flex items-center justify-between font-semibold text-gray-800 dark:text-gray-200">
                                        <span>{{ $other->reason }}</span>
                                        <span class="text-[10px] uppercase {{ $other->status === 'pending' ? 'text-amber-600' : 'text-emerald-600' }}">{{ $other->status }}</span>
                                    </div>
                                    <p class="text-gray-400 text-[11px] mt-0.5">Por {{ $other->user ? $other->user->name : 'Anónimo' }} • {{ $other->created_at->format('d/m/Y') }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
