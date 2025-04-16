@php
    $colors = collect($getState())
        ->map(fn ($color) => is_array($color) ? $color['hex'] ?? null : $color)
        ->filter()
        ->values();
@endphp

@if ($colors->isNotEmpty())
    <div class="flex gap-2 mt-2">
        <h3 class="text-sm text-gray-500"> Apperçu des couleurs du dress code</h3>

        @foreach ($colors as $hex)
          <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border shadow" style="background-color: {{ $hex }};"></div>
                <span class="text-sm font-mono">{{ $hex }}</span>
            </div>

        @endforeach
    </div>
@else
    <p class="text-sm text-gray-500">Aucune couleur sélectionnée.</p>
@endif
