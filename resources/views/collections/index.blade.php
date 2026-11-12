<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-900 dark:text-gray-100 tracking-tight">
                    {{ __('My Boards & Collections') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Organize, categorize, and showcase your artworks and visual inspirations.
                </p>
            </div>
            
            <a href="{{ route('collections.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-full font-bold text-xs text-white hover:bg-indigo-700 active:scale-95 transition-all shadow-md hover:shadow-indigo-500/25">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>New Board</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 min-h-screen bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors"
         x-data="{
             sort: '{{ $sort }}',
             filter: '{{ $filter }}',
             sortDropdownOpen: false,
             isSavingOrder: false,
             draggedItem: null,

             changeSort(newSort) {
                 this.sort = newSort;
                 this.sortDropdownOpen = false;
                 const url = new URL(window.location.href);
                 url.searchParams.set('sort', newSort);
                 window.location.href = url.toString();
             },

             changeFilter(newFilter) {
                 this.filter = newFilter;
                 const url = new URL(window.location.href);
                 url.searchParams.set('filter', newFilter);
                 url.searchParams.delete('page');
                 window.location.href = url.toString();
             },

             // Drag & Drop for Custom Ordering
             dragStart(event, id) {
                 if (this.sort !== 'custom') return;
                 this.draggedItem = event.target.closest('[data-collection-id]');
                 event.dataTransfer.effectAllowed = 'move';
                 event.dataTransfer.setData('text/plain', id);
                 this.draggedItem.classList.add('opacity-40');
             },

             dragOver(event) {
                 if (this.sort !== 'custom') return;
                 event.preventDefault();
                 event.dataTransfer.dropEffect = 'move';
                 const target = event.target.closest('[data-collection-id]');
                 if (target && target !== this.draggedItem) {
                     const grid = target.parentElement;
                     const children = Array.from(grid.children);
                     const targetIndex = children.indexOf(target);
                     const draggedIndex = children.indexOf(this.draggedItem);
                     if (targetIndex > draggedIndex) {
                         grid.insertBefore(this.draggedItem, target.nextSibling);
                     } else {
                         grid.insertBefore(this.draggedItem, target);
                     }
                 }
             },

             dragEnd(event) {
                 if (this.draggedItem) {
                     this.draggedItem.classList.remove('opacity-40');
                 }
                 if (this.sort === 'custom') {
                     this.saveCustomOrder();
                 }
             },

             async saveCustomOrder() {
                 const container = document.getElementById('collections-grid');
                 if (!container) return;
                 const ids = Array.from(container.children).map(el => el.getAttribute('data-collection-id')).filter(Boolean);
                 if (ids.length === 0) return;

                 this.isSavingOrder = true;
                 try {
                     const response = await fetch('{{ route('collections.reorder') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json',
                         },
                         body: JSON.stringify({ ordered_ids: ids })
                     });
                     if (response.ok) {
                         // Subtle feedback
                     }
                 } catch (e) {
                     console.error(e);
                 } finally {
                     this.isSavingOrder = false;
                 }
             }
         }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Pinterest Style Filter and Sort Toolbar (Ref2.png) -->
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                
                <div class="flex items-center gap-2.5">
                    <!-- Sort Dropdown Trigger -->
                    <div class="relative">
                        <button type="button"
                                @click="sortDropdownOpen = !sortDropdownOpen"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors shadow-xs"
                                title="Sorting options">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </button>

                        <!-- Sort Menu Popup (Ref2.png style) -->
                        <div x-cloak
                             x-show="sortDropdownOpen"
                             @click.outside="sortDropdownOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 top-12 z-40 w-60 rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl p-2.5 text-xs">
                            
                            <div class="px-3 py-1.5 font-bold text-gray-400 uppercase tracking-wider text-[10px]">
                                Sort by
                            </div>

                            <!-- De la A a la Z -->
                            <button type="button"
                                    @click="changeSort('az')"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <span>A to Z</span>
                                <span x-show="sort === 'az'" class="text-indigo-600 dark:text-indigo-400 font-black">✓</span>
                            </button>

                            <!-- Personalizado -->
                            <button type="button"
                                    @click="changeSort('custom')"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="flex flex-col">
                                    <span>Custom</span>
                                    <span class="text-[10px] text-gray-400 font-normal">Drag & drop</span>
                                </div>
                                <span x-show="sort === 'custom'" class="text-indigo-600 dark:text-indigo-400 font-black">✓</span>
                            </button>

                            <!-- Último Pin / Obra agregada -->
                            <button type="button"
                                    @click="changeSort('recent')"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <span>Last artwork added</span>
                                <span x-show="sort === 'recent'" class="text-indigo-600 dark:text-indigo-400 font-black">✓</span>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Pills (All, Public, Private) -->
                    <button type="button"
                            @click="changeFilter('all')"
                            :class="filter === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition-all">
                        All
                    </button>

                    <button type="button"
                            @click="changeFilter('public')"
                            :class="filter === 'public' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition-all">
                        Public
                    </button>

                    <button type="button"
                            @click="changeFilter('private')"
                            :class="filter === 'private' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Private</span>
                    </button>
                </div>

                <!-- Custom Sorting Helper Hint -->
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span x-show="sort === 'custom'" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                        Drag cards to reorder
                    </span>
                    <span x-show="isSavingOrder" class="text-[11px] text-gray-400 animate-pulse">
                        Saving order...
                    </span>
                </div>
            </div>

            <!-- Collections Grid with 3-image Pinterest Cards -->
            @if ($collections->count() > 0)
                <div id="collections-grid" 
                     class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                     @dragover="dragOver($event)">
                    @foreach ($collections as $collection)
                        <div data-collection-id="{{ $collection->id }}"
                             :draggable="sort === 'custom'"
                             @dragstart="dragStart($event, {{ $collection->id }})"
                             @dragend="dragEnd($event)"
                             class="relative group/wrapper">
                            
                            <x-collection-card :collection="$collection" />

                            <!-- Quick Action Floating Overlay on Card Hover -->
                            <div class="absolute top-3 right-3 opacity-0 group-hover/wrapper:opacity-100 transition-opacity flex items-center gap-1 z-20">
                                <a href="{{ route('collections.edit', $collection) }}"
                                   class="w-7 h-7 rounded-full bg-white/90 dark:bg-gray-800/90 hover:bg-white dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200 shadow-md flex items-center justify-center transition-transform hover:scale-110"
                                   title="Edit board and select cover">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <form method="POST" action="{{ route('collections.destroy', $collection) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            @click.prevent="$dispatch('open-confirm', {
                                                title: 'Delete Board',
                                                message: 'Are you sure you want to move \'{{ addslashes($collection->name) }}\' to trash? The artworks will not be deleted.',
                                                form: $el.closest('form'),
                                                confirmText: 'Delete',
                                                isDanger: true
                                            })"
                                            class="w-7 h-7 rounded-full bg-white/90 dark:bg-gray-800/90 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/80 dark:hover:text-red-400 text-gray-700 dark:text-gray-200 shadow-md flex items-center justify-center transition-transform hover:scale-110"
                                            title="Delete board">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $collections->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-3xl border border-dashed border-gray-300 dark:border-gray-800 max-w-xl mx-auto p-8 shadow-xs">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">No boards yet</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Create your first board to organize artworks by style, project, or theme.</p>
                    <a href="{{ route('collections.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Create My First Board</span>
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
