<?php
namespace App\Mail;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;
    public $invitation;
    public $customMessage;
    public $lienInvitation;
    public $oranisateur;
    public function __construct(Guest $guest, Invitation $invitation, $customMessage,$oranisateur)
    {
        $this->guest          = $guest;
        $this->invitation     = $invitation;
        $this->oranisateur     = $oranisateur;
        $this->customMessage  = $customMessage ; // ✅ Vérification
        $this->lienInvitation = route('invitation.show', ['reference' => $this->invitation->reference]);
    }
    public function build()
    {
        return $this->subject('Votre Invitation pour le '.$this->oranisateur)
            ->view('emails.invitation')
            ->with([
                'nom'             => $this->guest->nom,
                'message'         =>$this->invitation,
                'lien_invitation' => $this->lienInvitation,

                // 'lien_invitation' => route('invitation.show', ['reference' => $this->invitation->reference]),
            ]);

    }
}
