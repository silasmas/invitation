<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

// require_once app_path('Libraries/qrcode/qrlib.php');
class MessageHelper
{
    public static function cleanFormattedMessage(string $html): string
    {
        // Remplace <br> par 1 saut de ligne
        $text = preg_replace('/<br\s*\/@endphp/i', "\n", $html);

                                                         // Remplace </p> par 2 sauts de ligne
        $text = preg_replace('/<\/p>/i', "\n\n", $text); // ✅ ici la vraie correction

        // Supprime les <p> ouvrants
        $text = preg_replace('/<p[^>]*>/i', '', $text);

        // Simule le gras (**texte**)
        $text = preg_replace('/<(strong|b)[^>]*>(.*?)<\/(strong|b)>/i', '*$2*', $text);

        // Convertit les entités HTML (&nbsp;, etc.)
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Supprime toutes les autres balises
        $text = strip_tags($text);

        // Nettoie les multiples sauts de ligne
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
    public static function isValidPhone(?string $phone): bool
    {
        return ! empty($phone) && preg_match('/^\+?[1-9]\d{9,14}$/', $phone);
    }
    public static function isValidEmail(?string $email): bool
{
    return ! empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
}
public static function cleanMessageForSms(?string $message, int $maxLength = 160): string
{
    if (empty($message)) {
        return '';
    }

    // 1. Supprime les balises HTML
    $message = strip_tags($message);

    // 2. Convertit les entités HTML (&nbsp;, &amp; etc.)
    $message = html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // 3. Supprime les multiples espaces, tabulations, retours à la ligne
    $message = preg_replace('/\s+/', ' ', $message);

    // 4. Nettoyage final
    $message = trim($message);

    // 5. Limite le message à $maxLength caractères
    if (mb_strlen($message) > $maxLength) {
        $message = mb_substr($message, 0, $maxLength - 3) . '...';
    }

    return $message;
}


public static function sendSms($phoneNumber, $message)
{
    // 🔹 Vérification : Si le numéro est vide, ne pas envoyer de SMS
    if (empty($phoneNumber)) {
        Notification::make()->title("Erreur")->body("Le numéro de téléphone est vide")->danger()->send();
        Log::error("Erreur : Le numéro de téléphone est vide. ");

    }

    // 🔹 Vérification : Si le numéro n'est pas valide, ne pas envoyer de SMS
    if (! MessageHelper::isValidPhone($phoneNumber)) {
        Notification::make()->title("Erreur")->body("Le numéro de téléphone n'est pas valide.")->danger()->send();
        Log::error("Erreur : Le numéro de téléphone n'est pas valide. ");

    }

    // URL de l'API de Keccel (remplacez par l'URL réelle)
    $apiUrl = 'https://api.keccel.com/sms/v2/message.asp';
    $apiKey = 'BAPK3A29RHG6QY2';
    $msg    = MessageHelper::cleanMessageForSms($message, 500);
// dd($msg);
    // Données à envoyer
    $postData = [
        "token"   => $apiKey,
        "to"      => $phoneNumber,
        "from"    => 'KWETU',
        "message" => $msg,
    ];
    // dd( $postData);

    // Initialisation de cURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey",
    ]);

    // Exécuter la requête
    $response = curl_exec($ch);

    // Vérifier les erreurs
    if (curl_errno($ch)) {
        echo "Erreur cURL : " . curl_error($ch);
    }

    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        "status_code" => true,
        "response"    => json_decode($response, true),
    ];
}

}
