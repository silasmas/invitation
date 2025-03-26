<?php

namespace App\Filament\Widgets;

use App\Models\Invitation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvitationStats extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Invitation::count();
        $envoyees = Invitation::where('status', 'send')->count();
        $acceptees = Invitation::where('status', 'accept')->count();
        $refusees = Invitation::where('status', 'refuse')->count();
        $fermer = Invitation::where('status', 'close')->count();

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
            ];
        }
        protected ?string $heading = '📨 Invitations';

        protected ?string $description = 'Statistiques sur les invitations ';
        protected static ?int $sort = 4;
}
