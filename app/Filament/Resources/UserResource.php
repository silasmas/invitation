<?php
namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Password;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\UserResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $permission     = 'access_dashboard';

    protected static ?int $navigationSort = 7;
      public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }
    public static function getLabel(): string
    {
        return 'Utilisateurs';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make("Informations Utilisateur")->schema([

                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nom complet')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Adresse e-mail')
                                ->email()
                                ->required()
                                ->unique(User::class, 'email', ignoreRecord: true)
                                ->maxLength(255),
                        ]),

                        Grid::make(2)->schema([
                            // TextInput::make('password')
                            //     ->label('Mot de passe')
                            //     ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // facultatif en édition
                            //     ->confirmed()
                            //     ->password()
                            //     ->minLength(8)
                            //     ->maxLength(255),
                            TextInput::make('password')
                                ->label('Mot de passe')
                                ->password()
                                ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                ->dehydrateStateUsing(fn($state) => filled($state) ? \Hash::make($state) : null)
                                ->dehydrated(fn($state) => filled($state)) // Ne l'enregistre que si rempli
                                ->confirmed()
                                ->maxLength(255),

                            TextInput::make('password_confirmation')
                                ->label('Confirmer le mot de passe')
                                ->password()
                                ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                ->maxLength(255),
                        ]),

                        Select::make('roles')
                            ->label('Rôle')
                            ->required()
                            ->preload()
                            ->columnSpan(12)
                                                        // ->relationship('guests', 'nom')
                            ->relationship('roles', 'name') // fonctionne si User a `HasRoles`
                            ->multiple(false)               // true si multi-rôles
                                                        // ->options([
                                                        //     'admin' => 'Administrateur',
                                                        //     'editor' => 'Éditeur',
                                                        //     'user' => 'Utilisateur',
                                                        // ])
                            ->searchable(),

                    ])->columnS(12),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom complet')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Adresse e-mail')
                    ->searchable(),
                TextColumn::make('roles.name')->label('Rôle(s)')->badge(),
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
//     public static function canViewAny(): bool
// {
//     return auth()->user()?->hasRole('admin');
// }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_any_user');
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_user');
    }
}
