<?php
namespace App\Filament\Pages;

use App\Models\Guest;
use Filament\Pages\Page;
use App\Helpers\MessageHelper;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class WhatsAppLinks extends Page
{
    protected static ?string $permission = 'access_stats_dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view  = 'filament.pages.whatsapp-links';
    protected static ?string $slug = 'whatsapp';

    protected static bool $shouldRegisterNavigation = false; // 1/ Cache la page du menu

    public Collection $guests;
    public Collection $invalidGuests;
    public string $messageTemplate = "";

    public function mount()
    {

            $this->messageTemplate = session( 'messageTxt',[]);
//
        if (request()->isMethod('post')) {
            session()->forget('guest_ids');
            session()->forget('messageTxt');
            $this->guests = collect(); // vide la liste
            return;
        }

        $ids       = session('guest_ids', []);
        $allGuests = Guest::whereIn('id', $ids)->get();

        $this->guests        = $allGuests->filter(fn($guest) =>MessageHelper::isValidPhone($guest->phone));
        $this->invalidGuests = $allGuests->reject(fn($guest) =>MessageHelper::isValidPhone($guest->phone));

    }

    public function handleDelete()
    {
        session()->forget('guest_ids');
        session()->forget('messageTxt');
        $this->guests        = collect();
        $this->invalidGuests = collect();
        Notification::make()
            ->title('Liens supprimés')
            ->success()
            ->send();
    }


}
