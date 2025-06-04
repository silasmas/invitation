<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuestsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected $failures = []; // Stocker les erreurs
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

   public function model(array $row)
{
    // 1) Si la ligne est entièrement vide, on ignore
    if (
        (empty($row['type']) || is_null($row['type'])) &&
        (empty($row['nom'])  || is_null($row['nom']))  &&
        (empty($row['email'])|| is_null($row['email']))&&
        (empty($row['phone'])|| is_null($row['phone']))&&
        (empty($row['relation'])|| is_null($row['relation']))
    ) {
        // Retourner null fait que Maatwebsite n’essaiera pas d’enregistrer ce guest
        return null;
    }

    // 2) S’assurer que les clés existent (au cas où le heading row serait décalé)
    $type     = $row['type']     ?? null;
    $nom      = $row['nom']      ?? null;
    $email    = $row['email']    ?? null;
    $phoneRaw = $row['phone']    ?? null;
    $relation = $row['relation'] ?? 'autre';

    // 3) Nettoyer le téléphone (si vous gardez la regex actuelle)
    $cleanPhone = $phoneRaw ? str_replace(' ', '', $phoneRaw) : null;

    return new Guest([
        'type'     => $type,
        'event_id' => $this->eventId,
        'nom'      => $nom,
        'email'    => $email,
        'phone'    => $cleanPhone,
        'relation' => $relation,
    ]);
}

// 🔹 Ajout de messages d'erreur personnalisés
public function customValidationMessages()
{
    return [
        '*.nom.required' => 'Le champ "Nom" est obligatoire.',
        '*.nom.string' => 'Le champ "Nom" doit être une chaîne de caractères.',
        '*.nom.max' => 'Le champ "Nom" ne doit pas dépasser 255 caractères.',

        '*.email.email' => 'Le champ "Email" doit être une adresse email valide.',
        '*.email.unique' => 'Cet email est déjà enregistré.',

        '*.phone.string' => 'Le champ "Téléphone" doit être une chaîne de caractères.',
        '*.phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
        '*.phone.regex' => 'Le numéro de téléphone doit contenir uniquement des chiffres et peut commencer par un "+".',
        '*.phone.digits_between' => 'Le numéro de téléphone doit contenir entre 8 et 15 chiffres.',
        'starts_with:+,0,1,2,3,4,5,6,7,8,9', // Accepte les numéros commençant par ces caractères
    ];
}

    public function rules(): array
    {
        return [
            '*.nom' => 'nullable|string|max:255',
            '*.type' => 'nullable|string|max:255',
            // '*.email' =>  [
            //     'nullable',
            //     'email',
            //     Rule::unique('guests', 'email')->where(fn ($query) => $query->where('event_id', $this->eventId)),
            // ],
            '*.phone' => [
                'nullable',
                // 'regex:/^\+?[0-9]{8,15}$/',
                // 'starts_with:+,0,1,2,3,4,5,6,7,8,9',
            ],
        ];
    }
     // Capturer les erreurs de validation
     public function onFailure(Failure ...$failures)
     {
        Log::error('Échec de l’importation avec des erreurs', ['failures' => $failures]);
         $this->failures = $failures;
     }

     public function getFailures()
     {
         return $this->failures;
     }

    function sendSms($phoneNumber, $message)
    {
        // URL de l'API de Keccel (remplacez par l'URL réelle)
        $apiUrl = env('SMS_URL');

        // Clé API ou identifiants d'authentification (remplacez par vos informations)
        $apiKey = env('SMS_TOKEN');

        // Données à envoyer
        $postData = [
            "token" => $apiKey,    // taken
            "to" => $phoneNumber,    // Numéro de téléphone du destinataire
            "from" => env('SMS_FROM'), // Optionnel : Nom ou numéro de l'expéditeur
            "message" => $message,   // Contenu du message
        ];

        // Initialisation de cURL
        $ch = curl_init();

        // Configuration de la requête
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // Conversion des données en JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey", // Clé API incluse dans les en-têtes
        ]);

        // Exécuter la requête
        $response = curl_exec($ch);

        // Vérifier les erreurs
        if (curl_errno($ch)) {
            echo "Erreur cURL : " . curl_error($ch);
        }

        // Décoder la réponse
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Afficher la réponse pour débogage
        return [
            "status_code" => $responseCode,
            "response" => json_decode($response, true),
        ];
    }


}
