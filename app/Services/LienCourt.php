<?php

namespace App\Services;

use App\Models\ShortLink;
use Illuminate\Support\Str;

class LienCourt
{
    public static function generate(string $reference, int $ceremonieId): string
{
    // Vérifie s’il existe déjà un lien pour cette référence et cérémonie
    $existing = ShortLink::where('reference', $reference)
        ->where('ceremonie_id', $ceremonieId)
        ->first();

    if ($existing) {
        return url('https://event.kwetu.cd/i/' . $existing->code);
    }

    // Générer un code court unique
    do {
        $code = strtoupper(Str::random(6));
    } while (ShortLink::where('code', $code)->exists());

    ShortLink::create([
        'reference'     => $reference,
        'ceremonie_id'  => $ceremonieId,
        'code'          => $code,
    ]);

    return url('https://event.kwetu.cd/i/' . $code);
}

}
