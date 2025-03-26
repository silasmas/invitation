<?php

namespace App\Filament\Resources\EventsResource\Widgets;

use Carbon\Carbon;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EventStats extends BaseWidget
{
    protected function getCards(): array
    {
        $total = Event::count();
        $today = Event::whereDate('date', Carbon::today())->count();
        $upcoming = Event::whereDate('date', '>', Carbon::today())->count();
        $past = Event::whereDate('date', '<', Carbon::today())->count();

        return [

            Stat::make('🎯 Total événements', $total)
                ->description("Tous les événements créés")
                ->icon('heroicon-o-rectangle-group')
                ->color('primary'),

            Stat::make('📅 Aujourd’hui', $today)
                ->description("Événements prévus aujourd’hui")
                ->icon('heroicon-o-calendar-days')
                ->color($today > 0 ? 'success' : 'gray'),

            Stat::make('📆 À venir', $upcoming)
                ->description("Événements futurs")
                ->icon('heroicon-o-arrow-trending-up')
                ->color($upcoming > 0 ? 'info' : 'gray'),

            Stat::make('📜 Passés', $past)
                ->description("Événements déjà passés")
                ->icon('heroicon-o-archive-box')
                ->color('warning')
                ->extraAttributes(['class' => 'text-sm']),
        ];
    }
}
