{{-- @extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found')) --}}


{{-- resources/views/errors/404.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page non trouvée - 404</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f5f5f5;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        h1 {
            font-size: 4rem;
            color: #e74c3c;
        }
        p {
            font-size: 1.2rem;
            margin-top: -10px;
        }
        a {
            margin-top: 20px;
            color: #3490dc;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>404 😕</h1>
    <p>Oups, la page que vous cherchez n'existe pas...</p>
    <a href="{{ url('/') }}">Retour à l'accueil</a>
</body>
</html>

