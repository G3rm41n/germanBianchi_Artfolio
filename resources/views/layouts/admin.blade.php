<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Artfolio') }} - Panel de Administración</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen flex">
            <!-- Sidebar Admin -->
            <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-sm flex flex-col hidden sm:block">
                <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700">
                    <x-application-logo class="block h-8 w-auto fill-current text-indigo-600" />
                    <span class="ml-3 font-semibold text-gray-800 dark:text-gray-200">AdminPanel</span>
                </div>
                
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    
                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Plataforma</p>
                        <a href="#" class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-not-allowed opacity-50">Usuarios</a>
                        <a href="#" class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-not-allowed opacity-50">Reportes</a>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Panel de Administración</h1>
                    
                    <div class="flex items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('home') }}">
                                    Volver a la Galería
                                </x-dropdown-link>
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
