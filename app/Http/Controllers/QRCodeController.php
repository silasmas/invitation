<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function generate(Request $request)
    {
        require_once app_path('Librairies/phpqrcode/qrlib.php');


        // Identifiant ou slug passé en paramètre
        $invitationId = $request->input('id', 'default');

        // URL à encoder dans le QR code
        $url = url('https://event.kwetu.cd/reception/' . $invitationId);
        // $text = $request->input('text', 'https://event.kwetu.cd');
        $filename = 'qrcode_' . Str::random(8) . '.png';
        $filepath = public_path('qrcodes/' . $filename);

        // Créer le dossier si nécessaire
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0777, true);
        }

        \QRcode::png($url, $filepath, QR_ECLEVEL_L, 6);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
       // Télécharger le QR code
       public function downloadQrCode($invitation)
       {
           require_once app_path('Librairies/phpqrcode/qrlib.php');
        $inv=Invitation::where('reference', $invitation)->firstOrFail();
           $qrData = url('https://event.kwetu.cd/reception/', $inv->reference); // Par exemple, lien vers la page d'invitation


        // URL à encoder dans le QR code
        $url = url('https://event.kwetu.cd/reception/' . $inv->reference);
        // $text = $request->input('text', 'https://event.kwetu.cd');
        $filename = 'qrcode_' . Str::random(8) . '.png';
        $filepath = public_path('qrcodes/' . $filename);

        // Créer le dossier si nécessaire
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0777, true);
        }

        \QRcode::png($url, $filepath, QR_ECLEVEL_L, 6);

        return response()->download($filepath)->deleteFileAfterSend(true);
       }
}

