<?php

require 'global.php';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= $Controller->Settings('description'); ?>">

    <title><?= $Controller->Settings('name'); ?> - <?= $Controller->Settings('description'); ?></title>

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
            <li class="nav-item"><a class="nav-link active" href="/">Accueil</a></li>
			<li class="nav-item"><a class="nav-link" href="plans">Plans</a></li>
			<?php if(isset($_SESSION['account'])) { ?>
			<li class="nav-item"><a class="nav-link" href="client/">Bonjour <?= $_SESSION['account']['username']; ?></a></li>
			<?php } else { ?>
			<li class="nav-item"><a class="nav-link" href="client/login">Connexion</a></li>
		    <li class="nav-item"><a class="nav-link" href="client/register">Inscription</a></li>
			  <?php } ?>
          </ul>
        </div>

      </div>
    </nav>

    <header class="header fadeout header-inverse pb-0 h-fullscreen" style="background-image: linear-gradient(to bottom, #243949 0%, #517fa4 100%);">
      <canvas class="constellation"></canvas>

      <div class="container">
        <div class="row h-full">
          <div class="col-12 text-center align-self-center">
            <h1 class="fs-50 fw-600 lh-15 hidden-sm-down"><?= $Controller->Settings('name'); ?> est <span class="text-primary" data-type="Innovant, Puissant, Numéro 1, Fiable, Indispensable"></span></h1>
            <h1 class="fs-35 fw-600 lh-15 hidden-md-up"><?= $Controller->Settings('name'); ?> est <span class="text-primary" data-type="Innovant, Puissant, Numéro 1, Fiable, Indispensable"></span></h1>
            <br>
            <p class="fs-20 hidden-sm-down">Avec <strong><?= $Controller->Settings('name'); ?></strong> vous allez en avoir pleins les yeux ! Commandez dès maintenant votre rétro-habbo !</p>
            <p class="fs-16 hidden-md-up">Avec <strong><?= $Controller->Settings('name'); ?></strong> vous allez en avoir pleins les yeux ! Commandez dès maintenant votre rétro-habbo !</p>
            <br>
            <hr class="w-60 hidden-sm-down">
            <br>
            <?php if(isset($_SESSION['account'])) { ?>
			<a class="btn btn-xl btn-round btn-primary fs-20 fw-500 w-350 shadow-3 hidden-sm-down" href="/client/">Espace client</a>
            <a class="btn btn-lg btn-round btn-primary w-250 shadow-3 hidden-md-up" href="/client/">Espace client</a>
            <br>
            <p class="mt-2 mt-md-4"><a href="/client/?logout=1"><small>ou déconnectez-vous</small></a></p>
			<?php } else { ?>
			<a class="btn btn-xl btn-round btn-primary fs-20 fw-500 w-350 shadow-3 hidden-sm-down" href="/client/register">Inscription</a>
            <a class="btn btn-lg btn-round btn-primary w-250 shadow-3 hidden-md-up" href="/client/register">Inscription</a>
            <br>
            <p class="mt-2 mt-md-4"><a href="/client/login"><small>ou connectez-vous</small></a></p>
			<?php } ?>
          </div>

          <div class="col-12 align-self-end text-center pb-70">
            <a class="scroll-down-2 scroll-down-inverse" href="#" data-scrollto="section-demo"><span></span></a>
          </div>
        </div>
      </div>
    </header>

    <main class="main-content">

      <section class="section bg-grey1 pb-0 overflow-hidden hidden-sm-down">
        <div class="container">
          <header class="section-header">
            <h2>Nos compteurs</h2>
            <hr>
            <p class="lead">Voici nos compteurs. Ceux-ci sont à jour automatiquement.</p>
          </header>

          <div class="flexbox flex-grow-all text-center mx-auto w-600">
            
            <div>
              <span class="text-info fs-50"><?= $StingCMS->ADMNbClient(); ?></span><br>
              <p>Client<?= $Controller->isMany($StingCMS->ADMNbClient()); ?></p>
            </div>

            <div>
              <span class="text-info fs-50"><?= $StingCMS->ADMNbRetroActif(); ?></span><br>
              <p>Rétro<?= $Controller->isMany($StingCMS->ADMNbRetroActif()); ?> actif<?= $Controller->isMany($StingCMS->ADMNbRetroActif()); ?></p>
            </div>
            
          </div>

          <br><br>

        </div>
      </section>

      <section class="section">
        <div class="container">
          <header class="section-header">
            <h2>Nos caractèristiques</h2>
            <hr>
            <p class="lead">Vous allez en apprendre un peu plus sur nous.</p>
          </header>



          <div class="row gap-y">
			  
            <div class="col-12 col-md-6 col-xl-4 text-center">
              <i class="fa fa-tv fs-25 mb-3 text-secondary"></i>
              <h6 class="fs-18 mb-3">Responsive</h6>
              <p class="fs-14">Notre site est compatible sur ordinateur, mobile et tablette.</p>
            </div>


            <div class="col-12 col-md-6 col-xl-4 text-center">
              <i class="fa fa-wrench fs-25 mb-3 text-secondary"></i>
              <h6 class="fs-18 mb-3">Rétro personnalisé</h6>
              <p class="fs-14">Vous avez la possibilité de modifier votre rétro à 100%.</p>
            </div>


            <div class="col-12 col-md-6 col-xl-4 text-center">
              <i class="fa fa-euro fs-25 mb-3 text-secondary"></i>
              <h6 class="fs-18 mb-3">Prix faible</h6>
              <p class="fs-14">Nos prix sont faible pour que même les petits moyens puissent commander.</p>
            </div>


            <div class="col-12 col-md-6 col-xl-4 text-center">
              <i class="fa fa-code fs-25 mb-3 text-secondary"></i>
              <h6 class="fs-18 mb-3">Code propre</h6>
              <p class="fs-14">Notre site est développé et amélioré chaque jour.</p>
            </div>


            <div class="col-12 col-md-6 col-xl-4 text-center">
              <i class="fa fa-file-text-o fs-25 mb-3 text-secondary"></i>
              <h6 class="fs-18 mb-3">Forum</h6>
              <p class="fs-14">Un forum est à votre dispostion pour vous aider.</p>
            </div>


            <div class="col-12 col-md-6 col-xl-4 text-center">
              <i class="fa fa-download fs-25 mb-3 text-secondary"></i>
              <h6 class="fs-18 mb-3">Mise à jour</h6>
              <p class="fs-14">On s'engage à vous offrir un maximum de mise à jour.</p>
            </div>


          </div>
        </div>
      </section>
		
		<section class="section bg-gray">
        <div class="container">
          <header class="section-header">
            <small>YourHabbo</small>
            <h2>Plus d'information</h2>
            <hr>
            <p class="lead">Vous avez envie d'en savoir encore plus sur YourHabbo ? Vous êtes au bon endroit!</p>
          </header>


          <div class="row gap-y align-items-center">
            <div class="col-12 col-md-5">
              <img class="rounded shadow-2 aos-init" src="assets/img/yh1.png" alt="yh1" data-aos="zoom-in">
            </div>

            <div class="col-12 col-md-7">
              <h4>Mode de paiement</h4>
              <p>Grâce à notre solution de paiement DediPass, vous avez un large choix pour recharger votre compte. Vous pouvez choisir le montant souhaitez parmi un large choix. Mais vous pouvez aussi recharger votre compte grâce à des codes KDO distribué par l'équipe.</p>
            </div>
          </div>


          <hr>


          <div class="row gap-y align-items-center">
            <div class="col-12 col-md-7">
              <h4>Abonnements VIP</h4>
              <p>Vous avez envie d'une nouvelle couleur ? De nouvelles options ? Le VIP est fait pour vous ! En vous abonnent à celui-ci, vous débloquerait de nouvelle page comme le loto qui va vous permettre de gagner des points gratuitement, ou bien le forum, pour vous aider.</p>
            </div>

            <div class="col-12 col-md-5">
              <img class="rounded shadow-2 aos-init" src="assets/img/yh2.png" alt="yh2" data-aos="zoom-in">
            </div>
          </div>


          <hr>


          <div class="row gap-y align-items-center">
            <div class="col-12 col-md-5">
              <img class="rounded shadow-2 aos-init" src="assets/img/yh3.png" alt="yh3" data-aos="zoom-in">
            </div>

            <div class="col-12 col-md-7">
              <h4>Mises à jour</h4>
              <p>Vous voulez savoir l'actualité sur nos serveurs ? Une page contenant les améliorations, incident ainsi que les maintenances est disponible pour que vous soyez informé directement.</p>
            </div>
          </div>



        </div>
      </section>
		
		<section class="section bb-1">
        <div class="container">
          <header class="section-header">
            <h2>Les avis des clients</h2>
            <hr>
            <p class="lead">Voici les 3 derniers avis de nos clients.</p>
          </header>


          <div class="row gap-y">

			  <?php
			  
			  $db = Connect::getDB();
			  
			  $req = $db->query('SELECT * FROM sting_view ORDER BY date DESC LIMIT 3');
			  while($r = $req->fetch()) {
				  $inf = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				  $inf->execute(array($r['clef_user']));
				  $f = $inf->fetch();
				  echo '<div class="col-12 col-md-4">
              <div class="card card-shadowed">
                <div class="card-block px-30">
                  <div class="rating mb-12">
                    '.$Controller->Etoile($r['étoile']).'
                  </div>

                  <p class="text-quoted mb-0">'.$r['comment'].'</p>
                  <div class="media align-items-center pb-0">
                    <img class="avatar avatar-xs" src="'.$f['avatar'].'" alt="">
                    <div class="media-body lh-1">
                      <h6 class="mb-0">'.$f['username'].'</h6>
                      <small>@'.$f['username'].'</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
			  }
			  
			  ?>
			  
          </div>



        </div>
      </section>
		
	<section class="section">
        <div class="container">
          <header class="section-header">
            <small>YourHabbo</small>
            <h2>L'équipe</h2>
            <hr>
            <p class="lead">Voici l'équipe de YourHabbo</p>
          </header>

          <div class="row gap-y">

			  <?php
			  
			  $db = Connect::getDB();
			  
			  $req = $db->prepare('SELECT * FROM sting_users WHERE rank >= ? ORDER BY rank DESC');
			  $req->execute(array('5'));
			  
			  while($r = $req->fetch()) {
				  echo '<div class="col-12 col-md-4 team-2">
              <a href="#">
                <img src="'.$r['avatar'].'" style="height:80px; width: 80px"  alt="...">
              </a>
              <h5 class="'.$Controller->RankColor($r['rank']).'">'.$r['username'].'</h5>
              <small>'.$Controller->Rank($r['rank']).'</small>
            </div>';
			  }
			  
			  ?>
			  
          </div>

        </div>
      </section>
		
		<section class="section">
        <div class="container">
          <header class="section-header">
            <h2>Partenaires</h2>
            <hr>
            <p class="lead">Voici la liste de nos partenaires.</p>
          </header>

          <div class="row gap-y partner">
            Aucun
          </div>

        </div>
      </section>

      <section class="section section-inverse py-40" style="background-color: #8ea6e6">
        <div class="container">
          <div class="row gap-y align-items-center">
            <div class="col-12 col-md-9">
              <h4 class="fw-300 mb-0">Déjà satisfait ? Alors inscris-toi dès maintenant !</h4>
            </div>

            <div class="col-12 col-md-3">
              <a class="btn btn-lg btn-round btn-white" href="/client/register">Inscription</a>
            </div>
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
    <script>
    fbq('track', 'ViewContent', {
    content_ids: ['19778599'],
    content_type: 'regular',
    value: 0.00,
    currency: 'USD'
    });
    </script>

    <script type="text/javascript">
    $( '.header .btn' ).click(function() {
    fbq('track', 'AddToCart', {
    content_ids: ['19778599'],
    content_type: 'regular',
    value: 0.00,
    currency: 'USD'
    });
    });
    </script>

  </body>
</html>