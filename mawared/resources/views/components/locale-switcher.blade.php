@php
    $current = app()->getLocale();
@endphp

<div class="locale-switcher" role="navigation" aria-label="{{ __('common.language') }}">
    @foreach (config('locales.labels') as $code => $label)
        @if ($code === $current)
            <span class="locale-switcher__active">{{ $label }}</span>
        @else
            <a href="{{ route('locale.switch', $code) }}" class="locale-switcher__link">{{ $label }}</a>
        @endif
    @endforeach
</div>
