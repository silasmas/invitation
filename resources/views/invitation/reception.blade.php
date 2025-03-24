@include('parties.entete')

<!--=================================
 login-->

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
                                    <button type="button" class="btn rsvp-btn mt-10" id="close-btn"
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
