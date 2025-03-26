<div class="-mx-4">
    <div class="bg-white rounded-xl shadow p-6 space-y-6 w-full">
        {{-- 📊 Statistiques principales --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 w-full">
            <div class="rounded-xl border shadow p-4 text-center">
                <div class="text-2xl font-bold text-purple-700 flex justify-center items-center gap-1">
                    👥 {{ $total }}
                </div>
                <div class="text-sm text-gray-600">Invités total</div>
            </div>

            <div class="rounded-xl border shadow p-4 text-center">
                <div class="text-2xl font-bold text-green-700 flex justify-center items-center gap-1">
                    📧 {{ $emailValid }}
                </div>
                <div class="text-sm text-gray-600">Emails valides</div>
            </div>

            <div class="rounded-xl border shadow p-4 text-center">
                <div class="text-2xl font-bold text-red-700 flex justify-center items-center gap-1">
                    📧 {{ $emailMissing }}
                </div>
                <div class="text-sm text-gray-600">Sans email</div>
            </div>

            <div class="rounded-xl border shadow p-4 text-center">
                <div class="text-2xl font-bold text-blue-700 flex justify-center items-center gap-1">
                    📱 {{ $phoneValid }}
                </div>
                <div class="text-sm text-gray-600">Téléphones valides</div>
            </div>

            <div class="rounded-xl border shadow p-4 text-center">
                <div class="text-2xl font-bold text-orange-700 flex justify-center items-center gap-1">
                    📱 {{ $phoneMissing }}
                </div>
                <div class="text-sm text-gray-600">Sans téléphone</div>
            </div>
        </div>

        {{-- 🙋‍♂️ Répartition par relation --}}
        <div class="mt-6">
            <h3 class="text-base font-bold mb-3 text-gray-700 flex items-center gap-1">
                📁 Répartition par relation :
            </h3>

            <div class="flex flex-wrap gap-4">
                @php
                    $colors = [
                        'text-red-600', 'text-pink-600', 'text-purple-600', 'text-indigo-600',
                        'text-blue-600', 'text-green-600', 'text-amber-600', 'text-orange-600',
                        'text-teal-600', 'text-gray-600',
                    ];
                @endphp

                @foreach ($grouped as $relation => $group)
                    @php
                        $hash = crc32($relation);
                        $color = $colors[$hash % count($colors)];
                    @endphp
                    <div class="bg-gray-50 border rounded-xl px-4 py-3 min-w-[160px] text-center shadow">
                        <div class="text-sm font-bold uppercase {{ $color }}">{{ ucfirst($relation) }}</div>
                        <div class="text-xl font-semibold">{{ $group->count() }}</div>
                        <div class="text-xs text-gray-500">invités</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
