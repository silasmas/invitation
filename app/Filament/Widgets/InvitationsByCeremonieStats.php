<?php

namespace App\Filament\Widgets;

use App\Models\Ceremonie;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Widgets\Concerns\FiltersByUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvitationsByCeremonieStats extends BaseWidget
{
     use FiltersByUser;
    // protected function getStats(): array
    // {
    //      // On suppose que chaque cérémonie a une relation "invitations"
    //      $ceremonies = Ceremonie::withCount([
    //         'invitation as envoyees_count' => function ($query) {
    //             $query->where('status', 'send');
    //         }
    //     ])->get();

    //     $colors = ['blue', 'green', 'pink', 'purple', 'orange', 'red', 'teal', 'indigo', 'amber', 'gray'];

    //     $cards = [];

    //     foreach ($ceremonies as $ceremonie) {
    //         $hash = crc32($ceremonie->nom);
    //         $color = $colors[$hash % count($colors)];

    //         $cards[] = Stat::make("📩 {$ceremonie->nom}", $ceremonie->envoyees_count)
    //             ->description("Invitations envoyées")
    //             ->color($color);
    //     }

    //     return $cards;
    // }
    protected function getStats(): array
    {
        $base = Ceremonie::query();
        $filtered = $this->applyUserEventFilter(clone $base, 'invitation.ceremonies')->with('invitation')->get();

        $grouped = $filtered->groupBy(function ($inv) {
            return $inv->ceremonies->nom ?? 'Sans cérémonie';
        });

        $cards = [];
        foreach ($grouped as $ceremonyName => $items) {
            $count = $items->count();
            $label = mb_strimwidth($ceremonyName, 0, 28, '...');
            $cards[] = Stat::make($label, $count)
                ->description("Invitations: {$count}")
                ->color('primary');
        }

        if (empty($cards)) {
            $cards[] = Stat::make('Aucune cérémonie', 0)
                ->description('Aucune invitation visible')
                ->color('gray');
        }

        return $cards;
    }
    protected ?string $heading = 'Invitations par cérémonie';

    protected ?string $description = 'Statistiques sur les invitations envoyées par cérémonie';
        protected static ?int $sort = 6; // pour l'afficher en haut
}
