<?php

namespace App\Filament\Resources\InvitationsResource\Pages;

use App\Filament\Resources\InvitationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\Actions\Action;
class EditInvitations extends EditRecord
{
    protected static string $resource = InvitationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // Action::make('qrCode')
            // ->label('Télécharger QR Code')
            // ->icon('heroicon-o-qrcode')
            // ->url(fn () => route('generate.qrcode', ['id' => $this->record->id]))
            // ->openUrlInNewTab(),
        ];
    }
}
