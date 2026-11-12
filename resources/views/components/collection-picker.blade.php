@props([
    'artworkId' => null,
    'artworkIdExpr' => null,
    'size' => 'md',
    'placement' => 'top',
    'align' => 'right',
])

@php
    $isAuth = auth()->check();
    $alignClasses = match($align) {
        'left' => 'left-0',
        'modal' => 'right-0 sm:-right-20',
        default => 'right-0',
    };
@endphp

<div class="relative inline-block text-left"
     x-data="{
        open: false,
        loading: false,
        search: '',
        collections: [],
        hasLoaded: false,
        lastLoadedArtworkId: null,
        creating: false,
        newCollectionName: '',
        newCollectionVisibility: 'public',
        submitting: false,
        feedback: '',
        feedbackTimeout: null,

        get currentArtworkId() {
            @if($artworkIdExpr)
                return ({{ $artworkIdExpr }});
            @else
                return {{ $artworkId ?? 'null' }};
            @endif
        },

        toggleDropdown() {
            @if(!$isAuth)
                this.$dispatch('open-guest-modal', { context: 'coleccion' });
                return;
            @endif

            const aid = this.currentArtworkId;
            if (!aid) return;

            this.open = !this.open;
            this.$dispatch('collection-picker-toggled', { open: this.open });
            if (this.open) {
                if (!this.hasLoaded || this.lastLoadedArtworkId !== aid) {
                    this.loadCollections();
                }
            }
        },

        closeDropdown() {
            if (this.open) {
                this.open = false;
                this.creating = false;
                this.$dispatch('collection-picker-toggled', { open: false });
            }
        },

        loadCollections() {
            const aid = this.currentArtworkId;
            if (!aid) return;

            this.loading = true;
            this.lastLoadedArtworkId = aid;
            fetch('/collections/artwork-status/' + aid, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.collections = data.collections || [];
                this.hasLoaded = true;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
        },

        get filteredCollections() {
            if (!this.search.trim()) {
                return this.collections;
            }
            const q = this.search.toLowerCase().trim();
            return this.collections.filter(c => c.name.toLowerCase().includes(q));
        },

        toggleArtwork(collection) {
            const aid = this.currentArtworkId;
            if (!aid) return;

            fetch('/collections/' + collection.id + '/toggle-artwork', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ artwork_id: aid })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    collection.is_in_collection = data.attached;
                    this.showFeedback(data.message);
                }
            });
        },

        createAndAdd() {
            const aid = this.currentArtworkId;
            const name = this.newCollectionName.trim();
            if (!name || !aid) return;

            this.submitting = true;
            fetch('/collections/quick-create-with-artwork', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    visibility: this.newCollectionVisibility,
                    artwork_id: aid
                })
            })
            .then(res => res.json())
            .then(data => {
                this.submitting = false;
                if (data.success && data.collection) {
                    this.collections.unshift(data.collection);
                    this.newCollectionName = '';
                    this.creating = false;
                    this.showFeedback(data.message);
                }
            })
            .catch(() => {
                this.submitting = false;
            });
        },

        showFeedback(msg) {
            this.feedback = msg;
            clearTimeout(this.feedbackTimeout);
            this.feedbackTimeout = setTimeout(() => {
                this.feedback = '';
            }, 2500);
        }
     }"
     @click.stop
     @click.outside="closeDropdown()"
     @keydown.escape.window="closeDropdown()">
    
    <!-- Trigger Button -->
    <button type="button"
            @click.stop="toggleDropdown()"
            class="transition-colors focus:outline-none rounded-lg p-1 hover:bg-gray-100 dark:hover:bg-gray-700/60 {{ $size === 'lg' ? 'text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' : 'text-gray-300 dark:text-gray-600 hover:text-indigo-600 dark:hover:text-indigo-400' }}"
            title="Add to Collection">
        <svg class="{{ $size === 'lg' ? 'w-7 h-7' : ($size === 'sm' ? 'w-4 h-4' : 'w-6 h-6') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </button>

    @auth
        <!-- Dropdown Popover Menu -->
        <div x-show="open"
             x-transition:enter="ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
             style="display: none;"
             class="absolute {{ $alignClasses }} {{ $placement === 'bottom' ? 'top-full mt-2' : 'bottom-full mb-2' }} w-72 sm:w-[290px] max-w-[calc(100vw-2rem)] rounded-2xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700 z-[100] p-3 select-none text-left">
            
            <!-- Popover Header -->
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700/60 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Add to Collection
                </span>
                <button type="button" @click="closeDropdown()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Feedback Message Toast -->
            <div x-show="feedback" x-transition class="mb-2 p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-xs font-semibold text-indigo-700 dark:text-indigo-200 flex items-center gap-2 shadow-sm relative z-10">
                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="feedback"></span>
            </div>

            <!-- Search Input (Countries style) -->
            <div class="relative mb-2" x-show="collections.length > 2 && !creating">
                <input type="text"
                       x-model="search"
                       placeholder="Filter collections..."
                       class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white py-1.5 pl-8 pr-3 focus:border-indigo-500 focus:ring-indigo-500">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Collections List -->
            <div class="max-h-48 overflow-y-auto space-y-1 pr-1" x-show="!creating">
                <!-- Loading State -->
                <div x-show="loading" class="py-6 text-center text-xs text-gray-400 flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span>Loading collections...</span>
                </div>

                <!-- Empty State (No collections) -->
                <div x-show="!loading && collections.length === 0" class="py-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">You don't have any collections yet.</p>
                    <button type="button" @click="creating = true" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        + Create your first collection
                    </button>
                </div>

                <!-- Filtered Collections -->
                <template x-for="col in filteredCollections" :key="col.id">
                    <button type="button"
                            @click="toggleArtwork(col)"
                            class="w-full flex items-center justify-between p-2 rounded-xl text-xs hover:bg-gray-100 dark:hover:bg-gray-700/60 transition group text-left"
                            :class="col.is_in_collection ? 'bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300'">
                        <div class="flex items-center space-x-2 min-w-0 pr-2">
                            <!-- Checkbox icon -->
                            <div class="w-4 h-4 rounded border flex items-center justify-center shrink-0 transition"
                                 :class="col.is_in_collection ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 dark:border-gray-600 group-hover:border-indigo-400'">
                                <svg x-show="col.is_in_collection" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="truncate font-semibold" x-text="col.name"></span>
                        </div>

                        <!-- Visibility Indicator badge -->
                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded shrink-0"
                              :class="{
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400': col.visibility === 'public',
                                'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400': col.visibility === 'unlisted',
                                'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400': col.visibility === 'private'
                              }"
                              x-text="col.visibility"></span>
                    </button>
                </template>
            </div>

            <!-- Inline Collection Quick-Create Form -->
            <div x-show="creating" x-transition class="pt-2 border-t border-gray-100 dark:border-gray-700/60 space-y-2">
                <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300">New Collection Name</label>
                <input type="text"
                       x-model="newCollectionName"
                       @keydown.enter.prevent="createAndAdd()"
                       placeholder="e.g. Sci-Fi Vehicles, Sketches..."
                       class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white py-1.5 px-3 focus:border-indigo-500 focus:ring-indigo-500">

                <!-- Visibility selector pills -->
                <div class="flex items-center gap-1.5 pt-1">
                    <button type="button" @click="newCollectionVisibility = 'public'"
                            :class="newCollectionVisibility === 'public' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                            class="text-[10px] font-semibold px-2 py-1 rounded-lg transition">Public</button>
                    <button type="button" @click="newCollectionVisibility = 'unlisted'"
                            :class="newCollectionVisibility === 'unlisted' ? 'bg-amber-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                            class="text-[10px] font-semibold px-2 py-1 rounded-lg transition">Unlisted</button>
                    <button type="button" @click="newCollectionVisibility = 'private'"
                            :class="newCollectionVisibility === 'private' ? 'bg-rose-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                            class="text-[10px] font-semibold px-2 py-1 rounded-lg transition">Private</button>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="creating = false; newCollectionName = ''" class="px-2.5 py-1 text-[11px] font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        Cancel
                    </button>
                    <button type="button" @click="createAndAdd()" :disabled="submitting || !newCollectionName.trim()" class="px-3 py-1 text-[11px] font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition disabled:opacity-50 flex items-center gap-1">
                        <svg x-show="submitting" class="animate-spin w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span>Create & Add</span>
                    </button>
                </div>
            </div>

            <!-- Footer: "+ Create collection" trigger -->
            <div x-show="!creating && collections.length > 0" class="pt-2 mt-2 border-t border-gray-100 dark:border-gray-700/60">
                <button type="button"
                        @click="creating = true; $nextTick(() => $el.closest('[x-data]').querySelector('input[type=text]')?.focus())"
                        class="w-full text-left px-2 py-1.5 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Create collection</span>
                </button>
            </div>
        </div>
    @endauth
</div>
