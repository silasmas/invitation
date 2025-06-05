<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Message;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MessageResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MessageResource\RelationManagers;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $permission = 'access_dashboard';
    protected static ?int $navigationSort = 6;
    public static function getLabel(): string
    {
        return 'Message';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make("Formulaire")->schema([
                    TextInput::make('titre')
                            ->label(label: 'Titre')
                            ->columnSpan(6),
                        Select::make('ceremonie_id')
                            ->label(label: 'Evenement')
                            ->searchable()
                            ->columnSpan(6)
                            ->preload()
                            ->relationship('ceremonie', 'nom'),
                            RichEditor::make('message')
                            ->label(label: 'Message')
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
              TextColumn::make('titre')
                    ->label(label: 'Titre')
                    ->searchable()
                    ->sortable(),
            //   TextColumn::make('message')
            //         ->label(label: 'Message')
            //         ->limit(50)
            //         ->searchable()
            //         ->sortable(),

TextColumn::make('message')
    ->label('Message')
    ->limit(50)
                    ->searchable()
    ->formatStateUsing(fn ($state) => Str::limit(strip_tags($state), 100)),

                  TextColumn::make('ceremonie.nom')
                        ->label(label: 'Cérémonie')
                        ->searchable()
                        ->numeric()
                        ->sortable(),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
