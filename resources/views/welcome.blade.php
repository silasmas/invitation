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
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/site/demo-one-page/wedding-card/css/wedding-card.css') }} " />

    <!-- Responsive -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/site/css/responsive.css') }} " />

    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes floatUpDown {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .floral-top.animate,
        .floral-bottom.animate {
            animation: fadeInDown 2s ease-out forwards, floatUpDown 8s ease-in-out infinite;
        }

        .animate-on-load.animate {
            opacity: 1;
        }

        .animate-on-load {
            opacity: 0;
            transition: opacity 1s ease, transform 1s ease;
        }

        .floral-top.animate-on-load {
            animation: fadeInDown 1s ease-out forwards;
        }

        .floral-bottom.animate-on-load {
            animation: zoomIn 2.5s ease-out forwards;
        }

        .couple-photo.animate-on-load {
            animation: zoomIn 1.2s ease-out forwards;
        }

        .falling-petal {
            position: fixed;
            top: -50px;
            width: 20px;
            height: 20px;
            background-image: url('{{ asset('assets/images/petal.png') }}');
            /* mets une petite image de pétale ici */
            background-size: cover;
            opacity: 0.8;
            pointer-events: none;
            z-index: 10;
            animation: fall 12s linear infinite;
        }

        @keyframes fall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .page-wrapper {
            opacity: 0;
            transform: scale(0.98);
        }

        .page-wrapper.animate {
            opacity: 1;
            transform: scale(1);
            transition: opacity 1s ease, transform 1s ease;
        }

        .intro-screen {
            position: fixed;
            z-index: 9999;
            background: white;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Tangerine', cursive;
            font-size: 60px;
            color: #cc3366;
            opacity: 1;
            transition: opacity 2s ease, transform 2s ease;
        }

        .intro-screen.fade-out {
            opacity: 0;
            transform: scale(1.05);
            pointer-events: none;
        }

        .curtain-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            z-index: 9999;
            display: flex;
            justify-content: space-between;
            pointer-events: none;
        }

        .curtain {
            background: linear-gradient(to right, #b90e5b, #e54a86);
            /* couleur de rideau */
            width: 50vw;
            height: 100vh;
            transition: transform 2.5s ease-in-out;
        }

        .left-curtain {
            transform: translateX(0);
            border-right: 2px solid #fff;
        }

        .right-curtain {
            transform: translateX(0);
            border-left: 2px solid #fff;
        }

        /* rideau ouvert */
        .curtain.open-left {
            transform: translateX(-100%);
        }

        .curtain.open-right {
            transform: translateX(100%);
        }
        body.block-scroll {
  overflow: hidden;
}

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
  background: white;
  pointer-events: auto;
}

