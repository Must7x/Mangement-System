@props(['title', 'subtitle' => null])

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h2 class="page-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
