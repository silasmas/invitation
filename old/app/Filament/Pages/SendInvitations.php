<?php
namespace App\Filament\Pages;

use App\Mail\InvitationMail;
use App\Models\Ceremonie;
use App\Models\Groupe;
use App\Models\Guest;
use App\Models\Invitation;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendInvitations extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static string $view            = 'filament.pages.send-invitations';

    public $selectedGuests         = [];
    public string $messageWhatsapp = "Bonjour {nom}, vous êtes invité à notre événement via WhatsApp !";
    public string $messageEmail    = "Bonjour {nom}, ceci est une invitation par email.";
    public string $messageSms      = "Bonjour {nom}, message par SMS.";

    public $ceremonieId;
    public $table;
    public $message              = '';
    public string $activeChannel = 'whatsapp'; // par défaut

    public function mount(): void
    {
        // Éventuellement précharger des choses ici
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
        if ($this->Invitation()) {
            match ($this->activeChannel) {
                'whatsapp' => $this->envoyerViaWhatsapp(),
                'email' => $this->envoyerViaEmail(),
                'sms' => $this->envoyerViaSms(),
            };
        } else {
            Notification::make()
                ->title('Erreur')
                ->body("Impossible de créer les invitations.")
                ->danger()
                ->send();
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
            Radio::make('activeChannel')
                ->label('Canal d’envoi')
                ->options([
                    'whatsapp' => 'WhatsApp',
                    'email'    => 'Email',
                    'sms'      => 'SMS',
                ])
                ->default('whatsapp')
                ->inline()
                ->required()
                ->reactive(),
            Tabs::make('Modes d\'envoi')
                ->tabs([

                    Tabs\Tab::make('WhatsApp')
                        ->visible(fn($get) => $get('activeChannel') === 'whatsapp')
                        ->schema([
                            Section::make("Formulaire")->schema([
                                Select::make('selectedGuests')
                                    ->label('Invités (WhatsApp)')
                                    ->columnSpan(12)
                                    ->options(
                                        Guest::whereNotNull('phone')
                                            ->where('phone', '!=', '')
                                            ->pluck('nom', 'id')
                                    )
                                    ->searchable()
                                    ->multiple()
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
                                // Textarea::make('messageWhatsapp')
                                //     ->label('Message WhatsApp')
                                //     ->helperText('Utilisez {nom} pour personnaliser le message')
                                //     ->rows(5)
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
                            ])->columnS(12),
                        ]),

                    Tabs\Tab::make('Email')
                        ->visible(fn($get) => $get('activeChannel') === 'email')
                        ->schema([
                            Select::make('selectedGuests')
                                ->label('Invités (Email)')
                                ->options(
                                    Guest::whereNotNull('email')
                                        ->where('email', '!=', '')
                                        ->pluck('nom', 'id')
                                )
                                ->searchable()
                                ->multiple()
                                ->required(),

                            Textarea::make('messageEmail')
                                ->label('Message Email')
                                ->helperText('Utilisez {nom} pour personnaliser le message')
                                ->rows(5)
                                ->required(),
                        ]),

                    Tabs\Tab::make('SMS')
                        ->visible(fn($get) => $get('activeChannel') === 'sms')
                        ->schema([
                            Select::make('selectedGuests')
                                ->label('Invités (SMS)')
                                ->options(
                                    Guest::whereNotNull('phone')
                                        ->where('phone', '!=', '')
                                        ->pluck('nom', 'id')
                                )
                                ->searchable()
                                ->multiple()
                                ->required(),

                            Textarea::make('messageSms')
                                ->label('Message SMS')
                                ->helperText('Utilisez {nom} pour personnaliser le message')
                                ->rows(5)
                                ->required(),
                        ]),
                ]),
        ];
    }

    public function envoyerViaWhatsapp()
    {
        // message WhatsApp + boucle sur guests + wa.me ou API
        $guestIds = $this->selectedGuests;

        // Sélectionner uniquement les invités qui ont une invitation et un numéro
        $guests = Guest::whereIn('id', $guestIds)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->whereHas('invitation', function ($query) {
                $query->whereNotNull('message');
            })
            ->get();

        // Sauvegarder les invités valides dans la session
        session()->put('guest_ids', $guests->pluck('id')->toArray());

        // ✅ Rediriger vers la page des liens
        return redirect()->route('filament.admin.pages.whatsapp');

    }

    public function envoyerViaEmail()
    {
        // message email + boucle + Mail::to()->send()
    }

    public function envoyerViaSms()
    {
        // message SMS + boucle + API SMS (Keccel ou Twilio)
        // if ($guest->phone || $guest->email) {
        //     // Envoyer un SMS
        //     if ($guest->phone != null) {
        //         $titre    = Ceremonie::find($this->ceremonieId)->event->nom;
        //         $messages = $titre . " Cher(e) " . $guest->type . " " . $guest->nom . " C’est avec une immense joie que nous vous invitons à célébrer notre mariage " . $ceremony->nom .
        //         ". cliquez sur ce lien pour confirmer votre présence " . "https://event.kwetu.cd/invitation.show/" . $invitation->reference;

        //         $smsResponse = $this->sendSms($guest->phone, $messages);

        //         // Log::info("SMS : " . $smsResponse);
        //         if ($smsResponse['status_code'] === true) {
        //             Log::info("SMS envoyé avec succès au numéro : " . $guest->phone);

        //             Notification::make()->title("Succès")->body("Les invitations ont été envoyées par SMS avec succès.")->success()->send();
        //         } else {
        //             Log::error("Erreur lors de l'envoi du SMS au numéro : " . $guest->phone);

        //             Notification::make()->title("Erreur")->body("Erreur lors de l'envoi du SMS au numéro : " . $guest->phone)->danger()->send();
        //         }
        //     }
        //     if ($guest->email != null) {
        //         $sujet = Ceremonie::find($this->ceremonieId);
        //         $m     = htmlspecialchars(strip_tags($customMessage));

        //         // Envoyer l'email avec le lien de l'invitation
        //         Mail::to($guest->email)->send(new InvitationMail($guest, $invitation, $m, $sujet->event->nom));

        //         Notification::make()->title("Succès")->body("Les invitations ont été envoyées par mail avec succès.")->success()->send();

        //     }
        // }
    }

    public function Invitation(): bool
    {
        try {
            $ceremony = Ceremonie::find($this->ceremonieId);
            $guests   = Guest::whereIn('id', $this->selectedGuests)->get();

            foreach ($guests as $guest) {
                // Générer une référence unique
                do {
                    $reference = "INV-" . date('Ymd') . "-" . strtoupper(Str::random(6));
                } while (Invitation::where('reference', $reference)->exists());

                // Message personnalisé
                $customMessage = str_replace(
                    ['{adresse}', '{categorie}', '{nom}', '{ceremony}'],
                    [$ceremony->adresse, $guest->type, $guest->nom, $ceremony->nom],
                    $this->message ?? ''
                );

                // Création ou mise à jour de l’invitation
                Invitation::updateOrCreate(
                    [
                        'guest_id'     => $guest->id,
                        'ceremonie_id' => $this->ceremonieId,
                        'groupe_id'    => $this->table,
                    ],
                    [
                        'status'    => 'send',
                        'message'   => $customMessage,
                        'reference' => $reference,
                    ]
                );
            }

            return true; // ✅ succès
        } catch (\Throwable $e) {
            Log::error("Erreur lors de la création des invitations : " . $e->getMessage());
            return false; // ❌ échec
        }
    }


}
