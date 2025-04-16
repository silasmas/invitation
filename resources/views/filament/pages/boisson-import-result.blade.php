<x-filament::page>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold">Doublons ignorés lors de l'importation</h2>

        <div class="space-x-2 flex flex-wrap justify-end">
            @php
                $encodedDuplicates = base64_encode(json_encode($duplicates));
            @endphp

            {{-- Bouton Exporter --}}
            <a href="{{ route('boissons.export-duplicates', ['data' => $encodedDuplicates]) }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 transition"
                target="_blank">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                Télécharger les doublons
            </a>


            {{-- Bouton Actualiser --}}
            <a href="{{ \App\Filament\Pages\BoissonImportResult::getUrl() }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <x-heroicon-o-arrow-path class="w-5 h-5 mr-2" />
                Actualiser
            </a>

            {{-- Bouton Retour --}}
            <a href="{{ route('filament.admin.resources.boissons.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-danger-600 rounded-md hover:bg-danger-700 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                Retour à la liste
            </a>
        </div>
    </div>

    @if (count($duplicates) > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Nom</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($duplicates as $row)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-800">{{ $row['nom'] ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">{{ $row['description'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-gray-600">Aucun doublon détecté.</p>
    @endif
</x-filament::page>
