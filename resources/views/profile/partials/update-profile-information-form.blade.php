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

        <!-- Avatar Drag-to-Crop (RF-01) -->
        <div x-data="{
            avatarUrl: '{{ old('avatar_url', $user->avatar_url) }}',
            cropX: {{ old('crop_x', $user->crop_x ?? 0) }},
            cropY: {{ old('crop_y', $user->crop_y ?? 0) }},
            cropZoom: {{ old('crop_zoom', $user->crop_zoom ?? 1) }},
            isDragging: false,
            startX: 0,
            startY: 0,
            startCropX: 0,
            startCropY: 0,
            imageError: false,

            init() {
                this.$watch('avatarUrl', () => {
                    this.imageError = false;
                });
            },

            onMouseDown(e) {
                if (!this.avatarUrl || this.imageError) return;
                this.isDragging = true;
                this.startX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                this.startY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                this.startCropX = parseFloat(this.cropX) || 0;
                this.startCropY = parseFloat(this.cropY) || 0;
            },

            onMouseMove(e) {
                if (!this.isDragging) return;
                const clientX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                const clientY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                const dx = clientX - this.startX;
                const dy = clientY - this.startY;
                
                // Contenedor de 128px (w-32)
                const containerSize = 128;
                const zoomFactor = Math.max(parseFloat(this.cropZoom) || 1, 0.5);
                this.cropX = Math.round(this.startCropX + ((dx / containerSize) * 100) / zoomFactor);
                this.cropY = Math.round(this.startCropY + ((dy / containerSize) * 100) / zoomFactor);
            },

            onMouseUp() {
                this.isDragging = false;
            },

            onWheel(e) {
                if (!this.avatarUrl || this.imageError) return;
                const delta = e.deltaY > 0 ? -0.05 : 0.05;
                let next = parseFloat((parseFloat(this.cropZoom) + delta).toFixed(2));
                this.cropZoom = Math.min(Math.max(next, 0.5), 4.0);
            },

            zoomIn() {
                let next = parseFloat((parseFloat(this.cropZoom) + 0.1).toFixed(2));
                this.cropZoom = Math.min(next, 4.0);
            },

            zoomOut() {
                let next = parseFloat((parseFloat(this.cropZoom) - 0.1).toFixed(2));
                this.cropZoom = Math.max(next, 0.5);
            },

            resetCrop() {
                this.cropX = 0;
                this.cropY = 0;
                this.cropZoom = 1;
            }
        }" 
        @mouseup.window="onMouseUp" 
        @touchend.window="onMouseUp" 
        @mousemove.window="onMouseMove" 
        @touchmove.window="onMouseMove"
        class="bg-gray-50/50 dark:bg-gray-900/40 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-700/80">
            
            <div class="flex items-center justify-between mb-3">
                <x-input-label for="avatar_url" value="Foto de Perfil / Avatar (Enlace Externo)" class="font-bold text-gray-900 dark:text-white" />
                <button type="button" @click="resetCrop()" x-show="avatarUrl && !imageError" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Restablecer Encuadre
                </button>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-6 items-center sm:items-start">
                <!-- Visor Circular Interactivo (Drag & Wheel) -->
                <div class="relative w-32 h-32 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 shadow-md flex-shrink-0 cursor-move border-4 border-white dark:border-gray-700 select-none group"
                     @mousedown.prevent="onMouseDown" 
                     @touchstart.prevent="onMouseDown" 
                     @wheel.prevent="onWheel"
                     title="Haz clic y arrastra para mover la imagen. Usa la rueda del ratón para zoom.">
                     
                     <template x-if="avatarUrl && !imageError">
                         <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                              :style="`background-image: url('${avatarUrl}'); transform: translate(${cropX}%, ${cropY}%) scale(${cropZoom});`">
                         </div>
                     </template>

                     <template x-if="!avatarUrl || imageError">
                        <div class="w-full h-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-4xl shadow-inner">
                            {{ mb_strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                     </template>

                     <!-- Overlay sutil con instrucciones -->
                     <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none" x-show="avatarUrl && !imageError">
                        <svg class="w-6 h-6 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                     </div>
                </div>
                
                <div class="flex-grow w-full space-y-4">
                    <!-- URL Input -->
                    <div>
                        <input type="url" id="avatar_url" name="avatar_url" x-model.lazy="avatarUrl" placeholder="https://ejemplo.com/mifoto.jpg" class="block w-full text-sm rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" />
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Introduce una URL directa de imagen (PNG, JPG, WebP) alojada en la web.</p>
                    </div>
                    
                    <!-- Zoom Slider con Botones y Display Numérico -->
                    <div x-show="avatarUrl" class="space-y-1.5 pt-1">
                        <div class="flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-300">
                            <span>Zoom del Encuadre</span>
                            <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 font-mono text-[11px]" x-text="Number(cropZoom).toFixed(2) + 'x'"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="zoomOut()" :disabled="cropZoom <= 0.5" class="p-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <input type="range" x-model.number="cropZoom" min="0.5" max="4.0" step="0.05" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-indigo-600">
                            <button type="button" @click="zoomIn()" :disabled="cropZoom >= 4.0" class="p-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hidden inputs to send crop data -->
            <input type="hidden" name="crop_x" :value="cropX">
            <input type="hidden" name="crop_y" :value="cropY">
            <input type="hidden" name="crop_zoom" :value="cropZoom">
            
            <x-input-error class="mt-2" :messages="$errors->get('avatar_url')" />
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

        <!-- Estado de Comisiones / Encargos -->
        <div>
            <x-input-label for="commission_status" value="Disponibilidad para Comisiones / Encargos" />
            <select id="commission_status" name="commission_status" class="mt-1 block w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="open" {{ old('commission_status', $user->commission_status) === 'open' ? 'selected' : '' }}>🟢 Abierto a Comisiones (Disponible)</option>
                <option value="consult" {{ old('commission_status', $user->commission_status) === 'consult' ? 'selected' : '' }}>🔵 A Consultar (Bajo Demanda)</option>
                <option value="closed" {{ old('commission_status', $user->commission_status) === 'closed' ? 'selected' : '' }}>⚪ Comisiones Cerradas (No Disponible)</option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Este indicador aparecerá en tu portafolio público para orientar a posibles clientes.</p>
            <x-input-error class="mt-2" :messages="$errors->get('commission_status')" />
        </div>

        <!-- Biografía Artística (bio) -->
        <div x-data="{ bioText: '{{ addslashes(old('bio', $user->bio ?? '')) }}' }">
            <div class="flex items-center justify-between">
                <x-input-label for="bio" value="Biografía Artística" />
                <span class="text-xs text-gray-400 font-mono" x-text="(1000 - (bioText ? bioText.length : 0)) + ' caracteres restantes'"></span>
            </div>
            <textarea id="bio" name="bio" rows="4" maxlength="1000" x-model="bioText" placeholder="Describe tu trayectoria, herramientas favoritas (Blender, Photoshop, Procreate...), estilo visual o intereses artísticos..." class="mt-1 block w-full text-sm rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400"></textarea>
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
