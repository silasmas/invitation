<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;

class RoleResource extends Resource
{
    // protected static ?string $model = Role::class;
    protected static ?string $model = \Spatie\Permission\Models\Role::class;

    protected static ?int $navigationSort    = 8;
    public static function getLabel(): string
    {
        return 'Gestion des Roles';
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\MultiSelect::make('permissions')
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('name')
                    ->label('Nom du rôle')
                    ->numeric()
                    ->sortable(),
               TextColumn::make('guard_name')
                    ->label('Type de rôle')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        $isSuperAdmin = $user && (method_exists($user, 'hasRole')
            ? $user->hasRole('super_admin')
            : optional($user->role)->name === 'super_admin');

        return $isSuperAdmin;
    }
}
