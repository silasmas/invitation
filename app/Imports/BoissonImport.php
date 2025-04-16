<?php
namespace App\Imports;

use App\Models\Boisson;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class BoissonImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected $failures          = []; // Stocker les erreurs
    protected $skippedDuplicates = []; // Stocker les doublons ignorés
    public function __construct()
    {
    }

    public function model(array $row)
    {
// Vérifier l'existence d'une boisson avec le même nom
        if (Boisson::where('nom', $row['nom'])->exists()) {
            $this->skippedDuplicates[] = $row;
            return null; // Ignorer la ligne
        }
        return new Boisson([
            'nom'         => $row['nom'], // Correspond au nom de la colonne dans Excel
            'description' => $row['description'],
        ]);
    }
// 🔹 Ajout de messages d'erreur personnalisés
    public function customValidationMessages()
    {
        return [
            '*.nom.required' => 'Le champ "Nom" est obligatoire.',
            '*.nom.string'   => 'Le champ "Nom" doit être une chaîne de caractères.',
            '*.nom.max'      => 'Le champ "Nom" ne doit pas dépasser 255 caractères.',
        ];
    }

    public function rules(): array
    {
        return [
            '*.nom'         => 'required|string|max:255',
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

    public function getSkippedDuplicates()
    {
        return $this->skippedDuplicates;
    }
}
