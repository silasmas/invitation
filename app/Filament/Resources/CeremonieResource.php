<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CeremonieResource\Pages;
use App\Models\Ceremonie;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class CeremonieResource extends Resource
{
    protected static ?string $model      = Ceremonie::class;
    protected static ?string $permission = 'access_dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?int $navigationSort    = 2;
    public static function getLabel(): string
    {
        return 'Céremonie';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make("Formulaire")->schema([
                        TextInput::make('nom')
                            ->label(label: 'Titre')
                            ->columnSpan(6)
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('date')
                            ->columnSpan(6)
                            ->label(label: 'Date')
                            ->required(),

                        RichEditor::make('adresse')
                            ->label(label: 'Adresse')
                            ->required()
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
                        RichEditor::make('description')
                            ->label(label: "Message de pour la cérémonie")
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
                        Select::make('event_id')
                            ->required()
                            ->label(label: 'Evenement')
                            ->columnSpan(6)
                            ->preload()
                            ->relationship('event', 'nom'),
                        Select::make('typeDressecode')
                            ->required()
                            ->label(label: 'Type de dress code')
                            ->options([
                                'tissu'        => 'Tissu',
                                'couleur'      => 'Couleur',
                                'tissuCouleur' => 'Tissu et couleur',
                            ])
                            ->columnSpan(6),
                        FileUpload::make('image')
                            ->columnSpan(12)
                            ->label('Photo des mariés')
                            ->directory('image')
                            ->imageEditor()
                            ->imageEditorMode(2)
                            ->downloadable()
                            ->image()
                            ->maxSize(3024)
                            ->previewable(true),
                        FileUpload::make('tissu')
                            ->columnSpan(12)
                            ->label('Tissu en image')
                            ->directory('tissu')
                            ->imageEditor()
                            ->multiple()
                            ->imageEditorMode(2)
                            ->downloadable()
                            ->image()
                            ->maxSize(3024)
                            ->previewable(true),
                        Repeater::make('dressCode')
                            ->label('Couleurs du dress code')
                            ->schema([
                                ColorPicker::make('hex')
                                    ->label('Couleur')
                                    ->rule('regex:/^#[0-9A-Fa-f]{6}$/')
                                    ->dehydrateStateUsing(fn ($state) => $state), // garde juste le code hex
                                TextInput::make('name')
                                    ->label('Nom de la couleur')
                                    ->placeholder('Ex: Rouge Bordeaux')
                                    ->maxLength(30),
                            ])
                            ->rule(function (\Closure $get) {
                                $colors = collect($get('dressCode'))
                                    ->pluck('hex')
                                    ->filter()
                                    ->values();

                                // Aucune couleur → champ réellement optionnel, pas d’erreur
                                if ($colors->isEmpty()) {
                                    return null;
                                }

                                // Si l'utilisateur commence à remplir : 2 à 3 couleurs obligatoires
                                if ($colors->count() < 2 || $colors->count() > 3) {
                                    return 'Tu dois choisir entre 2 et 3 couleurs.';
                                }

                                // Couleurs doivent être uniques
                                if ($colors->duplicates()->isNotEmpty()) {
                                    return 'Les couleurs doivent être uniques.';
                                }

                                return null;
                            })
                            ->afterStateHydrated(function (Repeater $component, $state) {
                                // Transforme l'ancien format ['#hex', '#hex2'] → [['hex' => '#hex'], ...]
                                if (is_array($state) && isset($state[0]) && is_string($state[0])) {
                                    $component->state(
                                        collect($state)
                                            ->map(fn ($color) => ['hex' => $color])
                                            ->toArray(),
                                    );
                                }
                            })
                            ->beforeStateDehydrated(function ($state) {
                                $colors = collect($state)
                                    ->pluck('hex')
                                    ->filter()
                                    ->values();

                                // Si aucune couleur choisie → on n'empêche pas l'enregistrement,
                                // et on enregistre un tableau vide (champ vraiment optionnel)
                                if ($colors->isEmpty()) {
                                    return [];
                                }

                                // La validation est déjà gérée dans ->rule(),
                                // ici on ne fait que normaliser les données pour le stockage.
                                return $colors->toArray();
                            })
                            ->minItems(0)
                            ->maxItems(3)
                            ->reorderable(false)
                            ->addActionLabel('Ajouter une couleur')
                            ->columnSpan(6)
                            ->dehydrated(true) // important pour que le champ soit envoyé
                            ->statePath('dressCode')
                            ->validationAttribute('couleurs du dress code'),
                        View::make('filament.components.dress-code-preview')
                            ->label('Aperçu des couleurs sélectionnées')
                            ->columnSpan(6)
                            ->statePath('dressCode') // ✅ ajoute ceci
                            ->visible(fn($get) => ! empty($get('dressCode'))),

                    ])->columnS(12),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->searchable(),
                TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->label('Photo des mariés')
                              // ->disk('storage')      // ou 'storage' selon ton système de fichiers
                    ->height(60)  // hauteur de l’image
                    ->width(60)   // largeur de l’image
                    ->circular(), // optionnel : rend l’image ronde
                ImageColumn::make('tissu')
                    ->label('Tissu')
                    ->stacked()
                    ->overlap(2)
                    ->wrap()
                              // ->disk('storage')      // ou 'storage' selon ton système de fichiers
                    ->height(60)  // hauteur de l’image
                    ->width(60)   // largeur de l’image
                    ->circular(), // optionnel : rend l’image ronde
                TextColumn::make('event.nom')
                    ->numeric()
                    ->sortable(),
                ViewColumn::make('dressCode')
                    ->label('Dress Code')
                    ->view('filament.tables.columns.dress-code'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListCeremonies::route('/'),
            'create' => Pages\CreateCeremonie::route('/create'),
            'edit'   => Pages\EditCeremonie::route('/{record}/edit'),
        ];
    }
}
