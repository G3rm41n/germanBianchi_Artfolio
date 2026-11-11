<section>
    <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Profile Information
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Manage your personal appearance, visual identity, and location displayed on your public portfolio.
        </p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')
        <input type="hidden" name="active_tab" value="profile">

        <!-- Avatar & Banner Visual Identity Grid -->
        <div class="space-y-6">
            <!-- 1. Avatar with Interactive Pan & Zoom Modal -->
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
                    this.closeModal();
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
                },

                onWheel(e) {
                    if (e.deltaY < 0) this.zoomIn();
                    else this.zoomOut();
                }
            }">
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Profile Picture
                </label>

                <!-- Hidden inputs for persisting coordinates & URL -->
                <input type="hidden" name="avatar_url" :value="avatarUrl">
                <input type="hidden" name="crop_x" :value="cropX">
                <input type="hidden" name="crop_y" :value="cropY">
                <input type="hidden" name="crop_zoom" :value="cropZoom">

                <div class="flex items-center space-x-6 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60">
                    <!-- Live circular preview -->
                    <div class="relative w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 border-2 border-indigo-500 shadow-md flex-shrink-0 cursor-pointer"
                         @click="openModal()" title="Click to edit picture">
                        <template x-if="avatarUrl">
                            <div class="w-full h-full bg-cover bg-center bg-no-repeat"
                                 :style="`background-image: url('${avatarUrl}'); transform: translate(${cropX}%, ${cropY}%) scale(${cropZoom});`">
                            </div>
                        </template>
                        <template x-if="!avatarUrl">
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gradient-to-tr from-gray-700 to-gray-800 text-white font-bold text-2xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </template>
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="openModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                                <span x-text="avatarUrl ? 'Adjust Framing / Change' : 'Upload Avatar'"></span>
                            </button>
                            <button type="button" x-show="avatarUrl" @click="removeAvatar()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-700 hover:bg-red-100 hover:text-red-700 dark:hover:bg-red-950/40 dark:hover:text-red-400 text-gray-700 dark:text-gray-300 transition">
                                Remove
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Square image recommended. Supports pan & zoom positioning.
                        </p>
                    </div>
                </div>

                <!-- Modal Dialog -->
                <div x-show="isModalOpen" 
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     style="display: none;" 
                     @keydown.escape.window="closeModal()">
                    <div x-show="isModalOpen" 
                         x-transition:enter="ease-out duration-200" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-150" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
                         @click="closeModal()"></div>

                    <div class="flex min-h-full items-center justify-center p-4">
                        <div x-show="isModalOpen"
                             x-transition:enter="ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="relative rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-800"
                             @click.stop
                             @mouseup.window="onMouseUp()"
                             @touchend.window="onMouseUp()"
                             @mousemove.window="onMouseMove($event)"
                             @touchmove.window="onMouseMove($event)">

                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Adjust Avatar</h3>
                                <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Image URL</label>
                                    <input type="url" x-model.lazy="tempUrl" placeholder="https://example.com/avatar.jpg" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                                </div>

                                <div class="bg-gray-950 p-6 rounded-2xl flex flex-col items-center justify-center relative select-none border border-gray-800">
                                    <div class="relative w-48 h-48 rounded-full overflow-hidden bg-gray-900 border-4 border-indigo-500 cursor-move select-none"
                                         @mousedown.prevent="onMouseDown"
                                         @touchstart.prevent="onMouseDown"
                                         @wheel.prevent="onWheel">
                                        <template x-if="tempUrl && !imageError">
                                            <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                                                 :style="`background-image: url('${tempUrl}'); transform: translate(${tempCropX}%, ${tempCropY}%) scale(${tempCropZoom});`"></div>
                                        </template>
                                        <template x-if="!tempUrl || imageError">
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 text-xs">
                                                <span>No image provided</span>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2">Click & drag to reposition</p>
                                </div>

                                <div x-show="tempUrl" class="space-y-1">
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Zoom</span>
                                        <button type="button" @click="resetCrop()" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Reset</button>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="range" x-model.number="tempCropZoom" min="0.5" max="3.0" step="0.05" class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg accent-indigo-600 cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300">Cancel</button>
                                <button type="button" @click="applyChanges()" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Cover Banner URL -->
            <div x-data="{ bannerUrl: '{{ old('banner_url', $user->banner_url) }}' }">
                <label for="banner_url" class="block text-sm font-bold text-gray-900 dark:text-white">
                    Cover Banner Image
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                    A panoramic banner displayed across the top of your public artist portfolio (recommended 1920x640px).
                </p>

                <div class="space-y-3">
                    <input type="url"
                           id="banner_url"
                           name="banner_url"
                           x-model="bannerUrl"
                           placeholder="https://images.unsplash.com/photo-..."
                           class="block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">

                    <!-- Live Banner Preview Card -->
                    <div class="h-32 sm:h-40 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 relative bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                        <template x-if="bannerUrl">
                            <img :src="bannerUrl" alt="Banner preview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!bannerUrl">
                            <div class="text-center p-4 text-gray-400 dark:text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-1 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs">No banner set (Default portfolio header will be used)</span>
                            </div>
                        </template>
                    </div>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('banner_url')" />
            </div>
        </div>

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-sm font-bold text-gray-900 dark:text-white">
                Full Display Name <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name', $user->name) }}"
                   required
                   class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Professional Headline -->
        <div>
            <label for="headline" class="block text-sm font-bold text-gray-900 dark:text-white">
                Professional Headline
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                Your primary artistic role (e.g., Senior Concept Artist, 3D Character Modeler, Illustrator).
            </p>
            <input type="text"
                   id="headline"
                   name="headline"
                   maxlength="100"
                   value="{{ old('headline', $user->headline) }}"
                   placeholder="e.g. Senior Concept Artist & Illustrator"
                   class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <x-input-error class="mt-2" :messages="$errors->get('headline')" />
        </div>

        <!-- Location: City and Reactive Country Autocomplete -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- City -->
            <div>
                <label for="city" class="block text-sm font-bold text-gray-900 dark:text-white">
                    City
                </label>
                <input type="text"
                       id="city"
                       name="city"
                       maxlength="100"
                       value="{{ old('city', $user->city) }}"
                       placeholder="e.g. Buenos Aires, Tokyo, Montreal"
                       class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            <!-- Country with Reactive 250 Countries Autocomplete -->
            <div x-data="{
                countries: [],
                searchQuery: '{{ old('country', $user->country ?? '') }}',
                selectedCountry: '{{ old('country', $user->country ?? '') }}',
                isOpen: false,
                loading: true,
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
                        const el = document.getElementById('profile-country-option-' + this.highlightedIndex);
                        if (el) el.scrollIntoView({ block: 'nearest' });
                    });
                },

                async init() {
                    const cached = sessionStorage.getItem('artfolio_countries_v2');
                    if (cached) {
                        try {
                            const parsed = JSON.parse(cached);
                            if (Array.isArray(parsed) && parsed.length >= 240) {
                                this.countries = parsed;
                                this.loading = false;
                                return;
                            }
                        } catch (e) {}
                    }

                    try {
                        const response = await fetch('{{ route('api.countries') }}');
                        if (response.ok) {
                            const list = await response.json();
                            list.sort((a, b) => a.localeCompare(b));
                            this.countries = list;
                            this.loading = false;
                            sessionStorage.setItem('artfolio_countries_v2', JSON.stringify(list));
                            return;
                        }
                    } catch (e) {}

                    this.countries = [
                        'Argentina', 'Australia', 'Brazil', 'Canada', 'Chile', 'Colombia', 'France',
                        'Germany', 'Italy', 'Japan', 'Mexico', 'Spain', 'United Kingdom', 'United States'
                    ];
                    this.loading = false;
                }
            }"
            @click.outside="closeDropdown()"
            class="relative">
                <label for="profile_country_input" class="block text-sm font-bold text-gray-900 dark:text-white">
                    Country
                </label>

                <!-- Hidden input submitted with the form -->
                <input type="hidden" name="country" :value="selectedCountry">

                <div class="relative mt-1">
                    <input type="text"
                           id="profile_country_input"
                           x-ref="countryInput"
                           x-model="searchQuery"
                           @focus="openDropdown()"
                           @input="isOpen = true; highlightedIndex = 0;"
                           @keydown.down.prevent="onArrowDown()"
                           @keydown.up.prevent="onArrowUp()"
                           @keydown.enter.prevent="onEnter()"
                           @keydown.escape="closeDropdown()"
                           autocomplete="off"
                           placeholder="Type to search country..."
                           class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm pl-4 pr-10">

                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Dropdown -->
                <div x-show="isOpen && !loading"
                     x-transition
                     class="absolute z-30 mt-1.5 w-full max-h-60 overflow-y-auto rounded-xl bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700 py-1"
                     style="display: none;">
                    <template x-for="(country, idx) in filteredCountries" :key="country">
                        <button type="button"
                                :id="'profile-country-option-' + idx"
                                @click="select(country)"
                                @mouseenter="highlightedIndex = idx"
                                :class="highlightedIndex === idx ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-700 dark:text-gray-300'"
                                class="w-full text-left px-4 py-2 text-xs transition-colors flex items-center justify-between">
                            <span x-text="country"></span>
                            <span x-show="selectedCountry === country" class="text-indigo-600 dark:text-indigo-400">✓</span>
                        </button>
                    </template>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/60">
            <x-primary-button class="rounded-xl px-6 py-2.5">
                {{ __('Save Changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated' && session('active_tab') === 'profile')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>

