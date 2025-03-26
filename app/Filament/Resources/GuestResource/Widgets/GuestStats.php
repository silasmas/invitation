<?php

namespace App\Filament\Resources\GuestResource\Widgets;


use App\Models\Guest;
use Filament\Widgets\Widget;
use Illuminate\View\View;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class GuestStats extends BaseWidget
{
    protected static string $view = 'filament.widgets.guest-stats';
  // ✅ force le widget à occuper toute la largeur
//   protected static ?int $maxWidth = null;
  protected static string $maxWidth = 'full';


    public function render(): View
    {
        $guests = Guest::all();

        $grouped = $guests->groupBy(fn($g) => strtolower($g->relation ?? 'autre'));

        $emailValid = $guests->whereNotNull('email')->filter(fn($g) => str_contains($g->email, '@'))->count();
        $emailMissing = $guests->count() - $emailValid;

        $phoneValid = $guests->whereNotNull('telephone')->filter(fn($g) => trim($g->telephone) !== '')->count();
        $phoneMissing = $guests->count() - $phoneValid;

        return view('filament.widgets.guest-stats', [
            'grouped' => $grouped,
            'total' => $guests->count(),
            'emailValid' => $emailValid,
            'emailMissing' => $emailMissing,
            'phoneValid' => $phoneValid,
            'phoneMissing' => $phoneMissing,
        ]);
    }
}
