<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invitation au mariage de Nathan & Emily</title>

  <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

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
      background: url('assets/images/voilage.png') center center / cover no-repeat;
    }

    .curtain {
      width: 50vw;
      height: 100vh;
      background: url('assets/images/rideau-texture.png') repeat-y;
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
      background-image: url('assets/images/petal.png');
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
<body class="block-scroll">

<div class="curtain-wrapper" id="curtain">
  <div class="curtain left-curtain"></div>
  <div class="curtain-content">
    <h1>Bienvenue au mariage de</h1>
    <h2>Nathan & Emily</h2>
    <button class="enter-btn" onclick="openCurtain()">Entrer dans l’invitation</button>
  </div>
  <div class="curtain right-curtain"></div>
</div>

<!-- Musique de fond -->
<audio id="bg-music" src="assets/site/audio/wedding-music.mp3" loop></audio>

<!-- Exemple de contenu principal -->
<section class="animate-on-load" style="text-align:center; padding:100px 20px;">
  <h2 data-aos="fade-up">Cérémonie le 15 avril à 17h</h2>
  <p data-aos="fade-up" data-aos-delay="300">Lieu : Wedding Garden Plot, 17504 Carlton Cuevas Rd, Gulfport</p>
</section>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
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

      AOS.init({ duration: 1500, once: true });
      document.getElementById('bg-music')?.play();
    }, 2500);
  }
</script>

</body>
</html>
