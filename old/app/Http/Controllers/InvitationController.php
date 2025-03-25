<?php
namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvitationController extends Controller
{

    public function show($reference)
    {
        $invitation = Invitation::where('reference', $reference)->firstOrFail();
        // dd($invitation->ceremonies->event );
        return view('invitation.show', compact('invitation'));
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
            'boissons' => 'nullable|array',
            'message' => 'nullable|string',
            'cadeau' => 'nullable|string'
        ]);

        $invitation = Invitation::where('reference',$request->reference)->firstOrFail();

        $invitation->update([
            'status' => 'accept',
            'confirmation' =>true,
            'text' => $request->message,
            'cadeau' => $request->cadeau,
            'boissons' => $request->boissons, // à condition que ce champ soit casté comme array
            'updated_at' => now(),
        ]);


        return response()->json([
            'reponse' => true,
            'message' => 'Confirmation enregistrée.',
        ]);
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

        if (!$ceremonie || !$ceremonie->ceremonies->date) {
            return response()->json([
                'reponse' => false,
                'message' => 'Date de cérémonie non définie.'
            ], 400);
        }

        $aujourdHui = Carbon::today();

        if ($ceremonie->ceremonies->date->isSameDay($aujourdHui)) {
            // C’est le jour J
            // Tu peux clôturer l’invitation ici si tu veux
            $ceremonie->update(['status' => 'close']);

            return response()->json([
                'reponse' => true,
                'message' => 'Accès autorisé à la cérémonie.'
            ]);
        } else {
            return response()->json([
                'reponse' => false,
                'message' => 'Ce n’est pas encore le jour de la cérémonie.'
            ]);
        }

    }
}
