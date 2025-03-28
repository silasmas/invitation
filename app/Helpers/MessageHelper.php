<?php
namespace App\Helpers;

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



    // public static function generateQRCode()
    // {
    //     $text     = 'https://event.kwetu.cd';
    //     $filename = public_path('qrcode/myqr.png');

    //     \QRcode::png($text, $filename, QR_ECLEVEL_L, 4);

    //     return response()->file($filename);
    // }

}
