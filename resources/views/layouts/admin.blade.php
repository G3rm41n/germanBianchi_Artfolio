<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Artfolio') }} - Admin Panel</title>

        <!-- Theme Initialization Script -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen">
        <div class="min-h-screen flex">
            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm sm:hidden"
                 style="display: none;"></div>

            <!-- Admin Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
                   class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-transform duration-300 ease-in-out sm:static sm:translate-x-0 shadow-lg sm:shadow-none">
                
                <!-- Logo & Admin Badge -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 dark:border-gray-800">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5">
                        <x-application-logo class="block h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                        <span class="font-black text-xl tracking-tight bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Artfolio</span>
                    </a>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                        Admin
                    </span>
                </div>
                
                <!-- Navigation Links -->
                @php
                    $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
                @endphp
                <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Dashboard
                        </div>
                    </a>

                    <!-- Reports Moderation -->
                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                            Reports
                        </div>
                        @if($pendingReportsCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-amber-500 rounded-full animate-pulse">
                                {{ $pendingReportsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Users & Artists Management -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Users & Artists
                        </div>
                    </a>

                    <!-- Global Trash -->
                    <a href="{{ route('admin.trash.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.trash.*') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.trash.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Global Trash
                        </div>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Quick Links</p>
                        
                        <a href="{{ route('home') }}" class="flex items-center px-3.5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Public Gallery
                        </a>

                        <a href="{{ route('dashboard') }}" class="flex items-center px-3.5 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Artist Dashboard
                        </a>
                    </div>
                </nav>

                <!-- Footer Sidebar (Usuario Conectado) -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex items-center min-w-0 mr-2">
                        <x-user-avatar :user="auth()->user()" size="sm" />
                        <div class="ml-2.5 truncate">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-1">
                        <x-theme-toggle />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Log Out">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Header -->
                <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 sm:px-6 z-10">
                    <div class="flex items-center">
                        <!-- Mobile Hamburger Button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 mr-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                            @yield('title', 'Admin Panel')
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <x-theme-toggle />
                        <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            View Site
                        </a>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mx-6 mt-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between text-sm text-emerald-800 dark:text-emerald-300">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-6 mt-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 flex items-center justify-between text-sm text-red-800 dark:text-red-300">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Contenido Principal -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
