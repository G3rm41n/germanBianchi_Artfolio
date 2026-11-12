<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Publish New Artwork') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    
                    <form method="POST" action="{{ route('artworks.store') }}" @submit="submitting = true" x-data="{
                        url: '{{ old('external_url', '') }}',
                        title: '{{ old('title', '') }}',
                        category: '{{ old('category', '') }}',
                        loading: false,
                        submitting: false,
                        preview: null,
                        errorMessage: '',

                        checkUrl() {
                            const trimmed = (this.url || '').trim();
                            if (!trimmed || !trimmed.startsWith('http')) {
                                this.preview = null;
                                this.errorMessage = '';
                                return;
                            }
                            this.loading = true;
                            this.errorMessage = '';

                            fetch('{{ route('artworks.preview') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ url: trimmed })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.loading = false;
                                if (data.success) {
                                    this.preview = data;
                                    if (data.category) {
                                        this.category = data.category;
                                    }
                                    if (data.title && (!this.title || this.title.trim() === '')) {
                                        this.title = data.title;
                                    }
                                } else {
                                    this.preview = null;
                                    this.errorMessage = data.message || 'Could not process the URL.';
                                }
                            })
                            .catch(() => {
                                this.loading = false;
                            });
                        }
                    }" x-init="if (url) checkUrl()">
                        @csrf

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Artwork Title <span class="text-red-500">*</span></label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="title" x-model="title" required autofocus />
                            @error('title')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- External URL -->
                        <div class="mb-6">
                            <label for="external_url" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Media Link (URL) <span class="text-red-500">*</span></label>
                            <input id="external_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="url" name="external_url" x-model="url" @input.debounce.600ms="checkUrl()" @paste="$nextTick(() => checkUrl())" placeholder="https://pinterest.com/pin/... or https://youtube.com/..." required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <strong>Supported sites:</strong> Pinterest, YouTube, Vimeo, Sketchfab, ArtStation, SoundCloud, Spotify, DeviantArt, Google Drive, CodePen, Bandcamp, Imgur.
                            </p>
                            @error('external_url')
                                <p class="text-red-500 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror

                            <!-- Loading Feedback -->
                            <div x-show="loading" x-cloak class="mt-3 p-3.5 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600 flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-300 animate-pulse">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Analyzing link and extracting media...</span>
                            </div>

                            <!-- Preview of Linked Media -->
                            <div x-show="preview && preview.thumbnail_url" x-cloak class="mt-4 p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 shadow-sm transition-all">
                                <div class="flex items-start space-x-4">
                                    <div class="relative w-24 h-24 rounded-lg overflow-hidden flex-shrink-0 bg-gray-900 border border-indigo-200 dark:border-indigo-700 shadow-sm">
                                        <img :src="preview.thumbnail_url" alt="Preview" referrerpolicy="no-referrer" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-black/75 text-white" x-text="preview.provider"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300">
                                                <svg class="w-3.5 h-3.5 mr-1 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Media linked successfully
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="
                                                preview.provider === 'pinterest' ? 'Original Quality' : 
                                                (preview.provider === 'deviantart' ? 'HD Quality (DeviantArt)' : 
                                                (preview.provider === 'sketchfab' ? '3D Render (Sketchfab)' : 
                                                (preview.provider === 'youtube' ? 'HD Video (YouTube)' : 
                                                (preview.provider === 'spotify' ? 'Official Audio (Spotify)' : 
                                                (preview.provider === 'google_docs' ? 'Document (Google Docs)' : preview.provider)))))"></span>
                                        </div>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="preview.title || 'Ready to publish'"></p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-all truncate font-mono" x-text="preview.thumbnail_url"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Error -->
                            <div x-show="errorMessage" x-cloak class="mt-3 p-3 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/40 text-xs text-red-600 dark:text-red-400 flex items-center space-x-2">
                                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="errorMessage"></span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Description (Optional)</label>
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tags / Hashtags (Up to 15) -->
                        <div class="mb-6">
                            <label for="tags" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                Tags / Hashtags <span class="text-xs text-gray-400 font-normal">(Optional, comma separated, up to 15)</span>
                            </label>
                            <input type="text" 
                                   id="tags" 
                                   name="tags" 
                                   value="{{ old('tags') }}" 
                                   placeholder="e.g. #blender, conceptart, characterdesign, 3d, fantasy" 
                                   class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Separate tags with commas. Helps users discover your work in search and tag filters.
                            </p>
                            @error('tags')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price / Category / Subcategory / Visibility -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="category" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Category <span class="text-red-500">*</span></label>
                                <select id="category" name="category" x-model="category" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="" disabled selected>Select a category</option>
                                    <option value="image" {{ old('category') == 'image' ? 'selected' : '' }}>Illustration / 2D</option>
                                    <option value="video" {{ old('category') == 'video' ? 'selected' : '' }}>Animation / Video</option>
                                    <option value="3d" {{ old('category') == '3d' ? 'selected' : '' }}>3D Modeling</option>
                                    <option value="audio" {{ old('category') == 'audio' ? 'selected' : '' }}>Music / Audio</option>
                                    <option value="doc" {{ old('category') == 'doc' ? 'selected' : '' }}>Document / Code / Comic</option>
                                </select>
                                @error('category')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subcategory" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Subcategory <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                                <input id="subcategory" type="text" name="subcategory" value="{{ old('subcategory') }}" placeholder="e.g. Concept Art, Sculpting" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" />
                                @error('subcategory')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price_hint" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Price Hint</label>
                                <input id="price_hint" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" type="text" name="price_hint" value="{{ old('price_hint') }}" placeholder="e.g. $150 USD" />
                                @error('price_hint')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Visibility</label>
                                <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Public (Gallery)</option>
                                    <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Hidden (Private)</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Software Used & License Grid -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="software_used" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Software / Tools Used <span class="text-xs text-gray-400 font-normal">(Optional, comma separated)</span>
                                </label>
                                <input type="text"
                                       id="software_used"
                                       name="software_used"
                                       value="{{ is_array(old('software_used')) ? implode(', ', old('software_used')) : old('software_used') }}"
                                       placeholder="e.g. Blender, Photoshop, ZBrush, Procreate, Unreal Engine"
                                       class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Highlighted badges will show in artwork viewer details.</p>
                                @error('software_used')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="license" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    License / Usage Rights
                                </label>
                                <select id="license" name="license" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="all_rights_reserved" {{ old('license', 'all_rights_reserved') == 'all_rights_reserved' ? 'selected' : '' }}>All Rights Reserved (Todos los derechos reservados)</option>
                                    <option value="cc_by" {{ old('license') == 'cc_by' ? 'selected' : '' }}>Creative Commons CC BY (Attribution)</option>
                                    <option value="cc_by_sa" {{ old('license') == 'cc_by_sa' ? 'selected' : '' }}>Creative Commons CC BY-SA (ShareAlike)</option>
                                    <option value="cc_by_nc" {{ old('license') == 'cc_by_nc' ? 'selected' : '' }}>Creative Commons CC BY-NC (NonCommercial)</option>
                                    <option value="cc_by_nc_nd" {{ old('license') == 'cc_by_nc_nd' ? 'selected' : '' }}>Creative Commons CC BY-NC-ND (NoDerivatives)</option>
                                    <option value="cc0" {{ old('license') == 'cc0' ? 'selected' : '' }}>Public Domain (CC0)</option>
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Informs visitors and clients how they may use or reference your work.</p>
                                @error('license')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- AI Content Disclosure Button / Toggle -->
                        <div x-data="{ isAi: {{ old('is_ai_generated') ? 'true' : 'false' }} }"
                             class="mb-4 p-4 rounded-2xl border transition-all"
                             :class="isAi ? 'bg-purple-50/70 dark:bg-purple-950/20 border-purple-300 dark:border-purple-800' : 'bg-gray-50/70 dark:bg-gray-800/40 border-gray-200 dark:border-gray-700'">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors shrink-0"
                                         :class="isAi ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-sm text-gray-900 dark:text-white">
                                            AI-Generated or AI-Assisted Artwork
                                        </span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Indicate if this artwork was created or partially generated with AI tools (Midjourney, Stable Diffusion, etc.).
                                        </p>
                                    </div>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" name="is_ai_generated" value="1" x-model="isAi" class="sr-only">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer transition-colors" :class="isAi ? '!bg-purple-600' : ''"></div>
                                    <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform" :class="isAi ? 'transform translate-x-5' : ''"></div>
                                </label>
                            </div>
                            <div x-show="isAi" x-transition class="mt-2.5 pt-2.5 border-t border-purple-200 dark:border-purple-900/50 flex items-center gap-2 text-xs text-purple-700 dark:text-purple-300 font-semibold" style="display: none;">
                                <span class="px-2 py-0.5 rounded bg-purple-200/80 dark:bg-purple-900 text-[10px] font-bold tracking-wider uppercase">AI Label Active</span>
                                <span>This artwork will display an AI badge for community transparency.</span>
                            </div>
                        </div>

                        <!-- Mature / 18+ Content Disclosure Button / Toggle -->
                        <div x-data="{ isMature: {{ old('is_mature') ? 'true' : 'false' }} }"
                             class="mb-6 p-4 rounded-2xl border transition-all"
                             :class="isMature ? 'bg-rose-50/70 dark:bg-rose-950/20 border-rose-300 dark:border-rose-800' : 'bg-gray-50/70 dark:bg-gray-800/40 border-gray-200 dark:border-gray-700'">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors shrink-0"
                                         :class="isMature ? 'bg-rose-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'">
                                        <span class="font-black text-xs">18+</span>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-sm text-gray-900 dark:text-white">
                                            Mature & Sensitive Content (+18)
                                        </span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Flag this artwork if it contains adult themes, graphic elements, artistic nudity, or sensitive material.
                                        </p>
                                    </div>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" name="is_mature" value="1" x-model="isMature" class="sr-only">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer transition-colors" :class="isMature ? '!bg-rose-600' : ''"></div>
                                    <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform" :class="isMature ? 'transform translate-x-5' : ''"></div>
                                </label>
                            </div>
                            <div x-show="isMature" x-transition class="mt-2.5 pt-2.5 border-t border-rose-200 dark:border-rose-900/50 flex items-center gap-2 text-xs text-rose-700 dark:text-rose-300 font-semibold" style="display: none;">
                                <span class="px-2 py-0.5 rounded bg-rose-200/80 dark:bg-rose-900 text-[10px] font-bold tracking-wider uppercase">18+ Sensitive Active</span>
                                <span>This artwork will respect user safety preferences (blurred, hidden, or shown).</span>
                            </div>
                        </div>

                        <!-- Collections -->
                        @if(isset($collections) && $collections->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-3">Add to Collections</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($collections as $collection)
                                        <label class="inline-flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <input type="checkbox" name="collections[]" value="{{ $collection->id }}" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                                {{ (is_array(old('collections')) && in_array($collection->id, old('collections'))) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $collection->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('artworks.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition mr-4">
                                Cancel
                            </a>
                            <button type="submit" :disabled="submitting" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150">
                                <span x-show="!submitting">Save Artwork</span>
                                <span x-show="submitting" class="flex items-center gap-1.5">
                                    <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Publishing...
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
