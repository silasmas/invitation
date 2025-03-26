<?php

namespace App\Filament\Resources\CeremonieResource\Widgets;

use App\Models\Ceremonie;
use Filament\Widgets\Widget;
use Illuminate\View\View;
class CeremonieListe extends Widget
{
    protected static string $view = 'filament.widgets.ceremonie-liste';

    protected ?string $heading = 'Cérémonies';

    protected ?string $description = 'Liste des cérémonies à venir';
    protected static ?int $sort = 10;

    public function render(): View
    {
        return view('filament.widgets.ceremonie-liste', [
            'ceremonies' => Ceremonie::orderBy('date')->get(),
        ]);
    }
}
