<x-app-layout>
    <div x-data="{ 
            tagsModalOpen: false,
            modalFilter: '',
            matchMode: '{{ $matchMode }}',
            selectedTags: {{ json_encode($selectedTags) }},
            selectedTools: {{ json_encode($selectedTools) }},

            toggleTag(name) {
                const idx = this.selectedTags.indexOf(name);
                if (idx > -1) {
                    this.selectedTags.splice(idx, 1);
                } else {
                    this.selectedTags.push(name);
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
                this.selectedTags = [];
                this.selectedTools = [];
            },

            totalSelected() {
                return this.selectedTags.length + this.selectedTools.length;
            }
         }" 
         class="py-8 bg-gray-50 dark:bg-gray-950 min-h-screen text-gray-900 dark:text-gray-100 transition-colors">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. Scope Tabs: Artworks vs Artists (Artstation style) -->
            <div class="flex items-center justify-center sm:justify-start border-b border-gray-200 dark:border-gray-800">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <!-- Artworks Scope Tab (Active) -->
                    <a href="{{ route('home') }}" 
                       class="border-indigo-500 text-indigo-600 dark:text-indigo-400 whitespace-nowrap py-3 px-1 border-b-2 font-black text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Artworks
                        <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                            {{ $artworks->total() }}
                        </span>
                    </a>

                    <!-- Artists Scope Tab -->
                    <a href="{{ route('artists.index') }}" 
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 whitespace-nowrap py-3 px-1 border-b-2 font-bold text-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Artists
                    </a>
                </nav>
            </div>

            <!-- 2. Search & Filter Bar with Tags & Software Pop-up Modal -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-5 border border-gray-200/80 dark:border-gray-800 shadow-sm space-y-4">
                <form method="GET" action="{{ route('home') }}" id="artworkFilterForm" class="space-y-4">
                    <!-- Dynamic Hidden Inputs for Selected Tags & Tools -->
                    <input type="hidden" name="match" :value="matchMode">
                    <template x-for="t in selectedTags" :key="'tag-' + t">
                        <input type="hidden" name="tags[]" :value="t">
                    </template>
                    <template x-for="tl in selectedTools" :key="'tool-' + tl">
                        <input type="hidden" name="tools[]" :value="tl">
                    </template>

                    <!-- Top Row: Search Input + Category Dropdown + Sort Dropdown + Apply -->
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 flex-1">
                            <!-- Keyword Search -->
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Title, description or artist..." 
                                       class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white pl-9 pr-8 py-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                                @if(!empty($search))
                                    <a href="{{ route('home', array_filter(['category' => $category, 'sort' => $sort !== 'latest' ? $sort : null, 'match' => $matchMode])) }}" 
                                       class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" 
                                       title="Clear search">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </a>
                                @endif
                            </div>

                            <!-- Category / Media Dropdown -->
                            <div>
                                <select name="category" 
                                        class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 shadow-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Media: All Categories</option>
                                    <option value="image" {{ $category === 'image' ? 'selected' : '' }}>2D Digital (Images)</option>
                                    <option value="video" {{ $category === 'video' ? 'selected' : '' }}>Animation / Video</option>
                                    <option value="3d" {{ $category === '3d' ? 'selected' : '' }}>3D Models</option>
                                    <option value="audio" {{ $category === 'audio' ? 'selected' : '' }}>Music & Audio</option>
                                    <option value="doc" {{ $category === 'doc' ? 'selected' : '' }}>Documents</option>
                                </select>
                            </div>

                            <!-- Sort Criteria Dropdown -->
                            <div>
                                <select name="sort" 
                                        class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white py-2 shadow-xs focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Sort: Latest</option>
                                    <option value="likes" {{ $sort === 'likes' ? 'selected' : '' }}>Sort: Most Liked</option>
                                    <option value="views" {{ $sort === 'views' ? 'selected' : '' }}>Sort: Most Viewed</option>
                                    <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Sort: Oldest</option>
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
                                <a href="{{ route('home') }}" 
                                   class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-xs transition-colors"
                                   title="Reset all filters">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Row: Tags & Software Trigger Bar with Match Mode Switcher and Active Pills -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800/80 flex flex-wrap items-center gap-2">
                        <!-- Pop-up Trigger Button -->
                        <button type="button" 
                                @click="tagsModalOpen = true" 
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border transition-all text-xs font-bold shadow-xs select-none"
                                :class="totalSelected() > 0 ? 'bg-indigo-600 border-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            <span>Tags & Software</span>
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
                            <template x-for="tag in selectedTags" :key="'active-tg-' + tag">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <span x-text="'#' + tag"></span>
                                    <button type="button" @click="toggleTag(tag)" class="hover:text-red-500 focus:outline-hidden" title="Remove tag">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </span>
                            </template>

                            <template x-for="tool in selectedTools" :key="'active-tl-' + tool">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    <span x-text="tool"></span>
                                    <button type="button" @click="toggleTool(tool)" class="hover:text-red-500 focus:outline-hidden" title="Remove software">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </span>
                            </template>

                            <span x-show="totalSelected() === 0" class="text-xs text-gray-400 dark:text-gray-500 ml-1">
                                Click to select disciplines, tags, and software tools...
                            </span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Active Filter Chips -->
            @if($activeFiltersCount > 0)
                <div class="flex flex-wrap items-center gap-2 px-1">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Active filters:</span>

                    @if(!empty($search))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
                            <span>Search: <strong>{{ $search }}</strong></span>
                            <a href="{{ route('home', array_filter(['category' => $category, 'sort' => $sort !== 'latest' ? $sort : null, 'match' => $matchMode])) }}" class="text-indigo-500 hover:text-indigo-800 dark:hover:text-indigo-100" title="Remove search filter">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </a>
                        </span>
                    @endif

                    @if(!empty($category))
                        @php
                            $catLabels = ['image' => '2D Digital', 'video' => 'Animation', '3d' => '3D Models', 'audio' => 'Music & Audio', 'doc' => 'Documents'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800">
                            <span>Category: <strong>{{ $catLabels[$category] ?? $category }}</strong></span>
                            <a href="{{ route('home', array_filter(['search' => $search, 'sort' => $sort !== 'latest' ? $sort : null, 'match' => $matchMode])) }}" class="text-purple-500 hover:text-purple-800 dark:hover:text-purple-100" title="Remove category filter">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </a>
                        </span>
                    @endif

                    @foreach($selectedTags as $tag)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800">
                            <span>Tag: <strong>#{{ $tag }}</strong></span>
                            <button type="button" @click="toggleTag('{{ addslashes($tag) }}'); $nextTick(() => document.getElementById('artworkFilterForm').submit())" class="text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </span>
                    @endforeach

                    @foreach($selectedTools as $tool)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800">
                            <span>Software: <strong>{{ $tool }}</strong></span>
                            <button type="button" @click="toggleTool('{{ addslashes($tool) }}'); $nextTick(() => document.getElementById('artworkFilterForm').submit())" class="text-purple-500 hover:text-purple-800 dark:hover:text-purple-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </span>
                    @endforeach

                    <a href="{{ route('home') }}" class="text-xs text-rose-600 dark:text-rose-400 hover:underline font-semibold ml-2 inline-flex items-center gap-1" title="Clear all filters">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Clear all filters
                    </a>
                </div>
            @endif

            <!-- 3. Tags & Software Selection Modal (Pop-up with Backdrop Blur) -->
            <div x-cloak
                 x-show="tagsModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog" 
                 aria-modal="true">
                
                <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                    <!-- Backdrop Blur Overlay -->
                    <div x-show="tagsModalOpen" 
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="tagsModalOpen = false" 
                         class="fixed inset-0 bg-black/70 backdrop-blur-xs transition-opacity"></div>

                    <!-- Modal Dialog Card -->
                    <div x-show="tagsModalOpen"
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
                                    Artwork Tags, Disciplines & Software Toolkit
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Select one or more options to filter artworks matching these artistic specialties or tools.
                                </p>
                            </div>
                            <button type="button" 
                                    @click="tagsModalOpen = false" 
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
                                       placeholder="Quick filter tags, disciplines or tools..."
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
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="matchMode === 'all' ? 'Artworks must match ALL of your selected disciplines and tools.' : 'Artworks with AT LEAST ONE of your selected disciplines or tools will match.'"></p>
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
                            <!-- Section 1: Artistic Disciplines & Tags -->
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2.5">
                                    Artistic Disciplines & Tags
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($availableDisciplines as $discipline)
                                        <button type="button" 
                                                x-show="!modalFilter || '{{ strtolower($discipline) }}'.includes(modalFilter.toLowerCase())"
                                                @click="toggleTag('{{ addslashes($discipline) }}')"
                                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border flex items-center gap-1.5"
                                                :class="selectedTags.includes('{{ addslashes($discipline) }}') 
                                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' 
                                                    : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border-gray-200/80 dark:border-gray-700 text-gray-700 dark:text-gray-300'">
                                            <span x-show="selectedTags.includes('{{ addslashes($discipline) }}')">✓</span>
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
                                        @click="tagsModalOpen = false" 
                                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors">
                                    Done
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 4. Artworks Gallery Results Header -->
            <div class="flex justify-between items-center pt-2">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                        @if(!empty($search))
                            Results for "{{ $search }}"
                        @elseif(!empty($category))
                            @php
                                $categoryNames = [
                                    'image' => '2D Digital Works',
                                    'video' => 'Animations & Videos',
                                    '3d'    => '3D Models & Sculptures',
                                    'audio' => 'Music & Soundtracks',
                                    'doc'   => 'Creative Documents',
                                ];
                            @endphp
                            {{ $categoryNames[$category] ?? 'Selected Media' }}
                        @else
                            Discover Artworks
                        @endif
                    </h2>
                    @if($activeFiltersCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/70 dark:text-indigo-300">
                            {{ $activeFiltersCount }} active {{ \Illuminate\Support\Str::plural('filter', $activeFiltersCount) }}
                        </span>
                    @endif
                </div>

                @if($activeFiltersCount > 0)
                    <a href="{{ route('home') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 hover:underline inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Clear all filters
                    </a>
                @endif
            </div>

            <!-- 5. Masonry Grid View (Consistent Gallery Presentation) -->
            @if ($artworks->count() > 0)
                <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 [column-fill:_balance]">
                    @foreach ($artworks as $artwork)
                        <div class="break-inside-avoid mb-6">
                            <x-artwork-card :artwork="$artwork" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-20 text-center bg-white dark:bg-gray-900 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800 p-8">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">No artworks found</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        No artworks match your selected combination of tags, software tools, and search criteria.
                    </p>
                    @if($activeFiltersCount > 0)
                        <div class="mt-6">
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl shadow-xs text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- 6. Pagination -->
            <div class="mt-10">
                {{ $artworks->links() }}
            </div>
        </div>
    </div>

    <!-- Adaptive Artwork Viewer Modal -->
    <x-viewer-modal />
</x-app-layout>
