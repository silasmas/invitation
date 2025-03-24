<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Invitation</title>
</head>
<body>
    {{-- <p>Bonjour {{ $nom }},</p> --}}
    {{-- <p>{{ $message }}</p> --}}
    <p>{!! $invitation->message !!}</p>


    <p><strong>Consultez votre invitation ici :</strong></p>
    <p><a href="{{ $lien_invitation }}" target="_blank">Voir mon invitation</a></p>

    <p>À très bientôt !</p>
</body>
</html>
