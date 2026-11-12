@props(['collection'])

@php
    $previews = $collection->preview_artworks;
    $mainArtwork = $previews->get(0);
    $secondArtwork = $previews->get(1);
    $thirdArtwork = $previews->get(2);
    $artworksCount = $collection->artworks_count ?? $collection->artworks()->count();
@endphp

<div class="collection-card-contain group relative flex flex-col cursor-pointer transition-transform duration-200"
     data-collection-id="{{ $collection->id }}">
    
    <!-- Pinterest Mosaic 3-Artwork Container -->
    <a href="{{ $collection->url }}"
       class="relative block w-full aspect-[4/3] rounded-3xl overflow-hidden bg-gray-200 dark:bg-gray-800 shadow-sm group-hover:shadow-xl transition-all duration-300 ring-1 ring-black/5 dark:ring-white/10 group-hover:ring-indigo-500/30">
        
        <div class="w-full h-full flex gap-1 p-1 bg-gray-100 dark:bg-gray-900">
            <!-- Left / Main Image (approx 66% width) -->
            <div class="w-2/3 h-full rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-800 relative">
                @if($mainArtwork)
                    <img src="{{ $mainArtwork->preview_url }}" 
                         alt="{{ $mainArtwork->title }}" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-800/80">
                        <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] font-medium">Empty</span>
                    </div>
                @endif
            </div>

            <!-- Right Column (approx 34% width with 2 stacked images) -->
            <div class="w-1/3 h-full flex flex-col gap-1">
                <!-- Secondary Top -->
                <div class="w-full h-1/2 rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-800 relative">
                    @if($secondArtwork)
                        <img src="{{ $secondArtwork->preview_url }}" 
                             alt="{{ $secondArtwork->title }}" 
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gray-200/60 dark:bg-gray-800/50 flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                        </div>
                    @endif
                </div>

                <!-- Secondary Bottom -->
                <div class="w-full h-1/2 rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-800 relative">
                    @if($thirdArtwork)
                        <img src="{{ $thirdArtwork->preview_url }}" 
                             alt="{{ $thirdArtwork->title }}" 
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gray-200/60 dark:bg-gray-800/50 flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Privacy Badge Overlay in Top-Left -->
        <div class="absolute top-3 left-3 pointer-events-none z-10">
            @if($collection->visibility === 'private')
                <div class="w-7 h-7 rounded-full bg-black/85 flex items-center justify-center text-white shadow-md" title="Private Board">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            @elseif($collection->visibility === 'unlisted')
                <div class="w-7 h-7 rounded-full bg-black/85 flex items-center justify-center text-amber-300 shadow-md" title="Unlisted Board">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            @endif
        </div>

        <!-- Subtle Hover Overlay -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors pointer-events-none"></div>
    </a>

    <!-- Collection Details (Title & Meta) -->
    <div class="mt-2.5 px-1">
        <a href="{{ $collection->url }}"
           class="block font-bold text-base text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
           title="{{ $collection->name }}">
            {{ $collection->name }}
        </a>
        <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500 dark:text-gray-400">
            <span>{{ $artworksCount }} {{ Str::plural('artwork', $artworksCount) }}</span>
            <span>•</span>
            <span>{{ $collection->updated_at ? $collection->updated_at->diffForHumans(null, true) : 'recent' }}</span>
        </div>
    </div>
</div>

