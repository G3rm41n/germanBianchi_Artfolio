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

            <!-- Country (Required - Interactive Searchable Combobox) -->
            <div x-data="{
                countries: [],
                loading: true,
                source: '',
                selectedCountry: '{{ old('country', $user->country ?? '') }}',
                searchQuery: '{{ old('country', $user->country ?? '') }}',
                isOpen: false,
                highlightedIndex: 0,

                get filteredCountries() {
                    if (!this.searchQuery || !this.searchQuery.trim()) {
                        return this.countries;
                    }
                    const q = this.searchQuery.toLowerCase().trim();
                    const startsWith = [];
                    const contains = [];

                    for (const c of this.countries) {
                        const lower = c.toLowerCase();
                        if (lower.startsWith(q)) {
                            startsWith.push(c);
                        } else if (lower.includes(q)) {
                            contains.push(c);
                        }
                    }

                    return [...startsWith, ...contains];
                },

                select(country) {
                    this.selectedCountry = country;
                    this.searchQuery = country;
                    this.isOpen = false;
                    this.highlightedIndex = 0;
                },

                openDropdown() {
                    if (!this.loading) {
                        this.isOpen = true;
                        this.highlightedIndex = 0;
                    }
                },

                closeDropdown() {
                    this.isOpen = false;
                    // If user typed something that matches an existing country exactly, keep it
                    const match = this.countries.find(c => c.toLowerCase() === this.searchQuery.toLowerCase().trim());
                    if (match) {
                        this.select(match);
                    } else if (this.selectedCountry) {
                        this.searchQuery = this.selectedCountry;
                    }
                },

                onArrowDown() {
                    if (!this.isOpen) {
                        this.openDropdown();
                        return;
                    }
                    if (this.highlightedIndex < this.filteredCountries.length - 1) {
                        this.highlightedIndex++;
                        this.scrollToHighlighted();
                    }
                },

                onArrowUp() {
                    if (this.highlightedIndex > 0) {
                        this.highlightedIndex--;
                        this.scrollToHighlighted();
                    }
                },

                onEnter() {
                    if (this.isOpen && this.filteredCountries.length > 0) {
                        const choice = this.filteredCountries[this.highlightedIndex] || this.filteredCountries[0];
                        if (choice) this.select(choice);
                    }
                },

                scrollToHighlighted() {
                    this.$nextTick(() => {
                        const el = document.getElementById('country-option-' + this.highlightedIndex);
                        if (el) el.scrollIntoView({ block: 'nearest' });
                    });
                },

                clearSelection() {
                    this.selectedCountry = '';
                    this.searchQuery = '';
                    this.isOpen = true;
                    this.$refs.countryInput.focus();
                },

                async init() {
                    // 1. Check browser session cache for instantaneous 0ms reload
                    const cached = sessionStorage.getItem('artfolio_countries_v2');
                    if (cached) {
                        try {
                            const parsed = JSON.parse(cached);
                            if (Array.isArray(parsed) && parsed.length >= 240) {
                                this.countries = parsed;
                                this.loading = false;
                                this.source = 'Session Cache';
                                return;
                            }
                        } catch (e) {}
                    }

                    // 2. Fetch from Open Dataset (CORS-enabled: *)
                    try {
                        const response = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
                        if (!response.ok) throw new Error('CDN returned HTTP ' + response.status);
                        const data = await response.json();

                        const list = data
                            .map(item => item?.name?.common)
                            .filter(name => typeof name === 'string' && name.trim().length > 0);

                        list.sort((a, b) => a.localeCompare(b));

                        this.countries = list;
                        this.loading = false;
                        this.source = 'Open Countries API';
                        sessionStorage.setItem('artfolio_countries_v2', JSON.stringify(list));
                        return;
                    } catch (errExternal) {
                        console.warn('External API call failed, falling back to internal gateway:', errExternal);
                    }

                    // 3. Fallback to Laravel Internal API Gateway
                    try {
                        const response = await fetch('{{ route('api.countries') }}');
                        if (!response.ok) throw new Error('Gateway returned HTTP ' + response.status);
                        const list = await response.json();

                        list.sort((a, b) => a.localeCompare(b));

                        this.countries = list;
                        this.loading = false;
                        this.source = 'Internal API Gateway';
                        sessionStorage.setItem('artfolio_countries_v2', JSON.stringify(list));
                        return;
                    } catch (errInternal) {
                        console.warn('Internal API gateway failed, activating local fallback:', errInternal);
                    }

                    // 4. Complete ISO 3166-1 250 Countries Fallback
                    this.countries = [
                        'Afghanistan', 'Åland Islands', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla',
                        'Antarctica', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
                        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan',
                        'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory',
                        'British Virgin Islands', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada',
                        'Cape Verde', 'Caribbean Netherlands', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China',
                        'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Cook Islands', 'Costa Rica',
                        'Croatia', 'Cuba', 'Curaçao', 'Cyprus', 'Czechia', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic',
                        'DR Congo', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia',
                        'Falkland Islands', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'French Guiana', 'French Polynesia',
                        'French Southern and Antarctic Lands', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece',
                        'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guyana',
                        'Haiti', 'Heard Island and McDonald Islands', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia',
                        'Iran', 'Iraq', 'Ireland', 'Isle of Man', 'Israel', 'Italy', 'Ivory Coast', 'Jamaica', 'Japan', 'Jersey', 'Jordan',
                        'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho',
                        'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Madagascar', 'Malawi', 'Malaysia',
                        'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico',
                        'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar',
                        'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria',
                        'Niue', 'Norfolk Island', 'North Korea', 'North Macedonia', 'Northern Mariana Islands', 'Norway', 'Oman',
                        'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines',
                        'Pitcairn Islands', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Réunion', 'Romania', 'Russia', 'Rwanda',
                        'Saint Barthélemy', 'Saint Helena, Ascension and Tristan da Cunha', 'Saint Kitts and Nevis', 'Saint Lucia',
                        'Saint Martin', 'Saint Pierre and Miquelon', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
                        'São Tomé and Príncipe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore',
                        'Sint Maarten', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia',
                        'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Svalbard and Jan Mayen', 'Sweden',
                        'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tokelau',
                        'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Türkiye', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu',
                        'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States',
                        'United States Minor Outlying Islands', 'United States Virgin Islands', 'Uruguay', 'Uzbekistan', 'Vanuatu',
                        'Vatican City', 'Venezuela', 'Vietnam', 'Wallis and Futuna', 'Western Sahara', 'Yemen', 'Zambia', 'Zimbabwe'
                    ];
                    this.loading = false;
                    this.source = 'Local Fallback';
                }
            }"
            @click.outside="closeDropdown()"
            class="relative">
                <label for="country_search_input" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Country <span class="text-red-500">*</span>
                </label>

                <!-- Hidden Input bound to form submission -->
                <input type="hidden" name="country" :value="selectedCountry" required>

                <div class="relative">
                    <!-- Searchable Input Field -->
                    <input type="text"
                           id="country_search_input"
                           x-ref="countryInput"
                           x-model="searchQuery"
                           :disabled="loading"
                           @focus="openDropdown()"
                           @click="openDropdown()"
                           @input="openDropdown()"
                           @keydown.arrow-down.prevent="onArrowDown()"
                           @keydown.arrow-up.prevent="onArrowUp()"
                           @keydown.enter.prevent="onEnter()"
                           @keydown.escape="closeDropdown()"
                           autocomplete="off"
                           :placeholder="loading ? 'Connecting to Open Countries API...' : 'Type to search country (e.g. Argentina, Canada)...'"
                           class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-4 pr-10 shadow-sm disabled:opacity-60 transition-all">

                    <!-- Right Action Icons (Clear / Arrow / Spinner) -->
                    <div class="absolute right-3.5 top-3.5 flex items-center gap-1.5 text-gray-400">
                        <!-- Spinner while loading -->
                        <div x-show="loading" class="animate-spin h-4 w-4 text-indigo-500">
                            <svg fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>

                        <!-- Clear button if text entered -->
                        <button type="button"
                                x-show="searchQuery && !loading"
                                @click.stop="clearSelection()"
                                class="hover:text-gray-600 dark:hover:text-gray-200 p-0.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                                title="Clear country">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <!-- Chevron indicator -->
                        <button type="button"
                                @click.stop="isOpen ? closeDropdown() : openDropdown()"
                                class="hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="isOpen ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <!-- Autocomplete Dropdown List -->
                    <div x-show="isOpen && !loading"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         style="display: none;"
                         class="absolute z-50 mt-1.5 w-full bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto py-2 focus:outline-none">

                        <!-- Filtered items count summary -->
                        <div class="px-4 py-1.5 text-[10px] uppercase font-bold tracking-wider text-gray-400 border-b border-gray-100 dark:border-gray-700/60 mb-1 flex items-center justify-between">
                            <span x-text="filteredCountries.length + ' matching countries'"></span>
                            <span class="text-[9px] lowercase font-normal">Use ↑ ↓ & Enter</span>
                        </div>

                        <!-- No results message -->
                        <template x-if="filteredCountries.length === 0">
                            <div class="px-4 py-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                <p class="font-semibold">No countries found</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Try searching with a different spelling.</p>
                            </div>
                        </template>

                        <!-- Country Options List -->
                        <template x-for="(c, index) in filteredCountries" :key="c">
                            <div :id="'country-option-' + index"
                                 @click="select(c)"
                                 @mouseenter="highlightedIndex = index"
                                 :class="{
                                     'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-semibold': index === highlightedIndex,
                                     'text-gray-700 dark:text-gray-200': index !== highlightedIndex
                                 }"
                                 class="px-4 py-2 text-xs sm:text-sm cursor-pointer flex items-center justify-between transition-colors">
                                <span x-text="c"></span>
                                <template x-if="selectedCountry === c">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </template>
                            </div>
                        </template>
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

