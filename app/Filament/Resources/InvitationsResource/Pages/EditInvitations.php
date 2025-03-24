<?php

namespace App\Filament\Resources\InvitationsResource\Pages;

use App\Filament\Resources\InvitationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvitations extends EditRecord
{
    protected static string $resource = InvitationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
