<?php
namespace App\Filament\Resources\CeremonieResource\Widgets;

use App\Models\Ceremonie;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class CeremonieStats extends BaseWidget
{
    protected function getCards(): array
    {
        $total      = Ceremonie::count();
        $ceremonies = Ceremonie::orderBy('date')->get();

        $liste = $ceremonies->map(function ($c) {
            return "{$c->nom} → " . $c->date->format('d/m/Y') . " à " . $c->heure;
        })->implode('<br>');

        return [
            Stat::make('🎉 Total Cérémonies', $total)->color('primary')
            ->description(Ceremonie::orderBy('date')
            ->take(5)
            ->get()
            ->map(fn($c) => "{$c->nom}")
                        ->implode(' | ')
                    ),

            // Stat::make('🗓️ Détails des Cérémonies', '')
            //     ->description('Dates & heures')
            //     ->extraAttributes(['class' => 'text-left text-sm'])
            //     ->content(view('filament.widgets.components.ceremonie-liste', compact('liste'))),
            Stat::make('🗓️ Prochaines cérémonies',"")
                ->description(
                    Ceremonie::orderBy('date')
                        ->take(3)
                        ->get()
                        ->map(fn($c) => "{$c->nom} ({$c->date->format('d/M')} à {$c->date->format('h\hi')})")
                        ->implode(' | ')
                )
                ->color('primary'),

        ];
    }
}
