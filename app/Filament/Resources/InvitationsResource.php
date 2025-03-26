<?php
namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Invitation;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use App\Filament\Widgets\InvitationStats;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\InvitationsResource\Pages;
use App\Filament\Resources\InvitationsResource\Pages\EditInvitations;
use App\Filament\Resources\InvitationsResource\Pages\ListInvitations;
use App\Filament\Resources\InvitationsResource\Pages\CreateInvitations;

class InvitationsResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort    = 4;
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
                        Select::make('categorie')
                            ->label('Catégorie')
                            ->columnSpan(4)
                            ->required()
                            ->options([
                                'Couple' => 'Couple',
                                'Mme'    => 'Madame',
                                'Mr'     => 'Monsier',
                                'Mlle'   => 'Mademoiselle',
                            ]),
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
                            ->required()
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
                        TextInput::make('cadeau')
                            ->label("Cadeau")
                            ->columnSpan(4),
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
                TextColumn::make('reference')
                    ->label("Référence")
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
                    })->formatStateUsing(fn (string $state) => match ($state) {
                        'pedding' => 'En attente',
                        'send'    => 'Envoyée',
                        'accept'  => 'Acceptée',
                        'refuse'  => 'Refusée',
                        default   => ucfirst($state)
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('groupe.nom')
                    ->label("Table")
                    ->searchable(),
                IconColumn::make('confirmation')
                ->label("Etat")
                    ->boolean(),
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
                    ->options(fn () => \App\Models\Invitation::query()
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])->headerActions([
                Action::make('statistiques')
                    ->label(fn () => '📊 ' . \App\Models\Invitation::count() . ' invitations au total')
                    ->disabled() // juste pour l'afficher
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
