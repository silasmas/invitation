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
{{-- <link rel="shortcut icon" href="{{ asset('assets/site/images/favicon.ico') }}" /> --}}

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
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/monStyle.css') }} " /> --}}

<!-- Wedding card -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/demo-one-page/wedding-card/css/wedding-card.css') }} " />

<!-- Responsive -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/responsive.css') }} " />
<style>
    body { margin: 0; font-family: 'Tangerine', cursive; }
    body.block-scroll { overflow: hidden; }

    .curtain-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 100vw;
      z-index: 9999;
      display: flex;
      justify-content: space-between;
      align-items: center;
      pointer-events: auto;
      overflow: hidden;
      background: url('../assets/images/voilage.png') center center / cover no-repeat;
    }

    .curtain {
      width: 50vw;
      height: 100vh;
      background: url('../assets/images/rideau-texture.png') repeat-y;
      background-size: cover;
      transition: transform 2.5s ease-in-out;
      box-shadow: inset 0 0 30px rgba(0,0,0,0.3);
    }

    .left-curtain { transform: translateX(0); border-radius: 0 50% 50% 0; }
    .right-curtain { transform: translateX(0); border-radius: 50% 0 0 50%; }
    .curtain.open-left { transform: translateX(-100%); }
    .curtain.open-right { transform: translateX(100%); }

    .curtain-content {
      position: absolute;
      z-index: 10000;
      width: 100%;
      text-align: center;
      color: white;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
      padding: 20px;
    }
    .curtain-content h1 { font-size: 6vw; margin: 0; }
    .curtain-content h2 { font-size: 10vw; margin-bottom: 20px; }
    .enter-btn {
      padding: 10px 24px;
      font-size: 4vw;
      max-width: 90vw;
      background: white;
      color: #b90e5b;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .enter-btn:hover { background: #f9cce1; }
    @media (min-width: 768px) {
      .curtain-content h1 { font-size: 40px; }
      .curtain-content h2 { font-size: 64px; }
      .enter-btn { font-size: 18px; padding: 14px 36px; }
    }

    .falling-petal {
      position: fixed;
      top: -50px;
      width: 20px;
      height: 20px;
      background-image: url('../assets/images/petal.png');
      background-size: cover;
      opacity: 0.8;
      pointer-events: none;
      z-index: 10;
      animation: fall 12s linear infinite;
    }

    @keyframes fall {
      0% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
      50% { opacity: 1; }
      100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
  </style>
</head>

<body>
  
<!--=================================
 preloader -->

<div id="pre-loader">
    <img src="{{ asset('assets/site/images/pre-loader/loader-15.svg') }}" alt="">
</div>

<!--=================================
 preloader -->
