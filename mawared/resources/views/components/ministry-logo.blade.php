@props(['size' => 'md', 'showText' => true, 'variant' => 'light'])

@php
    $sizes = [
        'sm' => ['img' => '2.5rem', 'title' => '0.7rem', 'sub' => '0.65rem'],
        'md' => ['img' => '3.25rem', 'title' => '0.8rem', 'sub' => '0.7rem'],
        'lg' => ['img' => '5.5rem', 'title' => '1rem', 'sub' => '0.85rem'],
        'xl' => ['img' => '7rem', 'title' => '1.15rem', 'sub' => '0.9rem'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $textColor = $variant === 'light' ? '#fff' : 'var(--color-text)';
    $mutedColor = $variant === 'light' ? 'rgb(255 255 255 / 0.8)' : 'var(--color-muted)';
@endphp

@php
    $logo = file_exists(public_path('images/mtnima-logo.png'))
        ? asset('images/mtnima-logo.png')
        : asset('images/mtnima-logo.svg');
@endphp

<div {{ $attributes->merge(['class' => 'ministry-logo']) }} style="display:flex;align-items:center;gap:0.75rem;">
    <img src="{{ $logo }}"
         alt="شعار وزارة التحول الرقمي — MTNIMA"
         style="width:{{ $s['img'] }};height:{{ $s['img'] }};object-fit:contain;flex-shrink:0;background:#fff;border-radius:0.5rem;padding:0.2rem;">
    @if ($showText)
        <div style="min-width:0;">
            <p style="margin:0;font-size:{{ $s['sub'] }};color:{{ $mutedColor }};line-height:1.4;">
                وزارة التحول الرقمي والابتكار وعصرنة الإدارة
            </p>
            <p style="margin:0.15rem 0 0;font-size:{{ $s['title'] }};font-weight:700;color:{{ $textColor }};line-height:1.35;">
                {{ $slot->isEmpty() ? 'نظام إدارة الموارد والمعدات' : $slot }}
            </p>
        </div>
    @endif
</div>
