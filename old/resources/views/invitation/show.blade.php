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
                        <h1 class="my-2">{{ $invitation->guests->type . ' ' . $invitation->guests->nom }}</h1>
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
                                    <h3 class="uppercase my-3">Cérémonie du mariage {{ $invitation->ceremonies->nom }}</h3>
                                    <h5> {!! $invitation->ceremonies->adresse !!}</h5>
                                </div>
                            @break

                            @case('Civile')
                                <div class="wedding-card-date mt-3">
                                    <div class="row justify-content-center">
                                        <div class="col-md-3 theme-color text-end">Saturday<br>April</div>
                                        <div class="col-md-2 theme-color text-center date xs-mt-20">15</div>
                                        <div class="col-md-3 theme-color text-start xs-mt-20">At 5 pm <br>2021</div>
                                    </div>
                                </div>
                                <div class="wedding-address">
                                    <h3 class="uppercase my-3">Wedding Garden Plot</h3>
                                    <h5> 17504 Carlton Cuevas Rd, Gulfport, MS, 39503</h5>
                                </div>
                            @break

                            @case('Réligieux')
                                <div class="wedding-card-date mt-3">
                                    <div class="row justify-content-center">
                                        <div class="col-md-3 theme-color text-end">Saturday<br>April</div>
                                        <div class="col-md-2 theme-color text-center date xs-mt-20">15</div>
                                        <div class="col-md-3 theme-color text-start xs-mt-20">At 5 pm <br>2021</div>
                                    </div>
                                </div>
                                <div class="wedding-address">
                                    <h3 class="uppercase my-3">Wedding Garden Plot</h3>
                                    <h5> 17504 Carlton Cuevas Rd, Gulfport, MS, 39503</h5>
                                </div>
                            @break

                            @default
                        @endswitch

                        @switch($invitation->status)
                            @case('send')
                                <button type="button" class="btn rsvp-btn mt-10" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">Confirmez votre présence</button>
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
                                <button type="button" class="btn rsvp-btn mt-10" data-bs-toggle="modal"
                                    data-bs-target="#map">Voir le QRCODE</button>
                            @break

                            @default
                        @endswitch
                        @if ($invitation->status != 'refuse')
                            <button type="button" class="btn map-btn theme-color mt-10" id="decline-btn"
                                data-invitation-id="{{ $invitation->reference }}">Refuser l'invitation</button>
                        @endif

                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content p-5">
                                    <form id="accept-form">
                                        @csrf
                                        <input type="hidden" name="reference" value="{{ $invitation->reference }}">

                                        <div class="contact-form clearfix">
                                            <div class="mb-4">
                                                <label class="form-label fw-bold d-block">Choisissez vos boissons
                                                    préférées :</label>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Coca-Cola" id="boisson-coca">
                                                    <label class="form-check-label" for="boisson-coca">Coca-Cola</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Fanta" id="boisson-fanta">
                                                    <label class="form-check-label" for="boisson-fanta">Fanta</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Jus de Bissap" id="boisson-bissap">
                                                    <label class="form-check-label" for="boisson-bissap">Jus de
                                                        Bissap</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Eau" id="boisson-eau">
                                                    <label class="form-check-label" for="boisson-eau">Eau</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Vin rouge" id="boisson-vin">
                                                    <label class="form-check-label" for="boisson-vin">Vin
                                                        rouge</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Coca-Cola" id="boisson-coca">
                                                    <label class="form-check-label"
                                                        for="boisson-coca">Coca-Cola</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Fanta" id="boisson-fanta">
                                                    <label class="form-check-label" for="boisson-fanta">Fanta</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Jus de Bissap" id="boisson-bissap">
                                                    <label class="form-check-label" for="boisson-bissap">Jus de
                                                        Bissap</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Eau" id="boisson-eau">
                                                    <label class="form-check-label" for="boisson-eau">Eau</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="boissons[]"
                                                        value="Vin rouge" id="boisson-vin">
                                                    <label class="form-check-label" for="boisson-vin">Vin
                                                        rouge</label>
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <label for="cadeau" class="form-label fw-bold">Quel cadeau
                                                    promettez-vous aux mariés ?</label>
                                                <input type="text" id="cadeau" name="cadeau"
                                                    class="form-control"
                                                    placeholder="Ex : Enveloppe, électroménager, etc.">
                                            </div>

                                            <div class="mb-4">
                                                <label for="message" class="form-label fw-bold">Quel est votre
                                                    souhait pour les mariés ?</label>
                                                <textarea id="message" name="message" rows="5" class="form-control"
                                                    placeholder="Un petit mot pour les mariés..."></textarea>
                                            </div>
                                            <div class="section-field submit-button">
                                                <button id="submit" name="submit" type="submit" value="Send"
                                                    class="button"> Je confirme ma présence</button>
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
                                                Merci de télécharger votre QR code. <br> Il est indispensable pour
                                                accéder à la cérémonie. <br> Conservez-le soigneusement.
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
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
