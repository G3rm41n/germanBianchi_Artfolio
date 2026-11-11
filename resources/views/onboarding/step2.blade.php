<x-onboarding-layout :currentStep="2" title="Professional Profile Setup">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white font-['Outfit'] tracking-tight">
            Professional Profile & Skills
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Showcase your artistic disciplines, software toolkit, and work availability to recruiters and the creative community.
        </p>
    </div>

    <form method="POST" action="{{ route('onboarding.step2.save') }}" class="space-y-8">
        @csrf

        <!-- 1. Username / Slug Handle with Live Debounced Availability Check -->
        <div x-data="{
            slug: '{{ old('slug', $user->slug) }}',
            status: '', // 'checking', 'available', 'taken', 'invalid'
            message: '',
            debounceTimer: null,

            checkAvailability() {
                this.slug = this.slug.toLowerCase().replace(/[^a-z0-9_\-]/g, '');
                
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
        }"
        x-init="if (slug) checkAvailability()">
            <label for="slug" class="block text-sm font-bold text-gray-900 dark:text-white">
                Public Portfolio Handle <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                Your unique URL on Artfolio. Only lowercase letters, numbers, underscores, and dashes.
            </p>

            <div class="relative flex rounded-2xl shadow-sm">
                <span class="inline-flex items-center px-4 rounded-l-2xl border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 text-gray-500 dark:text-gray-400 text-xs sm:text-sm font-mono select-none">
                    {{ request()->getHost() }}/artista/
                </span>
                <input type="text"
                       id="slug"
                       name="slug"
                       x-model="slug"
                       @input="checkAvailability()"
                       required
                       minlength="3"
                       maxlength="50"
                       placeholder="your_handle"
                       class="flex-1 min-w-0 block w-full px-4 py-3 rounded-r-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm font-medium focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <!-- Live Status Message -->
            <div class="mt-2 flex items-center gap-2 text-xs font-semibold" x-show="message" style="display: none;">
                <template x-if="status === 'checking'">
                    <span class="text-gray-400 flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="message"></span>
                    </span>
                </template>
                <template x-if="status === 'available'">
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
            @error('slug')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- 2. Bio / Professional Summary -->
        <div x-data="{ bioText: '{{ old('bio', $user->bio) }}' }">
            <div class="flex items-center justify-between mb-1">
                <label for="bio" class="block text-sm font-bold text-gray-900 dark:text-white">
                    Professional Summary / Bio <span class="text-red-500">*</span>
                </label>
                <span class="text-xs text-gray-400" :class="bioText.length < 20 ? 'text-amber-500' : 'text-gray-400'">
                    <span x-text="bioText.length"></span> / 1000 (min 20)
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                Describe your creative focus, artistic experience, style, or background.
            </p>
            <textarea id="bio"
                      name="bio"
                      rows="4"
                      x-model="bioText"
                      required
                      minlength="20"
                      maxlength="1000"
                      placeholder="e.g. Concept artist and illustrator with a passion for sci-fi environments and character design. Currently crafting worlds for games and publishing..."
                      class="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 shadow-sm"></textarea>
            @error('bio')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- 3. Skills Tag Input (Alpine.js) -->
        @php
            $initialSkills = old('skills', is_array($user->skills) ? $user->skills : (json_decode($user->skills ?? '[]', true) ?: []));
            $initialSkillsJson = json_encode(array_values($initialSkills));
        @endphp
        <div x-data="{
            skills: {{ $initialSkillsJson }},
            newSkill: '',
            suggestedSkills: [
                'Concept Art', 'Character Design', '3D Modeling', 'Digital Painting',
                'Illustration', 'Environment Art', 'Animation', 'Sculpting',
                'Storyboarding', 'Texturing', 'UI/UX Art', 'VFX'
            ],
            addSkill(tag) {
                tag = (tag || this.newSkill).trim();
                if (tag && !this.skills.includes(tag) && this.skills.length < 15) {
                    this.skills.push(tag);
                    this.newSkill = '';
                }
            },
            removeSkill(index) {
                this.skills.splice(index, 1);
            }
        }"
        class="p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/80 dark:border-gray-700/60">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Artistic Disciplines & Skills <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Type a skill and press <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-[10px] font-mono">Enter</kbd> or click the suggested tags below (at least 1, max 15).
            </p>

            <!-- Hidden input with JSON representation -->
            <input type="hidden" name="skills" :value="JSON.stringify(skills)">

            <!-- Active Tag Pills -->
            <div class="flex flex-wrap gap-2 mb-3 min-h-[38px] p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <template x-for="(skill, index) in skills" :key="index">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 animate-fadeIn">
                        <span x-text="skill"></span>
                        <button type="button" @click="removeSkill(index)" class="hover:text-red-500 focus:outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </span>
                </template>
                <input type="text"
                       x-model="newSkill"
                       @keydown.enter.prevent="addSkill()"
                       @keydown.comma.prevent="addSkill()"
                       placeholder="Add skill..."
                       class="flex-1 min-w-[120px] border-0 p-0 text-xs focus:ring-0 bg-transparent text-gray-900 dark:text-white placeholder-gray-400">
            </div>

            <!-- Quick Add Chips -->
            <div class="flex flex-wrap gap-1.5 items-center">
                <span class="text-[11px] text-gray-400 font-medium mr-1">Suggestions:</span>
                <template x-for="s in suggestedSkills" :key="s">
                    <button type="button"
                            @click="addSkill(s)"
                            :disabled="skills.includes(s)"
                            :class="skills.includes(s) ? 'opacity-40 cursor-default bg-gray-200 dark:bg-gray-800 text-gray-400' : 'bg-white dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition-colors">
                        + <span x-text="s"></span>
                    </button>
                </template>
            </div>
            @error('skills')
                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- 4. Software Tag Input (Alpine.js) -->
        @php
            $initialSoftware = old('software', is_array($user->software) ? $user->software : (json_decode($user->software ?? '[]', true) ?: []));
            $initialSoftwareJson = json_encode(array_values($initialSoftware));
        @endphp
        <div x-data="{
            tools: {{ $initialSoftwareJson }},
            newTool: '',
            suggestedTools: [
                'Blender', 'Photoshop', 'ZBrush', 'Maya', 'Unreal Engine',
                'Procreate', 'Substance 3D', 'Clip Studio Paint', 'Cinema 4D',
                'After Effects', 'Figma', 'Unity'
            ],
            addTool(tool) {
                tool = (tool || this.newTool).trim();
                if (tool && !this.tools.includes(tool) && this.tools.length < 15) {
                    this.tools.push(tool);
                    this.newTool = '';
                }
            },
            removeTool(index) {
                this.tools.splice(index, 1);
            }
        }"
        class="p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/80 dark:border-gray-700/60">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Software & Tools <span class="text-xs font-normal text-gray-400">(Optional)</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                List the 2D, 3D, and digital creation software you use in your workflow.
            </p>

            <!-- Hidden input with JSON representation -->
            <input type="hidden" name="software" :value="JSON.stringify(tools)">

            <!-- Active Tag Pills -->
            <div class="flex flex-wrap gap-2 mb-3 min-h-[38px] p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <template x-for="(tool, index) in tools" :key="index">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 animate-fadeIn">
                        <span x-text="tool"></span>
                        <button type="button" @click="removeTool(index)" class="hover:text-red-500 focus:outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </span>
                </template>
                <input type="text"
                       x-model="newTool"
                       @keydown.enter.prevent="addTool()"
                       @keydown.comma.prevent="addTool()"
                       placeholder="Add software..."
                       class="flex-1 min-w-[120px] border-0 p-0 text-xs focus:ring-0 bg-transparent text-gray-900 dark:text-white placeholder-gray-400">
            </div>

            <!-- Quick Add Chips -->
            <div class="flex flex-wrap gap-1.5 items-center">
                <span class="text-[11px] text-gray-400 font-medium mr-1">Suggestions:</span>
                <template x-for="t in suggestedTools" :key="t">
                    <button type="button"
                            @click="addTool(t)"
                            :disabled="tools.includes(t)"
                            :class="tools.includes(t) ? 'opacity-40 cursor-default bg-gray-200 dark:bg-gray-800 text-gray-400' : 'bg-white dark:bg-gray-800 hover:bg-purple-50 dark:hover:bg-purple-900/40 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition-colors">
                        + <span x-text="t"></span>
                    </button>
                </template>
            </div>
            @error('software')
                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- 5. Commission Status Radio Cards -->
        @php
            $selectedCommission = old('commission_status', $user->commission_status ?? 'open');
        @endphp
        <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                Commission Availability <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Open -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-indigo-400 dark:hover:border-indigo-600 {{ $selectedCommission === 'open' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">
                    <input type="radio" name="commission_status" value="open" {{ $selectedCommission === 'open' ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-sm text-gray-900 dark:text-white">Open</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Accepting new custom commissions and requests.</span>
                </label>

                <!-- Consult -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-indigo-400 dark:hover:border-indigo-600 {{ $selectedCommission === 'consult' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">
                    <input type="radio" name="commission_status" value="consult" {{ $selectedCommission === 'consult' ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="font-bold text-sm text-gray-900 dark:text-white">Consult First</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Selectively reviewing offers based on scope and schedule.</span>
                </label>

                <!-- Closed -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-indigo-400 dark:hover:border-indigo-600 {{ $selectedCommission === 'closed' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">
                    <input type="radio" name="commission_status" value="closed" {{ $selectedCommission === 'closed' ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                        <span class="font-bold text-sm text-gray-900 dark:text-white">Closed</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Currently unavailable for custom commission work.</span>
                </label>
            </div>
            @error('commission_status')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- 6. Hiring Opportunities Checkboxes -->
        @php
            $currentHiring = old('hiring_types', is_array($user->hiring_types) ? $user->hiring_types : (json_decode($user->hiring_types ?? '[]', true) ?: []));
        @endphp
        <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-1">
                Opportunities Interested In <span class="text-xs font-normal text-gray-400">(Optional)</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                Select the engagement models you are open to discussing with studios and clients.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <input type="checkbox" name="hiring_types[]" value="full-time" {{ in_array('full-time', $currentHiring) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Full-time Job</span>
                </label>
                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <input type="checkbox" name="hiring_types[]" value="contract" {{ in_array('contract', $currentHiring) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Contract / Project</span>
                </label>
                <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <input type="checkbox" name="hiring_types[]" value="freelance" {{ in_array('freelance', $currentHiring) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Freelance Gigs</span>
                </label>
            </div>
            @error('hiring_types')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- 7. Public Email Toggle -->
        @php
            $publicEmail = old('public_email_enabled', $user->public_email_enabled ?? false);
        @endphp
        <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 flex items-start gap-3">
            <input type="checkbox"
                   id="public_email_enabled"
                   name="public_email_enabled"
                   value="1"
                   {{ $publicEmail ? 'checked' : '' }}
                   class="mt-1 rounded text-indigo-600 focus:ring-indigo-500">
            <div>
                <label for="public_email_enabled" class="block text-sm font-bold text-gray-900 dark:text-white cursor-pointer">
                    Show contact email publicly
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    When enabled, potential clients and recruiters can see your email address ({{ $user->email }}) on your public portfolio to inquire about work.
                </p>
            </div>
        </div>

        <!-- Bottom Navigation Buttons -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <a href="{{ route('onboarding.step', ['step' => 1]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-semibold text-xs sm:text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Back to Step 1</span>
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <span>Save & Complete Setup</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</x-onboarding-layout>
