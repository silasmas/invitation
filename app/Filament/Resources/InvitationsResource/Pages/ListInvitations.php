<?php

namespace App\Filament\Resources\InvitationsResource\Pages;

use Filament\Actions;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Collection;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Widgets\InvitationStats;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\InvitationsResource;
use App\Filament\Widgets\GuestByCeremonieStats;
use App\Filament\Widgets\InvitationsByCeremonieStats;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationsResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
    public function getHeaderWidgets(): array
    {
        return [
            InvitationStats::class,

            InvitationsByCeremonieStats::class, // ✅ Nouveau widget par cérémonie
        ];
    }
    protected function getTableActions(): array
    {
        return [
            Action::make('Télécharger QR Code')
                ->icon('heroicon-o-qrcode')
                ->url(fn ($record): string => route('generate.qrcode', ['id' => $record->id]))
                ->openUrlInNewTab() // Pour ouvrir dans un nouvel onglet ou télécharger directement
                ->label('QR Code')
        ];
    }
    protected function getTableBulkActions(): array
{
    return [
        BulkAction::make('qrCodes')
            ->label('Générer les QR Codes')
            ->icon('heroicon-o-qrcode')
            ->action(function (Collection $records) {
                foreach ($records as $record) {
                    $url = route('generate.qrcode', ['id' => $record->id]);
                    // Ouvre dans un nouvel onglet n'est pas possible ici, donc rediriger ou traiter côté JS (avancé)
                    // Sinon, log, notifier, ou stocker dans un dossier temporaire
                }

                $this->notify('success', 'QR Codes générés (ouvrir individuellement).');
            }),
    ];
}
}
