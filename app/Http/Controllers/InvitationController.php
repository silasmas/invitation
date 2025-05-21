<?php
namespace App\Http\Controllers;

use App\Models\Boisson;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvitationController extends Controller
{

    public function show($reference)
    {
        $invitation = Invitation::where('reference', $reference)->firstOrFail();
        $boissons=Boisson::get();

         $invitation->ceremonies->dressCode;
         $tissu=$invitation->ceremonies->tissu;
         $type=$invitation->ceremonies->typeDressecode;
         $colors = collect($invitation->ceremonies->dressCode)
                                            ->map(fn($color) => is_array($color) ? $color['hex'] ?? null : $color)
                                            ->filter()
                                            ->values();
                                        // dd($type);
        return view('invitation.show', compact('invitation','boissons', 'colors','tissu','type'));
    }
    public function voir($reference)
    {
        $invitation = Invitation::where('reference', $reference)->firstOrFail();
        //  dd($invitation->ceremonies->day_of_week );
        return view('invitation.reception', compact('invitation'));
    }

    public function accept(Request $request)
    {
        $request->validate([
            'reference' => 'required|exists:invitations,reference',
            'boissons'  => 'nullable|array',
            'message'   => 'nullable|string',
            'cadeau'    => 'nullable|string',
        ]);

        $invitation = Invitation::where('reference', $request->reference)->firstOrFail();

        $invitation->update([
            'status'       => 'accept',
            'confirmation' => true,
            'text'         => $request->message,
            'cadeau'       => $request->cadeau,
            'boissons'     => $request->boissons, // à condition que ce champ soit casté comme array
            'updated_at'   => now(),
        ]);

        return response()->json([
            'reponse' => true,
            'message' => 'Confirmation enregistrée.',
        ]);
    }
    public function confirmation(Request $request)
    {
        $request->validate([
            'reference' => 'required|exists:invitations,reference',
        ]);

        $invitation = Invitation::where('reference', $request->reference)->firstOrFail();
        if (! $invitation || ! $invitation->ceremonies->date) {
            return response()->json([
                'reponse' => false,
                'message' => 'Date de cérémonie non définie.',
            ], 400);
        }
        $aujourdHui    = Carbon::today();
        $dateCeremonie = $invitation->ceremonies->date;
        if ($dateCeremonie->isSameDay($aujourdHui)) {
            // echo "La cérémonie a lieu aujourd'hui (" . $dateCeremonie->translatedFormat('l d F Y') . ").";
            switch ($invitation->status) {
                case 'accept':
                    $invitation->update([
                        'status'       => 'close',
                        'confirmation' => true,
                        'updated_at'   => now(),
                    ]);
                    return response()->json([
                        'reponse' => true,
                        'message' => 'Accès accordé, Invitation cloturée.',
                    ]);
                case 'refuse':
                    return response()->json([
                        'reponse' => false,
                        'message' => "Accès réfuser,l'invité a décliner l'invitation.",
                    ]);
                case 'close':
                    return response()->json([
                        'reponse' => false,
                        'message' => "Accès réfuser,l'invitation est déjà cloturée.",
                    ]);
                case 'close':
                    return response()->json([
                        'reponse' => false,
                        'message' => "Accès réfuser,l'invitation est déjà cloturée.",
                    ]);
            }
        } elseif ($dateCeremonie->isBefore($aujourdHui)) {
            $joursPasse = $dateCeremonie->diffForHumans($aujourdHui);
            $invitation->update([
                'status'       => 'close',
                'confirmation' => true,
                'updated_at'   => now(),
            ]);
            return response()->json([
                'reponse' => false,
                'message' => "Invitation invalide car la cérémonie a eu lieu le " . $dateCeremonie->translatedFormat('l d F Y') . " (il y a $joursPasse).",
            ]);
        } else {
            $joursRestant = $aujourdHui->diffForHumans($aujourdHui);
            $invitation->update([
                'status'       => 'close',
                'confirmation' => true,
                'updated_at'   => now(),
            ]);
            return response()->json([
                'reponse' => false,
                'message' => "La cérémonie aura lieu le " .$dateCeremonie->translatedFormat('l d F Y'). " (dans $joursRestant).",
            ]);
        }

    }

    // Refuser l'invitation
    public function decline($invitation)
    {
        Invitation::where('reference', $invitation)
            ->limit(1)
            ->update([
                'status' => 'refuse',
            ]);

        return response()->json([
            'reponse' => true,
            'message' => 'Invitation refusée.',
        ]);
    }
    public function close($invitation)
    {

        $ceremonie = Invitation::where('reference', $invitation)->firstOrFail();

        if (! $ceremonie || ! $ceremonie->ceremonies->date) {
            return response()->json([
                'reponse' => false,
                'message' => 'Date de cérémonie non définie.',
            ], 400);
        }

        $aujourdHui = Carbon::today();

        if ($ceremonie->ceremonies->date->isSameDay($aujourdHui)) {
            // C’est le jour J
            // Tu peux clôturer l’invitation ici si tu veux
            $ceremonie->update(['status' => 'close']);

            return response()->json([
                'reponse' => true,
                'message' => 'Accès autorisé à la cérémonie.',
            ]);
        } else {
            return response()->json([
                'reponse' => false,
                'message' => 'Ce n’est pas encore le jour de la cérémonie.',
            ]);
        }

    }
}
