<?php

require '../../global.php';

if(isset($_SESSION['admin'])) {
	if($_SESSION['admin'] == 'active') {
		if($_SESSION['account']['rank'] >= 8) {
			
		} else {
			header('Location: index');
		}
		
	} else {
		header('Location: login');
	}
	
} else {
	header('Location: login');
}

$page = 'payment';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<title>Configuration Paiement - <?= $Controller->Settings('name'); ?></title>

        <link rel="stylesheet" href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="vendors/bower_components/animate.css/animate.min.css">
        <link rel="stylesheet" href="vendors/bower_components/jquery.scrollbar/jquery.scrollbar.css">
        <link rel="stylesheet" href="vendors/bower_components/fullcalendar/dist/fullcalendar.min.css">
		<link rel="icon" href="img/favicon.ico">
        <link rel="stylesheet" href="css/app.min.css">
    </head>

    <body data-ma-theme="blue">
        <main class="main">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

            <header class="header">
                <div class="navigation-trigger hidden-xl-up" data-ma-action="aside-open" data-ma-target=".sidebar">
                    <div class="navigation-trigger__inner">
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                    </div>
                </div>

                <div class="header__logo hidden-sm-down">
                    <h1><a href="index.html">Administration YH</a></h1>
                </div>

                <form class="search">
                    <div class="search__inner">
                        <input type="text" class="search__text" placeholder="Rechercher un client">
                        <i class="zmdi zmdi-search search__helper" data-ma-action="search-close"></i>
                    </div>
                </form>

                <ul class="top-nav">
                    <li class="hidden-xl-up"><a href="" data-ma-action="search-open"><i class="zmdi zmdi-search"></i></a></li>
                </ul>
            </header>

            <?php require 'templates/navigator.tpl'; ?>
			
            <section class="content">
				
				<div class="row">
					<div class="col-md-6">
						<div id="statut"></div>
						<div class="card-demo">
							<div class="card">

								<div class="card-header">
									<h2 class="card-title">Configuration du DediPass</h2>
								</div>
						
								<div class="card-block">
									<form method="post" action="/core/action/action?to=admpayment" id="paiement">
									<div class="form-group">
										<label>Clé public</label>
										<input type="text" name="public" class="form-control" placeholder="Clé public" value="<?= $Controller->Settings('dedipass_public_key'); ?>">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Clé privée</label>
										<input type="text" name="private" class="form-control" placeholder="Clé privée" value="<?= $Controller->Settings('dedipass_private_key'); ?>">
										<i class="form-group__bar"></i>
									</div>
									<button class="btn btn-primary btn-block waves-effect">Valider</button>
									</form>
								</div>

							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card-demo">
							<div class="card">

								<div class="card-header">
									<h2 class="card-title" align="center">Information</h2>
								</div>
						
								<div class="card-block">
									<center><p>Vous trouverez les clés dans <b>Services</b> puis dans <b>Installer</b>.</p><p><b>Attention</b>, la clé privée est visible simplement dans le <b>code PHP</b>.</p></center>
								</div>

							</div>
						</div>
					</div>
				</div>
				</div>
				

                <footer class="footer hidden-xs-down">
                    <p>© Copyright 2015 - <?= date('Y'); ?> YourHabbo. Développé par Mathis L. Tous droits réservés.</p>
                </footer>
            </section>
        </main>

        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="vendors/bower_components/tether/dist/js/tether.min.js"></script>
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendors/bower_components/Waves/dist/waves.min.js"></script>
        <script src="vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js"></script>
        <script src="vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js"></script>
        <script src="vendors/bower_components/Waves/dist/waves.min.js"></script>

        <script src="vendors/bower_components/flot/jquery.flot.js"></script>
        <script src="vendors/bower_components/flot/jquery.flot.resize.js"></script>
        <script src="vendors/bower_components/flot.curvedlines/curvedLines.js"></script>
        <script src="vendors/bower_components/jqvmap/dist/jquery.vmap.min.js"></script>
        <script src="vendors/bower_components/jqvmap/dist/maps/jquery.vmap.world.js"></script>
        <script src="vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <script src="vendors/bower_components/salvattore/dist/salvattore.min.js"></script>
        <script src="vendors/jquery.sparkline/jquery.sparkline.min.js"></script>
        <script src="vendors/bower_components/moment/min/moment.min.js"></script>
        <script src="vendors/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>

        <script src="demo/js/flot-charts/curved-line.js"></script>
        <script src="demo/js/flot-charts/line.js"></script>
        <script src="demo/js/flot-charts/chart-tooltips.js"></script>
        <script src="demo/js/other-charts.js"></script>
        <script src="demo/js/jqvmap.js"></script>
	
        <script src="js/app.min.js"></script>
		<script src="vendors/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
		<script src="js/adm/paiement.js"></script>	
    </body>
</html>