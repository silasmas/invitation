
<div class="flex gap-4 overflow-x-auto w-full px-2 py-4">
    @php
        $colors = [
            'text-red-600', 'text-pink-600', 'text-purple-600', 'text-indigo-600',
            'text-blue-600', 'text-green-600', 'text-amber-600', 'text-orange-600',
            'text-teal-600', 'text-gray-600',
        ];
    @endphp

    @forelse ($ceremonies as $ceremonie)
        @php
            $hash = crc32($ceremonie->nom);
            $colorClass = $colors[$hash % count($colors)];
        @endphp

        <div class="min-w-[300px] bg-white border rounded-xl shadow p-4 shrink-0">
            <div class="text-lg font-semibold {{ $colorClass }}">{{ $ceremonie->nom }}</div>
            <div class="text-sm text-gray-600 mt-1">
                📅 {{ \Carbon\Carbon::parse($ceremonie->date)->format('d/m/Y') }}<br>
                🕒 {{  \Carbon\Carbon::parse($ceremonie->date)->format('H\h:i') }}
            </div>
            <div class="mt-2 text-xs uppercase text-gray-500 font-medium tracking-wide">
                Message : {{ $ceremonie->description?"✅ Defini":"❌ Non défini" }}
            </div>
        </div>
    @empty
        <div class="text-gray-500">Aucune cérémonie disponible.</div>
    @endforelse
</div>

