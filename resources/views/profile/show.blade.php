<x-app-layout>
    <div x-data="{
        openContact: false,
        subject: 'Art Commission / Inquiry',
        message: '',
        loading: false,
        successMsg: '',
        errorMsg: '',

        submitContact() {
            const trimmedMsg = (this.message || '').trim();
            if (!this.subject.trim() || trimmedMsg.length < 10) {
                this.errorMsg = 'Please write a message of at least 10 characters.';
                return;
            }
            this.loading = true;
            this.errorMsg = '';
            this.successMsg = '';

            fetch('{{ route('artist.contact', $user->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    subject: this.subject,
                    message: trimmedMsg
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                this.loading = false;
                if (status >= 200 && status < 300 && body.success) {
                    this.successMsg = body.message;
                    this.message = '';
                    setTimeout(() => {
                        this.openContact = false;
                        this.successMsg = '';
                    }, 2600);
                } else {
                    this.errorMsg = body.message || 'Could not send the message.';
                }
            })
            .catch(() => {
                this.loading = false;
                this.errorMsg = 'An error occurred while processing your request. Please try again.';
            });
        }
    }">
        <!-- Artist Profile Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center md:space-x-8">
                    <!-- Avatar -->
                    <x-user-avatar :user="$user" size="2xl" class="border-4 border-white dark:border-gray-900 shadow-xl mb-6 md:mb-0 mx-auto md:mx-0" />
                    
                    <!-- Info -->
                    <div class="text-center md:text-left flex-1">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </h1>

                            <!-- Actions / Contact Button -->
                            <div class="flex justify-center md:justify-end">
                                @if(auth()->check() && auth()->id() === $user->id)
                                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Edit My Profile
                                    </a>
                                @elseif(auth()->check())
                                    <button @click="openContact = true" type="button" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all focus:outline-none">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Contact Artist
                                    </button>
                                @else
                                    <button @click="$dispatch('open-guest-modal', { context: 'contacto' })" type="button" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all focus:outline-none">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Contact Artist
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto md:mx-0 mb-4 whitespace-pre-line text-sm leading-relaxed">
                            {{ $user->bio ?: 'This artist prefers to let their artwork speak for itself.' }}
                        </div>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-xs font-semibold">
                            @if($user->commission_status === 'open')
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                    Commissions Open
                                </span>
                            @endif
                            
                            @if($user->price_guide)
                                <span class="text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/60 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                                    Rates: {{ $user->price_guide }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artist's Gallery Section -->
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white shrink-0">Portfolio</h2>
                    
                    @if(isset($collections) && $collections->count() > 0)
                        <!-- Collection Tabs -->
                        <div class="flex-1 overflow-x-auto pb-2 -mb-2 no-scrollbar">
                            <nav class="flex space-x-2 md:justify-end min-w-max" aria-label="Tabs">
                                <a href="{{ route('profile.public', $user->slug) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ !$selectedCollectionId ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">
                                    All
                                </a>
                                @foreach($collections as $collection)
                                    <a href="{{ route('profile.public', ['slug' => $user->slug, 'collection_id' => $collection->id]) }}" class="px-4 py-2 text-sm font-medium rounded-full transition-colors {{ $selectedCollectionId == $collection->id ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">
                                        {{ $collection->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $artworks->total() }} published {{ Str::plural('artwork', $artworks->total()) }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($artworks as $artwork)
                        <x-artwork-card :artwork="$artwork" />
                    @empty
                        <div class="col-span-full py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 shadow-sm">
                            <p class="text-gray-500 dark:text-gray-400">This artist has not published any artworks in this section yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $artworks->links() }}
                </div>
            </div>
        </div>

        <!-- Contact Modal -->
        <div x-show="openContact" x-cloak class="relative z-50" style="display: none;" aria-labelledby="contact-modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="if(!loading) openContact = false"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div @click.away="if(!loading) openContact = false" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                        
                        <!-- Modal Header -->
                        <div class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white relative">
                            <button @click="if(!loading) openContact = false" type="button" class="absolute top-4 right-4 text-white/80 hover:text-white focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 bg-white/10 rounded-xl backdrop-blur-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold" id="contact-modal-title">Contact {{ $user->name }}</h3>
                                    <p class="text-xs text-indigo-100 mt-0.5">Secure direct message</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <!-- Success Feedback -->
                            <div x-show="successMsg" x-cloak class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span x-text="successMsg"></span>
                            </div>

                            <!-- Error Feedback -->
                            <div x-show="errorMsg" x-cloak class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="errorMsg"></span>
                            </div>

                            @auth
                                <!-- Verified Sender Card -->
                                <div class="mb-5 p-3.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-300 flex items-center justify-between">
                                    <div class="flex items-center space-x-2 truncate">
                                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        <span class="truncate">Sending as: <strong class="text-gray-900 dark:text-white">{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">Verified</span>
                                </div>

                                <form @submit.prevent="submitContact()">
                                    <!-- Subject -->
                                    <div class="mb-4">
                                        <label for="contact-subject" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                            Reason for Contact <span class="text-rose-500">*</span>
                                        </label>
                                        <select id="contact-subject" x-model="subject" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="Art Commission / Inquiry">Art Commission / Inquiry</option>
                                            <option value="Question about an Artwork">Question about an Artwork</option>
                                            <option value="Collaboration Proposal">Collaboration Proposal</option>
                                            <option value="Rates / Licensing Inquiry">Rates / Licensing Inquiry</option>
                                            <option value="Other reason">Other reason</option>
                                        </select>
                                    </div>

                                    <!-- Message -->
                                    <div class="mb-5">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <label for="contact-message" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                                Message <span class="text-rose-500">*</span>
                                            </label>
                                            <span class="text-[11px] text-gray-400" x-text="(message ? message.length : 0) + ' / 2500 characters'"></span>
                                        </div>
                                        <textarea id="contact-message" x-model="message" rows="5" maxlength="2500" required placeholder="Hello {{ $user->name }}, I loved your portfolio and would like to..." class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm placeholder-gray-400"></textarea>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center justify-end space-x-3 pt-2">
                                        <button @click="openContact = false" type="button" :disabled="loading" class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="loading" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition disabled:opacity-50">
                                            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                            <span x-text="loading ? 'Sending...' : 'Send Message'"></span>
                                        </button>
                                    </div>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visor Modal Adaptativo -->
    <x-viewer-modal />
</x-app-layout>
