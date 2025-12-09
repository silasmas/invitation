<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Widgets\Concerns\FiltersByUser;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\Builder;

class InvitationStats extends BaseWidget
{
    use FiltersByUser;

    //    protected static string $view = 'filament.widgets.invitation-stats';

    // public function getData(): array
    // {
    //     $query = Invitation::query();

    //     // applyUserEventFilter attend une relation dot vers Event depuis Invitation :
    //     // dans votre modèle Invitation la relation vers Ceremonie s'appelle 'ceremonies'
    //     // et Ceremonie a la relation 'event' => dot = 'ceremonies.event'
    //     $query = $this->applyUserEventFilter($query, 'ceremonies.event');

    //     $count = $query->count();

    //     return [
    //         'count' => $count,
    //     ];
    // }
    protected function getStats(): array
    {
        $total = Invitation::count();
        $envoyees = Invitation::where('status', 'send')->count();
        $acceptees = Invitation::where('status', 'accept')->count();
        $refusees = Invitation::where('status', 'refuse')->count();
        $fermer = Invitation::where('status', 'close')->count();
        $enDure = Invitation::where('moyen', 'enDure')->count();
        $virtuel = Invitation::where('moyen',"!=", 'enDure')->count();

        $enAttente = $total - $envoyees - $acceptees - $refusees-$fermer;

        return [

                Stat::make('📨 Total invitations', $total)
                    ->description('Toutes les invitations')
                    ->color('primary'),

                Stat::make('✉️ Envoyées', $envoyees)
                    ->description("Invitations envoyées")
                    ->color('info'),

                Stat::make('✅ Acceptées', $acceptees)
                    ->description("Réponses positives")
                    ->color('success'),

                Stat::make('❌ Refusées', $refusees)
                    ->description("Réponses négatives")
                    ->color('danger'),

                Stat::make('🕒 En attente', $enAttente)
                    ->description("Pas encore traitées")
                    ->color('gray'),
                Stat::make('📨 Colturer', $fermer)
                    ->description("Déjà cloturées")
                    ->color('success'),
                Stat::make('📨 En dure', $enDure)
                    ->description("Invitation en dure")
                    ->color('warning'),
                Stat::make('Virtuel', $fermer)
                    ->description("Invitation virtuel")
                    ->color('info'),
            ];
        }
        protected ?string $heading = '📨 Invitations';

        protected ?string $description = 'Statistiques sur les invitations ';
        // protected static ?int $sort = 4;

       

    protected static ?int $sort = 1;

    public function getData(): array
    {
        $base = Invitation::query();
        $filtered = $this->applyUserEventFilter(clone $base, 'ceremonies.event');

        return [
            'count' => $filtered->count(),
        ];
    }

    protected function getCards(): array
    {
        $base = Invitation::query();
        $filtered = $this->applyUserEventFilter(clone $base, 'ceremonies.event');

        $total = $filtered->count();
        $sent = (clone $filtered)->where('status', 'send')->count();
        $reminders = (clone $filtered)->where('rappel', true)->count();
        $pending = max(0, $total - $sent);

        return [
            Card::make('📦 Total invitations', $total)
                ->description('Invitations visibles')
                ->color('primary')
                ->icon('heroicon-o-inbox'),

            Card::make('✅ Envoyées', $sent)
                ->description('Invitations marquées send')
                ->color('success')
                ->icon('heroicon-o-check'),

            Card::make('🔔 Rappels', $reminders)
                ->description('Invitations avec rappel')
                ->color('warning')
                ->icon('heroicon-o-bell'),

            Card::make('⏳ En attente', $pending)
                ->description('Non envoyées')
                ->color('secondary')
                ->icon('heroicon-o-clock'),
        ];
    }
}
