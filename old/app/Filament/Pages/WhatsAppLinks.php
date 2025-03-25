<?php
namespace App\Filament\Pages;

use App\Models\Guest;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class WhatsAppLinks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view  = 'filament.pages.whatsapp-links';
    protected static ?string $slug = 'whatsapp';

    protected static bool $shouldRegisterNavigation = false; // 1/ Cache la page du menu

    public Collection $guests;
    public Collection $invalidGuests;
    public string $messageTemplate = "Bonjour {categorie} {nom},\n
                                    Vous êtes invité à la cérémonie : {ceremonie}.\n
                                    Cliquez ici pour voir votre invitation : {lien}";

    public function mount()
    {
        if (empty($this->messageTemplate)) {
            $this->messageTemplate = "Bonjour {categorie} {nom},\n
                                        Vous êtes invité à la cérémonie : {ceremonie}.\n
                                        Cliquez ici pour voir votre invitation : {lien}";
        }
        if (request()->isMethod('post')) {
            session()->forget('guest_ids');
            $this->guests = collect(); // vide la liste
            return;
        }

        $ids       = session('guest_ids', []);
        $allGuests = Guest::whereIn('id', $ids)->get();

        $this->guests        = $allGuests->filter(fn($guest) => $this->isValidPhone($guest->phone));
        $this->invalidGuests = $allGuests->reject(fn($guest) => $this->isValidPhone($guest->phone));
    }

    public function handleDelete()
    {
        session()->forget('guest_ids');
        $this->guests        = collect();
        $this->invalidGuests = collect();
        Notification::make()
            ->title('Liens supprimés')
            ->success()
            ->send();
    }
    private function isValidPhone(?string $phone): bool
    {
        return ! empty($phone) && preg_match('/^\+?[1-9]\d{9,14}$/', $phone);
    }

}
