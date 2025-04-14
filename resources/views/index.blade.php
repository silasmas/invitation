<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Invitation de Mariage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdf7f7;
            overflow-x: hidden;
        }

        .envelope-wrapper {
            width: 100vw;
            height: 100vh;
            background-color: #fdf7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 999;
        }

        .envelope-img {
            width: 300px;
            height: auto;
            animation: envelopeFadeOut 3s ease 2s forwards;
        }

        @keyframes envelopeFadeOut {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(1.5) translateY(-100px);
            }
        }

        .invitation-content {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .invitation-content.show {
            opacity: 1;
        }
    </style>
</head>

<body>

    {{-- ENVELOPPE --}}
    <div class="envelope-wrapper">
        <img src="{{ asset('images/envelope-florale.png') }}" alt="Enveloppe" class="envelope-img">
    </div>

    {{-- INVITATION --}}
    <div class="invitation-content" id="invitation">
        <section class="wedding-card page-section-ptb">
            <div class="container">
                <div class="row justify-content-center no-gutter">
                    <div class="col-lg-8 align-self-center">
                        <div class="wedding-invitation white-bg p-5">
                            <div class="wedding-card-head text-center">
                                <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/top-bg.png') }}">
                            </div>
                            <div class="wedding-card-body text-center position-relative">
                                <h5>Wedding Invitation</h5>
                                <div class="bg-image">
                                    <img src="{{ asset('assets/site/images/couple.png') }}" alt="" class="img-fluid">
                                    <div class="mask"></div>
                                </div>

                                @if ($invitation)
                                    <h1 class="my-2">
                                        {{ $invitation->guests->type . ' ' . $invitation->guests->nom }}
                                    </h1>
                                    <h6>
                                        Soyez les bienvenue(s) à la célébration de notre mariage
                                        {{ $invitation->ceremonies->nom }}.
                                    </h6>
                                    <div class="wedding-address">
                                        <h3 class="uppercase my-3">Table :
                                            <span class="theme-color text-center">
                                                {{ $invitation->groupe->nom }}
                                            </span>
                                        </h3>
                                    </div>

                                    @switch($invitation->status)
                                        @case('close')
                                            <div class="wedding-address">
                                                <h3 class="uppercase my-3">Etat :
                                                    <span class="theme-color text-center">
                                                        L'invitation est clôturée
                                                    </span>
                                                </h3>
                                            </div>
                                            @break

                                        @case('refuse')
                                            <div class="wedding-address">
                                                <h3 class="uppercase my-3">Etat :
                                                    <span class="theme-color text-center">
                                                        L'invité a refusé l'invitation
                                                    </span>
                                                </h3>
                                            </div>
                                            @break

                                        @default
                                            <button type="button" class="btn rsvp-btn mt-10" id="close-btn"
                                                data-invitation-id="{{ $invitation->reference }}">
                                                Valider l'invitation
                                            </button>
                                    @endswitch
                                @endif
                            </div>
                            <div class="wedding-card-footer text-center">
                                <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/bottom-bg.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- SCRIPT POUR L'AFFICHAGE --}}
    <script>
        setTimeout(() => {
            document.querySelector('.envelope-wrapper').style.display = 'none';
            document.getElementById('invitation').classList.add('show');
        }, 5000); // temps total de l'animation
    </script>

</body>

</html>
