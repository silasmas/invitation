<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Groupe;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Imports\GroupeImpor;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Group;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\BoissonImportResult;
use App\Filament\Resources\GroupeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GroupeResource\RelationManagers;

class GroupeResource extends Resource
{
    protected static ?string $model = Groupe::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?int $navigationSort    = 5;
    protected static ?string $label = 'groupes';
    public static function getLabel(): string
    {
        return 'Groupes';
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
                            ->columnSpan(6),
                            Select::make('ceremonie_id')
                            ->label(label: 'Cérémonie')
                            ->searchable()
                            ->columnSpan(6)
                            ->preload()
                            ->relationship('ceremonie', 'nom'),
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
                TextColumn::make(name: 'ceremonie.nom')
                ->label("Ceremonie")
                 ->searchable()
                ->sortable(),
                TextColumn::make(name: 'description')
                ->label("Description")
                ->limit(50)
                 ->searchable()
                ->sortable(),
            ])
            ->headerActions([
                Action::make('import')
                    ->label('Importer avec un fichier Excel')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->form([
                        View::make('filament.forms.download-template2'),
                        FileUpload::make('file')
                            ->label('Fichier Excel')
                            ->disk('local')                // Utilisation du stockage local (dans storage/app/)
                            ->directory('uploads/imports/table') // Dossier où le fichier sera stocké
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
                        Log::info("Importation du fichier : {$filePath}");
                        // 🔹 Capturer les erreurs avec try-catch
                        try {
                            // 🔹 Création d'une instance de l'import pour capturer les erreurs
                            $import = new GroupeImpor($eventId);

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
                                $doublons = $import->getSkippedDuplicates();
                                if (! empty($doublons)) {
                                    Log::error('Erreurs de validation détectées', ['doublons' => $doublons]);

                                    $doublonMessages = collect($doublons)->map(function ($doublon, $index) {
                                        $nom = $doublon['nom'] ?? 'Nom inconnu';
                                        return "Ligne approximative " . ($index + 2) . " : $nom (déjà existant)";
                                    })->implode("\n");
                                    Log::error("Erreurs détectées : \n" . $doublonMessages);
                                    Notification::make()
                                        ->title('Erreurs de doublon')
                                        // ->body($doublonMessages)
                                        ->body(count($doublons) . ' doublon(s) ont été ignorés pendant l’import.')
                                        ->danger()
                                        ->send();
        // ✅ Optionnel : rediriger vers une page avec tableau
                                    session()->put('duplicates', $doublons);
                                    session()->put('page', "table");
                                    // return redirect()->route('filament.pages.boisson-import-result');
                                    return redirect(BoissonImportResult::getUrl());

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
            'index' => Pages\ListGroupes::route('/'),
            'create' => Pages\CreateGroupe::route('/create'),
            'edit' => Pages\EditGroupe::route('/{record}/edit'),
        ];
    }
}
