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
    protected ?string $description = 'Statistiques sur les invités';
    protected static ?int $sort    = 2; // pour l'afficher en haut

    // ...existing code...

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
        // Détecter super_admin
        $user = auth()->user();
        $isSuperAdmin = $user && (method_exists($user, 'hasRole')
            ? $user->hasRole('super_admin')
            : optional($user->role)->name === 'super_admin');

        $base = Guest::query();
        $filtered = $this->applyUserEventFilter(clone $base, 'invitation.ceremonies.event');

        // total global pour super_admin, sinon total visible pour l'utilisateur
        $total = $isSuperAdmin ? Guest::count() : $filtered->count();

        $emailValid = $isSuperAdmin
            ? Guest::whereNotNull('email')->where('email', 'like', '%@%')->count()
            : (clone $filtered)->whereNotNull('email')->where('email', 'like', '%@%')->count();

        $phoneValid = $isSuperAdmin
            ? Guest::whereNotNull('phone')->where('phone', '!=', '')->count()
            : (clone $filtered)->whereNotNull('phone')->where('phone', '!=', '')->count();

        $emailMissing = max(0, $total - $emailValid);
        $phoneMissing = max(0, $total - $phoneValid);

        return [
            Stat::make('👥 Invités total', $total)
                ->description($isSuperAdmin ? 'Tous les invités (admin)' : "Vos invités")
                ->color('primary'),

            Stat::make('📧 Emails valides', $emailValid)
                ->description("{$emailMissing} sans email")
                ->color('success'),

            Stat::make('📱 Téléphones valides', $phoneValid)
                ->description("{$phoneMissing} sans numéro")
                ->color('info'),
        ];
    }
    // protected function getStats(): array
    // {
    //     $total = Guest::count();

    //     $emailValid   = Guest::whereNotNull('email')->where('email', 'like', '%@%')->count();
    //     $emailMissing = $total - $emailValid;

    //     $phoneValid   = Guest::whereNotNull('phone')->where('phone', '!=', '')->count();
    //     $phoneMissing = $total - $phoneValid;

    //     return [
    //         Stat::make('👥 Invités total', $total)
    //             ->description('Tous les invités enregistrés')
    //             ->color('primary'),

    //         Stat::make('📧 Emails valides', $emailValid)
    //             ->description("{$emailMissing} sans email")
    //             ->color('success'),

    //         Stat::make('📱 Téléphones valides', $phoneValid)
    //             ->description("{$phoneMissing} sans numéro")
    //             ->color('info'),
    //     ];

    // }
}
