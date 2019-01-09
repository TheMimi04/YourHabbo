<?php

require '../../global.php';

if(isset($_SESSION['admin'])) {
	if($_SESSION['admin'] == 'active') {
		if($_SESSION['account']['rank'] >= 6) {
			
		} else {
			header('Location: index');
		}
		
	} else {
		header('Location: login');
	}
	
} else {
	header('Location: ../login');
}

$page = 'user';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<title>Bannissement - <?= $Controller->Settings('name'); ?></title>

        <link rel="stylesheet" href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="vendors/bower_components/animate.css/animate.min.css">
        <link rel="stylesheet" href="vendors/bower_components/jquery.scrollbar/jquery.scrollbar.css">
        <link rel="stylesheet" href="vendors/bower_components/fullcalendar/dist/fullcalendar.min.css">
		<link rel="stylesheet" href="css/font-awesome.min.css">
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
						<form method="post" id="ban" action="/core/action/action?to=admban">
							<div class="card-demo">
							<div class="card">

								<div class="card-header">
									<h2 class="card-title">Bannissement</h2>
								</div>
						
								<div class="card-block">
									<form method="post" action="/core/action/action?to=admban" id="ban">
									<div class="form-group">
										<label>Nom d'utilisateur</label>
										<input type="text" name="username" class="form-control" placeholder="Nom d’utilisateur">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Raison</label>
										<input type="text" name="raison" class="form-control" placeholder="Raison">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Type</label>
										<select name="type" class="form-control">
											<option value="Shoutbox">Shoutbox</option>
											<option value="IP">IP</option>
											<option value="Site&IP">Site + IP</option>
										</select>
									</div>
									<div class="form-group">
										<label>Temps (en minutes)</label>
										<input type="number" name="temps" class="form-control" placeholder="Temps">
										<i class="form-group__bar"></i>
									</div>
									<button class="btn btn-primary btn-block waves-effect">Valider</button>
									</form>
								</div>

							</div>
						</div>
						</form>
					</div>
					<div class="col-md-6" id="show_ban" style="max-height: 467px; overflow-y: auto; overflow-x: hidden; word-break: break-all; text-overflow: ellipsis; white-space: nowrap;">
						<p class="text-center"><i class="fa fa-spinner fa-spin fa-fw"></i> Chargement en cours</p>
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
		<script src="js/adm/ban.js"></script>
		<script>
			$(function(){
				$('#show_ban').load('/core/action/action?to=banlist');
			});
</script>
    </body>
</html>