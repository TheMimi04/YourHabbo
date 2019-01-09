<?php

require 'global.php';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
       (adsbygoogle = window.adsbygoogle || []).push({
       google_ad_client: "ca-pub-4963033265711496",
       enable_page_level_ads: true
     });
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Plans">
    <meta name="keywords" content="yourhabbo plans">

    <title>Plans - <?= $Controller->Settings('name'); ?></title>

    <link href="assets/css/core.min.css" rel="stylesheet">
    <link href="assets/css/thesaas.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <link rel="icon" href="assets/img/favicon.ico">
  </head>

  <body>
	<noscript><META http-equiv="refresh" content="0; URL=/nojs/"></noscript>
    <nav class="topbar topbar-inverse topbar-expand-md topbar-sticky">
      <div class="container">
        
        <div class="topbar-left">
          <button class="topbar-toggler">&#9776;</button>
          <a class="topbar-brand" href="index.html">
            <img class="logo-default" src="assets/img/logo.png" alt="logo">
            <img class="logo-inverse" src="assets/img/logo-light.png" alt="logo">
          </a>
        </div>


        <div class="topbar-right">
          <ul class="topbar-nav nav">
            <li class="nav-item"><a class="nav-link" href="/">Accueil</a></li>
			<li class="nav-item"><a class="nav-link active" href="plans">Plans</a></li>
			<?php if(isset($_SESSION['account'])) { ?>
			<li class="nav-item"><a class="nav-link" href="/client/">Bonjour <?= $_SESSION['account']['username']; ?></a></li>
			<?php } else { ?>
			<li class="nav-item"><a class="nav-link" href="/client/login">Connexion</a></li>
		    <li class="nav-item"><a class="nav-link" href="/client/register">Inscription</a></li>
			  <?php } ?>
          </ul>
        </div>

      </div>
    </nav>
	  
    <header class="header header-inverse" style="background-image: linear-gradient(120deg, #204A77 0%, #204A77 100%);">
      <div class="container text-center">

        <div class="row">
          <div class="col-12 col-lg-8 offset-lg-2">

            <h1>Choix du plan</h1>
            <br>
            <p class="fs-20 opacity-70">Voici les prix des rétros-habbo</p>

          </div>
        </div>

      </div>
    </header>

    <main class="main-content">

      <section class="section bg-gray">
        <div class="container">

          <div class="row gap-y text-center">
            
            <?= $StingCMS->PlansRacine(); ?>

          </div>


        </div>
      </section>

    </main>

    <footer class="site-footer">
      <div class="container">
        <div class="row gap-y align-items-center">
          <div class="col-12 col-lg-3">
            <p class="text-center text-lg-left">
              <a href="index.html"><img src="assets/img/logo.png" alt="logo"></a>
            </p>
          </div>

          <div class="col-12 col-lg-6">
            <ul class="nav nav-inline nav-primary nav-hero">
              <li class="nav-item">
                <a class="nav-link" href="/client/login">Connexion</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/client/register">Inscription</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/client/">Espace client</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
	  
    <script src="assets/js/core.min.js"></script>
    <script src="assets/js/thesaas.min.js"></script>
    <script src="assets/js/script.js"></script>
	<?= $StingCMS->SuiviGoogle(); ?>

  </body>
</html>