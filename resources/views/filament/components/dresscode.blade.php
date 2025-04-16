@php
    $colors = collect($getState())->filter(fn($val) => is_string($val))->toArray();
@endphp


<div class="flex gap-2">
    @foreach ($colors as $color)
        <div class="w-6 h-6 rounded-full border shadow relative group" style="background-color: {{ $color }}">
            <span class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-xs bg-black text-white px-1 py-0.5 rounded opacity-0 group-hover:opacity-100 transition">
                {{ $color }}
            </span>
        </div>
    @endforeach
</div>
