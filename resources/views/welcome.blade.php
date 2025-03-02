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
<link rel="shortcut icon" href="images/favicon.ico" />

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
          <div class="wedding-card-body text-center">
            <h5>Wedding Invitation</h5>
            <h1 class="my-2">Nathan & Emily</h1>
            <h6>Invite you to celebarte their love and union</h6>
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

            <!-- Button trigger modal -->
            <button type="button" class="btn rsvp-btn mt-10" data-bs-toggle="modal" data-bs-target="#exampleModal">RSVP</button>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content p-5">
                  <h2>Submit Your RSVP</h2>
                  <p>Kindly respond by April 5 2021</p>
                  <div id="formmessage">Success/Error Message Goes Here</div>
                    <form id="contactform" role="form" method="post" action="php/contact-form.php">
                      <div class="contact-form clearfix">
                        <div class="section-field">
                          <input id="name" type="text" placeholder="Name*" class="form-control"  name="name">
                        </div>
                        <div class="section-field">
                          <input type="email" placeholder="Email*" class="form-control" name="email">
                        </div>
                          <div class="section-field">
                          <input type="text" placeholder="Phone*" class="form-control" name="phone">
                        </div>
                        <div class="section-field textarea">
                          <textarea class="form-control input-message" placeholder="Number of guest attending:" rows="7" name="message"></textarea>
                        </div>
                        <div class="section-field submit-button">
                          <input type="hidden" name="action" value="sendEmail"/>
                          <button id="submit" name="submit" type="submit" value="Send" class="button"> Send your message </button>
                        </div>
                      </div>
                    </form>
                    <div id="ajaxloader" style="display:none"><img class="mx-auto mt-30 mb-30 d-block" src="{{ asset('assets/site/images/pre-loader/loader-02.svg') }}" alt=""></div>
                </div>
              </div>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn map-btn theme-color mt-10" data-bs-toggle="modal" data-bs-target="#map">Venue Map</button>
            <!-- Modal -->
            <div class="modal fade" id="map" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content p-5">
                  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8351288872545!2d144.9556518!3d-37.8173306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2sin!4v1443621171568" style="border:0; width: 100%; height: 500px;"></iframe>
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

</div>


<!--=================================
 jquery -->

<!-- jquery -->
<script src="{{ asset('assets/site/js/jquery-3.6.0.min.js') }} "></script>

<!-- plugins-jquery -->
<script src="{{ asset('assets/site/js/plugins-jquery.js') }} "></script>

<!-- plugin_path -->
<script>var plugin_path = '../site/assets/js/';</script>

<!-- custom -->
<script src="{{ asset('assets/site/js/custom.js') }} "></script>



</body>
</html>
