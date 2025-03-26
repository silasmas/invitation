<?php

namespace App\Filament\Widgets;

use App\Models\Guest;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class GuestRelationStats extends BaseWidget
{
    protected ?string $heading = 'Par relation';

protected ?string $description = 'Répartition des invités par relation';
    protected function getStats(): array
    {
        $grouped = Guest::all()->groupBy(fn ($g) => strtolower($g->relation ?? 'autre'));

        $colors = [
            'red', 'pink', 'purple', 'indigo', 'blue', 'green', 'yellow', 'orange', 'teal', 'gray'
        ];

        $cards = [];

        foreach ($grouped as $relation => $guests) {
            $hash = crc32($relation);
            $color = $colors[$hash % count($colors)];
            $cards[] = Stat::make("👥 " . ucfirst($relation), $guests->count())
                ->description("Invités : " . $guests->count())
                ->color($color);
        }

        return $cards;
    }
    protected static ?int $sort = 3; // Pour l'afficher après les stats globales
}
