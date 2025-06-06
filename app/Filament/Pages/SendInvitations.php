<?php
namespace App\Filament\Pages;

use App\Helpers\MessageHelper;
use App\Mail\InvitationMail;
use App\Models\Ceremonie;
use App\Models\Groupe;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Message;
use App\Services\LienCourt;
use Carbon\Carbon;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendInvitations extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $permission = 'access_stats_dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static string $view            = 'filament.pages.send-invitations';

    public $selectedGuests         = [];
    public string $messageWhatsapp = "Bonjour {nom}, vous êtes invité à notre événement via WhatsApp !";
    public string $messageEmail    = "Bonjour {nom}, ceci est une invitation par email.";
    public string $messageSms      = "Bonjour {categorie} {nom}, Vous etes attendu(e) à la cérémonie du mariage {ceremony} de {femme} et {homme} {date}.
    Merci de ne pas oublier votre QR Code pour accéder à la cérémonie.";

    public ?int $ceremonieId = null;

    public $table;
    public $message              = '';
    public string $activeChannel = 'whatsapp'; // par défaut

    // protected static string $resource = MessageResource::class;

    // ID de la cérémonie sélectionnée
    public ?int $selectedCeremonieId  = null;
    public ?string $selectedMessageId = null;

    // Tableau des messages liés à la cérémonie
    public array $messagesDisponibles = [];

    protected $listeners = ['open-message-modal'];

    /**
     * Écouteur déclenché par le Select quand une cérémonie est sélectionnée.
     * Il récupère tous les messages liés et déclenche l’ouverture de la modale.
     */
    #[\Livewire\Attributes\On('open-message-modal')]
    public function openMessageModal($ceremonyId): void
    {
        $this->selectedCeremonieId = $ceremonyId;

        // Récupérer les messages liés à cette cérémonie
        $this->messagesDisponibles = Message::where('ceremonie_id', $ceremonyId)->get()->toArray();

        // Déclencher l’ouverture de la modale via JS
        $this->dispatchBrowserEvent('openModal', ['id' => 'modal-select-message']);
    }

    /**
     * Remplit le champ `message` avec le contenu choisi
     * depuis la modale, et ferme la modale.
     */
    public function remplirMessage(string $contenu): void
    {
        $this->message = $contenu;

        // Fermer la modale via JS
        $this->dispatchBrowserEvent('closeModal', ['id' => 'modal-select-message']);
    }

    public function mount(): void
    {
        // Éventuellement précharger des choses ici
    }

    public function submit()
    {
        if (empty($this->selectedGuests)) {
            Notification::make()
                ->title("Erreur")
                ->body("Veuillez sélectionner au moins un invité.")
                ->danger()
                ->send();
            return;
        }

        if (! $this->ceremonieId) {
            Notification::make()
                ->title("Erreur")
                ->body("Veuillez choisir une cérémonie.")
                ->danger()
                ->send();
            return;
        }

        if (! $this->table && $this->activeChannel === 'whatsapp') {
            Notification::make()
                ->title("Erreur")
                ->body("Veuillez choisir une table.")
                ->danger()
                ->send();
            return;
        }

        if ($this->smsCount > 3) {
            Notification::make()
                ->title('Message trop long')
                ->body("Le message dépasse 3 SMS (actuellement {$this->smsCount}). Réduisez-le avant d’envoyer.")
                ->danger()
                ->send();
            return;
        }

        // ✅ Ici, on vérifie explicitement si la création d’invitation a échoué
        if (! $this->Invitation()) {
            Notification::make()
                ->title('Erreur')
                ->body("Impossible de créer les invitations.")
                ->danger()
                ->send();
            return;
        }

        // ✅ Si tout est bon → on continue
        $guestIds = array_filter($this->selectedGuests);

        $guests = Guest::whereIn('id', $guestIds)
            ->whereHas('invitation', fn($query) => $query->whereNotNull('message'))
            ->with(['invitation', 'invitation.ceremonies.event'])
            ->get()
            ->filter(function ($guest) {
                return match ($this->activeChannel) {
                    'whatsapp', 'sms' => MessageHelper::isValidPhone($guest->phone ?? ''),
                    'email' => MessageHelper::isValidEmail($guest->email ?? ''),
                    default => false,
                };
            })->values();

        match ($this->activeChannel) {
            'whatsapp' => $this->envoyerViaWhatsapp($guests, $this->message, $this->ceremonieId),
            'email' => $this->envoyerViaEmail($guests),
            'sms' => $this->envoyerViaSms($guests),
            'enDure' => $this->envoyerEnDure($guests),
        };

        return;
    }

    public function updatedCeremonieId($state, Set $set)
    {
        Log::info("updatedCeremonyId() appelée avec ceremonyId : " . json_encode($state));

        if ($state) {
            $ceremony = Ceremonie::find($state);
            // Charger les messages liés à la cérémonie
            $this->messagesDisponibles = \App\Models\Message::where('ceremonie_id', $state)->pluck('titre', 'id')->toArray();

            // Réinitialiser le message sélectionné
            $this->selectedMessageId = null;
            $this->message           = '';

            if ($ceremony && ! empty($this->messagesDisponibles)) {
                Log::info("Cérémonie trouvée : " . $ceremony->nom . " - Description : " . $ceremony->description);
                Notification::make()
                    ->title("Succès")
                    ->body("Message rempli ")
                    ->success()
                    ->send();
                //  $set('message', "silas");
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
    public function updatedSelectedMessageId($value): void
    {
        if ($value) {
            $contenu       = \App\Models\Message::find($value)?->message;
            $this->message = $contenu ?? '';
        }
    }
    protected function getFormSchema(): array
    {
        return [
            Radio::make('activeChannel')
                ->label('Canal d’envoi')
                ->options([
                    'enDure'   => 'En dure',
                    'whatsapp' => 'WhatsApp',
                    'email'    => 'Email',
                    'sms'      => 'SMS (Juste pour rappeler les invités)',
                ])
                ->default('whatsapp')
                ->inline()
                ->required()
                ->reactive(),
            Tabs::make('Modes d\'envoi')
                ->tabs([

                    Tabs\Tab::make('WhatsApp ou SMS')
                        ->visible(fn($get) => $get('activeChannel') === 'whatsapp' || $get('activeChannel') === 'sms')
                        ->schema([
                            Section::make("")->schema([
                                Select::make('selectedGuests')
                                    ->label('Invités (WhatsApp)')
                                    ->columnSpan(12)
                                    // ->orderBy('nom')
                                    ->options(
                                        Guest::whereNotNull('phone')
                                            ->whereRaw("TRIM(phone) != ''")
                                            ->get()
                                            ->mapWithKeys(function ($guest) {
                                                return [
                                                    $guest->id => "{$guest->nom} (" . $guest->phone . ")",
                                                ];
                                            })
                                            ->toArray()
                                    )
                                // ->options(
                                //     Guest::whereNotNull('phone')
                                //         ->where('phone', '!=', '')
                                //         ->where('phone', 'REGEXP', '^\\+[0-9]{12}$')
                                //         ->pluck('nom', 'id')
                                // )
                                    ->searchable()
                                    ->multiple()
                                    ->required(),
                                // Sélection de la cérémonie
                                Select::make('ceremonieId')
                                    ->label('Choisir une cérémonie')
                                    ->columnSpan(4)
                                    ->options(Ceremonie::pluck('nom', 'id')->toArray()) // options sous forme [id => nom]
                                    ->searchable()
                                    ->reactive() // Rend le champ dynamique
                                    ->afterStateUpdated(fn($state) =>
                                        // Déclenche l’événement Livewire personnalisé
                                        $this->dispatch('open-message-modal', ceremonieId: $state)
                                    )
                                    ->required(),

                                Select::make('table')
                                    ->visible(fn($get) => $get('activeChannel') === 'whatsapp')
                                    ->label('Choisir une table')
                                    ->options(Groupe::pluck('nom', 'id'))
                                    ->searchable()
                                    ->columnSpan(4)
                                    ->required(),
                                Select::make('selectedMessageId')
                                    ->label('Choisir un message lié')
                                    ->options(fn() => $this->messagesDisponibles)
                                    ->searchable()
                                    ->hidden(fn() => empty($this->messagesDisponibles)) // Caché tant qu’aucun message n’est dispo
                                    ->reactive()
                                    ->columnSpan(4),
                                RichEditor::make('message')
                                    ->visible(fn($get) => $get('activeChannel') === 'whatsapp')
                                    ->label(label: 'Message personnalisé')
                                    ->helperText("Utilisez {categorie} {nom} pour Mr nom sur l'invitation, {ceremony} pour le nom de la cérémonie,
                                    {date} pour la date et l'huere de la ceremonie,{femme} et {homme}pour les noms des mariés, {lien} pour le lien vers l'invitation")
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

                                Group::make([
                                    Textarea::make('messageSms')
                                        ->helperText("Utilisez {categorie} {nom} pour Mr nom sur l'invitation, {ceremony} pour le nom de la cérémonie,
                                    {date} pour la date et l'huere de la ceremonie,{femme} et {homme}pour les noms des mariés, {lien} pour le lien vers l'invitation")
                                        ->label('Message à envoyer (SMS)')
                                        ->required()
                                        ->ascii()
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->ascii()
                                        ->reactive()
                                        ->maxLength(480) // sécurité supplémentaire
                                        ->visible(fn($get) => $get('activeChannel') === 'sms')
                                        ->extraAttributes([
                                            'x-model' => 'rawMessage',
                                        ]),

                                    View::make('filament.components.sms-counter')->columnSpanFull()
                                        ->visible(fn($get) => $get('activeChannel') === 'sms'),
                                    View::make('filament.components.preview-message')
                                        ->visible(fn($get) => $get('activeChannel') === 'sms')
                                    // ->visible(fn($get) => filled($get('messageSms')))
                                        ->columnSpanFull()->extraAttributes([
                                        'wire:model.debounce.1000ms' => 'messageSms',
                                    ]),
                                    View::make('filament.components.envoyer-button')
                                        ->columnSpanFull()
                                        ->visible(fn($get) => $get('activeChannel') === 'sms' || $get('activeChannel') === 'whatsapp'),

                                ])->columnSpanFull(),

                            ])->columnS(12),
                        ]),

                    Tabs\Tab::make('Email')
                        ->visible(fn($get) => $get('activeChannel') === 'email')
                        ->schema([
                            Section::make("")->schema([
                                Select::make('selectedGuests')
                                    ->label('Invités (par Mail)')
                                    ->columnSpan(12)
                                    ->options(
                                        Guest::whereNotNull('email')
                                            ->where('email', '!=', '')
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
                    Tabs\Tab::make('En dure')
                        ->visible(fn($get) => $get('activeChannel') === 'enDure')
                        ->schema([
                            Section::make("")->schema([
                                Select::make('selectedGuests')
                                    ->label('Invités (Tous les invités)')
                                    ->columnSpan(12)
                                    ->options(
                                        Guest::whereNotNull('nom')
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
                                    ->required(),
                                Select::make('table')
                                    ->label('Choisir une table')
                                    ->options(Groupe::pluck('nom', 'id'))
                                    ->searchable()
                                    ->columnSpan(6)
                                    ->required(),
                            ])->columnS(12),
                        ]),

                ]),

        ];
    }
    protected function getFormActions(): array
    {
        return []; // Empêche l’apparition du bouton de soumission par défaut
    }

    public function getCleanMessageProperty(): string
    {
        return preg_replace('/[^a-zA-Z0-9 ,.!?@()\'"\/\-:]/', '', $this->messageSms ?? '');
    }

    public function getSmsCountProperty(): int
    {
        $length = strlen($this->cleanMessage);
        return max(1, (int) ceil($length / 160));
    }

    public function updatedMessageSms()
    {
        if ($this->smsCount >= 3) {

            Notification::make()
                ->title('Message trop long')
                ->body('Le message dépasse 3 SMS (480 caractères max autorisés). Veuillez le raccourcir.')
                ->danger()
                ->send();

            return;
        }
    }

    public function envoyerViaWhatsapp($guests, $messageTxt, $ceremonie)
    {
        // Sauvegarder les invités valides dans la session
        session()->put('guest_ids', $guests->pluck('id')->toArray());
        session()->put('messageTxt', $messageTxt);
        session()->put('ceremonie', $ceremonie);
        // dd($ceremonie);
        // ✅ Rediriger vers la page des liens
        return redirect()->route('filament.admin.pages.whatsapp');

    }

    public function envoyerViaEmail($guests)
    {
        // Vérifie s'il y a des invités valides
        if ($guests->isEmpty()) {
            Notification::make()
                ->title("Aucun invité éligible")
                ->body("Aucun invité avec e-mail valide ou invitation complète.")
                ->danger()
                ->send();
            return;
        }
        $emailsEnvoyes = 0;
        $emailsErreurs = 0;
        foreach ($guests as $guest) {
            // 🔹 Récupérer l'invitation liée à la cérémonie en cours
            // dd($this->ceremonieId );
            $invitation = Invitation::where('guest_id', $guest->id)
                ->where('ceremonie_id', $this->ceremonieId)
                ->with('ceremonies.event')
                ->first();
            $customMessage = $invitation->message ?? '';
            $sujet         = $invitation->ceremonie->event->nom ?? 'Invitation';

            $messageFinal = htmlspecialchars(strip_tags($customMessage));
            if ($guest->email) {
                try {
                    Mail::to($guest->email)->send(new InvitationMail($guest, $invitation, $messageFinal, $sujet));
                    $emailsEnvoyes++;
                } catch (\Throwable $e) {
                    // Tu peux logger ou gérer les erreurs individuellement si besoin
                    Log::error("Erreur d'envoi mail à {$guest->email} : " . $e->getMessage());
                    $emailsErreurs++;
                }
            }
        }

        // ✅ Notification globale
        Notification::make()
            ->title('Résultat des envois')
            ->success()
            ->body("
        ✉️ Emails envoyés : {$emailsEnvoyes} (erreurs : {$emailsErreurs})")
            ->send();
    }
    public function envoyerEnDure($guests)
    {

        // ✅ Notification globale
        Notification::make()
            ->title('Résultat des envois')
            ->success()
            ->body("Enregistrer")
            ->send();
    }
    public function envoyerViaSms($guests)
    {
        // dd($this->smsCount);

        // Vérifie s'il y a des invités valides
        if ($guests->isEmpty()) {
            Notification::make()
                ->title("Aucun invité éligible")
                ->body("Aucun invité avec le numéro de téléphone valide ou invitation complète.")
                ->danger()
                ->send();
            return;
        }
        $smsEnvoyes = 0;
        $smsErreurs = 0;
        foreach ($guests as $guest) {
            $invitation = Invitation::where('guest_id', $guest->id)
                ->where('ceremonie_id', $this->ceremonieId)
                ->with('ceremonies.event')
                ->first();
            $ceremony     = $invitation->ceremonies;
            $event        = $ceremony->event ?? null;
            $titre        = $event->nom ?? 'Invitation';
            $messageFinal = htmlspecialchars(strip_tags($invitation->msgRappel));

            // ✅ Envoi par SMS
            if ($guest->phone) {
                $msg = MessageHelper::cleanMessageForSms($messageFinal, 500);

                $smsResponse = $this->sendSms($guest->phone, $msg);

                if ($smsResponse['status_code'] === true) {
                    Log::info("SMS envoyé à : {$guest->phone}");
                    $smsEnvoyes++;
                } else {
                    Log::error("Échec SMS à : {$guest->phone}");
                    $smsErreurs++;
                }
            }
        }
        // ✅ Notification globale
        Notification::make()
            ->title('Résultat des envois')
            ->success()
            ->body("
            📱 SMS envoyés : {$smsEnvoyes} (erreurs : {$smsErreurs})
        ")
            ->send();
    }

    public function Invitation(): bool
    {
        //    dd($this->ceremonieId);
        try {
            $ceremony = Ceremonie::find($this->ceremonieId);
            $guests   = Guest::whereIn('id', $this->selectedGuests)->get();

            foreach ($guests as $guest) {
                // Générer une référence unique
                do {
                    // $reference = "INV-" . date('Ymd') . "-" . strtoupper(Str::random(6));
                    $reference = "INV-{$this->ceremonieId}-" . date('Ymd') . "-" . strtoupper(Str::random(6));

                } while (Invitation::where('reference', $reference)->exists());
                $msg   = "";
                $moyen = "";
                switch ($this->activeChannel) {
                    case 'whatsapp':
                        $msg   = $this->message;
                        $moyen = "whatsapp";
                        break;
                    case 'email':
                        $msg   = $this->message;
                        $moyen = "email";
                        break;
                    case 'sms':
                        $msg   = $this->messageSms;
                        $moyen = "sms";
                        break;
                    case 'enDure':
                        $moyen = "enDure";
                        break;
                }
                $now          = Carbon::now()->startOfDay();
                $ceremonyDate = $ceremony->date->startOfDay();

                if ($ceremonyDate->equalTo($now)) {
                    $date = "aujourd'hui à " . $ceremony->date->format('H\hi');
                } else {
                    $date = "le " . $ceremony->date->format('d/m/Y') . " à " . $ceremony->date->format('H\hi');
                }
                                                                             // dd($reference."----".$this->ceremonieId);
                $lien = LienCourt::generate($reference, $this->ceremonieId); // on le crée juste après

                // Message personnalisé

                $customMessage = str_replace(
                    ['{homme}', '{femme}', '{adresse}', '{categorie}', '{nom}', '{ceremony}', '{date}', '{lien}'],
                    [
                        e($ceremony->event->homme),
                        e($ceremony->event->femme),
                        e($ceremony->adresse),
                        e($guest->type),
                        e($guest->nom),
                        e($ceremony->nom),
                        e($date),
                        e($lien),
                    ],
                    $msg ?? ''
                );
                if ($this->activeChannel != "sms") {
                    // Création ou mise à jour de l’invitation
                    Invitation::updateOrCreate(
                        [
                            'guest_id'     => $guest->id,
                            'ceremonie_id' => $this->ceremonieId,
                            'reference'    => $reference,
                        ],
                        [
                            'groupe_id' => $this->table,
                            'status'    => 'send',
                            'message'   => $customMessage,
                            'moyen'     => $moyen,
                        ]
                    );
                } else {
                    Invitation::updateOrCreate(
                        [
                            'guest_id'     => $guest->id,
                            'ceremonie_id' => $this->ceremonieId,
                        ],
                        [
                            'rappel'    => true,
                            'msgRappel' => $customMessage,
                        ]
                    );
                }
            }

            return true; // ✅ succès
        } catch (\Throwable $e) {
            Log::error("Erreur lors de la création des invitations : " . $e->getMessage());
            return false; // ❌ échec
        }
    }
    public function sendSms($phoneNumber, $message)
    {
        // 🔹 Vérification : Si le numéro est vide, ne pas envoyer de SMS
        if (empty($phoneNumber)) {
            Notification::make()->title("Erreur")->body("Le numéro de téléphone est vide")->danger()->send();
            Log::error("Erreur : Le numéro de téléphone est vide. ");

        }

        // 🔹 Vérification : Si le numéro n'est pas valide, ne pas envoyer de SMS
        if (! MessageHelper::isValidPhone($phoneNumber)) {
            Notification::make()->title("Erreur")->body("Le numéro de téléphone n'est pas valide.")->danger()->send();
            Log::error("Erreur : Le numéro de téléphone n'est pas valide. ");

        }

        // URL de l'API de Keccel (remplacez par l'URL réelle)
        $apiUrl = 'https://api.keccel.com/sms/v2/message.asp';
        $apiKey = 'BAPK3A29RHG6QY2';
        $msg    = MessageHelper::cleanMessageForSms($message, 500);
// dd($msg);
        // Données à envoyer
        $postData = [
            "token"   => $apiKey,
            "to"      => $phoneNumber,
            "from"    => 'KWETU',
            "message" => $msg,
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
    public function getPreviewMessageProperty(): ?string
    {
        if (! $this->ceremonieId || empty($this->selectedGuests)) {
            return null;
        }

        $guest    = Guest::find($this->selectedGuests[0]); // Juste un invité pour l'aperçu
        $ceremony = Ceremonie::with('event')->find($this->ceremonieId);
                                                                      // dd("ref----".$this->ceremonieId);
        $lien = LienCourt::generate("reference", $this->ceremonieId); // on le crée juste après

        if (! $guest || ! $ceremony) {
            return null;
        }

        $msg = match ($this->activeChannel) {
            'whatsapp', 'email' => $this->message,
            'sms'               => $this->messageSms,
            default             => '',
        };

        $now          = now()->startOfDay();
        $ceremonyDate = $ceremony->date->startOfDay();

        $date = $ceremonyDate->equalTo($now)
        ? "aujourd'hui à " . $ceremony->date->format('H\hi')
        : "le " . $ceremony->date->format('d/m/Y') . " à " . $ceremony->date->format('H\hi');

        return str_replace(
            ['{homme}', '{femme}', '{adresse}', '{categorie}', '{nom}', '{ceremony}', '{date}', '{lien}'],
            [
                $ceremony->event->homme,
                $ceremony->event->femme,
                $ceremony->adresse,
                $guest->type,
                $guest->nom,
                $ceremony->nom,
                $date,
                $lien,
            ],
            $msg
        );
    }

}
