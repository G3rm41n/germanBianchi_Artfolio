@extends('errors.layout')

@section('title', '500 - Server Error')

@section('content')
    <div class="w-16 h-16 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    </div>

    <span class="text-xs font-black uppercase tracking-widest text-rose-600 dark:text-rose-400 font-mono">Error 500</span>
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white font-['Outfit'] mt-1 mb-3">
        Unexpected Server Error
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8 leading-relaxed">
        Something went wrong on our end while processing your creative request. Our team has been notified.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-md">
            Back to Gallery
        </a>
        <a href="javascript:location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold transition">
            Reload Page
        </a>
    </div>
@endsection

