@props(['class' => '', 'style' => ''])

@php
    $logo = file_exists(public_path('images/mtnima-logo.png'))
        ? asset('images/mtnima-logo.png')
        : asset('images/mtnima-logo.svg');
@endphp

<img src="{{ $logo }}" alt="شعار MTNIMA" {{ $attributes->merge(['class' => $class]) }} @if($style) style="{{ $style }}" @endif>
