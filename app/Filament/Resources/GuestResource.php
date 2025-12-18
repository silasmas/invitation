<?php
namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Guest;
use Filament\Forms\Form;

use Filament\Tables\Table;
use App\Imports\GuestsImport;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GuestResource\Pages;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;
    protected static ?string $permission = 'access_dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort    = 3;
    public static function getLabel(): string
    {
        return 'Invité';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make("Formulaire")->schema([
                        Select::make('type')
                            ->label('Type')
                            ->columnSpan(4)
                            ->required()
                            ->options([
                                'Mr'     => 'Mr',
                                'Mme'    => 'Mme',
                                'Mlle'   => 'Mlle',
                                'Couple' => 'Couple',
                                'Enfant' => 'Enfant',
                            ]),
                        TextInput::make('nom')
                            ->label("Nom")
                            ->columnSpan(4)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->label('E-mail')
                            ->columnSpan(4)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->label('Téléphone')
                            ->columnSpan(4)
                            ->maxLength(255),
                        Select::make('relation')
                            ->label('Relation')
                            ->columnSpan(4)
                            ->required()
                            ->options([
                                'famille'  => 'Famille',
                                'ami'      => 'Ami',
                                'collegue' => 'Collegue',
                                'autre'    => 'Autres',
                            ]),
                        Select::make('event_id')
                            ->label(label: 'Événement')
                            ->searchable()
                            ->columnSpan(4)
                            ->preload()
                            ->relationship('event', 'nom'),
                            Toggle::make('all_ceremonie')
                                    ->label('Active (pour que l’invité puisse assister à toutes les cérémonies)')
                                    ->columnSpanFull()
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->required(),
                    ])->columnS(12),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label("Type")
                    ->searchable(),
                TextColumn::make('nom')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('relation'),
                IconColumn::make('all_ceremonie')
                ->label('Invité à toutes les cérémonies')
                ->boolean(),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->formatStateUsing(fn($record) => "https://wa.me/{$record->phone}?text=" . urlencode("Bonjour {$record->nom}, vous êtes invité à notre événement !"))
                    ->url(fn($record) => "https://wa.me/{$record->phone}?text=" . urlencode("Bonjour {$record->nom}, vous êtes invité à notre événement !"))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('success'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event.nom')
                    ->numeric()
                    ->label("Evenement")
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->url(fn($record) => 'https://wa.me/' . $record->phone . '?text=' . urlencode("Bonjour {$record->nom}, vous êtes invité à notre événement !"))
                    ->openUrlInNewTab()
                    ->disabled(fn($record) => empty($record->phone) || ! preg_match('/^\+?[1-9]\d{9,14}$/', $record->phone))
                    ->visible(fn($record) => ! empty($record->phone))
                    ->color('success'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('import')
                    ->label('Importer avec un fichier Excel')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->form([
                        View::make('filament.forms.download-template'),
                        Select::make('event_id')
                            ->label(label: 'Événement')
                            ->searchable()
                            ->columnSpan(6)
                            ->preload()
                            ->relationship('event', 'nom'),
                        FileUpload::make('file')
                            ->label('Fichier Excel')
                            ->disk('local')                // Utilisation du stockage local (dans storage/app/)
                            ->directory('uploads/imports') // Dossier où le fichier sera stocké
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                                'application/vnd.ms-excel',                                          // .xls
                                'text/csv',                                                          // .csv
                            ])
                            ->required(),

                    ])
                    ->action(function (array $data) {
                        Log::info('Début de l’importation'); // Vérifier si l'action se déclenche

                        // 🔹 Récupérer l'ID de l'événement depuis le formulaire
                        $eventId = $data['event_id'] ?? null;
                        $fileInput = $data['file'] ?? null;
                        Log::info('Fichier reçu : ' . print_r($fileInput, true));
                        Log::info('ID :' . $eventId); // Vérifier si l'ID est récupéré
                                                      // Correction du chemin du fichier
                        // $filePath = storage_path('app/uploads/imports/' . basename($data['file']));

                        if (is_string($fileInput)) {
                            $filePath = Storage::disk('local')->path($fileInput);
                        } elseif ($fileInput instanceof \Illuminate\Http\UploadedFile) {
                            $filePath = $fileInput->getRealPath();
                        }
                        // Vérification si le fichier existe avant d'importer
                        if (! file_exists($filePath)) {
                            Log::error("Fichier introuvable : {$filePath}");
                            Notification::make()
                                ->title('Erreur')
                                ->body("Le fichier n'existe pas. Vérifiez son emplacement.")
                                ->danger()
                                ->send();
                            return;
                        }
                        if (! $eventId) {
                            Log::error('Échec de l’importation : event_id manquant');
                            Notification::make()
                                ->title('Erreur')
                                ->body("Veuillez sélectionner un événement.")
                                ->danger()
                                ->send();
                            return;
                        }
                        Log::info("Importation du fichier : {$filePath}");
                        // 🔹 Capturer les erreurs avec try-catch
                        try {
                            // 🔹 Création d'une instance de l'import pour capturer les erreurs
                            $import = new GuestsImport($eventId);

                            // 🔹 Récupération des erreurs après l'importation
                            Excel::import($import, $filePath);
                            Log::info("Importation terminée avec succès.");

                            Log::info('Avant récupération des erreurs de validation');
                            $failures = $import->getFailures();
                            Log::info('Erreurs récupérées', ['failures' => $failures]);
                            if (! empty($failures)) {
                                Log::error('Erreurs de validation détectées', ['failures' => $failures]);

                                $errorMessages = collect($failures)->map(function ($failure) {
                                    return "Ligne {$failure->row()}: " . implode(", ", $failure->errors());
                                })->implode("\n");
                                Log::error("Erreurs détectées : \n" . $errorMessages);
                                Notification::make()
                                    ->title('Erreurs de validation')
                                    ->body($errorMessages)
                                    ->danger()
                                    ->send();

                                return;
                            }
                            Notification::make()
                                ->title('Importation réussie')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error("Erreur critique lors de l'importation : " . $e->getMessage());

                            Notification::make()
                                ->title('Erreur')
                                ->body("Une erreur s'est produite : " . $e->getMessage())
                                ->danger()
                                ->send();
                        }

                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                BulkAction::make('whatsapp_links')
                    ->label('Générer les liens WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->action(function ($records) {
                        // 🔥 On filtre uniquement ceux avec numéro valide
                        $valid = $records->filter(function ($record) {
                            return ! empty($record->phone) ;
                        });
                        session()->put('guest_ids', $valid->pluck('id')->toArray());

                        return redirect()->route('filament.admin.pages.whatsapp');
                    })
                    ->requiresConfirmation()
                    ->color('success'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit'   => Pages\EditGuest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Vérifier si l'utilisateur est super_admin
        $isSuperAdmin = $user && (method_exists($user, 'hasRole')
            ? $user->hasRole('super_admin')
            : optional($user->role)->name === 'super_admin');

        if ($isSuperAdmin) {
            return $query;
        }

        // Pour les utilisateurs normaux :
        // - afficher les invités liés à des cérémonies avec un événement actif (status != 'termine')
        // - ET ne pas exclure les invités qui n'ont pas encore d'invitation (cas lors de la création)
        return $query->where(function (Builder $q) {
            $q->doesntHave('invitation')
                ->orWhereHas('invitation', function (Builder $qInv) {
                    $qInv->whereHas('ceremonies', function (Builder $qCer) {
                        $qCer->whereHas('event', function (Builder $qEvt) {
                            $qEvt->where('status', '!=', 'termine');
                        });
                    });
                });
        });
    }
}
