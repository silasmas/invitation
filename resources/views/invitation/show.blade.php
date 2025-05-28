@include('parties.entete')

<style>
    .color-circle {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease, box-shadow 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .color-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .color-label {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.3rem;
    }

    @media (max-width: 575.98px) {

        /* Mobile */
        .color-circle {
            width: 45px;
            height: 45px;
            box-shadow: none;
        }
    }

    @media (min-width: 576px) and (max-width: 991.98px) {

        /* Tablette */
        .color-circle {
            width: 60px;
            height: 60px;
            box-shadow: none;
        }
    }

    @media (min-width: 992px) {

        /* Desktop */
        .color-circle {
            width: 70px;
            height: 70px;
        }

        .color-circle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
    }


    .dress-image {
        max-width: 100%;
        height: auto;
        width: 100%;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .dress-image:hover {
        transform: scale(1.01);
    }

    .click-to-zoom {
        font-size: 0.85rem;
        color: #777;
        margin-top: 0.5rem;
        font-style: italic;
    }

    /* Modal image adapts to screen size */
    .modal-body img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }
</style>

<div class="animate-on-load" style="text-align:center; padding:10px 20px;">
    <section class="wedding-card page-section-ptb">
        <div class="container">
            <div class="row justify-content-center no-gutter">
                @if ($invitation->status == 'refuse')
                    <div class="col-lg-4 align-self-center">
                        <div class="wedding-invitation white-bg p-5">
                            <div class="wedding-card-head text-center floral-top animate-on-load">
                                <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/top-bg.png') }}">
                            </div>
                            <div class="wedding-card-body text-center position-relative">
                                <h5>Invitation refusée</h5>
                                <h6>Vous avez refusé l'invitation de
                                    {{ $invitation->ceremonies->event->femme . ' & ' . $invitation->ceremonies->event->homme }}
                                </h6>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-lg-8 align-self-center">
                        <div class="wedding-invitation white-bg p-5">
                            <div class="wedding-card-head text-center floral-top animate-on-load">
                                <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/top-bg.png') }}">
                            </div>
                            <div class="wedding-card-body text-center position-relative mb-50">
                                <h1 class="my-2 mb-30 mt-30">Invitation</h1>
                                <div class="bg-image couple-photo animate-on-load"
                                    data-aos="zoom-in"data-aos-delay="200">
                                    <img src="{{ asset('storage/'.$invitation->ceremonies->image) }}" alt=""
                                        class="img-fluid">
                                    <div class="mask">
                                    {{-- <img src="{{ asset('assets/site/images/couple.png') }}" alt=""
                                        class="img-fluid">
                                    <div class="mask"> --}}

                                    </div>
                                </div>
                                <h1 class="my-2 mt-30">{{ $invitation->guests->type . ' ' . $invitation->guests->nom }}</h1>
                                <h6>{{ $invitation->ceremonies->event->femme . ' & ' . $invitation->ceremonies->event->homme }}
                                    ont le bonheur de vous recevoir à un moment inoubliable :</h6>
                                @switch($invitation->ceremonies->nom)
                                    @case('Coutumier')
                                        <div class="wedding-card-date mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-md-3 theme-color text-end">
                                                    {{ $invitation->ceremonies->day_of_week }}<br>
                                                    {{ $invitation->ceremonies->month }}</div>
                                                <div class="col-md-2 theme-color text-center date xs-mt-20">
                                                    {{ $invitation->ceremonies->day }}
                                                </div>
                                                <div class="col-md-3 theme-color text-start xs-mt-20">
                                                    {{ $invitation->ceremonies->time }}<br>
                                                    {{ $invitation->ceremonies->year }}</div>
                                            </div>
                                        </div>
                                        <div class="wedding-address">
                                            <h3 class="uppercase my-3" style="border: 1px solid #777; border-radius: 50px; padding: 10px;">Cérémonie du mariage {{ $invitation->ceremonies->nom }}
                                            </h3>
                                            <h5> {!! $invitation->ceremonies->adresse !!}</h5>
                                        </div>
                                    @break

                                    @case('Civile')
                                        <div class="wedding-card-date mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-md-3 theme-color text-end">
                                                    {{ $invitation->ceremonies->day_of_week }}<br>
                                                    {{ $invitation->ceremonies->month }}</div>
                                                <div class="col-md-2 theme-color text-center date xs-mt-20">
                                                    {{ $invitation->ceremonies->day }}
                                                </div>
                                                <div class="col-md-3 theme-color text-start xs-mt-20">
                                                    {{ $invitation->ceremonies->time }}<br>
                                                    {{ $invitation->ceremonies->year }}</div>
                                            </div>
                                        </div>
                                        <div class="wedding-address">
                                            <h3 class="uppercase my-3" style="border: 1px solid #777; border-radius: 50px; padding: 10px;">Cérémonie du mariage {{ $invitation->ceremonies->nom }}
                                            </h3>
                                            <h5> {!! $invitation->ceremonies->adresse !!}</h5>
                                        </div>
                                    @break

                                    @case('Réligieux')
                                        <div class="wedding-card-date mt-3">
                                            <div class="row justify-content-center">
                                                <div class="col-md-3 theme-color text-end">
                                                    {{ $invitation->ceremonies->day_of_week }}<br>
                                                    {{ $invitation->ceremonies->month }}</div>
                                                <div class="col-md-2 theme-color text-center date xs-mt-20">
                                                    {{ $invitation->ceremonies->day }}
                                                </div>
                                                <div class="col-md-3 theme-color text-start xs-mt-20">
                                                    {{ $invitation->ceremonies->time }}<br>
                                                    {{ $invitation->ceremonies->year }}</div>
                                            </div>
                                        </div>
                                        <div class="wedding-address">
                                            <h3 class="uppercase my-3" style="border: 1px solid #777; border-radius: 50px; padding: 10px;">Cérémonie du mariage {{ $invitation->ceremonies->nom }}
                                            </h3>
                                            <h5> {!! $invitation->ceremonies->adresse !!}</h5>
                                        </div>
                                    @break

                                    @default
                                @endswitch
                                @if (!empty($invitation->ceremonies->dressCode))
                                    @php

                                        $colors = collect($invitation->ceremonies->dressCode)
                                            ->map(fn($color) => is_array($color) ? $color['hex'] ?? null : $color)
                                            ->filter()
                                            ->values();
                                    @endphp


                                   <div class="container my-5">
                                        <div class="mx-auto p-4 shadow rounded-4 bg-white text-center"
                                            style="max-width: 480px; width: 100%;">

                                            <h4 class="mb-3" style="font-family: 'Georgia', cursive;">Dress code
                                            </h4>


                                            @switch($type)
                                                @case('couleur')
                                                    @if ($couleurs->isNotEmpty())
                                                        <p class="text-muted mb-4">
                                                            Merci de bien vouloir respecter la palette ci-dessous pour vos
                                                            tenues.
                                                        </p>
                                                        <div class="d-flex justify-content-center flex-wrap gap-4 mb-4">
                                                            @foreach ($couleurs as $color)
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <div class="rounded-circle color-circle"
                                                                        style="background-color: {{ $color['hex'] }};
                                                                        border: 2px solid {{ $color['hex'] }};
                                                                        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);">
                                                                    </div>
                                                                   <div class="text-muted small mt-1">{{  $color['name'] }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @case('tissu')
                                                    @if ($tissu)
                                                        <p class="text-muted mb-4">
                                                            Merci de bien vouloir respecter la palette ci-dessous pour vos
                                                            tenues.
                                                        </p>
                                                        <div class="d-flex justify-content-center flex-wrap gap-4 mb-4">
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#tissuModal">
                                                                <img src="{{ asset('storage/' . $tissu) }}"
                                                                    alt="Tissu dress code" class="dress-image">
                                                            </a>
                                                            <div class="click-to-zoom">Cliquez sur l'image pour agrandir</div>
                                                        </div>
                                                    @endif
                                                @break

                                                @case('tissuCouleur')
                                                    @if ($couleurs)
                                                        <p class="text-muted mb-4">
                                                            Merci de bien vouloir respecter la palette ci-dessous pour vos
                                                            tenues.
                                                        </p>
                                                        <div class="d-flex justify-content-center flex-wrap gap-4 mb-4">
                                                            @foreach ($couleurs as $color)
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <div class="rounded-circle color-circle"
                                                                        style="background-color: {{ $color['hex']}};
                                                                        border: 2px solid {{$color['hex']}};
                                                                        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);">
                                                                    </div>
                                                                    <div class="text-muted small mt-1">{{  $color['name'] }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if ($tissu)
                                                        <div class="d-flex justify-content-center flex-wrap gap-4 mb-4">
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#tissuModal">
                                                                <img src="{{ asset('storage/' . $tissu) }}"
                                                                    alt="Tissu dress code" class="dress-image">
                                                            </a>
                                                            <div class="click-to-zoom">Cliquez sur l'image pour agrandir</div>
                                                        </div>
                                                    @endif
                                                @break

                                                @default
                                                    <p class="text-muted">Aucun dress code précisé.</p>
                                            @endswitch
                                            <!-- Modal Bootstrap -->
                                            <div class="modal fade" id="tissuModal" tabindex="-1"
                                                aria-labelledby="tissuModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content bg-transparent border-0">
                                                        <div class="modal-body p-0">
                                                            <img src="{{ asset('storage/' . $tissu) }}"
                                                                alt="Tissu dress code"
                                                                class="img-fluid rounded shadow-lg">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>



                                @endif



                                @switch($invitation->status)
                                    @case('send')
                                        <button type="button" class="btn rsvp-btn mt-10 rounded-pill" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" style="border-radius: 50px !important">Confirmez votre présence</button>


                                            @break

                                    @case('refuse')
                                        <div class="wedding-address">
                                            <h3 class="uppercase my-3">
                                                <span class="theme-color text-center">
                                                    L'invité à refusé l'invitation
                                                </span>
                                            </h3>
                                        </div>
                                    @break

                                    @case('close')
                                        <div class="wedding-address">
                                            <h3 class="uppercase my-3">
                                                <span class="theme-color text-center">
                                                    L'invitation est cloturée
                                                </span>
                                            </h3>
                                        </div>
                                    @break

                                    @case('accept')
                                        <button type="button" class="btn rsvp-btn mt-10  rounded" data-bs-toggle="modal"
                                            data-bs-target="#map" style="border-radius: 50px !important">Voir le QRCODE</button>
                                    @break

                                    @default
                                @endswitch
                                @if ($invitation->status != 'refuse')
                                    <button type="button" class="btn map-btn theme-color mt-10" id="decline-btn"
                                        data-invitation-id="{{ $invitation->reference }}">Refuser
                                        l'invitation</button>
                                @endif

                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content p-5">
                                            <form id="accept-form">
                                                @csrf
                                                <input type="hidden" name="reference"
                                                    value="{{ $invitation->reference }}">

                                                <div class="contact-form clearfix">
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold d-block">Choisissez vos
                                                            boissons
                                                            préférées :</label>
                                                        @forelse ($boissons as $b)
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="boissons[]" value="{{ $b->nom }}"
                                                                    id="boisson-{{ $b->nom }}">
                                                                <label class="form-check-label"
                                                                    for="boisson-coca">{{ $b->nom . ' (' . $b->description . ')' }}</label>
                                                            </div>
                                                        @empty
                                                        @endforelse
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="cadeau" class="form-label fw-bold">Quel cadeau
                                                            promettez-vous aux mariés ?</label>
                                                        <input type="text" id="cadeau" name="cadeau"
                                                            class="form-control"
                                                            placeholder="Ex : Enveloppe, électroménager, etc.">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="message" class="form-label fw-bold">Quel est
                                                            votre
                                                            souhait pour les mariés ?</label>
                                                        <textarea id="message" name="message" rows="5" class="form-control"
                                                            placeholder="Un petit mot pour les mariés..."></textarea>
                                                    </div>
                                                    <div class="section-field submit-button">
                                                        <button id="submit" name="submit" type="submit"
                                                            value="Send" class="button rounded-pill"> Je confirme ma
                                                            présence</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="map" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content p-5">
                                            <div class="row justify-content-center">
                                                <div class="col-md-5 text-center">
                                                    <img src="{{ asset('assets/images/text.png') }}" width="250"
                                                        height="250" alt="" srcset="">

                                                </div>
                                                <div class="col-md-7 text-center">
                                                    <a href="{{ url('/invitations/' . $invitation->reference . '/download-qrcode') }}"
                                                        class="btn rsvp-btn mt-10 mb-10" download>
                                                        Télécharger mon QR Code
                                                    </a>
                                                    <h4 class="mt-10">
                                                        🎉 Merci pour votre confirmation !
                                                    </h4>
                                                    <p>
                                                        Merci de télécharger votre QR code. <br> Il est indispensable
                                                        pour
                                                        accéder à la cérémonie. <br> Conservez-le soigneusement.
                                                    </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wedding-card-footer text-center  floral-bottom animate-on-load mt-30">
                                <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/bottom-bg.png') }}">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
<!--=================================
 login-->

@include('parties.pied')

<script>
    $(document).ready(function() {
        // Injecter automatiquement le token CSRF dans les requêtes AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // Accepter l'invitation
        $(document).on('submit', '#accept-form', function(e) {
            e.preventDefault();
            let invitationId = $(this).data('invitation-id');
            // Récupérer les boissons cochées
            let boissons = [];
            $('input[name="boissons[]"]:checked').each(function() {
                boissons.push($(this).val());
            });
            let formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reference: $('input[name="reference"]').val(),
                boissons: boissons,
                message: $('#message').val(),
                cadeau: $('#cadeau').val()
            };
            $.ajax({
                url: '/invitations/accept',
                type: 'POST',
                data: formData,

                success: function(response) {
                    $('#exampleModal').modal('hide'); // ou remplace par l'ID de ton modal

                    Swal.fire({
                        icon: 'success',
                        title: 'Merci !',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false,
                        didOpen: () => {
                            $('.swal2-container').css('z-index',
                                2000); // ou plus si nécessaire
                        }
                    });

                    setTimeout(() => location.reload(), 3000);
                },
                error: function(xhr) {
                    $('#exampleModal').modal('hide'); // ou remplace par l'ID de ton modal

                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible d’enregistrer votre réponse.',
                        didOpen: () => {
                            $('.swal2-container').css('z-index',
                                2000); // ou plus si nécessaire
                        }
                    });
                }
            });
        });

        // Refuser l'invitation


        // Refuser l'invitation via AJAX
        $('#decline-btn').on('click', function() {
            let invitationId = $(this).data('invitation-id');
            Swal.fire({
                title: 'Es-tu sûr(e) ?',
                text: "Tu ne pourras plus revenir en arrière.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, refuser',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/invitations/' + invitationId + '/decline', function(response) {
                        if (response.reponse == true)
                            Swal.fire({
                                icon: 'success',
                                title: 'Invitation refusée',
                                text: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            });

                        setTimeout(() => location.reload(), 3000);
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de refuser l’invitation.'
                        });
                    });
                }
            });

        });

        // Télécharger le QR Code
        $('#download-qr-btn').on('click', function() {
            let invitationId = $(this).data('invitation-id');
            window.location.href = '/invitations/' + invitationId + '/download-qrcode';
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Injecter automatiquement le token CSRF dans les requêtes AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        // Refuser l'invitation via AJAX
        $('#close-btn').on('click', function() {
            let invitationId = $(this).data('invitation-id');
            $.post('/invitations/' + invitationId + '/close', function(response) {
                if (response.reponse == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Invitation',
                        text: response.message,
                        timer: 7000,
                        showConfirmButton: false
                    });

                    setTimeout(() => location.reload(), 5000);
                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Invitation',
                        text: response.message,
                        showConfirmButton: true
                    });
                }
                S
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de confirmer l’invitation.'
                });
            });

        });

        // Télécharger le QR Code
        $('#download-qr-btn').on('click', function() {
            let invitationId = $(this).data('invitation-id');
            window.location.href = '/invitations/' + invitationId + '/download-qrcode';
        });
    });
