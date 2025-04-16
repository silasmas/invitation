@include('parties.entete')

<!--=================================
 login-->

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
                        <h5>Accès refusée</h5>
                        <h6>L'invité avait refusé l'invitation de {{ $invitation->ceremonies->event->femme . ' & ' . $invitation->ceremonies->event->homme }}</h6>
                    </div>
                </div>
            </div>

        @else
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
                            <h1 class="my-2">{{ $invitation->guests->type . ' ' . $invitation->guests->nom }}</h1>
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
                                                L'invitation est cloturée
                                            </span>
                                        </h3>
                                    </div>
                                @break

                                @case('refuse')
                                    <div class="wedding-address">
                                        <h3 class="uppercase my-3">Etat :
                                            <span class="theme-color text-center">
                                                L'invité à refusé l'invitation
                                            </span>
                                        </h3>
                                    </div>
                                @break

                                @case('close')
                                    <div class="wedding-address">
                                        <h3 class="uppercase my-3">Etat :
                                            <span class="theme-color text-center">
                                                L'invitation est cloturée
                                            </span>
                                        </h3>
                                    </div>
                                @break

                                @default
                                    <button type="button" class="btn rsvp-btn mt-10 rounded-pill" id="close-btn"
                                        data-invitation-id="{{ $invitation->reference }}">Validé l'invitation </button>
                            @endswitch
                        @else
                        @endif


                    </div>
                    <div class="wedding-card-footer text-center">
                        <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/bottom-bg.png') }}">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
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
        $(document).on('click', '#close-btn', function(e) {
            e.preventDefault();
            $(this).text('Traitement en cours...').attr('disabled', true);
            let invitationId = $(this).data('invitation-id');

            let formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reference: invitationId
            };
            $.ajax({
                url: '/invitations/confirmation/',
                type: 'POST',
                data: formData,

                success: function(response) {
                    $('#close-btn').text("Validé l'invitation").attr('disabled', false);
                    $('#exampleModal').modal('hide'); // ou remplace par l'ID de ton modal
                    if (response.reponse) {
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
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Merci !',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false,
                            didOpen: () => {
                                $('.swal2-container').css('z-index',
                                    2000); // ou plus si nécessaire
                            }
                        });
                    }


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
