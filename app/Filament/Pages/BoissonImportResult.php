<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class BoissonImportResult extends Page
{

    protected static string $view = 'filament.pages.boisson-import-result';
    protected static ?string $navigationGroup = 'Gestion des boissons';
    protected static ?string $label = "Tableau d'importation des boissons";
    protected static ?string $navigationLabel = 'Résultat de l\'importation';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false; // 1/ Cache la page du menu
    protected static ?string $title = 'Résultat de l\'importation';
    protected static ?string $navigationUrl = '/boissons/import-result'; // 2/ URL de la page
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    // Ajout important pour générer une URL propre
    protected static ?string $slug = 'boissons/import-result';
    public $duplicates = [];

    public function mount()
    {
        $this->duplicates = session()->pull('duplicates', []);
    }
    protected function getHeaderActions(): array
    {
        return [];
    }
     // ✅ Pour résoudre l'erreur de route
    //  public static function getRouteName(?string $panel = null): string
    //  {
    //      return 'filament.pages.boisson-import-result';
    //  }

}
