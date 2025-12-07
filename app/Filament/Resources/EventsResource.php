<?php
namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Event;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventsResource\Pages;
use App\Filament\Resources\EventsResource\Widgets\EventStats;


class EventsResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $permission = 'access_dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort    = 1;
    public static function getLabel(): string
    {
        return 'Evenement';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make("Formulaire")->schema([
                        TextInput::make('nom')
                            ->label('Titre')
                            ->required()
                            ->columnSpan(4),
                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->columnSpan(4),
                            TextInput::make('lieu')
                            ->label('Lieu')
                            ->required()
                            ->columnSpan(4),
                        TextInput::make('homme')
                            ->label('Nom du mari')
                            ->required()
                            ->columnSpan(6),
                        TextInput::make('femme')
                            ->label('nom de la femme')
                            ->required()
                            ->columnSpan(6),

                        Select::make('status')
                            ->label('Status')
                            ->columnSpan(6)
                            ->required()
                            ->options([
                                'brouillon' => 'Brouillon',
                                'actif'     => 'Actif',
                                'termine'   => 'Terminé',
                            ]),
                        Select::make('user_id')
                            ->label(label: 'Organisateur')
                            ->searchable()
                            ->columnSpan(6)
                            ->preload()
                            ->relationship('user', 'name'),
                        RichEditor::make('description')
                            ->label(label: 'Description')
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
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: 'nom')
                ->label("Titre")
                 ->searchable()
                ->sortable(),
                TextColumn::make(name: 'date')
                ->label("Date")
                 ->searchable()
                ->sortable(),
                TextColumn::make(name: 'lieu')
                ->label("Lieu")
                 ->searchable()
                ->sortable(),
                TextColumn::make(name: 'user.name')
                ->label("Organisateur")
                 ->searchable()
                ->sortable(),
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
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvents::route('/create'),
            'edit'   => Pages\EditEvents::route('/{record}/edit'),
        ];
    }

    //   // Masquer les événements terminés du listing Filament
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->where('status', '!=', 'termine');
    //     // ou : parent::getEloquentQuery()->notTermine();
    // }

    // // Optionnel : n'afficher le menu que s'il y a des événements actifs
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return Event::where('status', '!=', 'termine')->exists();
    // }
 public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Si l'utilisateur est super_admin, montrer tout
        $isSuperAdmin = $user && (method_exists($user, 'hasRole')
            ? $user->hasRole('super_admin')
            : optional($user->role)->name === 'super_admin');

        if ($isSuperAdmin) {
            return $query;
        }

        return $query->where('status', '!=', 'termine');
    }

        public static function shouldRegisterNavigation(): bool
        {
            $user = Auth::user();
            $isSuperAdmin = $user && (method_exists($user, 'hasRole')
                ? $user->hasRole('super_admin')
                : optional($user->role)->name === 'super_admin');

            if ($isSuperAdmin) {
                return true;
            }

            return \App\Models\Event::where('status', '!=', 'termine')->exists();
        }
}
