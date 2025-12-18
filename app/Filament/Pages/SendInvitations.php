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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class SendInvitations extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $permission = 'access_stats_dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static string $view = 'filament.pages.send-invitations';

    // Form state
    public array $selectedGuests = [];
    public string $messageWhatsapp = "Bonjour {nom}, vous êtes invité à notre événement via WhatsApp !";
    public string $messageEmail = "Bonjour {nom}, ceci est une invitation par email.";
    public string $messageSms = "Bonjour {categorie} {nom}, Vous etes attendu(e) à la cérémonie du mariage {ceremony} de {femme} et {homme} {date}. Merci de ne pas oublier votre QR Code pour accéder à la cérémonie.";

    public ?int $ceremonieId = null;
    public $table;
    public string $message = '';
    public string $activeChannel = 'whatsapp';
    public ?int $selectedCeremonieId = null;
    public ?string $selectedMessageId = null;
    public array $messagesDisponibles = [];
    public array $tableOptions = [];

    protected $listeners = ['open-message-modal'];

    // ----- helpers -----
    private function currentUser()
    {
        return Auth::user();
    }

    private function isSuperAdmin(): bool
    {
        $user = $this->currentUser();
        if (! $user) {
            return false;
        }
        return method_exists($user, 'hasRole')
            ? $user->hasRole('super_admin')
            : optional($user->role)->name === 'super_admin';
    }

    // ----- Livewire events / modals -----
    #[\Livewire\Attributes\On('open-message-modal')]
    public function openMessageModal($ceremonyId): void
    {
        $this->selectedCeremonieId = $ceremonyId;
        $this->messagesDisponibles = Message::where('ceremonie_id', $ceremonyId)
            ->pluck('titre', 'id')
            ->toArray();

        $this->dispatchBrowserEvent('openModal', ['id' => 'modal-select-message']);
    }

    public function remplirMessage(string $contenu): void
    {
        $this->message = $contenu;
        $this->dispatchBrowserEvent('closeModal', ['id' => 'modal-select-message']);
    }

    // ----- lifecycle -----
    public function mount(): void
    {
        // rien de spécial au mount
    }

    // ----- validation et envoi -----
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

        if (! $this->table && $this->activeChannel === 'whatsapp') {
            Notification::make()->title("Erreur")->body("Veuillez choisir une table.")->danger()->send();
            return;
        }

        if ($this->smsCount > 3) {
            Notification::make()
                ->title('Message trop long')
                ->body("Le message dépasse 3 SMS (actuellement {$this->smsCount}). Réduisez-le avant d’envoyer.")
                ->danger()->send();
            return;
        }

        // Vérifier que la cérémonie sélectionnée est autorisée pour l'utilisateur non super_admin
        $ceremony = Ceremonie::with('event')->find($this->ceremonieId);
        if (! $ceremony) {
            Notification::make()->title("Erreur")->body("Cérémonie introuvable.")->danger()->send();
            return;
        }
        if (! $this->isSuperAdmin()) {
            $user = $this->currentUser();
            if (! ($ceremony->event && $ceremony->event->user_id === $user->id && $ceremony->event->status !== 'termine')) {
                Notification::make()->title("Accès refusé")->body("Vous ne pouvez pas envoyer d'invitations pour cette cérémonie.")->danger()->send();
                return;
            }
        }

        if (! $this->createInvitations()) {
            Notification::make()->title('Erreur')->body("Impossible de créer les invitations.")->danger()->send();
            return;
        }

        $guestIds = array_filter($this->selectedGuests);

        $queryGuests = Guest::whereIn('id', $guestIds)
            ->with(['invitation' => fn($q) => $q->where('ceremonie_id', $this->ceremonieId)->with('ceremonies.event')]);

        // Filtrer les invités selon canal et validité (phone/email)
        $guests = $queryGuests->get()->filter(function ($guest) {
            return match ($this->activeChannel) {
                'whatsapp', 'sms' => MessageHelper::isValidPhone($guest->phone ?? ''),
                'email' => MessageHelper::isValidEmail($guest->email ?? ''),
                default => true,
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

    // ----- sélection dynamique -----
    public function updatedCeremonieId($state, Set $set)
    {
        Log::info("updatedCeremonyId() appelée avec ceremonyId : " . json_encode($state));

        if ($state) {
            $ceremony = Ceremonie::with(['groupe', 'event'])->find($state);

            // Vérifier droits pour l'utilisateur
            if (! $this->isSuperAdmin()) {
                $user = $this->currentUser();
                if (! ($ceremony && $ceremony->event && $ceremony->event->user_id === $user->id && $ceremony->event->status !== 'termine')) {
                    Notification::make()->title("Accès refusé")->body("Cérémonie non accessible.")->warning()->send();
                    $this->messagesDisponibles = [];
                    $this->tableOptions = [];
                    return;
                }
            }

            $this->messagesDisponibles = Message::where('ceremonie_id', $state)->pluck('titre', 'id')->toArray();
            $this->tableOptions = Groupe::where('ceremonie_id', $state)->pluck('nom', 'id')->toArray();
            $this->selectedMessageId = null;
            $this->message = '';

            if ($ceremony && ! empty($this->messagesDisponibles)) {
                Notification::make()->title("Succès")->body("Messages disponibles chargés")->success()->send();
            } else {
                Notification::make()->title("Info")->body("Aucun message défini pour cette cérémonie")->warning()->send();
            }
        } else {
            Notification::make()->title("Erreur")->body("Cérémonie invalide")->warning()->send();
        }
    }

    public function updatedSelectedMessageId($value): void
    {
        if ($value) {
            $contenu = Message::find($value)?->message;
            $this->message = $contenu ?? '';
        }
    }

    // ----- form schema (filtré selon droits) -----
    protected function getFormSchema(): array
    {
        $isSuper = $this->isSuperAdmin();
        $user = $this->currentUser();

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

            Tabs::make('Modes d\'envoi')->tabs([

                Tabs\Tab::make('WhatsApp ou SMS')
                    ->visible(fn($get) => $get('activeChannel') === 'whatsapp' || $get('activeChannel') === 'sms')
                    ->schema([
                        Section::make("")->schema([

                            Select::make('selectedGuests')
                                ->label('Invités (WhatsApp)')
                                ->columnSpan(12)
                                ->options(function () use ($isSuper, $user) {
                                    $q = Guest::query()
                                        ->whereNotNull('phone')
                                        ->whereRaw("TRIM(phone) != ''");

                                    // Pour les utilisateurs non super admin, filtrer par événement de l'invité
                                    // (et non plus par invitation existante), afin d'inclure aussi les nouveaux invités
                                    if (! $isSuper) {
                                        $q->whereHas('event', function (Builder $qe) use ($user) {
                                            $qe->where('user_id', $user->id)
                                               ->where('status', '!=', 'termine');
                                        });
                                    }

                                    return $q->get()
                                        ->mapWithKeys(fn($g) => [$g->id => "{$g->nom} ({$g->phone})"])
                                        ->toArray();
                                })
                                ->searchable()
                                ->multiple()
                                ->required(),

                            Select::make('ceremonieId')
                                ->label('Choisir une cérémonie')
                                ->columnSpan(4)
                                ->options(function () use ($isSuper, $user) {
                                    $q = Ceremonie::query()->with('event');
                                    if (! $isSuper) {
                                        $q->whereHas('event', function (Builder $qe) use ($user) {
                                            $qe->where('user_id', $user->id)->where('status', '!=', 'termine');
                                        });
                                    }
                                    return $q->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(fn($state) => $this->dispatch('open-message-modal', ceremonieId: $state))
                                ->required(),

                            Select::make('table')
                                ->visible(fn($get) => $get('activeChannel') === 'whatsapp')
                                ->label('Choisir une table')
                                ->options(function (Get $get) {
                                    $cerId = $get('ceremonieId');
                                    if (! $cerId) {
                                        return [];
                                    }
                                    return Groupe::where('ceremonie_id', $cerId)->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->columnSpan(4)
                                ->required(),

                            Select::make('selectedMessageId')
                                ->label('Choisir un message lié')
                                ->options(fn() => $this->messagesDisponibles)
                                ->searchable()
                                ->hidden(fn() => empty($this->messagesDisponibles))
                                ->reactive()
                                ->columnSpan(4),

                            RichEditor::make('message')
                                ->visible(fn($get) => $get('activeChannel') === 'whatsapp')
                                ->label('Message personnalisé')
                                ->helperText("Utilisez {categorie} {nom} {ceremony} {date} {femme} {homme} {lien}")
                                ->reactive()
                                ->hidden(fn($get) => ! $get('message'))
                                ->toolbarButtons([
                                    'attachFiles','blockquote','bold','bulletList','codeBlock','h2','h3','italic','link','orderedList','redo','strike','underline','undo',
                                ])
                                ->columnSpanFull(),

                            Group::make([
                                Textarea::make('messageSms')
                                    ->label('Message à envoyer (SMS)')
                                    ->helperText("Utilisez {categorie} {nom} {ceremony} {date} {femme} {homme} {lien}")
                                    ->required()
                                    ->ascii()
                                    ->rows(4)
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->maxLength(480)
                                    ->visible(fn($get) => $get('activeChannel') === 'sms'),

                                View::make('filament.components.sms-counter')->columnSpanFull()
                                    ->visible(fn($get) => $get('activeChannel') === 'sms'),

                                View::make('filament.components.preview-message')
                                    ->visible(fn($get) => $get('activeChannel') === 'sms')
                                    ->columnSpanFull()->extraAttributes(['wire:model.debounce.1000ms' => 'messageSms']),

                                View::make('filament.components.envoyer-button')->columnSpanFull()
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
                                ->options(function () use ($isSuper, $user) {
                                    $q = Guest::query()
                                        ->whereNotNull('email')
                                        ->where('email', '!=', '');

                                    if (! $isSuper) {
                                        $q->whereHas('event', function (Builder $qe) use ($user) {
                                            $qe->where('user_id', $user->id)
                                               ->where('status', '!=', 'termine');
                                        });
                                    }

                                    return $q->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->multiple()
                                ->required(),

                            Select::make('ceremonieId')
                                ->label('Choisir une cérémonie')
                                ->options(function () use ($isSuper, $user) {
                                    $q = Ceremonie::query();
                                    if (! $isSuper) {
                                        $q->whereHas('event', function (Builder $qe) use ($user) {
                                            $qe->where('user_id', $user->id)->where('status', '!=', 'termine');
                                        });
                                    }
                                    return $q->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->reactive()
                                ->columnSpan(6)
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $ceremony = Ceremonie::find($state);
                                    $set('message', $ceremony && isset($ceremony->description) ? $ceremony->description : '');
                                })
                                ->required(),

                            Select::make('table')
                                ->label('Choisir une table')
                                ->options(function (Get $get) {
                                    $cerId = $get('ceremonieId');
                                    if (! $cerId) {
                                        return [];
                                    }
                                    return Groupe::where('ceremonie_id', $cerId)->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->columnSpan(6)
                                ->required(),

                            RichEditor::make('message')
                                ->label('Message personnalisé')
                                ->reactive()
                                ->hidden(fn($get) => ! $get('message'))
                                ->toolbarButtons(['attachFiles','blockquote','bold','bulletList','codeBlock','h2','h3','italic','link','orderedList','redo','strike','underline','undo'])
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
                                ->options(function () use ($isSuper, $user) {
                                    $q = Guest::query();

                                    if (! $isSuper) {
                                        $q->whereHas('event', function (Builder $qe) use ($user) {
                                            $qe->where('user_id', $user->id)
                                               ->where('status', '!=', 'termine');
                                        });
                                    }

                                    return $q->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->multiple()
                                ->required(),

                            Select::make('ceremonieId')
                                ->label('Choisir une cérémonie')
                                ->options(function () use ($isSuper, $user) {
                                    $q = Ceremonie::query();
                                    if (! $isSuper) {
                                        $q->whereHas('event', function (Builder $qe) use ($user) {
                                            $qe->where('user_id', $user->id)->where('status', '!=', 'termine');
                                        });
                                    }
                                    return $q->pluck('nom', 'id')->toArray();
                                })
                                ->searchable()
                                ->reactive()
                                ->columnSpan(6)
                                ->required(),

                            Select::make('table')
                                ->label('Choisir une table')
                                ->options(function (Get $get) {
                                    $cerId = $get('ceremonieId');
                                    if (! $cerId) {
                                        return [];
                                    }
                                    return Groupe::where('ceremonie_id', $cerId)->pluck('nom', 'id')->toArray();
                                })
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
        return []; // Pas de bouton submit Filament par défaut
    }

    // ----- helpers d'envoi -----
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
            Notification::make()->title('Message trop long')->body('Le message dépasse 3 SMS (480 caractères max autorisés). Veuillez le raccourcir.')->danger()->send();
        }
    }

    public function envoyerViaWhatsapp($guests, $messageTxt, $ceremonie)
    {
        session()->put('guest_ids', $guests->pluck('id')->toArray());
        session()->put('messageTxt', $messageTxt);
        session()->put('ceremonie', $ceremonie);
        return redirect()->route('filament.admin.pages.whatsapp');
    }

    public function envoyerViaEmail($guests)
    {
        if ($guests->isEmpty()) {
            Notification::make()->title("Aucun invité éligible")->body("Aucun invité avec e-mail valide ou invitation complète.")->danger()->send();
            return;
        }

        $emailsEnvoyes = 0;
        $emailsErreurs = 0;

        foreach ($guests as $guest) {
            $invitation = Invitation::where('guest_id', $guest->id)
                ->where('ceremonie_id', $this->ceremonieId)
                ->with('ceremonies.event')
                ->first();

            if (! $invitation) {
                $emailsErreurs++;
                continue;
            }

            $customMessage = $invitation->message ?? '';
            $sujet = $invitation->ceremonie->event->nom ?? 'Invitation';
            $messageFinal = htmlspecialchars(strip_tags($customMessage));

            if ($guest->email) {
                try {
                    Mail::to($guest->email)->send(new InvitationMail($guest, $invitation, $messageFinal, $sujet));
                    $emailsEnvoyes++;
                } catch (\Throwable $e) {
                    Log::error("Erreur d'envoi mail à {$guest->email} : " . $e->getMessage());
                    $emailsErreurs++;
                }
            }
        }

        Notification::make()->title('Résultat des envois')->success()->body("✉️ Emails envoyés : {$emailsEnvoyes} (erreurs : {$emailsErreurs})")->send();
    }

    public function envoyerEnDure($guests)
    {
        Notification::make()->title('Résultat des envois')->success()->body("Enregistrement effectué")->send();
    }

    public function envoyerViaSms($guests)
    {
        if ($guests->isEmpty()) {
            Notification::make()->title("Aucun invité éligible")->body("Aucun invité avec le numéro de téléphone valide ou invitation complète.")->danger()->send();
            return;
        }

        $smsEnvoyes = 0;
        $smsErreurs = 0;

        foreach ($guests as $guest) {
            $invitation = Invitation::where('guest_id', $guest->id)
                ->where('ceremonie_id', $this->ceremonieId)
                ->with('ceremonies.event')
                ->first();

            if (! $invitation) {
                $smsErreurs++;
                continue;
            }

            $ceremony = $invitation->ceremonies;
            $event = $ceremony->event ?? null;
            $titre = $event->nom ?? 'Invitation';
            $messageFinal = htmlspecialchars(strip_tags($invitation->msgRappel ?? $this->messageSms));

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

        Notification::make()->title('Résultat des envois')->success()->body("📱 SMS envoyés : {$smsEnvoyes} (erreurs : {$smsErreurs})")->send();
    }

    // ----- création d'invitations (persist) -----
    protected function createInvitations(): bool
    {
        try {
            $ceremony = Ceremonie::find($this->ceremonieId);
            $guests = Guest::whereIn('id', $this->selectedGuests)->get();

            foreach ($guests as $guest) {
                do {
                    $reference = "INV-{$this->ceremonieId}-" . date('Ymd') . "-" . strtoupper(Str::random(6));
                } while (Invitation::where('reference', $reference)->exists());

                $msg = "";
                $moyen = "";
                switch ($this->activeChannel) {
                    case 'whatsapp':
                        $msg = $this->message;
                        $moyen = "whatsapp";
                        break;
                    case 'email':
                        $msg = $this->message;
                        $moyen = "email";
                        break;
                    case 'sms':
                        $msg = $this->messageSms;
                        $moyen = "sms";
                        break;
                    case 'enDure':
                        $moyen = "enDure";
                        break;
                }

                $now = Carbon::now()->startOfDay();
                $ceremonyDate = optional($ceremony->date)->startOfDay();
                $date = $ceremonyDate && $ceremonyDate->equalTo($now)
                    ? "aujourd'hui à " . $ceremony->date->format('H\hi')
                    : "le " . optional($ceremony->date)->format('d/m/Y') . " à " . optional($ceremony->date)->format('H\hi');

                $lien = LienCourt::generate($reference, $this->ceremonieId);

                $customMessage = str_replace(
                    ['{homme}', '{femme}', '{adresse}', '{categorie}', '{nom}', '{ceremony}', '{date}', '{lien}'],
                    [
                        e($ceremony->event->homme ?? ''),
                        e($ceremony->event->femme ?? ''),
                        e($ceremony->adresse ?? ''),
                        e($guest->type ?? ''),
                        e($guest->nom ?? ''),
                        e($ceremony->nom ?? ''),
                        e($date),
                        e($lien),
                    ],
                    $msg ?? ''
                );

                if ($this->activeChannel != "sms") {
                    Invitation::updateOrCreate(
                        [
                            'guest_id' => $guest->id,
                            'ceremonie_id' => $this->ceremonieId,
                            'reference' => $reference,
                        ],
                        [
                            'groupe_id' => $this->table,
                            'status' => 'send',
                            'message' => $customMessage,
                            'moyen' => $moyen,
                        ]
                    );
                } else {
                    Invitation::updateOrCreate(
                        [
                            'guest_id' => $guest->id,
                            'ceremonie_id' => $this->ceremonieId,
                        ],
                        [
                            'rappel' => true,
                            'msgRappel' => $customMessage,
                        ]
                    );
                }
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("Erreur lors de la création des invitations : " . $e->getMessage());
            return false;
        }
    }

    // ----- utilitaires externes -----
    public function sendSms($phoneNumber, $message)
    {
        if (empty($phoneNumber)) {
            Log::error("Erreur : Le numéro de téléphone est vide.");
            return ["status_code" => false, "response" => null];
        }

        if (! MessageHelper::isValidPhone($phoneNumber)) {
            Log::error("Erreur : Le numéro de téléphone n'est pas valide.");
            return ["status_code" => false, "response" => null];
        }

        $apiUrl = 'https://api.keccel.com/sms/v2/message.asp';
        $apiKey = 'BAPK3A29RHG6QY2';
        $msg = MessageHelper::cleanMessageForSms($message, 500);

        $postData = [
            "token" => $apiKey,
            "to" => $phoneNumber,
            "from" => 'KWETU',
            "message" => $msg,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey",
        ]);

        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["status_code" => true, "response" => json_decode($response, true)];
    }

    public function getPreviewMessageProperty(): ?string
    {
        if (! $this->ceremonieId || empty($this->selectedGuests)) {
            return null;
        }

        $guest = Guest::find($this->selectedGuests[0]);
        $ceremony = Ceremonie::with('event')->find($this->ceremonieId);
        $lien = LienCourt::generate("reference", $this->ceremonieId);

        if (! $guest || ! $ceremony) {
            return null;
        }

        $msg = match ($this->activeChannel) {
            'whatsapp', 'email' => $this->message,
            'sms' => $this->messageSms,
            default => '',
        };

        $now = now()->startOfDay();
        $ceremonyDate = optional($ceremony->date)?->startOfDay();

        $date = $ceremonyDate && $ceremonyDate->equalTo($now)
            ? "aujourd'hui à " . $ceremony->date->format('H\hi')
            : "le " . optional($ceremony->date)->format('d/m/Y') . " à " . optional($ceremony->date)->format('H\hi');

        return str_replace(
            ['{homme}', '{femme}', '{adresse}', '{categorie}', '{nom}', '{ceremony}', '{date}', '{lien}'],
            [
                $ceremony->event->homme ?? '',
                $ceremony->event->femme ?? '',
                $ceremony->adresse ?? '',
                $guest->type ?? '',
                $guest->nom ?? '',
                $ceremony->nom ?? '',
                $date,
                $lien,
            ],
            $msg
        );
    }
}