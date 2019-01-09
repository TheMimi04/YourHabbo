<?php

/********************************************
* Fichier principal au fonctionnement du site
********************************************/

$LogAPI = array(
		'api' => 'azerty',
		'user' => 'user',
		'password' => 'password'
	);

require 'modules/connection.php';
require 'modules/controller.php';
require 'core/php/stingcms.php';

/****************************************
* Permet le rafraîchissement des sessions
****************************************/

$Controller->RefreshSession();

/****************************
* On sécurise les failles SQL
****************************/

$Controller->Injector();

/**********************************************
* Vérification des mails de confirmation expiré
**********************************************/

$Controller->ExpirationMail();

/*************************************
* Mise à jour de l'IP des utilisateurs
*************************************/

$Controller->UpdateIPSession();

/***************************************************************
* Banni les utilisateurs avec plus de 30 points d'avertissements
***************************************************************/

$Controller->BanAutoAvert();

/********************************************
* Redirection des utilisateurs bannis du site
********************************************/

$Controller->BanSiteIP();

/**************************
* Gère l'abonnement des VIP
**************************/

$Controller->ExpirationSubscriptions();

/*********************
* Ajout du badge staff
*********************/

$Controller->BadgeStaff();

/*********************
* Ajout du badge VIP's
*********************/

$Controller->BadgeVIP();

/********************************
* Ajout des préférences de profil
********************************/

$Controller->AddPrefProfile();

/****************************
* Expiration des sessions VPS
****************************/

$Controller->ExpirationSessionVPS();

/************************
* Expiration des services
************************/

$Controller->ExpirationServices();

/******************************
* Expirations des bannissements
******************************/

$Controller->ExpirationBan();

?>