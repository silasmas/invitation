<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="keywords" content="HTML5 Template" />
<meta name="description" content="Invitation pour le mariage du couple Arcel et Chrisiabelle" />
<meta name="author" content="silasmas.com" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name') }} |{{ isset($titre) ? $titre : '' }}</title>
<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('assets/site/images/favicon.ico') }}" />

<!-- font -->
<link href="https://fonts.googleapis.com/css?family=IBM+Plex+Mono:400,500,700|Tangerine:400,700" rel="stylesheet">

<!-- Plugins -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/plugins-css.css') }} " />

<!-- Typography -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/typography.css') }} " />

<!-- Shortcodes -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/shortcodes/shortcodes.css') }} " />

<!-- Style -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/style.css') }} " />

<!-- Wedding card -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/demo-one-page/wedding-card/css/wedding-card.css') }} " />

<!-- Responsive -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/responsive.css') }} " />

</head>

<body>

<!--=================================
 preloader -->

<div id="pre-loader">
    <img src="{{ asset('assets/site/images/pre-loader/loader-07.svg') }}" alt="">
</div>

<!--=================================
 preloader -->
