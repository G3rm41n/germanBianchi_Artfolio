<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Profile Setup' }} - {{ config('app.name', 'Artfolio') }}</title>

        <!-- Theme Initialization Script -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-slate-50 dark:bg-slate-900 transition-colors duration-200 min-h-screen flex flex-col justify-between">
        <!-- Minimal Top Navigation -->
        <header class="w-full border-b border-gray-200/80 dark:border-gray-800 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md sticky top-0 z-30">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <x-application-logo class="block h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform" />
                        <span class="font-black text-lg tracking-tight text-gray-900 dark:text-white font-['Outfit']">Artfolio</span>
                    </a>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">Creator Onboarding</span>
                </div>

                <!-- Right Actions: Theme Toggle & Logout -->
                <div class="flex items-center gap-3">
                    <x-theme-toggle />
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-grow py-8 sm:py-12 px-4 sm:px-6">
            <div class="max-w-2xl mx-auto">
                <!-- Step Indicator / Progress Bar -->
                @php
                    $step = $currentStep ?? 1;
                @endphp
                <div class="mb-8">
                    <div class="flex items-center justify-between text-xs sm:text-sm font-semibold mb-3">
                        <!-- Step 1 -->
                        <div class="flex items-center gap-2 {{ $step >= 1 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500' }}">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $step > 1 ? 'bg-emerald-500 text-white' : ($step === 1 ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-900/40' : 'bg-gray-200 dark:bg-gray-700 text-gray-500') }}">
                                @if($step > 1)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    1
                                @endif
                            </span>
                            <span class="hidden sm:inline">Personal Profile</span>
                        </div>

                        <!-- Connector 1 -->
                        <div class="flex-grow mx-2 sm:mx-4 h-0.5 rounded {{ $step > 1 ? 'bg-indigo-500 dark:bg-indigo-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>

                        <!-- Step 2 -->
                        <div class="flex items-center gap-2 {{ $step >= 2 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500' }}">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $step > 2 ? 'bg-emerald-500 text-white' : ($step === 2 ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-900/40' : 'bg-gray-200 dark:bg-gray-700 text-gray-500') }}">
                                @if($step > 2)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    2
                                @endif
                            </span>
                            <span class="hidden sm:inline">Professional Profile</span>
                        </div>

                        <!-- Connector 2 -->
                        <div class="flex-grow mx-2 sm:mx-4 h-0.5 rounded {{ $step >= 3 ? 'bg-indigo-500 dark:bg-indigo-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>

                        <!-- Step 3 -->
                        <div class="flex items-center gap-2 {{ $step === 3 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500' }}">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $step === 3 ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-900/40' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">
                                3
                            </span>
                            <span class="hidden sm:inline">Launch</span>
                        </div>
                    </div>
                </div>

                <!-- Flash Alert Messages -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-200 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm">
                        <div class="flex items-center gap-2 font-semibold mb-1">
                            <svg class="w-5 h-5 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Please review the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Card Body (Slot) -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700/60 p-6 sm:p-10 transition-colors">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full py-6 text-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200/60 dark:border-gray-800">
            <p>&copy; {{ date('Y') }} Artfolio &mdash; Experimental Digital Portfolio Platform.</p>
        </footer>
    </body>
</html>

