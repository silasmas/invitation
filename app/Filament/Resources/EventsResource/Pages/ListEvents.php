<?php

namespace App\Filament\Resources\EventsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EventsResource;
use App\Filament\Resources\EventsResource\Widgets\EventStats;

class ListEvents extends ListRecords
{
    protected static string $resource = EventsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            EventStats::class,
        ];
    }
}
