<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Artfolio') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen flex flex-col">
        <header class="w-full bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <x-application-logo class="h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                        <span class="ml-3 font-semibold text-xl tracking-tight">{{ config('app.name', 'Artfolio') }}</span>
                    </div>

                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition">Iniciar Sesión</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Registrarse
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center relative overflow-hidden">
            <!-- Contenido principal decorativo para la Etapa 1 -->
            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
                <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6 text-gray-900 dark:text-white">
                    Exhibe tu <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600">arte al mundo</span>
                </h1>
                <p class="mt-4 text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-10">
                    Artfolio es la plataforma definitiva para artistas multidisciplinarios. Centraliza tus creaciones, conecta con otros artistas y construye tu portafolio ideal.
                </p>
                <div class="flex justify-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition shadow-lg hover:shadow-xl">
                            Ir a tu Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition shadow-lg hover:shadow-xl">
                            Crea tu cuenta gratis
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 md:py-4 md:text-lg md:px-10 transition shadow">
                            Ya tengo cuenta
                        </a>
                    @endauth
                </div>
            </div>
            
            <!-- Elementos decorativos de fondo -->
            <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-indigo-50 dark:from-gray-800 to-transparent pointer-events-none"></div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-200 dark:bg-purple-900 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob pointer-events-none"></div>
            <div class="absolute top-48 -left-24 w-72 h-72 bg-indigo-200 dark:bg-indigo-900 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000 pointer-events-none"></div>
        </main>

        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Artfolio') }}. Todos los derechos reservados.
            </div>
        </footer>
    </body>
</html>
