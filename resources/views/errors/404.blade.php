@extends('errors.layout')

@section('title', '404 - Page Not Found')

@section('content')
    <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>

    <span class="text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 font-mono">Error 404</span>
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white font-['Outfit'] mt-1 mb-3">
        Artwork or Page Not Found
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8 leading-relaxed">
        The artwork, collection, or artist profile you are looking for might have been moved, removed, or temporarily set to private.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-md">
            Back to Gallery
        </a>
    </div>
@endsection

