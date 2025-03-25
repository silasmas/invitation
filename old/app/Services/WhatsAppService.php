<?php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Envoie un message WhatsApp via l'API officielle de Meta
     *
     * @param string $phoneNumber Numéro du destinataire (ex : +22990011234)
     * @param string $message     Message à envoyer
     * @return array              Réponse de l'API
     */
    public static function sendMessage($phoneNumber, $message): array
    {
        // 🔹 1. Construire l’URL d’envoi du message
        $url = env('WHATSAPP_API_URL') . env('WHATSAPP_PHONE_ID') . "/messages";

        // 🔹 2. Récupérer le token d’authentification
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');

        // 🔹 3. Construire et envoyer la requête HTTP POST
        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken", // Authentification via token
            'Content-Type' => 'application/json',
        ])->post($url, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phoneNumber,        // Numéro de téléphone du destinataire
            'type' => 'text',
            'text' => [
                'body' => $message,     // Contenu du message
            ],
        ]);

        // 🔹 4. Retourner la réponse sous forme de tableau
        return $response->json();
    }
}
