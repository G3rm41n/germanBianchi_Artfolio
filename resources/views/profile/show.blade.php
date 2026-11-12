<x-app-layout>
    <div>
        <!-- Artist Profile Header (with integrated background Cover Banner & edge fades) -->
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 {{ $user->banner_url ? '' : 'border-b border-gray-200 dark:border-gray-700' }}">
            @if($user->banner_url)
                <!-- Artist Cover Banner in Header Background (Strictly Contained + Bottom & Lateral Fades) -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none select-none z-0">
                    <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                         style="background-image: url('{{ $user->banner_url }}'); transform: translate({{ $user->banner_crop_x ?? 0 }}%, {{ $user->banner_crop_y ?? 0 }}%) scale({{ $user->banner_crop_zoom ?? 1 }}); transform-origin: center center;">
                        <img src="{{ $user->banner_url }}"
                             alt=""
                             referrerpolicy="no-referrer"
                             class="w-full h-full object-cover object-center pointer-events-none select-none"
                             onerror="this.style.display='none'" />
                    </div>

                    <!-- Subtle overall atmospheric tint for text readability -->
                    <div class="absolute inset-0 bg-white/40 dark:bg-gray-900/50 pointer-events-none"></div>

                    <!-- Left lateral fade gradient -->
                    <div class="absolute inset-y-0 left-0 w-20 sm:w-36 md:w-56 lg:w-72 bg-gradient-to-r from-gray-50 via-gray-50/70 to-transparent dark:from-gray-900 dark:via-gray-900/75 dark:to-transparent pointer-events-none"></div>

                    <!-- Right lateral fade gradient -->
                    <div class="absolute inset-y-0 right-0 w-20 sm:w-36 md:w-56 lg:w-72 bg-gradient-to-l from-gray-50 via-gray-50/70 to-transparent dark:from-gray-900 dark:via-gray-900/75 dark:to-transparent pointer-events-none"></div>

                    <!-- Bottom fade gradient (smooth transition into the gallery section below without abrupt cuts) -->
                    <div class="absolute inset-x-0 bottom-0 h-28 sm:h-36 md:h-44 bg-gradient-to-t from-gray-50 via-gray-50/85 to-transparent dark:from-gray-900 dark:via-gray-900/90 dark:to-transparent pointer-events-none"></div>
                </div>
            @endif

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                
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

                <div class="flex flex-col md:flex-row md:items-center md:space-x-8 mb-4">
                    <!-- Avatar -->
                    <x-user-avatar :user="$user" size="2xl" class="border-4 border-white dark:border-gray-800 shadow-2xl mb-4 md:mb-0 mx-auto md:mx-0 relative z-10 shrink-0" />
                    
                    <!-- Info -->
                    <div class="text-center md:text-left flex-1 min-w-0">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
                            <div>
                                <div class="flex items-center justify-center md:justify-start gap-2">
                                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">
                                        {{ $user->name }}
                                    </h1>
                                    @if($user->is_verified)
                                        <span class="inline-flex items-center text-blue-500" title="Verified Artist" aria-label="Verified Artist">
                                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.25 14.5l-3.5-3.5 1.41-1.41 2.09 2.09 5.67-5.67 1.41 1.41-7.08 7.08z"/></svg>
                                        </span>
                                    @endif
                                </div>
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

                                <!-- Official Social Links Icons -->
                                @php
                                    $socials = is_array($user->social_links) ? $user->social_links : (json_decode($user->social_links ?? '[]', true) ?: []);
                                    $hasSocials = !empty(array_filter($socials));
                                @endphp
                                @if($hasSocials)
                                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mt-2 text-gray-400">
                                        @if(!empty($socials['artstation']))
                                            <a href="{{ $socials['artstation'] }}" target="_blank" rel="noopener noreferrer" class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="ArtStation">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M0 17.723l2.027 3.505h.001a2.424 2.424 0 002.164 1.334h13.435l-2.99-5.182H3.722L0 17.723zm19.345-5.32l-7.07-12.247H7.727l9.447 16.36 4.908-8.497a2.43 2.43 0 00-.737-3.616v-.001l-2-.001v.001zm-5.753 5.32l-3.08-5.334-3.078 5.334h6.158z"/></svg>
                                            </a>
                                        @endif
                                        @if(!empty($socials['instagram']))
                                            <a href="{{ $socials['instagram'] }}" target="_blank" rel="noopener noreferrer" class="p-1.5 rounded-lg text-gray-500 hover:text-pink-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Instagram">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                            </a>
                                        @endif
                                        @if(!empty($socials['behance']))
                                            <a href="{{ $socials['behance'] }}" target="_blank" rel="noopener noreferrer" class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Behance">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22 7h-7v-2h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-4.357 0-6.625-2.844-6.625-6.96 0-4.041 2.502-7.04 6.471-7.04 4.095 0 6.002 3.013 5.751 6.85h-9.45c.075 2.531 1.782 4.542 4.568 4.542 1.938 0 3.197-.847 3.738-1.748l.648 1.356zm-7.986-5.592h6.818c-.08-2.247-1.455-3.8-3.375-3.8-2.016 0-3.328 1.547-3.443 3.8zm-11.74-7.408h6.417c2.617 0 4.398 1.365 4.398 3.518 0 1.444-.805 2.597-2.083 3.094 1.743.511 2.656 1.944 2.656 3.727 0 2.535-2.032 4.161-4.887 4.161h-6.501v-14.5zm2.75 6.046h3.192c1.233 0 2.059-.579 2.059-1.635 0-1.077-.85-1.602-2.039-1.602h-3.212v3.237zm0 5.645h3.396c1.378 0 2.308-.659 2.308-1.849 0-1.21-.955-1.848-2.348-1.848h-3.356v3.697z"/></svg>
                                            </a>
                                        @endif
                                        @if(!empty($socials['twitter']))
                                            <a href="{{ $socials['twitter'] }}" target="_blank" rel="noopener noreferrer" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="X / Twitter">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                            </a>
                                        @endif
                                        @if(!empty($socials['linkedin']))
                                            <a href="{{ $socials['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="LinkedIn">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.72a1.63 1.63 0 0 0-1.63 1.63c0 .9.73 1.63 1.63 1.63s1.63-.73 1.63-1.63c0-.9-.73-1.63-1.63-1.63z"/></svg>
                                            </a>
                                        @endif
                                        @if(!empty($socials['website']))
                                            <a href="{{ $socials['website'] }}" target="_blank" rel="noopener noreferrer" class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Personal Website">
                                                <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap items-center justify-center md:justify-end gap-2">
                                <!-- Share Portfolio Button -->
                                <button type="button"
                                        @click="$dispatch('open-share-modal', { title: '{{ addslashes($user->name) }}\'s Portfolio', url: '{{ route('profile.public', $user->slug) }}' })"
                                        class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 transition shadow-sm"
                                        title="Share Portfolio">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                    Share
                                </button>

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

                        <!-- Commission Sheet & Services (Tarifas) -->
                        @php
                            $services = is_array($user->commission_services) ? $user->commission_services : (json_decode($user->commission_services ?? '[]', true) ?: []);
                            $activeServices = array_filter($services, fn($s) => !empty($s['title']) && ($s['is_active'] ?? true));
                        @endphp
                        @if(count($activeServices) > 0 && $user->commission_status !== 'closed')
                            <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-700/80" x-data="{ expanded: false }">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                            Commission Sheet & Rates
                                        </h4>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold">
                                            {{ count($activeServices) }} {{ Str::plural('service', count($activeServices)) }}
                                        </span>
                                    </div>
                                    <button @click="expanded = !expanded" type="button" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <span x-text="expanded ? 'Hide Rates ▲' : 'View Rates ▼'"></span>
                                    </button>
                                </div>

                                <div x-show="expanded" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-3.5 mt-2" style="display: none;">
                                    @foreach($activeServices as $service)
                                        <div class="p-3.5 rounded-xl bg-gray-50/90 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 flex flex-col justify-between shadow-xs">
                                            <div>
                                                <div class="flex items-start justify-between gap-2 mb-1.5">
                                                    <h5 class="font-bold text-xs text-gray-900 dark:text-white leading-snug">{{ $service['title'] }}</h5>
                                                    @if(!empty($service['price_starting_at']))
                                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 font-extrabold text-[11px] shrink-0">
                                                            From {{ $service['currency'] ?? 'USD' }} ${{ $service['price_starting_at'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!empty($service['description']))
                                                    <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-relaxed mb-2 whitespace-pre-line">{{ $service['description'] }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center justify-between text-[10px] text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200/60 dark:border-gray-700/60 mt-1.5">
                                                @if(!empty($service['turnaround_time']))
                                                    <span class="flex items-center gap-1 font-medium">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $service['turnaround_time'] }}
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif
                                                @if($user->getPublicEmail())
                                                    <a href="mailto:{{ $user->getPublicEmail() }}?subject={{ urlencode('Commission Inquiry: ' . $service['title']) }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline flex items-center gap-0.5">
                                                        Order / Inquire
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Artist's Gallery Section -->
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Collections Showcase Section (Pinterest Style 3-Image Cards) -->
                @if(isset($collections) && $collections->count() > 0 && !$selectedCollectionId)
                    <div class="mb-14">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Collection Boards</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Curated boards by {{ $user->name }}</p>
                            </div>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                {{ $collections->count() }} {{ Str::plural('board', $collections->count()) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($collections as $col)
                                <x-collection-card :collection="$col" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white shrink-0">Portfolio</h2>
                        @if($selectedCollectionId)
                            @php
                                $currentCollection = $collections->firstWhere('id', $selectedCollectionId);
                            @endphp
                            @if($currentCollection)
                                <a href="{{ $currentCollection->url }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 transition-colors"
                                   title="Open as dedicated Pinterest-style board">
                                    <span>View as Board</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @endif
                        @endif
                    </div>
                    
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

                @if ($artworks->count() > 0)
                    <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 [column-fill:_balance]">
                        @foreach ($artworks as $artwork)
                            <div class="break-inside-avoid mb-6">
                                <x-artwork-card :artwork="$artwork" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 shadow-sm">
                        <p class="text-gray-500 dark:text-gray-400">This artist has not published any artworks in this section yet.</p>
                    </div>
                @endif

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
