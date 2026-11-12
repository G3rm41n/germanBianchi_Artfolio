<x-app-layout>
    <div x-data="{ 
            viewMode: '{{ $viewMode }}',
            skillsModalOpen: false,
            modalFilter: '',
            matchMode: '{{ $matchMode }}',
            selectedSkills: {{ json_encode($selectedSkills) }},
            selectedTools: {{ json_encode($selectedTools) }},

            toggleSkill(name) {
                const idx = this.selectedSkills.indexOf(name);
                if (idx > -1) {
                    this.selectedSkills.splice(idx, 1);
                } else {
                    this.selectedSkills.push(name);
                }
            },

            toggleTool(name) {
                const idx = this.selectedTools.indexOf(name);
                if (idx > -1) {
                    this.selectedTools.splice(idx, 1);
                } else {
                    this.selectedTools.push(name);
                }
            },

            clearAllSelections() {
                this.selectedSkills = [];
                this.selectedTools = [];
            },

            totalSelected() {
                return this.selectedSkills.length + this.selectedTools.length;
            }
         }" 
         class="py-8 bg-gray-50 dark:bg-gray-950 min-h-screen text-gray-900 dark:text-gray-100 transition-colors">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. Scope Tabs: Artworks vs Artists (Artstation style) -->
            <div class="flex items-center justify-center sm:justify-start border-b border-gray-200 dark:border-gray-800">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <!-- Artworks Scope Tab -->
                    <a href="{{ route('home') }}" 
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 whitespace-nowrap py-3 px-1 border-b-2 font-bold text-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Artworks
                    </a>

                    <!-- Artists Scope Tab (Active) -->
                    <a href="{{ route('artists.index') }}" 
                       class="border-indigo-500 text-indigo-600 dark:text-indigo-400 whitespace-nowrap py-3 px-1 border-b-2 font-black text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Artists
                        <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                            {{ $artists->total() }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- 2. Search & Filter Bar with Reorganized Skills Selector -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-5 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                <form method="GET" action="{{ route('artists.index') }}" id="artistFilterForm" class="space-y-4">
                    <input type="hidden" name="view" :value="viewMode">
                    <input type="hidden" name="match" :value="matchMode">

                    <!-- Dynamic Hidden Inputs for Selected Skills & Software -->
                    <template x-for="s in selectedSkills" :key="'skill-' + s">
                        <input type="hidden" name="skills[]" :value="s">
                    </template>
                    <template x-for="t in selectedTools" :key="'tool-' + t">
                        <input type="hidden" name="tools[]" :value="t">
                    </template>

                    <!-- Top Row: View Mode + Text Filters + Apply -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                        <!-- View Mode Switcher -->
                        <div class="flex items-center space-x-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-2xl w-fit shrink-0">
                            <button type="button" 
                                    @click="viewMode = 'grid'" 
                                    :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                <span>Grid view</span>
                            </button>

                            <button type="button" 
                                    @click="viewMode = 'list'" 
                                    :class="viewMode === 'list' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                <span>List view</span>
                            </button>
                        </div>

                        <!-- Filter Inputs Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 flex-1">
                            <!-- Name / Keyword -->
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Name or bio..." 
                                       class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                            </div>

                            <!-- City -->
                            <div>
                                <input type="text" 
                                       name="city" 
                                       value="{{ $city }}" 
                                       placeholder="City..." 
                                       class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                            </div>

                            <!-- Country -->
                            <div>
                                <input type="text" 
                                       name="country" 
                                       value="{{ $country }}" 
                                       placeholder="Country..." 
                                       class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                            </div>

                            <!-- Availability Dropdown -->
                            <div>
                                <select name="availability" 
                                        class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 shadow-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Availability: Any</option>
                                    <option value="commissions_open" {{ $availability === 'commissions_open' ? 'selected' : '' }}>Commissions Open</option>
                                    <option value="commissions_consult" {{ $availability === 'commissions_consult' ? 'selected' : '' }}>Upon Request</option>
                                    <option value="freelance" {{ $availability === 'freelance' ? 'selected' : '' }}>Freelance</option>
                                    <option value="full-time" {{ $availability === 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                    <option value="contract" {{ $availability === 'contract' ? 'selected' : '' }}>Contract</option>
                                </select>
                            </div>
                        </div>

                        <!-- Apply & Reset Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit" 
                                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                                Apply
                            </button>
                            @if($activeFiltersCount > 0)
                                <a href="{{ route('artists.index', ['view' => $viewMode]) }}" 
                                   class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-xs transition-colors"
                                   title="Reset all filters">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Row: Replaced "FEATURED" with the Skills & Toolkit Trigger Bar -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800/80 flex flex-wrap items-center gap-2">
                        <!-- Pop-up Trigger Button -->
                        <button type="button" 
                                @click="skillsModalOpen = true" 
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border transition-all text-xs font-bold shadow-xs select-none"
                                :class="totalSelected() > 0 ? 'bg-indigo-600 border-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            <span>Skills & Software</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                                  :class="totalSelected() > 0 ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                  x-text="totalSelected()">
                            </span>
                        </button>

                        <!-- Match Mode Selector (Or / All) -->
                        <div class="inline-flex items-center p-0.5 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 text-xs font-bold shrink-0"
                             title="Match Condition: Or (at least one) or All (every single one)">
                            <button type="button" 
                                    @click="matchMode = 'or'" 
                                    :class="matchMode === 'or' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                                    class="px-2.5 py-1 rounded-lg transition-all">
                                Or
                            </button>
                            <button type="button" 
                                    @click="matchMode = 'all'" 
                                    :class="matchMode === 'all' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                                    class="px-2.5 py-1 rounded-lg transition-all">
                                All
                            </button>
                        </div>

                        <!-- Active Selected Pills Preview with direct removal -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <template x-for="skill in selectedSkills" :key="'active-sk-' + skill">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    <span x-text="skill"></span>
                                    <button type="button" @click="toggleSkill(skill)" class="hover:text-red-500 focus:outline-hidden" title="Remove">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </span>
                            </template>

                            <template x-for="tool in selectedTools" :key="'active-tl-' + tool">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    <span x-text="tool"></span>
                                    <button type="button" @click="toggleTool(tool)" class="hover:text-red-500 focus:outline-hidden" title="Remove">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </span>
                            </template>

                            <span x-show="totalSelected() === 0" class="text-xs text-gray-400 dark:text-gray-500 ml-1">
                                Click to select disciplines and software tools to filter by...
                            </span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. Skills & Software Selection Modal (Pop-up style with Backdrop) -->
            <div x-cloak
                 x-show="skillsModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog" 
                 aria-modal="true">
                
                <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                    <!-- Backdrop Blur Overlay -->
                    <div x-show="skillsModalOpen" 
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="skillsModalOpen = false" 
                         class="fixed inset-0 bg-black/70 backdrop-blur-xs transition-opacity"></div>

                    <!-- Modal Dialog Card -->
                    <div x-show="skillsModalOpen"
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-800">
                        
                        <!-- Modal Header -->
                        <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                    Artistic Disciplines & Software Toolkit
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Select one or more options to filter artists specializing in these fields.
                                </p>
                            </div>
                            <button type="button" 
                                    @click="skillsModalOpen = false" 
                                    class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Quick Filter Search -->
                        <div class="px-6 pt-4">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" 
                                       x-model="modalFilter"
                                       placeholder="Quick filter disciplines or tools..."
                                       class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white pl-9 pr-3 py-2 shadow-xs focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Match Mode Condition Selector (Or / All) -->
                        <div class="px-6 pt-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                                        <span>Matching Logic:</span>
                                        <span class="text-indigo-600 dark:text-indigo-400 font-black uppercase text-[11px]" x-text="matchMode === 'all' ? 'All (Every single one)' : 'Or (At least one)'"></span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="matchMode === 'all' ? 'Artists must specialize in ALL of your selected disciplines and tools.' : 'Artists with AT LEAST ONE of your selected disciplines or tools will match.'"></p>
                                </div>

                                <div class="inline-flex items-center p-0.5 rounded-xl bg-gray-200/80 dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600 text-xs font-bold shrink-0 self-start sm:self-center">
                                    <button type="button" 
                                            @click="matchMode = 'or'" 
                                            :class="matchMode === 'or' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'"
                                            class="px-3 py-1 rounded-lg transition-all">
                                        Or
                                    </button>
                                    <button type="button" 
                                            @click="matchMode = 'all'" 
                                            :class="matchMode === 'all' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'"
                                            class="px-3 py-1 rounded-lg transition-all">
                                        All
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Body (Two Organized Sections) -->
                        <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto">
                            
                            <!-- Section 1: Artistic Disciplines & Specialties -->
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2.5">
                                    Artistic Disciplines & Specialties
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($availableDisciplines as $discipline)
                                        <button type="button" 
                                                x-show="!modalFilter || '{{ strtolower($discipline) }}'.includes(modalFilter.toLowerCase())"
                                                @click="toggleSkill('{{ addslashes($discipline) }}')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border flex items-center gap-1.5"
                                                :class="selectedSkills.includes('{{ addslashes($discipline) }}') 
                                                    ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' 
                                                    : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border-gray-200/80 dark:border-gray-700 text-gray-700 dark:text-gray-300'">
                                            <span x-show="selectedSkills.includes('{{ addslashes($discipline) }}')">✓</span>
                                            <span>{{ $discipline }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Section 2: Software & Production Toolkit -->
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2.5">
                                    Software & Production Toolkit
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($availableSoftware as $software)
                                        <button type="button" 
                                                x-show="!modalFilter || '{{ strtolower($software) }}'.includes(modalFilter.toLowerCase())"
                                                @click="toggleTool('{{ addslashes($software) }}')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border flex items-center gap-1.5"
                                                :class="selectedTools.includes('{{ addslashes($software) }}') 
                                                    ? 'bg-purple-600 border-purple-600 text-white shadow-xs' 
                                                    : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border-gray-200/80 dark:border-gray-700 text-gray-700 dark:text-gray-300'">
                                            <span x-show="selectedTools.includes('{{ addslashes($software) }}')">✓</span>
                                            <span>{{ $software }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 dark:bg-gray-800/60 px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <button type="button" 
                                    @click="clearAllSelections()" 
                                    class="text-xs font-semibold text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors">
                                Clear All
                            </button>

                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium" x-text="totalSelected() + ' selected (' + (matchMode === 'all' ? 'Match All' : 'Match Or') + ')'"></span>
                                <button type="button" 
                                        @click="skillsModalOpen = false" 
                                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                                    Done
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 4. Artists Results -->
            @if($artists->count() > 0)
                <!-- Grid View -->
                <div x-show="viewMode === 'grid'" 
                     class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($artists as $artist)
                        <x-artist-card :artist="$artist" />
                    @endforeach
                </div>

                <!-- List View -->
                <div x-show="viewMode === 'list'" 
                     class="space-y-3">
                    @foreach($artists as $artist)
                        @php
                            $skills = is_array($artist->skills) ? $artist->skills : (json_decode($artist->skills ?? '[]', true) ?: []);
                            $artCount = $artist->artworks_count ?? $artist->artworks()->published()->count();
                            $portfolioUrl = route('profile.public', $artist->slug);
                        @endphp
                        <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-200/80 dark:border-gray-800 shadow-xs hover:shadow-md transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-3.5 min-w-0">
                                <a href="{{ $portfolioUrl }}" class="relative shrink-0">
                                    <x-user-avatar :user="$artist" size="md" />
                                    @if($artist->is_verified)
                                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-indigo-600 text-white rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-900" title="Verified Artist">
                                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </span>
                                    @endif
                                </a>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ $portfolioUrl }}" class="font-bold text-sm text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 truncate">
                                            {{ $artist->name }}
                                        </a>
                                        <span class="text-xs text-gray-400 font-mono"><span>@</span>{{ $artist->slug }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-md">
                                        {{ $artist->headline ?: ($artist->bio ?: 'Artist on Artfolio') }}
                                    </p>
                                    @if($artist->city || $artist->country)
                                        <span class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ implode(', ', array_filter([$artist->city, $artist->country])) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
                                <!-- Skills -->
                                @if(!empty($skills))
                                    <div class="hidden md:flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach(array_slice($skills, 0, 2) as $sk)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                                {{ $sk }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Status Badge -->
                                @if($artist->commission_status === 'open')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Open
                                    </span>
                                @endif

                                <!-- Button -->
                                <a href="{{ $portfolioUrl }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-indigo-600 hover:text-white dark:bg-gray-800 dark:hover:bg-indigo-600 text-xs font-bold text-gray-700 dark:text-gray-200 transition-colors">
                                    View Portfolio
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($artists->hasPages())
                    <div class="pt-6">
                        {{ $artists->links() }}
                    </div>
                @endif
            @else
                <!-- 5. Empty State with Big Magnifying Glass -->
                <div class="py-20 px-4 text-center bg-white dark:bg-gray-900 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800 shadow-xs max-w-2xl mx-auto">
                    <div class="mx-auto w-20 h-20 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-500 mb-5">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        No artists found
                    </h3>
                    <p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        We couldn't find any creators matching your search filters. Try adjusting your keyword or exploring different artistic skills.
                    </p>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <a href="{{ route('artists.index') }}" 
                           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                            Clear all filters
                        </a>
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold transition-colors">
                            Search artworks instead
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
