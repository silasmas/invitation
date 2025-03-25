<?php

namespace App\Services;

use App\Models\ShortLink;
use Illuminate\Support\Str;

class LienCourt
{
    public static function generate(string $reference): string
    {
        // Vérifie si un lien existe déjà
        $existing = ShortLink::where('reference', $reference)->first();

        if ($existing) {
            return url('https://event.kwetu.cd/i/' . $existing->code);
        }

        // Sinon, créer un nouveau
        do {
            $code = strtoupper(Str::random(6));
        } while (ShortLink::where('code', $code)->exists());

        ShortLink::create([
            'reference' => $reference,
            'code'      => $code,
        ]);

        return url('https://event.kwetu.cd/i/' . $code);
    }
}
