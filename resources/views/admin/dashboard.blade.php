<x-admin-layout>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Stat: Usuarios -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Artistas Registrados</p>
                    <p class="text-3xl font-bold text-gray-700 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <!-- Stat: Admins -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Administradores</p>
                    <p class="text-3xl font-bold text-gray-700 dark:text-white">{{ $stats['total_admins'] }}</p>
                </div>
            </div>
        </div>

        <!-- Stat: Suspendidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Suspendidos</p>
                    <p class="text-3xl font-bold text-gray-700 dark:text-white">{{ $stats['suspended'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Estado de la Plataforma</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400">
                Bienvenido al Panel de Administración. Actualmente el sistema cuenta con los roles básicos funcionando (Administrador, Artista y Visitante). 
                En las próximas etapas se habilitarán las opciones para gestionar usuarios y moderar reportes.
            </p>
        </div>
    </div>
</x-admin-layout>
