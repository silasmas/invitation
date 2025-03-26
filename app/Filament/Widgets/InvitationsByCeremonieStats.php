<?php

namespace App\Filament\Widgets;

use App\Models\Ceremonie;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvitationsByCeremonieStats extends BaseWidget
{
    protected function getStats(): array
    {
         // On suppose que chaque cérémonie a une relation "invitations"
         $ceremonies = Ceremonie::withCount([
            'invitation as envoyees_count' => function ($query) {
                $query->where('status', 'send');
            }
        ])->get();

        $colors = ['blue', 'green', 'pink', 'purple', 'orange', 'red', 'teal', 'indigo', 'amber', 'gray'];

        $cards = [];

        foreach ($ceremonies as $ceremonie) {
            $hash = crc32($ceremonie->nom);
            $color = $colors[$hash % count($colors)];

            $cards[] = Stat::make("📩 {$ceremonie->nom}", $ceremonie->envoyees_count)
                ->description("Invitations envoyées")
                ->color($color);
        }

        return $cards;
    }
    protected ?string $heading = 'Invitations par cérémonie';

    protected ?string $description = 'Statistiques sur les invitations envoyées par cérémonie';
        protected static ?int $sort = 6; // pour l'afficher en haut
}