</script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
    document.addEventListener("DOMContentLoaded", function() {
        // Délai global avant que tout commence
        setTimeout(() => {
            // Activer les animations des éléments principaux
            document.querySelectorAll('.animate-on-load').forEach((el, i) => {
                setTimeout(() => el.classList.add('animate'), i * 200); // effet progressif
            });

            // Déclenche les pétales après 1.5s supplémentaires
            setTimeout(() => {
                for (let i = 0; i < 25; i++) {
                    let petal = document.createElement("div");
                    petal.classList.add("falling-petal");
                    petal.style.left = Math.random() * 100 + "vw";
                    petal.style.animationDelay = Math.random() * 5 + "s";
                    document.body.appendChild(petal);
                }
            }, 1500);

        }, 1000); // 1 seconde d'attente après le DOM chargé
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        setTimeout(() => {

            // Ensuite, on lance les animations
            setTimeout(() => {
                document.querySelectorAll('.animate-on-load').forEach((el, i) => {
                    setTimeout(() => el.classList.add('animate'), i * 400); // ralentir
                });

                // pétales tombants après
                setTimeout(() => {
                    for (let i = 0; i < 25; i++) {
                        let petal = document.createElement("div");
                        petal.classList.add("falling-petal");
                        petal.style.left = Math.random() * 100 + "vw";
                        petal.style.animationDelay = Math.random() * 6 + "s";
                        document.body.appendChild(petal);
                    }
                }, 1000);

                // Activer AOS après que tout soit visible
                AOS.init({
                    duration: 1500, // plus lent
                    once: true
                });

            }, 2000); // délai pour laisser le temps à l’intro de partir

        }, 2500); // durée affichage intro
    });
