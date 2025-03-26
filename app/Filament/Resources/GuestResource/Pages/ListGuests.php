<?php
namespace App\Filament\Resources\GuestResource\Pages;

use Filament\Actions;
// use App\Filament\Resources\GuestResource\Widgets\GuestStats;
use App\Filament\Widgets\GuestStats;
use App\Filament\Resources\GuestResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\GuestRelationStats;
use App\Filament\Widgets\GuestByCeremonieStats;

class ListGuests extends ListRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            GuestStats::class,
            GuestRelationStats::class, // Répartition par relation
        ];
    }
}
