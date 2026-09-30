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

        <!-- Avatar Drag-to-Crop -->
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
            onMouseDown(e) {
                if (!this.avatarUrl) return;
                this.isDragging = true;
                this.startX = e.clientX || e.touches[0].clientX;
                this.startY = e.clientY || e.touches[0].clientY;
                this.startCropX = this.cropX;
                this.startCropY = this.cropY;
            },
            onMouseMove(e) {
                if (!this.isDragging) return;
                const clientX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                const clientY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                const dx = clientX - this.startX;
                const dy = clientY - this.startY;
                
                // Calculate percentage relative to container (128px is w-32)
                const containerSize = 128;
                this.cropX = this.startCropX + ((dx / containerSize) * 100) / this.cropZoom;
                this.cropY = this.startCropY + ((dy / containerSize) * 100) / this.cropZoom;
            },
            onMouseUp() {
                this.isDragging = false;
            }
        }" @mouseup.window="onMouseUp" @touchend.window="onMouseUp" @mousemove.window="onMouseMove" @touchmove.window="onMouseMove">
            
            <x-input-label for="avatar_url" value="Enlace de la Foto de Perfil" />
            
            <div class="mt-2 flex flex-col sm:flex-row gap-6 items-start">
                
                <!-- Preview / Crop Area -->
                <div class="relative w-32 h-32 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 shadow-inner flex-shrink-0 cursor-move border-2 border-dashed border-gray-400 dark:border-gray-500 hover:border-indigo-500 transition-colors"
                     @mousedown.prevent="onMouseDown" @touchstart.prevent="onMouseDown" title="Arrastra para encuadrar">
                     
                     <template x-if="avatarUrl">
                         <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
                              :style="`background-image: url('${avatarUrl}'); transform: translate(${cropX}%, ${cropY}%) scale(${cropZoom});`">
                         </div>
                     </template>
                     <template x-if="!avatarUrl">
                        <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold text-3xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                     </template>
                </div>
                
                <div class="flex-grow w-full space-y-4">
                    <!-- URL Input -->
                    <input type="url" id="avatar_url" name="avatar_url" x-model.lazy="avatarUrl" placeholder="https://ejemplo.com/mifoto.jpg" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                    
                    <!-- Zoom Slider -->
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Zoom (Ajuste)</label>
                        <input type="range" x-model="cropZoom" min="0.1" max="5" step="0.01" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" :disabled="!avatarUrl">
                    </div>
                    
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pega el enlace de tu imagen. Luego haz clic y arrastra sobre la foto circular para encuadrarla.</p>
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
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
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

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
