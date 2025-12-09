<?php

namespace App\Filament\Widgets;

use App\Models\Guest;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Widgets\Concerns\FiltersByUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class GuestRelationStats extends BaseWidget
{
    use FiltersByUser;
    protected ?string $heading = 'Par relation';
//   protected ?string $heading = 'Invitations par relation';
    protected ?string $description = 'Répartition des invitations selon la relation de l\'invité';
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $base = Guest::query();
        $filtered = $this->applyUserEventFilter(clone $base, 'ceremonies.event')->with('invitation')->get();

        $grouped = $filtered->groupBy(function ($inv) {
            $relation = optional($inv->guests)->relation ?? null;
            return strtolower(trim($relation ?: 'autre'));
        });

        $colors = ['red','pink','purple','indigo','blue','green','yellow','orange','teal','gray'];

        $cards = [];
        foreach ($grouped as $relation => $items) {
            $hash = crc32($relation);
            $color = $colors[$hash % count($colors)];
            $label = ucfirst($relation);
            $cards[] = Stat::make("👥 {$label}", $items->count())
                ->description("Invitations: {$items->count()}")
                ->color($color);
        }

        // Si aucun résultat, retourner une carte générique
        if (empty($cards)) {
            $cards[] = Stat::make('Aucune relation', 0)
                ->description('Aucune invitation visible')
                ->color('gray');
        }

        return $cards;
    }
// // protected ?string $description = 'Répartition des invités par relation';
//     protected function getStats(): array
//     {
//         $grouped = Guest::all()->groupBy(fn ($g) => strtolower($g->relation ?? 'autre'));

//         $colors = [
//             'red', 'pink', 'purple', 'indigo', 'blue', 'green', 'yellow', 'orange', 'teal', 'gray'
//         ];

//         $cards = [];

//         foreach ($grouped as $relation => $guests) {
//             $hash = crc32($relation);
//             $color = $colors[$hash % count($colors)];
//             $cards[] = Stat::make("👥 " . ucfirst($relation), $guests->count())
//                 ->description("Invités : " . $guests->count())
//                 ->color($color);
//         }

//         return $cards;
//     }
    // protected static ?int $sort = 3; // Pour l'afficher après les stats globales
}
