<x-onboarding-layout :currentStep="1" title="Personal Profile Setup">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white font-['Outfit'] tracking-tight">
            Welcome to Artfolio, {{ $user->name }}!
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Let's start with the basics. Add your headline, your location, and your avatar so other artists and recruiters can recognize you.
        </p>
    </div>

    <form method="POST" action="{{ route('onboarding.step1.save') }}" class="space-y-6">
        @csrf

        <!-- Avatar Section with Discord-style Pan & Zoom Modal -->
        <div x-data="{
            avatarUrl: '{{ old('avatar_url', $user->avatar_url) }}',
            cropX: {{ old('crop_x', $user->crop_x ?? 0) }},
            cropY: {{ old('crop_y', $user->crop_y ?? 0) }},
            cropZoom: {{ old('crop_zoom', $user->crop_zoom ?? 1) }},

            isModalOpen: false,
            tempUrl: '',
            tempCropX: 0,
            tempCropY: 0,
            tempCropZoom: 1,

            isDragging: false,
            startX: 0,
            startY: 0,
            startCropX: 0,
            startCropY: 0,
            imageError: false,

            openModal() {
                this.tempUrl = this.avatarUrl || '';
                this.tempCropX = parseFloat(this.cropX) || 0;
                this.tempCropY = parseFloat(this.cropY) || 0;
                this.tempCropZoom = parseFloat(this.cropZoom) || 1;
                this.imageError = false;
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                this.isDragging = false;
            },

            applyChanges() {
                this.avatarUrl = this.tempUrl ? this.tempUrl.trim() : '';
                this.cropX = this.tempCropX;
                this.cropY = this.tempCropY;
                this.cropZoom = this.tempCropZoom;
                this.closeModal();
            },

            removeAvatar() {
                this.avatarUrl = '';
                this.tempUrl = '';
                this.cropX = 0;
                this.cropY = 0;
                this.cropZoom = 1;
            },

            resetCrop() {
                this.tempCropX = 0;
                this.tempCropY = 0;
                this.tempCropZoom = 1;
            },

            onMouseDown(e) {
                if (!this.tempUrl || this.imageError) return;
                this.isDragging = true;
                this.startX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                this.startY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                this.startCropX = parseFloat(this.tempCropX) || 0;
                this.startCropY = parseFloat(this.tempCropY) || 0;
            },

            onMouseMove(e) {
                if (!this.isDragging) return;
                const clientX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                const clientY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                const dx = clientX - this.startX;
                const dy = clientY - this.startY;
                const containerSize = 192;
                const zoomFactor = Math.max(parseFloat(this.tempCropZoom) || 1, 0.5);
                this.tempCropX = Math.round(this.startCropX + ((dx / containerSize) * 100) / zoomFactor);
                this.tempCropY = Math.round(this.startCropY + ((dy / containerSize) * 100) / zoomFactor);
            },

            onMouseUp() {
                this.isDragging = false;
            },

            zoomIn() {
                let next = parseFloat((parseFloat(this.tempCropZoom) + 0.1).toFixed(2));
                this.tempCropZoom = Math.min(next, 3.0);
            },

            zoomOut() {
                let next = parseFloat((parseFloat(this.tempCropZoom) - 0.1).toFixed(2));
                this.tempCropZoom = Math.max(next, 0.5);
            }
        }"
        @mouseup.window="onMouseUp"
        @touchend.window="onMouseUp"
        @mousemove.window="onMouseMove"
        @touchmove.window="onMouseMove"
        @keydown.escape.window="if (isModalOpen) closeModal()"
        class="p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/80 dark:border-gray-700/60">

            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                Profile Avatar
            </label>

            <!-- Hidden Inputs to submit with form -->
            <input type="hidden" name="avatar_url" :value="avatarUrl">
            <input type="hidden" name="crop_x" :value="cropX">
            <input type="hidden" name="crop_y" :value="cropY">
            <input type="hidden" name="crop_zoom" :value="cropZoom">

            <div class="flex items-center gap-5">
                <!-- Avatar Preview -->
                <div @click="openModal()"
                     class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 shadow-sm flex-shrink-0 cursor-pointer border-2 border-indigo-500/40 hover:border-indigo-600 transition-all group select-none"
                     title="Click to customize avatar">
                     <template x-if="avatarUrl">
                         <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none transition-transform"
                              :style="`background-image: url('${avatarUrl}'); transform: translate(${cropX}%, ${cropY}%) scale(${cropZoom});`">
                         </div>
                     </template>
                     <template x-if="!avatarUrl">
                        <div class="w-full h-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-2xl sm:text-3xl shadow-inner">
                            {{ mb_strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                     </template>
                     <!-- Hover Edit Indicator -->
                     <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <span class="text-[9px] font-bold uppercase tracking-wider">Edit</span>
                     </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="openModal()"
                                class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Choose Avatar
                        </button>
                        <button type="button"
                                x-show="avatarUrl"
                                @click="removeAvatar()"
                                class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                            Remove
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Paste a web image URL and pan/zoom to position your avatar.
                    </p>
                </div>
            </div>

            <!-- Avatar Pan/Zoom Modal -->
            <template x-teleport="body">
                <div x-show="isModalOpen"
                     style="display: none;"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div @click.away="closeModal()"
                         class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full p-6 text-gray-900 dark:text-gray-100 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold">Edit Profile Picture</h3>
                            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- URL Input -->
                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                Image Web URL
                            </label>
                            <input type="url"
                                   x-model="tempUrl"
                                   @input="imageError = false"
                                   placeholder="https://images.unsplash.com/..."
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Interactive Crop Canvas (Discord style) -->
                        <div class="flex flex-col items-center justify-center py-4 bg-gray-100 dark:bg-gray-900/60 rounded-2xl mb-4 select-none">
                            <div class="relative w-48 h-48 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 shadow-inner border-2 border-indigo-500 cursor-grab active:cursor-grabbing"
                                 @mousedown="onMouseDown"
                                 @touchstart="onMouseDown">
                                <template x-if="tempUrl && !imageError">
                                    <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                                         :style="`background-image: url('${tempUrl}'); transform: translate(${tempCropX}%, ${tempCropY}%) scale(${tempCropZoom});`">
                                    </div>
                                </template>
                                <template x-if="!tempUrl || imageError">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 text-xs text-center p-4">
                                        <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Enter a valid image URL to preview</span>
                                    </div>
                                </template>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-2">Click and drag inside circle to center photo</p>
                        </div>

                        <!-- Zoom Slider & Controls -->
                        <div class="mb-6 space-y-2">
                            <div class="flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-400">
                                <span>Zoom</span>
                                <span x-text="`${Math.round(tempCropZoom * 100)}%`"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="zoomOut()" class="p-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <input type="range" min="0.5" max="3.0" step="0.05" x-model.number="tempCropZoom" class="w-full accent-indigo-600">
                                <button type="button" @click="zoomIn()" class="p-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                                <button type="button" @click="resetCrop()" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline ml-1">Reset</button>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 transition-colors">
                                Cancel
                            </button>
                            <button type="button" @click="applyChanges()" class="px-5 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                                Apply Avatar
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Headline Input -->
        <div>
            <label for="headline" class="block text-sm font-bold text-gray-900 dark:text-white">
                Headline / Professional Title <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                A concise title summarizing what you do (e.g. "Senior 3D Artist", "Digital Painter & Illustrator", "Concept Artist").
            </p>
            <input type="text"
                   id="headline"
                   name="headline"
                   value="{{ old('headline', $user->headline) }}"
                   required
                   maxlength="100"
                   placeholder="e.g. Concept Artist & Character Designer"
                   class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 shadow-sm">
            @error('headline')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Location: City & Country -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- City (Optional) -->
            <div>
                <label for="city" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    City <span class="text-xs font-normal text-gray-400">(Optional)</span>
                </label>
                <input type="text"
                       id="city"
                       name="city"
                       value="{{ old('city', $user->city) }}"
                       maxlength="100"
                       placeholder="e.g. San Francisco, Tokyo, Berlin"
                       class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 shadow-sm">
                @error('city')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country (Required - Loaded dynamically from REST Countries API) -->
            <div x-data="{
                countries: [],
                loading: true,
                source: '',
                selectedCountry: '{{ old('country', $user->country ?? '') }}',

                async init() {
                    // 1. Check browser cache for instantaneous load
                    const cached = sessionStorage.getItem('artfolio_countries');
                    if (cached) {
                        try {
                            this.countries = JSON.parse(cached);
                            this.loading = false;
                            this.source = 'Session Cache';
                            return;
                        } catch (e) {}
                    }

                    // 2. Fetch live from REST Countries API
                    try {
                        const response = await fetch('https://restcountries.com/v3.1/all?fields=name');
                        if (!response.ok) throw new Error('API returned status ' + response.status);
                        const data = await response.json();

                        // Extract common country names and sort alphabetically
                        const list = data
                            .map(item => item?.name?.common)
                            .filter(name => typeof name === 'string' && name.trim().length > 0);

                        list.sort((a, b) => a.localeCompare(b));

                        this.countries = list;
                        this.loading = false;
                        this.source = 'REST Countries API';

                        // Save in sessionStorage to avoid repeated API requests
                        sessionStorage.setItem('artfolio_countries', JSON.stringify(list));
                    } catch (error) {
                        console.warn('REST Countries API unreachable, activating local fallback:', error);
                        // 3. Graceful local fallback (top international countries sorted alphabetically)
                        this.countries = [
                            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia',
                            'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados',
                            'Belarus', 'Belgium', 'Belize', 'Benin', 'Bolivia', 'Bosnia and Herzegovina',
                            'Botswana', 'Brazil', 'Bulgaria', 'Canada', 'Chile', 'China', 'Colombia',
                            'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark',
                            'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Estonia', 'Finland',
                            'France', 'Georgia', 'Germany', 'Greece', 'Guatemala', 'Honduras', 'Hungary',
                            'Iceland', 'India', 'Indonesia', 'Ireland', 'Israel', 'Italy', 'Jamaica',
                            'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kuwait', 'Latvia', 'Lebanon',
                            'Lithuania', 'Luxembourg', 'Malaysia', 'Malta', 'Mexico', 'Monaco', 'Morocco',
                            'Netherlands', 'New Zealand', 'Nicaragua', 'Nigeria', 'Norway', 'Panama',
                            'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania',
                            'Saudi Arabia', 'Serbia', 'Singapore', 'Slovakia', 'Slovenia', 'South Africa',
                            'South Korea', 'Spain', 'Sweden', 'Switzerland', 'Taiwan', 'Thailand',
                            'Turkey', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States',
                            'Uruguay', 'Venezuela', 'Vietnam', 'Other'
                        ];
                        this.loading = false;
                        this.source = 'Local Fallback';
                    }
                }
            }">
                <label for="country" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Country <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <select id="country"
                            name="country"
                            x-model="selectedCountry"
                            :disabled="loading"
                            required
                            class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 shadow-sm disabled:opacity-60 transition-opacity">
                        <option value="" disabled :selected="!selectedCountry">
                            <span x-text="loading ? 'Connecting to REST Countries API...' : 'Select your country...'">Select your country...</span>
                        </option>

                        <!-- Dynamic alphabetically sorted country options -->
                        <template x-for="c in countries" :key="c">
                            <option :value="c" x-text="c" :selected="selectedCountry === c"></option>
                        </template>

                        <!-- If user already has a saved country not in list yet during initial load -->
                        @if ($user->country)
                            <option value="{{ $user->country }}" class="hidden" x-show="!countries.includes('{{ $user->country }}')">
                                {{ $user->country }}
                            </option>
                        @endif
                    </select>

                    <!-- Small Loading Spinner inside Select when fetching -->
                    <div x-show="loading" class="absolute right-8 top-3.5 pointer-events-none">
                        <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Live API Status Indicator -->
                <div class="mt-1.5 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full" :class="loading ? 'bg-amber-400 animate-pulse' : 'bg-emerald-500'"></span>
                        <span x-text="loading ? 'Loading global country list...' : countries.length + ' countries loaded (A-Z)'"></span>
                    </span>
                    <span class="font-mono text-[10px] text-gray-400" x-text="source ? 'Source: ' + source : ''"></span>
                </div>

                @error('country')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Next Step Button -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <span class="text-xs text-gray-400">Step 1 of 3</span>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <span>Continue to Professional Profile</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</x-onboarding-layout>

