<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">¡Hola, {{ Auth::user()->name }}!</h3>
                        <p class="text-gray-500 dark:text-gray-400">Tu perfil público está disponible en: <span class="font-mono text-indigo-500">{{ url('/@' . Auth::user()->slug) }}</span></p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tarjeta para próximas etapas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-md mb-4 text-indigo-600 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Mis Obras</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Aún no has publicado ninguna obra. ¡Empieza a llenar tu portafolio!</p>
                        <button disabled class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest opacity-50 cursor-not-allowed">
                            Publicar Obra (Próximamente)
                        </button>
                    </div>
                </div>

                <!-- Enlace al perfil -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-md mb-4 text-purple-600 dark:text-purple-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Mi Perfil</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Actualiza tu biografía, estado de comisiones y cambia tu contraseña.</p>
                        <a href="{{ route('profile.edit') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                            Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
