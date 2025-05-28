<?php
namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Invitation;
use Filament\Tables\Table;
use App\Helpers\MessageHelper;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use App\Filament\Widgets\InvitationStats;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\InvitationsResource\Pages;

class InvitationsResource extends Resource
{
    protected static ?string $model = Invitation::class;
    protected static ?string $permission = 'access_dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?int $navigationSort    = 4;
    public string $messageWhatsapp           = "Bonjour {nom}, vous êtes invité à notre événement via WhatsApp !";

    public static function getLabel(): string
    {
        return 'Invitations';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make("Formulaire")->schema([

                        Select::make('guest_id')
                            ->label(label: 'Invité')
                            ->searchable()
                            ->columnSpan(4)
                            ->preload()
                            ->relationship('guests', 'nom'),
                        Select::make('ceremonie_id')
                            ->label(label: 'Ceremonie')
                            ->searchable()
                            ->columnSpan(4)
                            ->preload()
                            ->relationship('ceremonies', 'nom'),

                        TagsInput::make('boissons')
                            ->label('Les Boissons')
                            ->placeholder('vous pouvez ajouté pluslieurs boisson...')
                            ->separator(',')
                            ->saveRelationshipsWhenHidden() // Sauvegarde même si le champ est caché
                            ->columnSpan(4),
                        Select::make('status')
                            ->label('Etat')
                            ->columnSpan(4)
                            ->required()
                            ->options([
                                'pedding' => 'Entrant',
                                'send'    => 'Envoyé',
                                'accept'  => 'Accepté',
                                'refuse'  => 'Refusé',
                            ]),
                        Select::make('moyen')
                            ->label('Moyen')
                            ->columnSpan(4)
                            ->required()
                            ->options([
                                'enDure' => 'En dure',
                                'whatsapp'    => 'Whatsapp',
                                'sms'  => 'Email',
                                'mail'  => 'SMS',
                            ]),
                        TextInput::make('cadeau')
                            ->label("Cadeau")
                            ->columnSpan(4),
                             RichEditor::make('message')
                            ->label(label: "Message de l'invitaté")
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
                            ->columnSpan(6),
                        Toggle::make('confirmation')
                            ->columnSpan(3)
                            ->onColor('success')
                            ->offColor('danger')
                            ->label("Confirmation")
                            ->default(false)
                            ->required(),
                    ])->columnS(12),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guests.type')
                    ->label("Type")
                    ->searchable(),
                TextColumn::make('guests.nom')
                    ->label("Invité")
                    ->searchable(),
                    TextColumn::make('moyen')
                    ->label('Moyen')
                    ->badge() // active le badge
                    ->color(fn(string $state): string => match ($state) {
                        'enDure'                         => 'warning',
                        'whatsapp'                            => 'success',
                        'email'                          => 'info',
                        'sms'                          => 'primary',
                        default                           => 'gray',
                    })->formatStateUsing(fn(string $state) => match ($state) {
                    'enDure'                              => 'En dure',
                    'whatsapp'                                 => 'Whatsapp',
                    'email'                               => 'Email',
                    'sms'                               => 'SMS',
                    default                                => ucfirst($state)
                })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('reference')
                    ->label("Référence")
                    ->searchable(),
                TextColumn::make('guests.phone')
                    ->label("Téléphone")
                    ->searchable(),
                TextColumn::make('boissons')
                    ->label("Boissons")
                    ->searchable(),
                TextColumn::make('cadeau')
                    ->label("Cadeau")
                    ->searchable(),
                TextColumn::make('ceremonies.nom')
                    ->label("Cérémonie")
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge() // active le badge
                    ->color(fn(string $state): string => match ($state) {
                        'pedding'                         => 'info',
                        'send'                            => 'warning',
                        'accept'                          => 'success',
                        'refuse'                          => 'danger',
                        default                           => 'gray',
                    })->formatStateUsing(fn(string $state) => match ($state) {
                    'pedding'                              => 'En attente',
                    'send'                                 => 'Envoyée',
                    'accept'                               => 'Acceptée',
                    'refuse'                               => 'Refusée',
                    default                                => ucfirst($state)
                })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('groupe.nom')
                    ->label("Table")
                    ->searchable(),
                IconColumn::make('confirmation')
                    ->label("Etat")
                    ->boolean(),
                // ImageColumn::make('qr_code')
                //     ->label('QR Code')
                //     ->getStateUsing(fn($record) => 'assets/images/text.png')
                //     ->visibility('visible')
                //     ->height(60)
                //     ->circular(false),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pedding' => 'En attente',
                        'send'    => 'Envoyée',
                        'accept'  => 'Acceptée',
                        'refuse'  => 'Refusée',
                    ]),

                SelectFilter::make('confirmation')
                    ->label('Confirmation')
                    ->options([
                        'oui' => 'Oui',
                        'non' => 'Non',
                    ]),

                SelectFilter::make('boissons')
                    ->label('Boisson préférée')
                    ->options(fn() => \App\Models\Invitation::query()
                            ->select('boissons')
                            ->distinct()
                            ->pluck('boissons', 'boissons')
                            ->filter()),

                SelectFilter::make('ceremonie_id')
                    ->label('Cérémonie')
                    ->relationship('ceremonies', 'nom'),

                SelectFilter::make('groupe_id')
                    ->label('Table')
                    ->relationship('groupe', 'nom'),
            ], layout: FiltersLayout::AboveContent)
            ->searchable() // ✅ active la recherche globale
            ->actions([
                Action::make('qr_code')
                ->label('QR Code')
                ->color('success') // options : primary, danger, warning, success, gray
                ->icon('heroicon-o-arrow-down-on-square')
                ->button() // rend le bouton plus visible (vs. icône simple)
                ->url(fn($record) => route('generate.qrcode', ['id' => $record->reference]))
                ->openUrlInNewTab()
                ->disabled(fn ($record) => !in_array($record->status, ['send', 'accept','pendding'])),

                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),

                    ])->headerActions([
            Action::make('statistiques')
                ->label(fn() => '📊 ' . \App\Models\Invitation::count() . ' invitations au total')
                ->disabled() // juste pour l'afficher
                ->color('gray'),
            Action::make('export-filtré')
                ->label('Exporter ce qui est affiché')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function (\Filament\Tables\Table $livewire) {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\InvitationExport($livewire->getFilteredTableQuery()->get()),
                        'invitations-filtrees.xlsx'
                    );
                }),
            Action::make('export-tout')
                ->label('Exporter tout')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\InvitationExport(\App\Models\Invitation::all()),
                        'invitations-toutes.xlsx'
                    );
                }),

        ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('export')
                    ->label('Exporter la sélection')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($records) {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\InvitationExport($records),
                            'invitations-selection.xlsx'
                        );
                    }),
                BulkAction::make('whatsapp_links')
                    ->label('Générer les liens WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('success')
                    ->form([
                        Textarea::make('messageTxt')
                            ->label('Message personnalisé')
                            ->rows(6)
                            ->helperText("Utilisez {categorie} {nom} pour Mr nom sur l'invitation, {ceremony} pour le nom de la cérémonie,
                                    {date} pour la date et l'huere de la ceremonie,{femme} et {homme}pour les noms des mariés, {lien} pour le lien vers l'invitation")
                        // ->default('Bonjour {nom}, vous êtes invité à notre événement...')
                            ->required(),
                    ])
                    ->action(function ($records, array $data) {
                        // 🔥 On filtre uniquement ceux avec numéro valide
                        $valid = $records->filter(function ($record) {
                            return ! empty($record->guests?->phone);
                        });
                        $phones = $valid->map(fn($record) => $record->guests?->phone)->filter()->values();
                        // 🔁 On extrait les IDs des invités, pas des invitations
                        $guestIds = $valid
                            ->map(fn($record) => $record->guests?->id)
                            ->filter()
                            ->unique()
                            ->values();
                        session()->put('phones', $phones);
                        session()->put('guest_ids', $guestIds->toArray());
                        session()->put('messageTxt', $data['messageTxt']);
                        return redirect()->route('filament.admin.pages.whatsapp');
                    })
                    ->requiresConfirmation(),
                BulkAction::make('sms_links')
                    ->label('Envoyé un SMS de rappel')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('warning')
                    ->form([
                        Textarea::make('messageSms')
                            ->label('Message à envoyer (SMS)')
                            ->helperText("Utilisez {categorie} {nom}, {ceremony}, {date}, {homme}, {femme}, {lien}")
                            ->required()
                            ->rows(5)
                            ->maxLength(480),
                    ])->action(function ($records, array $data) {
                    foreach ($records as $invitation) {
                        $guest = $invitation->guests;

                        if (! $guest || ! MessageHelper::isValidPhone($guest->phone)) {
                            continue;
                        }

                        // Récupérer les données dynamiques
                        $message = str_replace([
                            '{categorie}', '{nom}', '{ceremony}', '{date}', '{homme}', '{femme}', '{lien}',
                        ], [
                            $guest->type ?? '',
                            $guest->nom ?? '',
                            optional($invitation->ceremonies)->nom ?? '',
                            optional($invitation->ceremonies)->date?->format('d/m/Y H:i') ?? '',
                            optional($invitation->event)->homme ?? '',
                            optional($invitation->event)->femme ?? '',
                            route('invitation.show', $invitation->reference),
                        ], $data['messageSms']);

                        // ✅ Appel du helper pour envoyer le SMS
                        MessageHelper::sendSms($guest->phone, $message);
                    }\Filament\Notifications\Notification::make()
                        ->title('SMS envoyés')
                        ->body('Les messages ont été envoyés aux ' . $records->count() . ' invités sélectionnés.')
                        ->success()
                        ->send();
                })
                    ->requiresConfirmation(),

            ])->defaultSort('created_at', 'desc');
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
            'index'  => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitations::route('/create'),
            'edit'   => Pages\EditInvitations::route('/{record}/edit'),
        ];
    }
    public static function getHeaderWidgets(): array
    {
        return [
            InvitationStats::class,
        ];
    }
}
