<x-admin-layout>
    <div x-data="{ 
            addOpen: false, 
            editOpen: false, 
            editTag: { id: null, name: '', type: 'discipline', is_suggested: true },
            openEdit(tag) {
                this.editTag = { ...tag };
                this.editOpen = true;
            }
         }" 
         class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-gray-800 gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Disciplines & Software Toolkit</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage standardized tags. Modifying or deleting tags automatically synchronizes across all artist profiles.</p>
            </div>
            
            <button @click="addOpen = true" 
                    type="button" 
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Curated Tag
            </button>
        </div>

        <!-- Feedback Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-sm text-red-800 dark:text-red-300 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Tabs and Filter Search -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Tabs -->
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800/80 p-1 rounded-xl">
                <a href="{{ route('admin.taxonomy.index', ['type' => 'discipline', 'search' => request('search')]) }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all {{ $type === 'discipline' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Artistic Disciplines ({{ $counts['discipline'] }})
                </a>
                <a href="{{ route('admin.taxonomy.index', ['type' => 'software', 'search' => request('search')]) }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all {{ $type === 'software' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Software & Tools ({{ $counts['software'] }})
                </a>
                <a href="{{ route('admin.taxonomy.index', ['type' => 'all', 'search' => request('search')]) }}" 
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all {{ $type === 'all' ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    All ({{ $counts['all'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.taxonomy.index') }}" class="flex gap-2">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tags..." class="text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 pl-8 pr-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full sm:w-60">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                @if(request('search'))
                    <a href="{{ route('admin.taxonomy.index', ['type' => $type]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Tags Table -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3">Tag Name</th>
                            <th scope="col" class="px-3 py-3.5">Category</th>
                            <th scope="col" class="px-3 py-3.5">Artists Using Tag</th>
                            <th scope="col" class="px-3 py-3.5">Suggested Autocomplete</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                        @forelse($tags as $tag)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="py-3.5 pl-6 pr-3">
                                    <div class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ $tag->name }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500">
                                        slug: {{ $tag->slug }}
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    @if($tag->type === 'discipline')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                            Discipline
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            Software / Tool
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    @php $usageCount = $tag->users_count; @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $usageCount > 0 ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                        {{ $usageCount }} {{ Str::plural('profile', $usageCount) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.taxonomy.toggleSuggested', $tag) }}">
                                        @csrf
                                        <button type="submit" 
                                                title="Click to toggle suggested visibility in artist forms"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-colors {{ $tag->is_suggested ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-gray-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tag->is_suggested ? 'bg-indigo-500' : 'bg-gray-400' }}"></span>
                                            {{ $tag->is_suggested ? 'Suggested' : 'Standard' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 pl-3 pr-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- Edit Tag Trigger -->
                                        <button @click="openEdit({ id: {{ $tag->id }}, name: '{{ addslashes($tag->name) }}', type: '{{ $tag->type }}', is_suggested: {{ $tag->is_suggested ? 'true' : 'false' }} })" 
                                                type="button" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                            Edit
                                        </button>

                                        <!-- Delete Tag Form -->
                                        <form method="POST" 
                                              action="{{ route('admin.taxonomy.destroy', $tag) }}" 
                                              onsubmit="return confirm('WARNING: Are you sure you want to delete \'{{ addslashes($tag->name) }}\'?\n\nThis tag will be permanently purged from ALL artist profiles without warning!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1 rounded-lg text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-9 w-9 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">No tags found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or add a new curated tag.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tags->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $tags->links() }}
                </div>
            @endif
        </div>

        <!-- Add Tag Modal -->
        <div x-show="addOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                <div x-show="addOpen" 
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="addOpen = false" 
                     class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

                <div x-show="addOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-800">
                    
                    <form method="POST" action="{{ route('admin.taxonomy.store') }}">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add Curated Tag</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This tag will become part of the official suggestions available to all artists.</p>

                            <div class="mt-4 space-y-4 text-xs">
                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                    <select name="type" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xs py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="discipline" {{ $type === 'software' ? '' : 'selected' }}>Artistic Discipline / Specialization</option>
                                        <option value="software" {{ $type === 'software' ? 'selected' : '' }}>Software & Production Tool</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Tag Name</label>
                                    <input type="text" name="name" required placeholder="e.g. Concept Art, Blender, Unreal Engine 5" class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xs py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="flex items-center pt-2">
                                    <input type="checkbox" name="is_suggested" id="add_is_suggested" value="1" checked class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                    <label for="add_is_suggested" class="ml-2 block text-xs text-gray-700 dark:text-gray-300">
                                        Display in recommended suggestion pills across forms
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3.5 flex items-center justify-end space-x-2 border-t border-gray-100 dark:border-gray-800">
                            <button @click="addOpen = false" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                                Save Tag
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Tag Modal -->
        <div x-show="editOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                <div x-show="editOpen" 
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="editOpen = false" 
                     class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

                <div x-show="editOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-800">
                    
                    <form method="POST" :action="'/admin/skills-software/' + editTag.id">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Curated Tag</h3>
                            
                            <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-800 dark:text-amber-300">
                                <span class="font-bold">Cascading Sync:</span> Renaming this tag will instantly update it across every artist profile currently using this tag in their profile.
                            </div>

                            <div class="mt-4 space-y-4 text-xs">
                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Tag Name</label>
                                    <input type="text" name="name" x-model="editTag.name" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xs py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="flex items-center pt-2">
                                    <input type="checkbox" name="is_suggested" id="edit_is_suggested" value="1" :checked="editTag.is_suggested" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                    <label for="edit_is_suggested" class="ml-2 block text-xs text-gray-700 dark:text-gray-300">
                                        Display in recommended suggestion pills across forms
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3.5 flex items-center justify-end space-x-2 border-t border-gray-100 dark:border-gray-800">
                            <button @click="editOpen = false" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                                Update & Cascade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

