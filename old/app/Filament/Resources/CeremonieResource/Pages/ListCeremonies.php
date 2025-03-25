<?php

namespace App\Filament\Resources\CeremonieResource\Pages;

use App\Filament\Resources\CeremonieResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCeremonies extends ListRecords
{
    protected static string $resource = CeremonieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
