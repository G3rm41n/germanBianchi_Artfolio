<x-onboarding-layout :currentStep="3" title="Congratulations! Profile Ready">
    <div class="text-center mb-8">
        <!-- Celebration Badge -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 mb-4 shadow-inner ring-8 ring-emerald-50 dark:ring-emerald-900/20">
            <span class="text-3xl">🎉</span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white font-['Outfit'] tracking-tight">
            You're All Set, {{ $user->name }}!
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 max-w-md mx-auto">
            Your Artfolio creator profile is ready. You are now part of our community of multidisciplinary digital artists and creators.
        </p>
    </div>

    <!-- Live Profile Summary Card -->
    <div class="rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-indigo-50/70 via-white to-purple-50/60 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 border border-indigo-100 dark:border-gray-700/80 shadow-md mb-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
            <!-- Avatar -->
            <div class="relative w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 shadow-md flex-shrink-0 border-2 border-indigo-500">
                @if ($user->avatar_url)
                    <div class="w-full h-full bg-cover bg-center bg-no-repeat"
                         style="background-image: url('{{ $user->avatar_url }}'); transform: translate({{ $user->crop_x ?? 0 }}%, {{ $user->crop_y ?? 0 }}%) scale({{ $user->crop_zoom ?? 1 }});">
                    </div>
                @else
                    <div class="w-full h-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-3xl">
                        {{ mb_strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Profile Info -->
            <div class="flex-grow space-y-3">
                <div>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white font-['Outfit']">
                            {{ $user->name }}
                        </h2>
                        <span class="text-xs font-mono text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 rounded-md">
                            {{ '@' . $user->slug }}
                        </span>
                        
                        <!-- Commission Status Badge -->
                        @if ($user->commission_status === 'open')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Open for Commissions
                            </span>
                        @elseif ($user->commission_status === 'consult')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Consult First
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                Commissions Closed
                            </span>
                        @endif
                    </div>

                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mt-1">
                        {{ $user->headline ?: 'Digital Creator & Visual Artist' }}
                    </p>

                    @if ($user->city || $user->country)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center justify-center sm:justify-start gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ implode(', ', array_filter([$user->city, $user->country])) }}</span>
                        </p>
                    @endif

                    @if ($user->getPublicEmail())
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5 flex items-center justify-center sm:justify-start gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>Contact: {{ $user->getPublicEmail() }}</span>
                        </p>
                    @endif
                </div>

                @if ($user->bio)
                    <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-3 leading-relaxed">
                        {{ $user->bio }}
                    </p>
                @endif

                <!-- Skills & Software Chips -->
                <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start pt-1">
                    @php
                        $skillsList = is_array($user->skills) ? $user->skills : (json_decode($user->skills ?? '[]', true) ?: []);
                        $softwareList = is_array($user->software) ? $user->software : (json_decode($user->software ?? '[]', true) ?: []);
                    @endphp

                    @foreach(array_slice($skillsList, 0, 5) as $skill)
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-100/80 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
                            {{ $skill }}
                        </span>
                    @endforeach

                    @foreach(array_slice($softwareList, 0, 4) as $tool)
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-purple-100/80 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300">
                            {{ $tool }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Final Launch Form & Action Button -->
    <div class="text-center space-y-4">
        <form method="POST" action="{{ route('onboarding.complete') }}">
            @csrf
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base shadow-lg hover:shadow-indigo-500/25 transition-all transform hover:-translate-y-0.5">
                <span>Go to Homepage & Explore Artfolio</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="flex items-center justify-center gap-4 text-xs text-gray-500 dark:text-gray-400 pt-2">
            <span>Need to change something?</span>
            <a href="{{ route('onboarding.step', ['step' => 1]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit Step 1</a>
            <span>&bull;</span>
            <a href="{{ route('onboarding.step', ['step' => 2]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit Step 2</a>
        </div>
    </div>
</x-onboarding-layout>
