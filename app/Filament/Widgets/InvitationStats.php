<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Widgets\Concerns\FiltersByUser;


class InvitationStats extends BaseWidget
{
    use FiltersByUser;

    //    protected static string $view = 'filament.widgets.invitation-stats';

    public function getData(): array
    {
        $query = Invitation::query();

        // applyUserEventFilter attend une relation dot vers Event depuis Invitation :
        // dans votre modèle Invitation la relation vers Ceremonie s'appelle 'ceremonies'
        // et Ceremonie a la relation 'event' => dot = 'ceremonies.event'
        $query = $this->applyUserEventFilter($query, 'ceremonies.event');

        $count = $query->count();

        return [
            'count' => $count,
        ];
    }
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
        protected static ?int $sort = 4;
}
