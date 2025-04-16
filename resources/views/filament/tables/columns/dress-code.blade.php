@php
    $colors = collect($getState())
        ->map(fn ($color) => is_array($color) ? $color['hex'] ?? null : $color)
        ->filter()
        ->values();
@endphp

<div class="flex gap-1">
    @foreach ($colors as $hex)
        <div class="w-5 h-5 rounded-full border shadow" style="background-color: {{ $hex }};" title="{{ $hex }}"></div>
    @endforeach
</div>