</script>

{{-- <script>
document.body.classList.add('block-scroll');

function openCurtain() {
  const left = document.querySelector('.left-curtain');
  const right = document.querySelector('.right-curtain');
  const curtain = document.getElementById('curtain');

  left.classList.add('open-left');
  right.classList.add('open-right');

  setTimeout(() => {
    curtain.style.display = "none";
    document.body.classList.remove('block-scroll');

    document.querySelectorAll('.animate-on-load').forEach((el, i) => {
      setTimeout(() => el.classList.add('animate'), i * 400);
    });

    setTimeout(() => {
      for (let i = 0; i < 25; i++) {
        let petal = document.createElement("div");
        petal.classList.add("falling-petal");
        petal.style.left = Math.random() * 100 + "vw";
        petal.style.animationDelay = Math.random() * 6 + "s";
        document.body.appendChild(petal);
      }
    }, 1000);

    AOS.init({ duration: 1500, once: true });

    document.getElementById('bg-music')?.play();

  }, 2500);
}
</script> --}}


<script>
    function openCurtain() {
        document.querySelector('.left-curtain').classList.add('open-left');
        document.querySelector('.right-curtain').classList.add('open-right');
        setTimeout(() => {
            document.getElementById('curtain').style.display = "none";
            document.body.classList.remove('block-scroll');

            document.querySelectorAll('.animate-on-load').forEach((el, i) => {
                setTimeout(() => el.classList.add('animate'), i * 400);
            });

            setTimeout(() => {
                for (let i = 0; i < 25; i++) {
                    let petal = document.createElement("div");
                    petal.classList.add("falling-petal");
                    petal.style.left = Math.random() * 100 + "vw";
                    petal.style.animationDelay = Math.random() * 6 + "s";
                    document.body.appendChild(petal);
                }
            }, 1000);

            AOS.init({
                duration: 1500,
                once: true
            });
            document.getElementById('bg-music')?.play();
        }, 2500);
    }
</script>
