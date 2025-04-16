<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Groupe;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GroupeImpor implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected $failures = []; // Stocker les erreurs
    protected $ceremonieId;

    public function __construct($ceremonieId)
    {
        $this->ceremonieId = $ceremonieId;
    }

    public function model(array $row)
    {
        return new Groupe([
            'nom'     => $row['nom'],  // Correspond au nom de la colonne dans Excel
            'description'    => $row['description'],
        ]);
    }
// 🔹 Ajout de messages d'erreur personnalisés
public function customValidationMessages()
{
    return [
        '*.nom.required' => 'Le champ "Nom" est obligatoire.',
        '*.nom.string' => 'Le champ "Nom" doit être une chaîne de caractères.',
        '*.nom.max' => 'Le champ "Nom" ne doit pas dépasser 255 caractères.',

        '*.ceremonieId.required' => 'Le champ "ceremonie" est obligatoire.',
        '*.ceremonieId.integer' => 'Le champ "ceremonie" doit être un entier',
    ];
}

    public function rules(): array
    {
        return [
            '*.nom' => 'required|string|max:255',
           '*.ceremonieId' => 'nullable|integer|exists:ceremonies,id',
            '*.description' => 'nullable|string|max:255',
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


}
