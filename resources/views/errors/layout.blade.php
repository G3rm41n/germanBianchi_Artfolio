<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Artfolio') - Artfolio</title>

        <!-- Theme script -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Favicons -->
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-950 min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">
        <!-- Top mini navbar -->
        <header class="py-6 px-6 sm:px-12 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                <img src="/android-chrome-512x512.png" alt="Artfolio Logo" class="h-9 w-auto">
                <span class="font-['Outfit'] font-black text-xl tracking-tight text-gray-900 dark:text-white">Artfolio</span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    Explore Gallery
                </a>
            </div>
        </header>

        <!-- Main centered error card -->
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="max-w-xl w-full text-center py-12 px-6 sm:px-10 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center text-xs text-gray-400 dark:text-gray-600">
            &copy; {{ date('Y') }} Artfolio Creative Network. All rights reserved.
        </footer>
    </body>
</html>

