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
                                    <input type="url" x-model="tempUrl" @input="imageError = false" placeholder="https://example.com/avatar.jpg" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
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

            <!-- 2. Cover Banner Positioning & URL -->
            <div x-data="{
                bannerUrl: '{{ old('banner_url', $user->banner_url) }}',
                bannerCropX: {{ old('banner_crop_x', $user->banner_crop_x ?? 0) }},
                bannerCropY: {{ old('banner_crop_y', $user->banner_crop_y ?? 0) }},
                bannerCropZoom: {{ old('banner_crop_zoom', $user->banner_crop_zoom ?? 1) }},
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
                    this.tempUrl = this.bannerUrl || '';
                    this.tempCropX = parseFloat(this.bannerCropX) || 0;
                    this.tempCropY = parseFloat(this.bannerCropY) || 0;
                    this.tempCropZoom = parseFloat(this.bannerCropZoom) || 1;
                    this.imageError = false;
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                    this.isDragging = false;
                },

                applyChanges() {
                    this.bannerUrl = this.tempUrl ? this.tempUrl.trim() : '';
                    this.bannerCropX = this.tempCropX;
                    this.bannerCropY = this.tempCropY;
                    this.bannerCropZoom = this.tempCropZoom;
                    this.closeModal();
                },

                removeBanner() {
                    this.bannerUrl = '';
                    this.tempUrl = '';
                    this.bannerCropX = 0;
                    this.bannerCropY = 0;
                    this.bannerCropZoom = 1;
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
                    const container = this.$refs.bannerCropContainer;
                    const containerWidth = container ? container.clientWidth : 500;
                    const containerHeight = container ? container.clientHeight : 180;
                    const zoomFactor = Math.max(parseFloat(this.tempCropZoom) || 1, 0.5);
                    this.tempCropX = Math.round(this.startCropX + ((dx / containerWidth) * 100) / zoomFactor);
                    this.tempCropY = Math.round(this.startCropY + ((dy / containerHeight) * 100) / zoomFactor);
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
                    Cover Banner Image
                </label>

                <!-- Hidden inputs for persisting coordinates & URL -->
                <input type="hidden" name="banner_url" :value="bannerUrl">
                <input type="hidden" name="banner_crop_x" :value="bannerCropX">
                <input type="hidden" name="banner_crop_y" :value="bannerCropY">
                <input type="hidden" name="banner_crop_zoom" :value="bannerCropZoom">

                <div class="space-y-3 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60">
                    <!-- Live Panoramic Banner Preview Card -->
                    <div class="h-32 sm:h-44 w-full rounded-2xl overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-700 relative bg-gray-100 dark:bg-gray-900 flex items-center justify-center cursor-pointer shadow-sm group transition-all hover:border-indigo-500"
                         @click="openModal()" title="Click to adjust banner framing">
                        <template x-if="bannerUrl">
                            <div class="w-full h-full bg-cover bg-center bg-no-repeat transition-transform duration-150"
                                 :style="`background-image: url('${bannerUrl}'); transform: translate(${bannerCropX}%, ${bannerCropY}%) scale(${bannerCropZoom});`">
                            </div>
                        </template>
                        <template x-if="!bannerUrl">
                            <div class="text-center p-4 text-gray-400 dark:text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-1 stroke-1 text-gray-400 dark:text-gray-500 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-medium">No banner set (Click to set & adjust panoramic banner)</span>
                            </div>
                        </template>

                        <!-- Hover Overlay Indicator -->
                        <div x-show="bannerUrl" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <span class="px-3 py-1.5 rounded-xl bg-black/60 text-white text-xs font-semibold backdrop-blur-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Adjust Framing
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="openModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span x-text="bannerUrl ? 'Adjust Framing / Change' : 'Upload Banner'"></span>
                            </button>
                            <button type="button" x-show="bannerUrl" @click="removeBanner()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-700 hover:bg-red-100 hover:text-red-700 dark:hover:bg-red-950/40 dark:hover:text-red-400 text-gray-700 dark:text-gray-300 transition">
                                Remove
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Panoramic banner (recommended 1920x640px). Pan & zoom supported.
                        </p>
                    </div>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('banner_url')" />

                <!-- Modal Dialog for Banner Framing -->
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
                             class="relative rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-2xl w-full max-w-2xl border border-gray-200 dark:border-gray-800"
                             @click.stop
                             @mouseup.window="onMouseUp()"
                             @touchend.window="onMouseUp()"
                             @mousemove.window="onMouseMove($event)"
                             @touchmove.window="onMouseMove($event)">

                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Adjust Cover Banner Framing
                                </h3>
                                <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Banner Image URL</label>
                                    <input type="url" x-model="tempUrl" @input="imageError = false" placeholder="https://example.com/banner.jpg" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 p-2.5">
                                </div>

                                <!-- Panoramic Rectangular Viewport -->
                                <div class="bg-gray-950 p-4 sm:p-6 rounded-2xl flex flex-col items-center justify-center relative select-none border border-gray-800">
                                    <div x-ref="bannerCropContainer"
                                         class="relative w-full h-44 sm:h-56 rounded-xl overflow-hidden bg-gray-900 border-2 border-indigo-500 cursor-move select-none shadow-inner"
                                         @mousedown.prevent="onMouseDown"
                                         @touchstart.prevent="onMouseDown"
                                         @wheel.prevent="onWheel">
                                        <template x-if="tempUrl && !imageError">
                                            <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                                                 :style="`background-image: url('${tempUrl}'); transform: translate(${tempCropX}%, ${tempCropY}%) scale(${tempCropZoom});`"></div>
                                        </template>
                                        <template x-if="!tempUrl || imageError">
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 text-xs">
                                                <svg class="w-10 h-10 mb-2 text-gray-600 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span>Provide a valid banner image URL above</span>
                                            </div>
                                        </template>

                                        <!-- Rule-of-thirds grid guidelines -->
                                        <div class="absolute inset-0 pointer-events-none grid grid-cols-3 grid-rows-3 opacity-20 border border-white/20">
                                            <div class="border-r border-b border-white/30"></div>
                                            <div class="border-r border-b border-white/30"></div>
                                            <div class="border-b border-white/30"></div>
                                            <div class="border-r border-b border-white/30"></div>
                                            <div class="border-r border-b border-white/30"></div>
                                            <div class="border-b border-white/30"></div>
                                            <div class="border-r border-white/30"></div>
                                            <div class="border-r border-white/30"></div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                        <span>Click & drag horizontally/vertically to position • Mouse wheel to zoom</span>
                                    </p>
                                </div>

                                <div x-show="tempUrl" class="space-y-1">
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">Zoom: <span class="font-mono text-indigo-600 dark:text-indigo-400" x-text="Number(tempCropZoom).toFixed(2) + 'x'"></span></span>
                                        <button type="button" @click="resetCrop()" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Reset Positioning</button>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" @click="zoomOut()" :disabled="tempCropZoom <= 0.5" class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                        <input type="range" x-model.number="tempCropZoom" min="0.5" max="3.0" step="0.05" class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg accent-indigo-600 cursor-pointer">
                                        <button type="button" @click="zoomIn()" :disabled="tempCropZoom >= 3.0" class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-300 transition">Cancel</button>
                                <button type="button" @click="applyChanges()" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
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

        <!-- ── Official Social Networks & Links ──────────────────────────────── -->
        @php
            $socials = is_array($user->social_links) ? $user->social_links : (json_decode($user->social_links ?? '[]', true) ?: []);
        @endphp
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60">
            <div class="mb-4">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Social Networks & Official Links
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Connect your professional channels. These will be highlighted on your public portfolio header.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- ArtStation -->
                <div>
                    <x-input-label for="social_artstation" :value="__('ArtStation Profile URL')" class="text-xs font-semibold" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M0 17.723l2.027 3.505h.001a2.424 2.424 0 002.164 1.334h13.435l-2.99-5.182H3.722L0 17.723zm19.345-5.32l-7.07-12.247H7.727l9.447 16.36 4.908-8.497a2.43 2.43 0 00-.737-3.616v-.001l-2-.001v.001zm-5.753 5.32l-3.08-5.334-3.078 5.334h6.158z"/></svg>
                        </div>
                        <x-text-input id="social_artstation" name="social_links[artstation]" type="url" class="block w-full text-xs pl-9 rounded-xl" :value="old('social_links.artstation', $socials['artstation'] ?? '')" placeholder="https://www.artstation.com/username" />
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('social_links.artstation')" />
                </div>

                <!-- Instagram -->
                <div>
                    <x-input-label for="social_instagram" :value="__('Instagram Profile URL')" class="text-xs font-semibold" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </div>
                        <x-text-input id="social_instagram" name="social_links[instagram]" type="url" class="block w-full text-xs pl-9 rounded-xl" :value="old('social_links.instagram', $socials['instagram'] ?? '')" placeholder="https://instagram.com/username" />
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('social_links.instagram')" />
                </div>

                <!-- Behance -->
                <div>
                    <x-input-label for="social_behance" :value="__('Behance Profile URL')" class="text-xs font-semibold" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22 7h-7v-2h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-4.357 0-6.625-2.844-6.625-6.96 0-4.041 2.502-7.04 6.471-7.04 4.095 0 6.002 3.013 5.751 6.85h-9.45c.075 2.531 1.782 4.542 4.568 4.542 1.938 0 3.197-.847 3.738-1.748l.648 1.356zm-7.986-5.592h6.818c-.08-2.247-1.455-3.8-3.375-3.8-2.016 0-3.328 1.547-3.443 3.8zm-11.74-7.408h6.417c2.617 0 4.398 1.365 4.398 3.518 0 1.444-.805 2.597-2.083 3.094 1.743.511 2.656 1.944 2.656 3.727 0 2.535-2.032 4.161-4.887 4.161h-6.501v-14.5zm2.75 6.046h3.192c1.233 0 2.059-.579 2.059-1.635 0-1.077-.85-1.602-2.039-1.602h-3.212v3.237zm0 5.645h3.396c1.378 0 2.308-.659 2.308-1.849 0-1.21-.955-1.848-2.348-1.848h-3.356v3.697z"/></svg>
                        </div>
                        <x-text-input id="social_behance" name="social_links[behance]" type="url" class="block w-full text-xs pl-9 rounded-xl" :value="old('social_links.behance', $socials['behance'] ?? '')" placeholder="https://behance.net/username" />
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('social_links.behance')" />
                </div>

                <!-- X / Twitter -->
                <div>
                    <x-input-label for="social_twitter" :value="__('X / Twitter URL')" class="text-xs font-semibold" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </div>
                        <x-text-input id="social_twitter" name="social_links[twitter]" type="url" class="block w-full text-xs pl-9 rounded-xl" :value="old('social_links.twitter', $socials['twitter'] ?? '')" placeholder="https://x.com/username" />
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('social_links.twitter')" />
                </div>

                <!-- LinkedIn -->
                <div>
                    <x-input-label for="social_linkedin" :value="__('LinkedIn Profile URL')" class="text-xs font-semibold" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.72a1.63 1.63 0 0 0-1.63 1.63c0 .9.73 1.63 1.63 1.63s1.63-.73 1.63-1.63c0-.9-.73-1.63-1.63-1.63z"/></svg>
                        </div>
                        <x-text-input id="social_linkedin" name="social_links[linkedin]" type="url" class="block w-full text-xs pl-9 rounded-xl" :value="old('social_links.linkedin', $socials['linkedin'] ?? '')" placeholder="https://linkedin.com/in/username" />
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('social_links.linkedin')" />
                </div>

                <!-- Personal Website -->
                <div>
                    <x-input-label for="social_website" :value="__('Personal Website / Portfolio URL')" class="text-xs font-semibold" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <x-text-input id="social_website" name="social_links[website]" type="url" class="block w-full text-xs pl-9 rounded-xl" :value="old('social_links.website', $socials['website'] ?? '')" placeholder="https://myartportfolio.com" />
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('social_links.website')" />
                </div>
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

