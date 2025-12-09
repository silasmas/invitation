<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;
use App\Filament\Widgets\Concerns\FiltersByUser;

class EventStats extends BaseWidget
{
    use FiltersByUser;
    protected static ?int $sort = 0; // Pour l'afficher après les stats globales
    protected ?string $heading = 'Événements';



    public function getData(): array
    {
        $query = Event::query();

        // Appliquer filtre : event directement lié => relation 'event' sur l'entité (ici Event lui-même)
        // pour Event, on applique la condition user_id/status directement :
        $user = auth()->user();
        $isSuperAdmin = $user && (method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : optional($user->role)->name === 'super_admin');

        if (! $isSuperAdmin) {
            $query->where('user_id', $user->id)->where('status', '!=', 'termine');
        }

        $count = $query->count();

        return [
            'count' => $count,
        ];
    }
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
