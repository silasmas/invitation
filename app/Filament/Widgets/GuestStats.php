<?php
namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\FiltersByUser;
use App\Models\Guest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GuestStats extends BaseWidget
{
    use FiltersByUser;
    protected ?string $heading = '👥 Invités';
    // public $total = Guest::count();

    protected ?string $description = 'Statistiques sur les invités';
    protected static ?int $sort    = 2; // pour l'afficher en haut

    // protected static string $view = 'filament.widgets.guest-stats';

    public function getData(): array
    {
        $query = Guest::query();

        // Guest -> invitation -> ceremonies -> event
        $query = $this->applyUserEventFilter($query, 'invitation.ceremonies.event');

        return [
            'count' => $query->count(),
        ];
    }
    protected function getStats(): array
    {
        $total = Guest::count();

        $emailValid   = Guest::whereNotNull('email')->where('email', 'like', '%@%')->count();
        $emailMissing = $total - $emailValid;

        $phoneValid   = Guest::whereNotNull('phone')->where('phone', '!=', '')->count();
        $phoneMissing = $total - $phoneValid;

        return [
            Stat::make('👥 Invités total', $total)
                ->description('Tous les invités enregistrés')
                ->color('primary'),

            Stat::make('📧 Emails valides', $emailValid)
                ->description("{$emailMissing} sans email")
                ->color('success'),

            Stat::make('📱 Téléphones valides', $phoneValid)
                ->description("{$phoneMissing} sans numéro")
                ->color('info'),
        ];

    }
}
