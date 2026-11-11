<x-app-layout>
    <div>
        @if($user->banner_url)
            <!-- Artist Cover Banner -->
            <div class="w-full h-48 sm:h-64 md:h-80 overflow-hidden relative bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                <img src="{{ $user->banner_url }}" alt="{{ $user->name }} cover banner" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
            </div>
        @endif

        <!-- Artist Profile Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center md:space-x-8">
                    <!-- Avatar -->
                    <x-user-avatar :user="$user" size="2xl" class="border-4 border-white dark:border-gray-900 shadow-xl mb-6 md:mb-0 mx-auto md:mx-0" />
                    
                    <!-- Info -->
                    <div class="text-center md:text-left flex-1">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                </h1>
                                @if($user->headline)
                                    <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mt-1">
                                        {{ $user->headline }}
                                    </p>
                                @endif
                                @if($user->city || $user->country)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center justify-center md:justify-start gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>{{ implode(', ', array_filter([$user->city, $user->country])) }}</span>
                                    </p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap items-center justify-center md:justify-end gap-2">
                                @if($user->getPublicEmail())
                                    <a href="mailto:{{ $user->getPublicEmail() }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm" title="Contact artist via {{ $user->getPublicEmail() }}">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Contact Artist
                                    </a>
                                @endif

                                @if(auth()->check() && auth()->id() === $user->id)
                                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit My Profile
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto md:mx-0 mb-4 whitespace-pre-line text-sm leading-relaxed">
                            {{ $user->bio ?: 'This artist prefers to let their artwork speak for itself.' }}
                        </div>
                        
                        <!-- Badges: Commission & Hiring -->
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-xs font-semibold mb-3">
                            @if($user->commission_status === 'open')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                    Commissions Open
                                </span>
                            @elseif($user->commission_status === 'consult')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full mr-2"></span>
                                    Consult for Commissions
                                </span>
                            @endif

                            @php
                                $userHiring = is_array($user->hiring_types) ? $user->hiring_types : (json_decode($user->hiring_types ?? '[]', true) ?: []);
                            @endphp
                            @foreach($userHiring as $hType)
                                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800 capitalize">
                                    Available for {{ str_replace('-', ' ', $hType) }}
                                </span>
                            @endforeach
                            
                            @if($user->price_guide)
                                <span class="text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/60 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                                    Rates: {{ $user->price_guide }}
                                </span>
                            @endif
                        </div>

                        <!-- Skills and Software Pills -->
                        @php
                            $userSkills = is_array($user->skills) ? $user->skills : (json_decode($user->skills ?? '[]', true) ?: []);
                            $userSoftware = is_array($user->software) ? $user->software : (json_decode($user->software ?? '[]', true) ?: []);
                        @endphp
                        @if(!empty($userSkills) || !empty($userSoftware))
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-1.5 pt-1">
                                @foreach($userSkills as $skill)
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                                @foreach($userSoftware as $tool)
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300">
                                        {{ $tool }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Artist's Gallery Section -->
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white shrink-0">Portfolio</h2>
                    
                    @if(isset($collections) && $collections->count() > 0)
                        <!-- Collection Tabs -->
                        <div class="flex-1 overflow-x-auto pb-2 -mb-2 no-scrollbar">
                            <nav class="flex space-x-2 md:justify-end min-w-max" aria-label="Tabs">
                                <a href="{{ route('profile.public', $user->slug) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ !$selectedCollectionId ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">
                                    All
                                </a>
                                @foreach($collections as $collection)
                                    <a href="{{ route('profile.public', ['slug' => $user->slug, 'collection_id' => $collection->id]) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors flex items-center gap-1.5 {{ $selectedCollectionId == $collection->id ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">
                                        <span>{{ $collection->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            @if($collection->visibility === 'unlisted')
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 uppercase tracking-wider" title="Unlisted collection">Unlisted</span>
                                            @elseif($collection->visibility === 'private')
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 uppercase tracking-wider" title="Private collection">Private</span>
                                            @endif
                                        @endif
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $artworks->total() }} published {{ Str::plural('artwork', $artworks->total()) }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($artworks as $artwork)
                        <x-artwork-card :artwork="$artwork" />
                    @empty
                        <div class="col-span-full py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 shadow-sm">
                            <p class="text-gray-500 dark:text-gray-400">This artist has not published any artworks in this section yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $artworks->links() }}
                </div>
            </div>
        </div>

    </div>

    <!-- Visor Modal Adaptativo -->
    <x-viewer-modal />
</x-app-layout>
