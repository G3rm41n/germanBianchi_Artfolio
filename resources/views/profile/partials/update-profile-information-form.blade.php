<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Avatar with Discord-style Editor Modal -->
        <div x-data="{
            // Form actual values (saved state)
            avatarUrl: '{{ old('avatar_url', $user->avatar_url) }}',
            cropX: {{ old('crop_x', $user->crop_x ?? 0) }},
            cropY: {{ old('crop_y', $user->crop_y ?? 0) }},
            cropZoom: {{ old('crop_zoom', $user->crop_zoom ?? 1) }},

            // Modal state
            isModalOpen: false,
            tempUrl: '',
            tempCropX: 0,
            tempCropY: 0,
            tempCropZoom: 1,

            // Dragging state inside modal
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
                this.tempUrl = '';
                this.tempCropX = 0;
                this.tempCropY = 0;
                this.tempCropZoom = 1;
                this.applyChanges();
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
                
                // Viewport size inside modal is 192px (w-48)
                const containerSize = 192;
                const zoomFactor = Math.max(parseFloat(this.tempCropZoom) || 1, 0.5);
                this.tempCropX = Math.round(this.startCropX + ((dx / containerSize) * 100) / zoomFactor);
                this.tempCropY = Math.round(this.startCropY + ((dy / containerSize) * 100) / zoomFactor);
            },

            onMouseUp() {
                this.isDragging = false;
            },

            onWheel(e) {
                if (!this.tempUrl || this.imageError) return;
                const delta = e.deltaY > 0 ? -0.05 : 0.05;
                let next = parseFloat((parseFloat(this.tempCropZoom) + delta).toFixed(2));
                this.tempCropZoom = Math.min(Math.max(next, 0.5), 4.0);
            },

            zoomIn() {
                let next = parseFloat((parseFloat(this.tempCropZoom) + 0.1).toFixed(2));
                this.tempCropZoom = Math.min(next, 4.0);
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
        class="bg-gray-50/60 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-800">
            
            <x-input-label value="Profile Picture / Avatar" class="font-bold text-gray-900 dark:text-white mb-3" />
            
            <!-- Compact Avatar Card with Change Button -->
            <div class="flex items-center gap-5">
                <!-- Avatar Preview (Clickable) -->
                <div @click="openModal()" 
                     class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 shadow-sm flex-shrink-0 cursor-pointer border-2 border-indigo-500/40 hover:border-indigo-600 transition-all group select-none"
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

                     <!-- Hover Camera Overlay (Discord style) -->
                     <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-[9px] font-bold uppercase tracking-wider">Edit</span>
                     </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="openModal()" 
                                class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Change Avatar
                        </button>
                        
                        <button type="button" 
                                x-show="avatarUrl" 
                                @click="removeAvatar()" 
                                class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors"
                                title="Remove current avatar and reset to initials">
                            Remove
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Adjust your profile picture preview, drag to center, and zoom in/out in the editor.
                    </p>
                </div>
            </div>

            <!-- Hidden Inputs for Form Submission -->
            <input type="hidden" name="avatar_url" :value="avatarUrl">
            <input type="hidden" name="crop_x" :value="cropX">
            <input type="hidden" name="crop_y" :value="cropY">
            <input type="hidden" name="crop_zoom" :value="cropZoom">
            <x-input-error class="mt-2" :messages="$errors->get('avatar_url')" />

            <!-- Discord-style Avatar Editor Modal -->
            <div x-show="isModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                 
                <!-- Backdrop with blur -->
                <div x-show="isModalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
                     @click="closeModal()"></div>

                <!-- Modal Dialog Box -->
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="isModalOpen"
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-800"
                         @click.stop>
                         
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">
                                    Edit Profile Picture
                                </h3>
                            </div>
                            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-5">
                            <!-- Image URL Input -->
                            <div>
                                <label for="temp_avatar_url" class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1.5">
                                    Image URL (External Link)
                                </label>
                                <input type="url" 
                                       id="temp_avatar_url" 
                                       x-model.lazy="tempUrl" 
                                       placeholder="https://images.unsplash.com/photo-..." 
                                       class="block w-full text-xs rounded-xl shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 p-2.5" />
                                <p class="text-[11px] text-gray-400 mt-1">Paste a direct image link (JPG, PNG, WebP) hosted online.</p>
                            </div>

                            <!-- Interactive Crop Viewport (Discord Style) -->
                            <div class="bg-gray-950 p-6 rounded-2xl flex flex-col items-center justify-center relative overflow-hidden select-none border border-gray-800">
                                <!-- Darkened canvas background with circular cutout -->
                                <div class="relative w-48 h-48 rounded-full overflow-hidden bg-gray-900 shadow-2xl flex-shrink-0 cursor-move border-4 border-indigo-500 select-none group"
                                     @mousedown.prevent="onMouseDown" 
                                     @touchstart.prevent="onMouseDown" 
                                     @wheel.prevent="onWheel"
                                     title="Click and drag to reposition. Use mouse wheel or slider to zoom.">
                                     
                                     <template x-if="tempUrl && !imageError">
                                         <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                                              :style="`background-image: url('${tempUrl}'); transform: translate(${tempCropX}%, ${tempCropY}%) scale(${tempCropZoom});`">
                                         </div>
                                     </template>

                                     <template x-if="!tempUrl || imageError">
                                        <div class="w-full h-full bg-gradient-to-tr from-gray-800 to-gray-700 flex flex-col items-center justify-center text-gray-400 p-3 text-center">
                                            <svg class="w-8 h-8 mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-[11px] font-semibold text-gray-300">No Image Selected</span>
                                            <span class="text-[10px] text-gray-500 mt-0.5">Paste a URL above</span>
                                        </div>
                                     </template>

                                     <!-- Reposition Move Icon Indicator -->
                                     <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none" x-show="tempUrl && !imageError">
                                        <div class="bg-black/60 backdrop-blur-xs px-2.5 py-1 rounded-full text-[10px] font-bold text-white flex items-center space-x-1 shadow">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            <span>Drag to move</span>
                                        </div>
                                     </div>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-2.5">Drag the image to adjust framing</p>
                            </div>

                            <!-- Zoom Slider and Reset Tool -->
                            <div x-show="tempUrl" class="space-y-2 pt-1">
                                <div class="flex items-center justify-between text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    <span class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                                        Zoom Level
                                    </span>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 font-mono text-[11px] text-gray-600 dark:text-gray-300" x-text="Number(tempCropZoom).toFixed(2) + 'x'"></span>
                                        <button type="button" @click="resetCrop()" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                            Reset Framing
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <button type="button" @click="zoomOut()" :disabled="tempCropZoom <= 0.5" class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <input type="range" x-model.number="tempCropZoom" min="0.5" max="4.0" step="0.05" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-indigo-600">
                                    <button type="button" @click="zoomIn()" :disabled="tempCropZoom >= 4.0" class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-gray-50/80 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <button type="button" 
                                    x-show="tempUrl"
                                    @click="removeAvatar()" 
                                    class="text-xs font-semibold text-red-600 hover:text-red-700 dark:text-red-400 hover:underline">
                                Remove Image
                            </button>
                            <span x-show="!tempUrl"></span>

                            <div class="flex items-center space-x-2">
                                <button type="button" 
                                        @click="closeModal()" 
                                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="button" 
                                        @click="applyChanges()" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Commission Status -->
        <div>
            <x-input-label for="commission_status" value="Commission Availability" />
            <select id="commission_status" name="commission_status" class="mt-1 block w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="open" {{ old('commission_status', $user->commission_status) === 'open' ? 'selected' : '' }}>🟢 Open for Commissions (Available)</option>
                <option value="consult" {{ old('commission_status', $user->commission_status) === 'consult' ? 'selected' : '' }}>🔵 Upon Request (Contact Me)</option>
                <option value="closed" {{ old('commission_status', $user->commission_status) === 'closed' ? 'selected' : '' }}>⚪ Closed for Commissions (Not Available)</option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This indicator will appear on your public portfolio to inform potential clients.</p>
            <x-input-error class="mt-2" :messages="$errors->get('commission_status')" />
        </div>

        <!-- Artist Bio -->
        <div x-data="{ bioText: '{{ addslashes(old('bio', $user->bio ?? '')) }}' }">
            <div class="flex items-center justify-between">
                <x-input-label for="bio" value="Artist Bio" />
                <span class="text-xs text-gray-400 font-mono" x-text="(1000 - (bioText ? bioText.length : 0)) + ' characters remaining'"></span>
            </div>
            <textarea id="bio" name="bio" rows="4" maxlength="1000" x-model="bioText" placeholder="Describe your background, favorite tools (Blender, Photoshop, Procreate...), visual style, or artistic interests..." class="mt-1 block w-full text-sm rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button class="rounded-xl px-5 py-2.5">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
