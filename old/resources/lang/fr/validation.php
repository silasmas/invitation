<?php
return [
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'unique' => 'Le champ :attribute est déjà utilisé.',
    '*.telephone.regex' => 'Le numéro de téléphone doit contenir uniquement des chiffres et peut commencer par un "+".',
    '*.telephone.digits_between' => 'Le numéro de téléphone doit contenir entre 8 et 15 chiffres.',
];
