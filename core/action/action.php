<?php

/**********************
* Module complet action
***********************/

require '../../global.php';

if($Controller->Security($_GET['to']) == 'login') {
    $StingCMS->Login();
} elseif($Controller->Security($_GET['to']) == 'register') {
    $StingCMS->Register();
} elseif($Controller->Security($_GET['to']) == 'forgot') {
    $StingCMS->Forgot();
} elseif($Controller->Security($_GET['to']) == 'sendmsgshoutbox') {
	$StingCMS->InsertMessage();
} elseif($Controller->Security($_GET['to']) == 'usersonline') {
	$StingCMS->UsersOnline();
} elseif($Controller->Security($_GET['to']) == 'loadshoutbox') {
	$StingCMS->ChargeShoutbox();
} elseif($Controller->Security($_GET['to']) == 'order') {
	$StingCMS->TermineOrder();
} elseif($Controller->Security($_GET['to']) == 'kdo') {
	$StingCMS->CodeKDO();
} elseif($Controller->Security($_GET['to']) == 'passwordchange') {
	$StingCMS->ChangePassword();
} elseif($Controller->Security($_GET['to']) == 'avatarchange') {
	$StingCMS->ChangeAvatar();
} elseif($Controller->Security($_GET['to']) == 'createticket') {
	$StingCMS->CreateTicket();
} elseif($Controller->Security($_GET['to']) == 'addmsg') {
	$StingCMS->AddMsg();
} elseif($Controller->Security($_GET['to']) == 'admlogin') {
	$StingCMS->ADMLogin();
} elseif($Controller->Security($_GET['to']) == 'admgeneral') {
	$StingCMS->ADMChangeGeneral();
} elseif($Controller->Security($_GET['to']) == 'admoptions') {
	$StingCMS->ADMChangeOptions();
} elseif($Controller->Security($_GET['to']) == 'admpayment') {
	$StingCMS->ADMPayment();
} elseif($Controller->Security($_GET['to']) == 'admsearch') {
	$StingCMS->ADMSearch();
} elseif($Controller->Security($_GET['to']) == 'admaccount1') {
	$StingCMS->ADMAccount1();
} elseif($Controller->Security($_GET['to']) == 'admaccount2') {
	$StingCMS->ADMAccount2();
} elseif($Controller->Security($_GET['to']) == 'banlist') {
	$StingCMS->ADMListeBan();
} elseif($Controller->Security($_GET['to']) == 'admban') {
	$StingCMS->ADMBan();
} elseif($Controller->Security($_GET['to']) == 'vipbasic') {
	$StingCMS->VIPBasic();
} elseif($Controller->Security($_GET['to']) == 'vipplus') {
	$StingCMS->VIPPlus();
} elseif($Controller->Security($_GET['to']) == 'vipultime') {
	$StingCMS->VIPUltime();
} elseif($Controller->Security($_GET['to']) == 'admvalidorder') {
	$StingCMS->ADMValidOrder();
} elseif($Controller->Security($_GET['to']) == 'badge') {
	$StingCMS->OrderBadge();
} elseif($Controller->Security($_GET['to']) == 'admaddrept') {
	$StingCMS->ADMAddRepT();
} elseif($Controller->Security($_GET['to']) == 'admcloseticket') {
	$StingCMS->ADMCloseTicket();
} elseif($Controller->Security($_GET['to']) == 'admchangenamer') {
	$StingCMS->ADMChangeNameR();
} elseif($Controller->Security($_GET['to']) == 'forumcomment') {
	$StingCMS->ForumComment();
} elseif($Controller->Security($_GET['to']) == 'likeordislike') {
	$StingCMS->LikeOrDislike();
} elseif($Controller->Security($_GET['to']) == 'createforum') {
	$StingCMS->CreateForum();
} elseif($Controller->Security($_GET['to']) == 'admcreateupdate') {
	$StingCMS->ADMCreateUpdate();
} elseif($Controller->Security($_GET['to']) == 'admupdateupdate') {
	$StingCMS->ADMUpdateUpdateScript();
} elseif($Controller->Security($_GET['to']) == 'admsupprupdate') {
	$StingCMS->ADMSupprUpdate();
} elseif($Controller->Security($_GET['to']) == 'profilemsg') {
	$StingCMS->SendMsgProfile();
} elseif($Controller->Security($_GET['to']) == 'settingsprofile') {
	$StingCMS->ChangeSettingsProfile();
} elseif($Controller->Security($_GET['to']) == 'passwordadmchange') {
	$StingCMS->PasswordADMChange();
} elseif($Controller->Security($_GET['to']) == 'addfriend') {
	$StingCMS->AddFriend();
} elseif($Controller->Security($_GET['to']) == 'friends') {
	$StingCMS->FriendsReq();
} elseif($Controller->Security($_GET['to']) == 'viewnotif') {
	$StingCMS->ViewNotif();
} elseif($Controller->Security($_GET['to']) == 'nbnotif') {
	$StingCMS->NBNotifications();
} elseif($Controller->Security($_GET['to']) == 'notif') {
	$StingCMS->Notifications();
} elseif($Controller->Security($_GET['to']) == 'deletefriend') {
	$StingCMS->DeleteFriend();
} elseif($Controller->Security($_GET['to']) == 'likestatut') {
	$StingCMS->LikeStatut();
} elseif($Controller->Security($_GET['to']) == 'reportstatut') {
	$StingCMS->ReportStatut();
} elseif($Controller->Security($_GET['to']) == 'addcomment') {
	$StingCMS->AddComment();
} elseif($Controller->Security($_GET['to']) == 'sessionvps') {
	$StingCMS->SessionVPS();
} elseif($Controller->Security($_GET['to']) == 'renouvvps') {
	$StingCMS->RenouvVPS();
} elseif($Controller->Security($_GET['to']) == 'packswf') {
	$StingCMS->PackSWF();
} elseif($Controller->Security($_GET['to']) == 'renouvellement') {
	$StingCMS->Renouvellement();
} elseif($Controller->Security($_GET['to']) == 'resilier') {
	$StingCMS->Resilier();
} elseif($Controller->Security($_GET['to']) == 'reboot') {
	$StingCMS->Reboot();
} elseif($Controller->Security($_GET['to']) == 'cms') {
	$StingCMS->ReinstallCMS();
} elseif($Controller->Security($_GET['to']) == 'db') {
	$StingCMS->ReinstallBDD();
} elseif($Controller->Security($_GET['to']) == 'admmodifvalide') {
	$StingCMS->ADMModifValide();
} elseif($Controller->Security($_GET['to']) == 'admmodifaccess') {
	$StingCMS->ADMModifAccess();
} elseif($Controller->Security($_GET['to']) == 'admswf') {
	$StingCMS->ADMSWF();
} elseif($Controller->Security($_GET['to']) == 'admopt') {
	$StingCMS->ADMOptionsGo();
} elseif($Controller->Security($_GET['to']) == 'loto') {
	$StingCMS->LotoScript();
}
     
?>