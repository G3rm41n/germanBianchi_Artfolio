@extends('errors.layout')

@section('title', '403 - Access Restricted')

@section('content')
    <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
    </div>

    <span class="text-xs font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 font-mono">Error 403</span>
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white font-['Outfit'] mt-1 mb-3">
        Access Restricted
    </h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8 leading-relaxed">
        {{ $exception->getMessage() ?: 'You do not have permission to view or edit this resource. This collection or artwork may be marked as private by its owner.' }}
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-md">
            Back to Gallery
        </a>
        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold transition">
            My Dashboard
        </a>
    </div>
@endsection
