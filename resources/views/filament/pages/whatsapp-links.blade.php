@php
    use App\Helpers\MessageHelper;
    // dd($guests);
@endphp

<x-filament::page>
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('filament.admin.resources.guests.index') }}" class="text-sm text-blue-600 hover:underline">
            ⬅️ Retour à la liste des invités
        </a>
        <button wire:click="handleDelete" onclick="return confirm('Supprimer tous les liens ?')"
            class="text-sm bg-red-500 text-blue-600 px-4 py-1 rounded hover:bg-red-600 transition">
            🗑 Supprimer les liens
        </button>
    </div>
    @if ($guests->isEmpty())
        <p>Aucun invité sélectionné ou valide.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($guests as $guest)
                @php

                    $invitation = $guest->invitation->first();
                    $ceremonieId = $invitation->ceremonies->id ?? null;

                    if ($invitation) {
                        // dd($ceremonieId);
                        // 1. Générer le lien long (via la route définie)
                        $lienLong = route('invitation.show', ['reference' => $invitation->reference]);

                        // 2. (Facultatif) Raccourcir le lien
                         $lienCourt = \App\Services\LienCourt::generate($invitation->reference, $ceremonieId); // on le crée juste après
                        //$lienCourt = App\Models\ShortLink::where('ceremonie_id', $ceremonieId)
                            //->where('reference', $invitation->reference)->first()->code;
                        // dd($lienCourt);

                        // 3. Créer le message en insérant {nom}, {categorie}, {lien}, etc.
                        // $messageHtml = str_replace(
                        //     ['{nom}', '{categorie}', '{lien}'],
                        //     [$guest->nom, $guest->categorie ?? '', $lienCourt],
                        //     $messageTemplate ?? '',
                        // );
                        $messageHtml = str_replace(
                            [
                                '{homme}',
                                '{femme}',
                                '{adresse}',
                                '{categorie}',
                                '{nom}',
                                '{ceremony}',
                                '{date}',
                                '{lien}',
                            ],
                            [
                                $invitation->ceremonies->event->homme,
                                $invitation->ceremonies->event->femme,
                                $invitation->ceremonies->adresse,
                                $guest->type,
                                $guest->nom,
                                $invitation->ceremonies->nom,
                                $invitation->ceremonies->adresse,
                                $lienCourt,
                            ],
                            $messageTemplate,
                        );
                        // 4. Nettoyer le HTML pour le format WhatsApp/SMS
                        $messageTexte = MessageHelper::cleanFormattedMessage($messageHtml);
                    }
                    // $message = str_replace('{nom}', $guest->nom, $messageTemplate);
                    // $ceremonieNom = optional($guest->event?->ceremonie?->first())->nom ?? 'Notre événement';
                    // $invitation = $guest->invitation->first();
                    // $messageHtml = $invitation->message ?? '';
                    // $messageTexte = cleanFormattedMessage($messageHtml);
                @endphp
                {{-- @dd($message ) --}}
                @if (!empty($messageTexte))
                    <div class="p-4 border rounded shadow-sm bg-white space-y-2">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $guest->nom }}</h3>
                        <p class="text-sm text-gray-500">📱 {{ $guest->phone }}</p>
                        <a href="https://wa.me/{{ $guest->phone }}?text={{ urlencode($messageTexte) }}" target="_blank"
                            class="inline-block bg-green-500 text-blue-600 px-3 py-2 rounded hover:bg-green-600 transition">
                            Envoyer sur WhatsApp
                        </a>
                    </div>
                @else
                    <span class="text-red-500 text-sm">❌ Message manquant</span>
                @endif
            @endforeach
        </div>
    @endif


    @if ($invalidGuests->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-red-600 font-bold text-lg mb-2">Invités sans numéro ou invalide :</h3>
            <ul class="list-disc pl-5 text-sm text-gray-700">
                @foreach ($invalidGuests as $guest)
                    <li>{{ $guest->nom }} — 📵 {{ $guest->phone ?? 'Aucun numéro' }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</x-filament::page>
