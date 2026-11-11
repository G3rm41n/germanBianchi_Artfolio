<section>
    <div class="mb-6 pb-4 border-b border-gray-100 dark:border-gray-700/60">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Resume & Professional Profile
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Showcase your skills, software toolkit, work preferences, and availability to recruiters and the creative community.
        </p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')
        <input type="hidden" name="active_tab" value="resume">

        <!-- 1. Username / Slug Handle with Live Debounced Availability Check -->
        <div x-data="{
            slug: '{{ old('slug', $user->slug) }}',
            initialSlug: '{{ $user->slug }}',
            status: '',
            message: '',
            debounceTimer: null,

            checkAvailability() {
                this.slug = this.slug.toLowerCase().replace(/[^a-z0-9_\-]/g, '');

                if (this.slug === this.initialSlug) {
                    this.status = 'current';
                    this.message = 'This is your current portfolio URL.';
                    return;
                }

                if (this.slug.length < 3) {
                    this.status = 'invalid';
                    this.message = 'Must be at least 3 characters.';
                    return;
                }

                this.status = 'checking';
                this.message = 'Checking availability...';

                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    fetch(`{{ route('onboarding.checkUsername') }}?slug=${encodeURIComponent(this.slug)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.available) {
                            this.status = 'available';
                            this.message = 'Handle is available!';
                        } else {
                            this.status = 'taken';
                            this.message = data.message || 'This handle is already taken.';
                        }
                    })
                    .catch(() => {
                        this.status = '';
                        this.message = '';
                    });
                }, 400);
            }
        }">
            <label for="profile_slug" class="block text-sm font-bold text-gray-900 dark:text-white">
                Public Portfolio Handle <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                Your direct vanity link on Artfolio (e.g. artfolio.local/artista/<span class="font-mono font-semibold text-indigo-500" x-text="slug || 'username'"></span>).
            </p>

            <div class="relative flex rounded-xl shadow-sm">
                <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-mono select-none">
                    {{ request()->getHost() }}/artista/
                </span>
                <input type="text"
                       id="profile_slug"
                       name="slug"
                       x-model="slug"
                       @input="checkAvailability()"
                       required
                       minlength="3"
                       maxlength="50"
                       class="flex-1 min-w-0 block w-full px-4 py-2.5 rounded-r-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white text-sm font-medium focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Live Status Message -->
            <div class="mt-2 flex items-center gap-2 text-xs font-semibold" x-show="message" style="display: none;">
                <template x-if="status === 'checking'">
                    <span class="text-gray-400 flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="message"></span>
                    </span>
                </template>
                <template x-if="status === 'available' || status === 'current'">
                    <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span x-text="message"></span>
                    </span>
                </template>
                <template x-if="status === 'taken' || status === 'invalid'">
                    <span class="text-red-500 dark:text-red-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span x-text="message"></span>
                    </span>
                </template>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
        </div>

        <!-- 2. Artist Bio with Character Counter -->
        <div x-data="{
            bioText: '{{ addslashes(old('bio', $user->bio ?? '')) }}',
            maxChars: 1000
        }">
            <div class="flex items-center justify-between mb-1">
                <label for="bio" class="block text-sm font-bold text-gray-900 dark:text-white">
                    Artist Bio / About Me
                </label>
                <span class="text-xs font-mono"
                      :class="(maxChars - bioText.length < 50) ? 'text-red-500 font-bold' : 'text-gray-400'"
                      x-text="(maxChars - bioText.length) + ' characters remaining'">
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                A brief overview of your artistic journey, influences, and passions.
            </p>
            <textarea id="bio"
                      name="bio"
                      rows="4"
                      maxlength="1000"
                      x-model="bioText"
                      placeholder="Share your creative background, techniques, or what inspires your work..."
                      class="block w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- 3. Disciplines and Skills (Tag Pills) -->
        <div x-data="{
            skills: {{ json_encode(old('skills', $user->skills ?? [])) }},
            newSkill: '',
            suggestions: [
                '3D Modeling', 'Concept Art', 'Illustration', 'Character Design',
                'Environment Art', 'Animation', 'Matte Painting', 'Storyboarding',
                'UI / UX Art', 'Digital Painting', 'Texturing', 'Lighting', 'Sculpting'
            ],
            addSkill(tag) {
                const clean = (tag || this.newSkill).trim();
                if (clean && !this.skills.includes(clean) && this.skills.length < 15) {
                    this.skills.push(clean);
                }
                this.newSkill = '';
            },
            removeSkill(index) {
                this.skills.splice(index, 1);
            }
        }">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Artistic Disciplines & Specialties
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Select from suggestions or type your custom discipline and press <kbd class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-[10px] font-mono border border-gray-300 dark:border-gray-700">Enter</kbd> (max 15).
            </p>

            <!-- Hidden inputs for Laravel array submission -->
            <template x-for="skill in skills" :key="skill">
                <input type="hidden" name="skills[]" :value="skill">
            </template>

            <!-- Active Tag Pills -->
            <div class="flex flex-wrap gap-2 mb-3 min-h-[38px] p-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/40">
                <template x-for="(skill, index) in skills" :key="skill">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <span x-text="skill"></span>
                        <button type="button" @click="removeSkill(index)" class="hover:text-red-500 focus:outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </span>
                </template>
                <input type="text"
                       x-model="newSkill"
                       @keydown.enter.prevent="addSkill()"
                       placeholder="+ Add custom skill..."
                       class="border-0 bg-transparent text-xs p-1 focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400 flex-1 min-w-[140px]">
            </div>

            <!-- Suggestions -->
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mr-1">Suggestions:</span>
                <template x-for="sug in suggestions" :key="sug">
                    <button type="button"
                            x-show="!skills.includes(sug)"
                            @click="addSkill(sug)"
                            class="text-xs px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 dark:bg-gray-800 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400 text-gray-600 dark:text-gray-300 transition-colors">
                        + <span x-text="sug"></span>
                    </button>
                </template>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('skills')" />
        </div>

        <!-- 4. Software Toolkit (Tag Pills) -->
        <div x-data="{
            software: {{ json_encode(old('software', $user->software ?? [])) }},
            newTool: '',
            toolsSuggestions: [
                'Blender', 'Photoshop', 'ZBrush', 'Unreal Engine', 'Maya',
                'Substance 3D Painter', 'Procreate', 'Cinema 4D', 'After Effects',
                'Houdini', 'Figma', 'Illustrator', 'Unity', 'Marvelous Designer'
            ],
            addTool(tool) {
                const clean = (tool || this.newTool).trim();
                if (clean && !this.software.includes(clean) && this.software.length < 15) {
                    this.software.push(clean);
                }
                this.newTool = '';
            },
            removeTool(index) {
                this.software.splice(index, 1);
            }
        }">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Software & Production Toolkit
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                List the software and apps you master in your daily production workflow (max 15).
            </p>

            <template x-for="tool in software" :key="tool">
                <input type="hidden" name="software[]" :value="tool">
            </template>

            <div class="flex flex-wrap gap-2 mb-3 min-h-[38px] p-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/40">
                <template x-for="(tool, index) in software" :key="tool">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span x-text="tool"></span>
                        <button type="button" @click="removeTool(index)" class="hover:text-red-500 focus:outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </span>
                </template>
                <input type="text"
                       x-model="newTool"
                       @keydown.enter.prevent="addTool()"
                       placeholder="+ Add custom software..."
                       class="border-0 bg-transparent text-xs p-1 focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400 flex-1 min-w-[140px]">
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mr-1">Popular:</span>
                <template x-for="tool in toolsSuggestions" :key="tool">
                    <button type="button"
                            x-show="!software.includes(tool)"
                            @click="addTool(tool)"
                            class="text-xs px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-emerald-50 hover:text-emerald-600 dark:bg-gray-800 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-400 text-gray-600 dark:text-gray-300 transition-colors">
                        + <span x-text="tool"></span>
                    </button>
                </template>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('software')" />
        </div>

        <!-- 5. Commission Availability Cards -->
        <div x-data="{ currentStatus: '{{ old('commission_status', $user->commission_status ?? 'open') }}' }">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Commission Availability Status
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Visible badge displayed at the top of your public profile to inform prospective clients.
            </p>

            <input type="hidden" name="commission_status" :value="currentStatus">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Open -->
                <button type="button"
                        @click="currentStatus = 'open'"
                        :class="currentStatus === 'open' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300'"
                        class="p-4 rounded-xl border text-left transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Open
                        </span>
                        <span x-show="currentStatus === 'open'" class="text-emerald-600">✓</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Actively taking new commissions and freelance client work.</p>
                </button>

                <!-- Consult First -->
                <button type="button"
                        @click="currentStatus = 'consult'"
                        :class="currentStatus === 'consult' ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-50/50 dark:bg-blue-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300'"
                        class="p-4 rounded-xl border text-left transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-blue-700 dark:text-blue-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Upon Request
                        </span>
                        <span x-show="currentStatus === 'consult'" class="text-blue-600">✓</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Depends on the scope, budget, and timeline. Contact first.</p>
                </button>

                <!-- Closed -->
                <button type="button"
                        @click="currentStatus = 'closed'"
                        :class="currentStatus === 'closed' ? 'border-gray-500 ring-2 ring-gray-500/20 bg-gray-50/50 dark:bg-gray-900' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300'"
                        class="p-4 rounded-xl border text-left transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-400 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
                            Closed
                        </span>
                        <span x-show="currentStatus === 'closed'" class="text-gray-600">✓</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Not accepting commissions or freelance projects at this time.</p>
                </button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('commission_status')" />
        </div>

        <!-- 6. Hiring Types / Work Opportunities -->
        @php
            $currentHiring = old('hiring_types', $user->hiring_types ?? []);
        @endphp
        <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Work Opportunities Desired
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Check the modalities you are open to discussing with studios and employers.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/40 hover:bg-gray-100/60 dark:hover:bg-gray-800/40 cursor-pointer">
                    <input type="checkbox" name="hiring_types[]" value="full-time" {{ in_array('full-time', $currentHiring) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2.5 text-xs font-semibold text-gray-800 dark:text-gray-200">Full-time employment</span>
                </label>

                <label class="flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/40 hover:bg-gray-100/60 dark:hover:bg-gray-800/40 cursor-pointer">
                    <input type="checkbox" name="hiring_types[]" value="contract" {{ in_array('contract', $currentHiring) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2.5 text-xs font-semibold text-gray-800 dark:text-gray-200">Contract / Project</span>
                </label>

                <label class="flex items-center p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/40 hover:bg-gray-100/60 dark:hover:bg-gray-800/40 cursor-pointer">
                    <input type="checkbox" name="hiring_types[]" value="freelance" {{ in_array('freelance', $currentHiring) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2.5 text-xs font-semibold text-gray-800 dark:text-gray-200">Freelance gigs</span>
                </label>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('hiring_types')" />
        </div>

        <!-- 7. Contact Email Visibility -->
        <div x-data="{
            publicEmail: {{ old('public_email_enabled', $user->public_email_enabled) ? 'true' : 'false' }},
            customEmail: '{{ old('contact_email', $user->contact_email ?? '') }}'
        }" class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Public Contact Email</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Display a "Contact Artist" button with an email address on your public portfolio.</p>
                </div>

                <input type="hidden" name="public_email_enabled" value="0">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="public_email_enabled" value="1" x-model="publicEmail" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div x-show="publicEmail" x-transition class="pt-3 border-t border-gray-200 dark:border-gray-700/60 space-y-2">
                <label for="profile_contact_email" class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                    Custom Contact Email (Optional)
                </label>
                <input type="email"
                       id="profile_contact_email"
                       name="contact_email"
                       x-model="customEmail"
                       placeholder="{{ $user->email }}"
                       class="block w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-[11px] text-gray-400">
                    Leave blank to use your primary account email (<span class="font-mono">{{ $user->email }}</span>).
                </p>
                <x-input-error class="mt-1" :messages="$errors->get('contact_email')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/60">
            <x-primary-button class="rounded-xl px-6 py-2.5">
                {{ __('Save Changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated' && session('active_tab') === 'resume')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
