<?php
namespace App\Filament\Pages;

use App\Mail\InvitationMail;
use App\Models\Ceremonie;
use App\Models\Groupe;
use App\Models\Guest;
use App\Models\Invitation;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendInvitations extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static string $view             = 'filament.pages.send-invitations';
    protected static ?string $slug            = 'send-invitations'; // Lien personnalisé
    protected static ?string $title           = 'Envoyer des Invitations';
    protected static ?string $navigationLabel = 'Envoyer Invitations';
    public $selectedGuests                    = [];
    public $ceremonieId;
    public $table;
    public $message = '';

    public function mount()
    {
        // $this->form->fill([
        //     'message' => "Bonjour {categorie} {nom},\n\nVous êtes invité à notre événement !\n\nDétails de la cérémonie : {ceremony}\n\nÀ très bientôt !",
        // ]);
    }

    public function submit()
    {
        if (empty($this->selectedGuests)) {
            Notification::make()->title("Erreur")->body("Veuillez sélectionner au moins un invité.")->danger()->send();
            return;
        }

        if (! $this->ceremonieId) {
            Notification::make()->title("Erreur")->body("Veuillez choisir une cérémonie.")->danger()->send();
            return;
        }
        if (! $this->table) {
            Notification::make()->title("Erreur")->body("Veuillez choisir une table.")->danger()->send();
            return;
        }

        $ceremony = Ceremonie::find($this->ceremonieId);
        $guests   = Guest::whereIn('id', $this->selectedGuests)->get();

        foreach ($guests as $guest) {
            // Générer la référence unique
            do {
                $reference = "INV-" . date('Ymd') . "-" . strtoupper(Str::random(6));
            } while (Invitation::where('reference', $reference)->exists());

            $customMessage = str_replace(
                ['{adresse}', '{categorie}', '{nom}', '{ceremony}'],
                [$ceremony->adresse, $guest->type, $guest->nom, $ceremony->nom],
                $this->message ?? ''
            );
            Log::info("Merssage perso: " . $customMessage);

            // if (! is_string($customMessage)) {
            //     $customMessage = ''; // ✅ Sécurisation
            // }
            // Créer l'invitation en base de données
            $invitation = Invitation::create([
                'guest_id'     => $guest->id,
                'groupe_id'    => $guest->id,
                'ceremonie_id' => $this->ceremonieId,
                'groupe_id'    => $this->table,
                'status'       => 'send',
                'message'      => $customMessage,
                'reference'    => $reference,
            ]);

            //
            if ($guest->phone || $guest->email) {
                // Envoyer un SMS
                if ($guest->phone != null) {
                    $titre=Ceremonie::find($this->ceremonieId)->event->nom;
                    $messages = $titre." Cher(e) " . $guest->type . " " . $guest->nom . " C’est avec une immense joie que nous vous invitons à célébrer notre mariage " . $ceremony->nom .
                    ". cliquez sur ce lien pour confirmer votre présence " . "https://event.kwetu.cd/invitation.show/" . $invitation->reference;

                    $smsResponse = $this->sendSms($guest->phone, $messages);

                    // Log::info("SMS : " . $smsResponse);
                    if ($smsResponse['status_code'] === true) {
                        Log::info("SMS envoyé avec succès au numéro : " . $guest->phone);

                        Notification::make()->title("Succès")->body("Les invitations ont été envoyées par SMS avec succès.")->success()->send();
                    } else {
                        Log::error("Erreur lors de l'envoi du SMS au numéro : " . $guest->phone);

                        Notification::make()->title("Erreur")->body("Erreur lors de l'envoi du SMS au numéro : " . $guest->phone)->danger()->send();
                    }
                }
                if ($guest->email != null) {
                    $sujet = Ceremonie::find($this->ceremonieId);
                    $m     = htmlspecialchars(strip_tags($customMessage));

                    // Envoyer l'email avec le lien de l'invitation
                    Mail::to($guest->email)->send(new InvitationMail($guest, $invitation, $m, $sujet->event->nom));

                    Notification::make()->title("Succès")->body("Les invitations ont été envoyées par mail avec succès.")->success()->send();

                }
            }

            // Envoyer l'email avec le lien de l'invitation
            // Mail::to($guest->email)->send(new InvitationMail($guest, $invitation, $m ,$sujet->event->nom));
        }

    }
    public function updatedCeremonieId($state, Set $set)
    {
        Log::info("updatedCeremonyId() appelée avec ceremonyId : " . json_encode($state));

        if ($state) {
            $ceremony = Ceremonie::find($state);

            if ($ceremony && ! empty($ceremony->description)) {
                Log::info("Cérémonie trouvée : " . $ceremony->nom . " - Description : " . $ceremony->description);

                Notification::make()
                    ->title("Succès")
                    ->body("Message rempli ")
                    ->success()
                    ->send();
                // $set('message', "silas");
            } else {
                Log::warning("Cérémonie sélectionnée mais sans description ou non trouvée.");

                // $set('message', "silas vide"); // Cache le champ si aucune cérémonie n'est sélectionnée
                Notification::make()
                    ->title("Erreur")
                    ->body("La cérémonie sélectionnée ne contient pas de description.")
                    ->warning()
                    ->send();
            }
        } else {
            Notification::make()->title("Erreur")->body(" La cérémonie sélectionnée n'a pas d'ID valide.")->warning()->send();

        }
    }

    protected function getFormSchema(): array
    {
        return [
            Group::make([
                Section::make("Formulaire")->schema([
                    Select::make('selectedGuests')
                        ->label('Sélectionner les invités')
                        ->options(Guest::withValidEmail()->pluck('nom', 'id')) // Récupère les noms et IDs
                        ->searchable()                       // Permet la recherche dans la liste
                        ->multiple()
                        ->columnSpan(12) // Permet la sélection multiple
                        ->preload()
                        ->required(),

                    Select::make('ceremonieId')
                        ->label('Choisir une cérémonie')
                        ->options(Ceremonie::pluck('nom', 'id'))
                        ->searchable()
                        ->reactive() // 🔥 Rend le champ dynamique
                        ->columnSpan(6)
                        ->afterStateUpdated(function ($state, Set $set) {
                            $ceremony = Ceremonie::find($state);

                            if ($ceremony && isset($ceremony->description)) { // 🔹 Vérifie si la cérémonie existe et si `description` est défini
                                $set('message', $ceremony->description);
                            } else {
                                $set('message', ''); // 🔹 Met un message vide si la cérémonie n’a pas de description
                            }
                        })
                        ->required(),
                    Select::make('table')
                        ->label('Choisir une table')
                        ->options(Groupe::pluck('nom', 'id'))
                        ->searchable()
                        ->columnSpan(6)
                        ->required(),

                    // Textarea::make('message')
                    //     ->label('Message personnalisé')
                    // // ->helperText("Utilisez {nom} pour le prénom de l'invité et {ceremony} pour le nom de la cérémonie.")
                    //     ->rows(5)
                    //     ->default('') // 🔹 Définit une valeur vide par défaut pour éviter l'erreur
                    //     ->columnSpan(12)
                    //     ->reactive()                           // 🔥 Rend le champ dynamique
                    //     ->hidden(fn($get) => ! $get('message')) // Cache le champ si `message` est vide
                    //     ->required(),
                    RichEditor::make('message')
                        ->label(label: 'Message personnalisé')
                        ->reactive()                           // 🔥 Rend le champ dynamique
                        ->hidden(fn($get) => ! $get('message')) // Cache le champ si `message` est vide
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->columnSpanFull(),
                    // 🔹 Ajout du bouton via Actions::make([])
                    // Actions::make([
                    //     Actions\Action::make('envoyerInvitations')
                    //         ->label('Envoyer les invitations')
                    //         ->action(fn() => $this->envoyerInvitations()) // Appelle la méthode d'envoi
                    //         ->color('primary')
                    //         ->icon('heroicon-o-paper-airplane'),
                    // ])->columnSpanFull(), // 🔥 Permet d'afficher sur toute la largeur               // 🔥 Occupe toute la largeur
                ])->columnS(12),
            ])->columnSpanFull(),
        ];
    }
    protected function getFormActions(): array
    {
        return []; // 🔥 Supprime tous les boutons automatiques ajoutés par Filament
    }
    public function sendSms($phoneNumber, $message)
    {
        // 🔹 Vérification : Si le numéro est vide, ne pas envoyer de SMS
        if (empty($phoneNumber)) {
            Notification::make()->title("Erreur")->body("Le numéro de téléphone est vide")->danger()->send();
            Log::error("Erreur : Le numéro de téléphone est vide. ");

            // return [
            //     "status_code" => 400,
            //     "response"    => "Erreur : Le numéro de téléphone est vide.",
            // ];
        }

        // 🔹 Vérification : Si le numéro n'est pas valide, ne pas envoyer de SMS
        if (! $this->isValidPhoneNumber($phoneNumber)) {
            Notification::make()->title("Erreur")->body("Le numéro de téléphone n'est pas valide.")->danger()->send();
            Log::error("Erreur : Le numéro de téléphone n'est pas valide. ");
            // return [
            //     "status_code" => 400,
            //     "response"    => "Erreur : Le numéro de téléphone n'est pas valide.",
            // ];
        }

        // URL de l'API de Keccel (remplacez par l'URL réelle)
        $apiUrl = 'https://api.keccel.com/sms/v2/message.asp';
        $apiKey = 'KR9DP24WQK5BF4A';

        // Données à envoyer
        $postData = [
            "token"   => $apiKey,
            "to"      => $phoneNumber,
            "from"    => 'DGRAD',
            "message" => $message,
        ];
        // dd( $postData);

        // Initialisation de cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey",
        ]);

        // Exécuter la requête
        $response = curl_exec($ch);

        // Vérifier les erreurs
        if (curl_errno($ch)) {
            echo "Erreur cURL : " . curl_error($ch);
        }

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            "status_code" => true,
            "response"    => json_decode($response, true),
        ];
    }
    public function isValidPhoneNumber($phoneNumber)
    {
        // 🔹 Vérification du format international (+XXX123456789)
        return preg_match('/^\+?[1-9]\d{9,14}$/', $phoneNumber);
    }

}
