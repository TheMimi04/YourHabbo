<?php

require '../../global.php';

$Controller->isDisconnected();

if($_SESSION['account']['rank'] <= 4) {
	header('Location: /client/');
} elseif(isset($_SESSION['admin'])) {
	header('Location: index');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<title>Administration - Connexion</title>
		
        <link rel="stylesheet" href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="vendors/bower_components/animate.css/animate.min.css">
		<link rel="icon" href="img/favicon.ico">
		
        <link rel="stylesheet" href="css/app.min.css">
		<link rel="stylesheet" href="demo/css/demo.css">
    </head>
    <body data-ma-theme="blue">
        <div class="login">

            <div class="login__block active">
                <div class="login__block__header">
                    <img src="<?= $_SESSION['account']['avatar']; ?>" height="40px" width="40px" alt="">
                    Bienvenue <?= $_SESSION['account']['username']; ?> !
                </div>

                <div class="login__block__body">
					<form method="post" action="/core/action/action?to=admlogin" id="login">
						<div class="form-group form-group--float form-group--centered">
							<input type="password" name="password" class="form-control">
							<label>Mot de passe</label>
							<i class="form-group__bar"></i>
						</div>
						<button class="btn btn--icon login__block__btn"><i class="zmdi zmdi-long-arrow-right"></i></button>
					</form>

                </div>
            </div>
        </div>

        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="vendors/bower_components/tether/dist/js/tether.min.js"></script>
        <script src="vendors/bower_components/Waves/dist/waves.min.js"></script>
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

        <script src="js/app.min.js"></script>
		<script src="demo/js/demo.js"></script>
		<script src="vendors/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
		<script src="js/login.js"></script>
    </body>
</html>