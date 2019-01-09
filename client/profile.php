<?php

require '../global.php';

/* Si l'utilsateur est déconnecté, on le redirige */
$Controller->isDisconnected();

/* Fonction de déconnexion*/
$Controller->Logout();

/* Activer la page dans le header */
$page_open = 'profile';

/* Fonction de profil */
$StingCMS->Profile();

/* ID Profile */

$_SESSION['explodeid'] = explode('/client/', $_SERVER['REQUEST_URI']);
$_SESSION['idbasic'] = $Controller->GetID('id');

?><!doctype html><html lang=en><head><meta charset=UTF-8><meta name=viewport content="width=device-width, initial-scale=1, user-scalable=no"><meta http-equiv=x-ua-compatible content="ie=edge"><title>Profil - <?= $Controller->Settings('name'); ?></title><link href=assets/images/favicon.ico rel=apple-touch-icon type=image/png sizes=144x144><link href=assets/images/favicon.ico rel=apple-touch-icon type=image/png sizes=114x114><link href=assets/images/favicon.ico rel=apple-touch-icon type=image/png sizes=72x72><link href=assets/images/favicon.ico rel=apple-touch-icon type=image/png><link href=assets/images/favicon.ico rel=icon type=image/png><link href=assets/images/favicon.ico rel="shortcut icon"><link type=text/css rel=stylesheet href=assets/fonts/fonts.css><link type=text/css rel=stylesheet href=assets/icons/icomoon/icomoon.css><link type=text/css rel=stylesheet href=css/animate.min.css><link type=text/css rel=stylesheet href=css/bootstrap.css><link type=text/css rel=stylesheet href=css/core.css><link type=text/css rel=stylesheet href=css/layout.css><link type=text/css rel=stylesheet href=css/bootstrap-extended.css><link type=text/css rel=stylesheet href=css/components.css><link type=text/css rel=stylesheet href=css/plugins.css><link type=text/css rel=stylesheet href=css/loaders.css><link type=text/css rel=stylesheet href=css/responsive.css><link type=text/css rel=stylesheet href=css/color-system.css><link type=text/css rel=stylesheet href=css/fancybox/jquery.fancybox.css><link href=assets/icons/font-awesome/css/font-awesome.min.css rel=stylesheet type=text/css></head><body class=material-menu id=top><?php require 'templates/header.tpl'; ?><?php require 'templates/navigator.tpl'; ?><section class=main-container2><div class=header></div><div class="container-fluid page-content"><?= $StingCMS->Profile(); ?><div class=row><div class="col-lg-3 col-sm-4"><div class=thumbnail><div class="thumb thumb-rounded thumb-slide"><img src=<?= $_SESSION['profile']['avatar']; ?> style=height:160px;weight:160px alt></div><div class="caption text-center"><h3 class="no-margin <?= $Controller->RankColor($_SESSION['profile']['rank']); ?>"><?= $_SESSION['profile']['username']; ?><small class=display-block><?= $Controller->Rank($_SESSION['profile']['rank']); ?></small></h3><?= $StingCMS->ProfileNetwork(); ?></div><?= $StingCMS->ButtonAddFriend(); ?></div><div class="panel panel-flat"><div class=panel-heading><h5 class="panel-title mb-10">Mes amis</h5></div><ul class="media-list media-list-linked pb-5" style=max-height:350px;*::-webkit-scrollbar{display:none};overflow:auto;overflow-y:scroll><?= $StingCMS->FriendsList(); ?></ul></div></div><div class="col-lg-9 col-sm-8"><?= $StingCMS->ProfileStatut(); ?><div id=newstatut></div><?= $StingCMS->ProfileStatutList(); ?><center><ul class="pagination pagination-flat pagination-rounded"><?php for($i=1;$i<=$_SESSION['tt'];$i++) {
													if($i == $_SESSION['PA']) {
															echo '<li class="active"><a href="#">'.$i.'</a></li>';
													} else {
														echo '<li><a href="?id='.$_SESSION['profile']['clef'].'&d='.$i.'">'.$i.'</a></li>';
													}
											} ?></ul></center></div></div></div><footer class=footer-container><div class=container-fluid><div class=row><div class="col-md-12 col-sm-12"><div class=footer-center align=center><span>© 2015 - <?= date('Y'); ?> <?= $Controller->Settings('name'); ?>&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;Tous droits réservés&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;Développé par <a href=https://twitter.com/MathisLts>Mathis L</a>&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;Version 1.2</span></div></div></div></div></footer></section><a id=scrollTop href=#top><i class=icon-arrow-up12></i></a><script src=js/jquery.js></script><script src=js/bootstrap.js></script> <script src=js/jquery.ui.js></script><script src=js/nav.accordion.js></script> <script src=js/hammerjs.js></script><script src=js/jquery.hammer.js></script> <script src=js/scrollup.js></script><script src=js/jquery.slimscroll.js></script> <script src=js/smart-resize.js></script><script src=js/blockui.min.js></script> <script src=js/wow.min.js></script><script src=js/fancybox.min.js></script> <script src=js/venobox.js></script><script src=js/forms/uniform.min.js></script> <script src=js/forms/switchery.js></script><script src=js/forms/select2.min.js></script> <script src=js/core.js></script><script src=js/pnotify.min.js></script> <script src=js/profile.js></script><script src=js/notifications.js></script><?= $StingCMS->SuiviGoogle(); ?></body></html>