@props([
    'user' => null,
    'size' => 'md',
])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-xl',
        '2xl' => 'w-32 h-32 md:w-40 md:h-40 text-4xl',
        default => 'w-10 h-10 text-sm',
    };

    $name = $user ? (is_array($user) ? ($user['name'] ?? '') : ($user->name ?? '')) : '';
    $initial = mb_strtoupper(mb_substr($name, 0, 1)) ?: 'A';

    $avatarUrl = $user ? (is_array($user) ? ($user['avatar_url'] ?? null) : ($user->avatar_url ?? null)) : null;
    $cropX = $user ? (is_array($user) ? ($user['crop_x'] ?? 0) : ($user->crop_x ?? 0)) : 0;
    $cropY = $user ? (is_array($user) ? ($user['crop_y'] ?? 0) : ($user->crop_y ?? 0)) : 0;
    $cropZoom = $user ? (is_array($user) ? ($user['crop_zoom'] ?? 1) : ($user->crop_zoom ?? 1)) : 1;
@endphp

@if($avatarUrl)
    <div {{ $attributes->merge(['class' => "relative rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 flex-shrink-0 shadow-sm {$sizeClasses}"]) }}>
        <div class="w-full h-full bg-cover bg-center bg-no-repeat pointer-events-none"
             style="background-image: url('{{ $avatarUrl }}'); transform: translate({{ $cropX }}%, {{ $cropY }}%) scale({{ $cropZoom }});">
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => "rounded-full bg-gradient-to-tr from-indigo-500 via-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center flex-shrink-0 shadow-sm select-none {$sizeClasses}"]) }}>
        {{ $initial }}
    </div>
@endif
