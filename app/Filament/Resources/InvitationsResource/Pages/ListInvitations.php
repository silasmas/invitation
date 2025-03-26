<?php

namespace App\Filament\Resources\InvitationsResource\Pages;

use Filament\Actions;
use App\Filament\Widgets\InvitationStats;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\InvitationsResource;
use App\Filament\Widgets\GuestByCeremonieStats;
use App\Filament\Widgets\InvitationsByCeremonieStats;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            InvitationStats::class,

            InvitationsByCeremonieStats::class, // ✅ Nouveau widget par cérémonie
        ];
    }
}
