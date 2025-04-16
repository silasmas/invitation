<?php
namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Ceremonie;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;

use Illuminate\Validation\ValidationException;
use App\Filament\Resources\CeremonieResource\Pages;

class CeremonieResource extends Resource
{
    protected static ?string $model = Ceremonie::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                            ->columnSpan(12)
                            ->preload()
                            ->relationship('event', 'nom'),
                        Repeater::make('dressCode')
                        ->label('Couleurs du dress code')
                        ->schema([
                            ColorPicker::make('hex')
                            ->label('Couleur')
                            ->required()
                            ->rule('regex:/^#[0-9A-Fa-f]{6}$/')
                            ->dehydrateStateUsing(fn ($state) => $state) // garde juste le code hex

                        ])->afterStateHydrated(function (Repeater $component, $state) {
                            // Pour convertir un tableau simple ['#ff0000', '#00ff00'] en [['hex' => '#ff0000'], ...]
                            if (is_array($state) && isset($state[0]) && is_string($state[0])) {
                                $component->state(
                                    collect($state)->map(fn ($color) => ['hex' => $color])->toArray()
                                );
                            }
                        })->beforeStateDehydrated(function ($state) {
                            $colors = collect($state)->pluck('hex')->filter()->values();

                            if ($colors->count() < 2 || $colors->count() > 3) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'dressCode' => 'Tu dois choisir entre 2 et 3 couleurs.',
                                ]);
                            }

                            if ($colors->duplicates()->isNotEmpty()) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'dressCode' => 'Les couleurs doivent être uniques.',
                                ]);
                            }

                            return $colors->toArray(); // propre, hex only
                        })
                        ->minItems(1)
                        ->maxItems(3)
                        ->reorderable(false)
                        ->addActionLabel('Ajouter une couleur')
                        ->columnSpan(6)
                        ->required()
                        ->dehydrated(true) // important pour que le champ soit envoyé
                        ->statePath('dressCode')->validationAttribute('couleurs du dress code'),
                        View::make('filament.components.dress-code-preview')
                        ->label('Aperçu des couleurs sélectionnées')
                        ->columnSpan(6)
                        ->visible(fn ($get) => !empty($get('dressCode'))),
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
                TextColumn::make('event.nom')
                    ->numeric()
                    ->sortable(),
                    ViewColumn::make('dressCode')
                    ->label('Dress Code')
                    ->view('filament.components.dresscode'),
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
