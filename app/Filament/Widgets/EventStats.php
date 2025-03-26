<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class EventStats extends BaseWidget
{
    protected static ?int $sort = 0; // Pour l'afficher après les stats globales
    protected ?string $heading = 'Événements';

protected ?string $description = 'Statistiques des événements';
    protected function getCards(): array
    {

        $total = Event::count();
        $today = Event::whereDate('date', Carbon::today())->count();
        $upcoming = Event::whereDate('date', '>', Carbon::today())->count();
        $past = Event::whereDate('date', '<', Carbon::today())->count();

        return [

            Card::make('🎯 Total événements', $total)
                ->description("Tous les événements créés")
                ->icon('heroicon-o-rectangle-group')
                ->color('primary'),

            Card::make('📅 Aujourd’hui', $today)
                ->description("Événements prévus aujourd’hui")
                ->icon('heroicon-o-calendar-days')
                ->color($today > 0 ? 'success' : 'gray'),

            Card::make('📆 À venir', $upcoming)
                ->description("Événements futurs")
                ->icon('heroicon-o-arrow-trending-up')
                ->color($upcoming > 0 ? 'info' : 'gray'),

            Card::make('📜 Passés', $past)
                ->description("Événements déjà passés")
                ->icon('heroicon-o-archive-box')
                ->color('warning')
                ->extraAttributes(['class' => 'text-sm']),
        ];
    }
}
