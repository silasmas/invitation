{{-- @php
    // $colors = $get('dressCode') ?? [];
     // Si la vue est utilisée dans une page "show" Filament (ViewRecord), $record est accessible
     $colors = $record->dressCode ?? [];
    $hexColors = collect($colors)
        ->map(fn ($color) => is_array($color) ? ($color['hex'] ?? null) : $color)
        ->filter()
        ->toArray();
@endphp

<div class="flex gap-2 mt-2">
    @foreach ($hexColors as $color)
    <div class="w-8 h-8 rounded-full border shadow relative group" style="background-color: {{ $color }}">
        <span class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-xs bg-black text-white px-1 py-0.5 rounded opacity-0 group-hover:opacity-100 transition">
            {{ $color }}
        </span>
    </div>
    @endforeach
</div> --}}
@php
    // Récupérer les données peu importe le contexte
    $rawColors =
         $colors ??            // si passée manuellement
         ($getState ?? null)?->__invoke('dressCode') ??
        ($get ?? null)?->__invoke('dressCode') ??
        ($record->dressCode ?? []);

    // Extraire les valeurs hex (dans le format [{"hex": "#xxxxxx"}])
    $hexColors = collect($rawColors)
        ->map(fn ($item) => is_array($item) ? ($item['hex'] ?? null) : $item)
        ->filter()
        ->toArray();
        // dd($hexColors)
        @endphp

<div class="flex gap-2 mt-2">

    {{-- {{ dd($hexColors)}} --}}
    @forelse ($hexColors as $color)
    {{-- {{ dd($color->dressCode)}} --}}
        <div class="w-8 h-8 rounded-full border shadow relative group" style="background-color: {{ $color }}">
            <span class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-xs bg-black text-white px-1 py-0.5 rounded opacity-0 group-hover:opacity-100 transition">
                {{ $color }}
            </span>
        </div>
    @empty
        <span class="text-sm text-gray-500 italic">Aucune couleur définie</span>
    @endforelse
</div>