/* rideaux */
.curtain {
  background: linear-gradient(to right, #b90e5b, #e54a86);
  width: 50vw;
  height: 100vh;
  transition: transform 2.5s ease-in-out;
}

.left-curtain {
  transform: translateX(0);
  border-right: 2px solid #fff;
}

.right-curtain {
  transform: translateX(0);
  border-left: 2px solid #fff;
}

.curtain.open-left {
  transform: translateX(-100%);
}

.curtain.open-right {
  transform: translateX(100%);
}

/* contenu du rideau */
.curtain-content {
  position: absolute;
  z-index: 10000;
  width: 100%;
  text-align: center;
  color: white;
  font-family: 'Tangerine', cursive;
  text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
}

.curtain-content h1 {
  font-size: 48px;
  margin: 0;
}

.curtain-content h2 {
  font-size: 72px;
  margin-bottom: 20px;
}

.enter-btn {
  padding: 12px 24px;
  font-size: 20px;
  background: white;
  color: #b90e5b;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.enter-btn:hover {
  background: #f8d6e0;
}

    </style>
</head>

<body>
    {{-- <div id="intro-screen" class="intro-screen">
        <div class="intro-text">Bienvenue à notre mariage</div>
      </div> --}}
    
    <div class="curtain-wrapper" id="curtain">
        <div class="curtain left-curtain"></div>
        <div class="curtain-content">
            <h1>Bienvenue au mariage de</h1>
            <h2>Nathan & Emily</h2>
            <button class="enter-btn" onclick="openCurtain()">Ouvrir l’invitation</button>
        </div>
        <div class="curtain right-curtain"></div>
    </div>

    <!--=================================
 preloader -->

    <div id="pre-loader">
        <img src="{{ asset('assets/site/images/pre-loader/loader-09.svg') }}" alt="">
    </div>

    <!--=================================
 preloader -->

    <!--=================================
 login-->
    <div class="page-wrapper animate-on-load">
        <section class="wedding-card page-section-ptb">
            <div class="container">
                <div class="row justify-content-center no-gutter">
                    <div class="col-lg-8 align-self-center">
                        <div class="wedding-invitation white-bg p-5">
                            <div class="wedding-card-head text-center floral-top animate-on-load" data-aos="fade-down"
                                data-aos-delay="200">
                                <img src="{{ asset('assets/site/demo-one-page/wedding-card/images/top-bg.png') }}">
                            </div>
                            <div class="wedding-card-body text-center position-relative">
                                <h5>Wedding Invitation</h5>
                                <div class="bg-image couple-photo animate-on-load"
                                    data-aos="zoom-in"data-aos-delay="200">
                                    <img src="{{ asset('assets/site/images/couple.png') }}" alt=""
                                        class="img-fluid">
                                    <div class="mask"></div>
                                </div>
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
                                <button type="button" class="btn rsvp-btn mt-10" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">Confirmez votre présence</button>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content p-5">
                                            <h2>Submit Your RSVP</h2>
                                            <p>Kindly respond by April 5 2021</p>
                                            <div id="formmessage">Success/Error Message Goes Here</div>
                                            <form id="contactform" role="form" method="post"
                                                action="php/contact-form.php">
                                                <div class="contact-form clearfix">
                                                    <div class="section-field">
                                                        <input id="name" type="text" placeholder="Name*"
                                                            class="form-control" name="name">
                                                    </div>
                                                    <div class="section-field">
                                                        <input type="email" placeholder="Email*"
                                                            class="form-control" name="email">
                                                    </div>
                                                    <div class="section-field">
                                                        <input type="text" placeholder="Phone*"
                                                            class="form-control" name="phone">
                                                    </div>
                                                    <div class="section-field textarea">
                                                        <textarea class="form-control input-message" placeholder="Number of guest attending:" rows="7" name="message"></textarea>
                                                    </div>
                                                    <div class="section-field submit-button">
                                                        <input type="hidden" name="action" value="sendEmail" />
                                                        <button id="submit" name="submit" type="submit"
                                                            value="Send" class="button"> Send your message </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div id="ajaxloader" style="display:none"><img
                                                    class="mx-auto mt-30 mb-30 d-block"
                                                    src="{{ asset('assets/site/images/pre-loader/loader-02.svg') }}"
                                                    alt=""></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Button trigger modal -->
                                <button type="button" class="btn map-btn theme-color mt-10" data-bs-toggle="modal"
                                    data-bs-target="#map">Venue Map</button>
                                <!-- Modal -->
                                <div class="modal fade" id="map" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content p-5">
                                            <iframe
                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8351288872545!2d144.9556518!3d-37.8173306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2sin!4v1443621171568"
                                                style="border:0; width: 100%; height: 500px;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wedding-card-footer text-center floral-bottom animate-on-load"
                                data-aos="fade-up" data-aos-delay="200">
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
    <script>
        var plugin_path = '../assets/site/js/';
    </script>

    <!-- custom -->
    <script src="{{ asset('assets/site/js/custom.js') }} "></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>



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
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const leftCurtain = document.querySelector('.left-curtain');
            const rightCurtain = document.querySelector('.right-curtain');

            // Laisse le rideau fermé 2s, puis ouvre-le
            setTimeout(() => {
                leftCurtain.classList.add('open-left');
                rightCurtain.classList.add('open-right');

                // Ensuite, lancer les animations de la page
                setTimeout(() => {
                    // Lancer les animations visibles
                    document.querySelectorAll('.animate-on-load').forEach((el, i) => {
                        setTimeout(() => el.classList.add('animate'), i * 400);
                    });

                    // Pétales
                    setTimeout(() => {
                        for (let i = 0; i < 25; i++) {
                            let petal = document.createElement("div");
                            petal.classList.add("falling-petal");
                            petal.style.left = Math.random() * 100 + "vw";
                            petal.style.animationDelay = Math.random() * 6 + "s";
                            document.body.appendChild(petal);
                        }
                    }, 1000);

                    // Lancer AOS (scroll)
                    AOS.init({
                        duration: 1500,
                        once: true
                    });

                    // Musique si activée
                    document.getElementById('bg-music')?.play();

                }, 2500); // après que le rideau soit totalement ouvert

            }, 2000); // temps de pause avant ouverture du rideau
        });
    </script> --}}

    <script>
    document.addEventListener("DOMContentLoaded", function () {
      const intro = document.getElementById('intro-screen');

      setTimeout(() => {
        intro.classList.add('fade-out');

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
            duration: 1500,  // plus lent
            once: true
          });

        }, 2000); // délai pour laisser le temps à l’intro de partir

      }, 2500); // durée affichage intro
    });
  </script>

  <script>
    document.body.classList.add('block-scroll'); // empêche scroll avant ouverture
  
    function openCurtain() {
      const left = document.querySelector('.left-curtain');
      const right = document.querySelector('.right-curtain');
      const curtain = document.getElementById('curtain');
  
      left.classList.add('open-left');
      right.classList.add('open-right');
  
      // Après ouverture (2.5s), on cache les rideaux et débloque la page
      setTimeout(() => {
        curtain.style.display = "none";
        document.body.classList.remove('block-scroll');
  
        // Animations
        document.querySelectorAll('.animate-on-load').forEach((el, i) => {
          setTimeout(() => el.classList.add('animate'), i * 400);
        });
  
        // Pétales
        setTimeout(() => {
          for (let i = 0; i < 25; i++) {
            let petal = document.createElement("div");
            petal.classList.add("falling-petal");
            petal.style.left = Math.random() * 100 + "vw";
            petal.style.animationDelay = Math.random() * 6 + "s";
            document.body.appendChild(petal);
          }
        }, 1000);
  
        // Scroll animations
        AOS.init({
          duration: 1500,
          once: true
        });
  
        // Musique
        document.getElementById('bg-music')?.play();
  
      }, 2500);
    }
  </script>
  

</body>

</html>
