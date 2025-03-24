<?php
namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Invitation;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\InvitationsResource\Pages;

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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index'  => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitations::route('/create'),
            'edit'   => Pages\EditInvitations::route('/{record}/edit'),
        ];
    }
}
