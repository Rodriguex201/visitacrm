@props([
    'name' => null,
    'size' => 22,
    'color' => 'currentColor',
])

@php
    $rawName = is_string($name) ? trim(strtolower($name)) : '';

    $aliases = [
        'web' => 'globe',
        'www' => 'globe',
        'internet' => 'globe',
        'url' => 'link',
        'soporte' => 'support',
        'ayuda' => 'support',
        'help' => 'support',
        'catalogo' => 'catalogo',
        'catálogo' => 'catalogo',
    ];

    $iconName = $aliases[$rawName] ?? $rawName;
@endphp

@if ($iconName === 'whatsapp')
    <svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M3.5 20.5L5.1 15.9A8.8 8.8 0 1 1 20.9 12a8.8 8.8 0 0 1-13.8 7.3L3.5 20.5z" stroke="{{ $color }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9.3 8.8c.2-.4.4-.5.7-.5h.5c.2 0 .4.1.5.4l.8 1.9c.1.2 0 .4-.1.5l-.5.7c-.1.1-.1.3 0 .4.4.7 1 1.3 1.7 1.8.1.1.3.1.4 0l.8-.5c.2-.1.4-.2.6-.1l1.8.8c.3.1.4.3.4.5v.5c0 .3-.1.5-.5.7-.5.2-1 .3-1.5.2-1.2-.2-2.4-.8-3.4-1.8-1.1-1-1.9-2.3-2.2-3.6-.2-.5-.2-1 .1-1.5z" fill="{{ $color }}"/>
    </svg>
@elseif ($iconName === 'support')
    <svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M4.5 12a7.5 7.5 0 0 1 15 0"/>
        <rect x="3" y="11" width="4" height="7" rx="2"/>
        <rect x="17" y="11" width="4" height="7" rx="2"/>
        <path d="M12 19v1a2 2 0 0 1-2 2h-1"/>
    </svg>
@elseif ($iconName === 'catalogo')
    <svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="3" y="3" width="8" height="8" rx="2"/>
        <rect x="13" y="3" width="8" height="8" rx="2"/>
        <rect x="3" y="13" width="8" height="8" rx="2"/>
        <rect x="13" y="13" width="8" height="8" rx="2"/>
    </svg>
@else
    <x-lucide-icon
        name="{{ in_array($iconName, ['globe', 'link'], true) ? $iconName : ($iconName ?: 'circle') }}"
        :size="$size"
        :color="$color"
        {{ $attributes }}
    />
@endif
