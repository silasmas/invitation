<?php

namespace App\Exports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvitationExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $invitations;

    public function __construct(Collection $invitations)
    {
        $this->invitations = $invitations;
    }

    public function collection(): Collection
    {
        return $this->invitations;
    }

    public function headings(): array
    {
        return [
            'Type',
            'Nom de l\'invité',
            'Référence',
            'Boissons',
            'Cadeau',
            'Cérémonie',
            'Statut',
            'Table',
            'Confirmé',
            'Date de création',
        ];
    }
    public function map($invitation): array
    {
        return [
            $invitation->guests->type ?? '',
            $invitation->guests->nom ?? '',
            $invitation->reference,
            $invitation->boissons,
            $invitation->cadeau,
            $invitation->ceremonies->nom ?? '',
            match($invitation->status) {
                'pedding' => 'En attente',
                'send' => 'Envoyée',
                'accept' => 'Acceptée',
                'refuse' => 'Refusée',
                default => ucfirst($invitation->status),
            },
            $invitation->groupe->nom ?? '',
            $invitation->confirmation ? 'Oui' : 'Non',
            $invitation->created_at?->format('d/m/Y H:i'),
        ];
    }
}
