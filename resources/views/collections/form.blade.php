<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($collection) ? __('Edit Collection') : __('New Collection') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST"
                          action="{{ isset($collection) ? route('collections.update', $collection) : route('collections.store') }}"
                          class="space-y-6 max-w-2xl"
                          x-data="{
                              visibility: '{{ old('visibility', $collection->visibility ?? 'public') }}',
                              submitting: false
                          }"
                          @submit="submitting = true">
                        @csrf
                        @if(isset($collection))
                            @method('PUT')
                        @endif

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Collection Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name', $collection->name ?? '')" required autofocus placeholder="e.g. 3D Models, Concept Art, WIP..." />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-xl shadow-sm text-sm" rows="3" placeholder="Brief description of the collection...">{{ old('description', $collection->description ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Visibility Settings -->
                        <div>
                            <x-input-label :value="__('Collection Privacy & Visibility')" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                Choose who can discover and access this collection of artworks.
                            </p>

                            <input type="hidden" name="visibility" :value="visibility">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Public -->
                                <button type="button"
                                        @click="visibility = 'public'"
                                        :class="visibility === 'public' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300'"
                                        class="p-4 rounded-xl border text-left transition-all">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                            Public
                                        </span>
                                        <span x-show="visibility === 'public'" class="text-emerald-600 font-bold">✓</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Visible on your portfolio tabs. Anyone can discover and browse.
                                    </p>
                                </button>

                                <!-- Unlisted -->
                                <button type="button"
                                        @click="visibility = 'unlisted'"
                                        :class="visibility === 'unlisted' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/50 dark:bg-amber-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300'"
                                        class="p-4 rounded-xl border text-left transition-all">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                            Unlisted
                                        </span>
                                        <span x-show="visibility === 'unlisted'" class="text-amber-600 font-bold">✓</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Hidden from public tabs. Only people with the direct link can access.
                                    </p>
                                </button>

                                <!-- Private -->
                                <button type="button"
                                        @click="visibility = 'private'"
                                        :class="visibility === 'private' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/50 dark:bg-rose-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300'"
                                        class="p-4 rounded-xl border text-left transition-all">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-bold text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Private
                                        </span>
                                        <span x-show="visibility === 'private'" class="text-rose-600 font-bold">✓</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Hidden to everyone. Only you can view this collection.
                                    </p>
                                </button>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('visibility')" />
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button class="rounded-xl px-5 py-2.5" ::disabled="submitting">
                                <span x-show="!submitting">{{ isset($collection) ? __('Save Changes') : __('Create Collection') }}</span>
                                <span x-show="submitting" class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Saving...
                                </span>
                            </x-primary-button>

                            <a href="{{ route('collections.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
