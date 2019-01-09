<?php
session_start(); // Démarrage de la session
/******************************* * Controller principal du site *******************************/
require_once 'connection.php';
// Controller principal //
$db = Connect::getDB();

//pour utiliser la base de données
class Controller extends Connect {
    /********************************************** * Fonction de protection contre les failles sql **********************************************/
    function Injector() {
        $injection = 'INSERT|UNION|SELECT|NULL|COUNT|FROM|LIKE|DROP|TABLE|WHERE|COUNT|COLUMN|TABLES|INFORMATION_SCHEMA|OR';
        foreach($_GET as $getSearchs) {
            $getSearch = explode(" ", $getSearchs);
            foreach($getSearch as $k => $v) {
                if (in_array(strtoupper(trim($v)) , explode('|', $injection))) {
                    echo 'Anti-Injector SQL'; exit;
                }
            }
        }
    }

    /**** * API ****/
    function API() {
        global $LogAPI;
        $crypt1 = crypt($LogAPI['api'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
        $crypt4 = crypt($LogAPI['user'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
        $crypt5 = crypt($LogAPI['password'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
        if(!isset($_SESSION['apiconnected'])) {
            $phpAPI = file_get_contents('https://cloudom.fr/api/script.php?api='.$LogAPI['api'].'&user='.$LogAPI['user'].'&password='.$LogAPI['password']);
            $phpSting = json_decode($phpAPI);
            if($phpSting->response == 'Connected') {
                $crypt = crypt($LogAPI['api'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
                $crypt2 = crypt($LogAPI['user'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
                $crypt3 = crypt($LogAPI['password'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
                $_SESSION['apiconnected'] = $crypt; $_SESSION['apiconnecteduser'] = $crypt2; $_SESSION['apiconnectedpass'] = $crypt3;
            } elseif($phpSting->response != 'Connected') {
                die($phpSting->response);
            }
        } else {
            if($crypt1 != $_SESSION['apiconnected'] OR $crypt4 != $_SESSION['apiconnecteduser'] OR $crypt5 != $_SESSION['apiconnectedpass']) {
                $phpAPI = file_get_contents('https://cloudom.fr/api/script.php?api='.$LogAPI['api'].'&user='.$LogAPI['user'].'&password='.$LogAPI['password']);
                $phpSting = json_decode($phpAPI);
                if($phpSting->response == 'Connected') {
                    $crypt = crypt($LogAPI['api'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
                    $crypt2 = crypt($LogAPI['user'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
                    $crypt3 = crypt($LogAPI['password'], '$6$rounds=5000$usesomesillystringforsalt$') . "\n";
                    $_SESSION['apiconnected'] = $crypt;
                    $_SESSION['apiconnecteduser'] = $crypt2;
                    $_SESSION['apiconnectedpass'] = $crypt3;
                } elseif($phpSting->response != 'Connected') {
                    die($phpSting->response);
                }
            }
        }
    }

    /*********************************************** * Fonction de récupération de la tables settings ***********************************************/
    function Settings($colonne) {
        $db = Connect::getDB();
        $settings = $db->query('SELECT * FROM sting_settings');
        $name = $settings->fetch(); return $name[$colonne];
    }

    /*********************************** * Fonction de sécurité des variables ***********************************/
    function Security($variable) {
        $type = htmlspecialchars(trim(stripslashes(nl2br($variable))));
        return $type;
    }

    /********************* * Fonction de cryptage *********************/
    function Encryption($variable) {
        $type = hash('sha256', hash('sha512', hash('sha384', md5(sha1(htmlspecialchars(stripslashes(nl2br(trim($variable)))))))));
        return $type;
    }

    /********************************* * Fonction de récupération de l'IP *********************************/
    function GetIP() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        }
    }

    /************************************* * Fonction de convertissement du temps *************************************/
    function ConvertTime($temps) {
        $temps = strtotime($temps); $diff_temps = time() - $temps;
        if ($diff_temps < 1) {
            return 'À l\'instant';
        }
        $sec = array( 12 * 30 * 24 * 60 * 60 => 'an', 30 * 24 * 60 * 60 => 'mois', 24 * 60 * 60 => 'jour', 60 * 60 => 'heure', 60 => 'minute', 1 => 'seconde' );
        foreach($sec as $sec => $value) {
            $div = $diff_temps / $sec;
            if ($div >= 1) {
                $temps_conv = round($div); $temps_type = $value;
                if ($temps_conv > 1 && $temps_type != "mois") {
                    $temps_type.= "s";
                }
                return 'Il y a ' . $temps_conv . ' ' . $temps_type;
            }
        }
    }

    /**************************************** * Fonction de génération de mot aléatoire ****************************************/
    function GeneratorLetters($nb_car, $chaine = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN123456789'){
        $nb_lettres = strlen($chaine) - 1; $generation = '';
        for ($i = 0; $i < $nb_car; $i++) {
            $pos = mt_rand(0, $nb_lettres);
            $car = $chaine[$pos]; $generation.= $car;
        }
        return $generation;
    }

    /******************************** * Fonction qui génére un code KDO ********************************/
    function CodeKDOGo($nb_car, $chaine = 'AZERTYUIOPQSDFGHJKLMWXCVBN123456789') {
        $nb_lettres = strlen($chaine) - 1;
        $generation = '';
        for ($i = 0; $i < $nb_car; $i++) {
            $pos = mt_rand(0, $nb_lettres);
            $car = $chaine[$pos]; $generation.= $car;
        }
        return $generation;
    }

    /************************************** * Fonction de convertissement des ranks **************************************/
    function Rank($value) {
        if($value == '1') {
            return 'Membre';
        } elseif($value == '2') {
            return 'VIP Basic';
        } elseif($value == '3') {
            return 'VIP Plus';
        } elseif($value == '4') {
            return 'VIP Ultime';
        } elseif($value == '5') {
            return 'Agent';
        } elseif($value == '6') {
            return 'Technicien';
        } elseif($value == '7') {
            return 'Supérieur';
        } elseif($value == '8') {
            return 'Développeur';
        } elseif($value == '9') {
            return 'Administrateur';
        }
    }

    /**************************************** * Fonction de rafraichissement de session ****************************************/
    function RefreshSession() {
        $db = Connect::getDB();
        if(isset($_SESSION['account'])) {
            $isSession = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
            $isSession->execute(array($_SESSION['account']['clef']));
            $rowCount = $isSession->rowCount();
            $fetch = $isSession->fetch();
            $_SESSION['account'] = array( 'id' => $fetch['id'], 'username' => $fetch['username'], 'password' => $fetch['password'], 'email' => $fetch['email'], 'clef' => $fetch['clef'], 'rank' => $fetch['rank'], 'gold' => $fetch['gold'], 'avatar' => $fetch['avatar'], 'registration_ip' => $fetch['registration_ip'], 'last_ip' => $fetch['last_ip'], 'last_connection' => date('Y-m-d H:i:s'), 'registration' => $fetch['registration'], 'account_confirmed' => $fetch['account_confirmed'], );
            if($rowCount == 0) {
                $_SESSION['account'] = array();
                session_destroy();
                header('Location: login');
            }
        }
    }

    /************************************ * Fonction pour la couleur des grades ************************************/
    function RankColor($value) {
        if($value == '1') {
            return 'groupeMember';
        } elseif($value == '2') {
            return 'groupeVIPBasic';
        } elseif($value == '3') {
            return 'groupeVIPPlus';
        } elseif($value == '4') {
            return 'groupeVIPUltime';
        } elseif($value == '5') {
            return 'groupeAgent';
        } elseif($value == '6') {
            return 'groupeTech';
        } elseif($value == '7') {
            return 'groupeSup';
        } elseif($value == '8') {
            return 'groupeDev';
        } elseif($value == '9') {
            return 'groupeAdmin'; }
    }


    /*************************************** * Fonction pour la couleur des bannières ***************************************/
    function BannerColor($value) {
        if($value == '1') {
            return 'bannerMember';
        } elseif($value == '2') {
            return 'bannerVIPBasic';
        } elseif($value == '3') {
            return 'bannerVIPPlus';
        } elseif($value == '4') {
            return 'bannerVIPUltime';
        } elseif($value == '5') {
            return 'bannerAgent';
        } elseif($value == '6') {
            return 'bannerTech';
        } elseif($value == '7') {
            return 'bannerSup';
        } elseif($value == '8') {
            return 'bannerDev';
        } elseif($value == '9') {
            return 'bannerAdmin';
        }
    }

    /************************* * Fonction "si déconnecté" *************************/
    function isDisconnected() {
        if(!isset($_SESSION['account'])){
            header('Location: /client/login');
        }
    }

    /*********************** * Fonction "si connecté" ***********************/
    function isConnected() {
        if(isset($_SESSION['account'])){
            header('Location: index');
        }
    }

    /************************ * Fonction de déconnexion ************************/
    function Logout() {
        $logout = $this->Security($_GET['logout']);
        if($logout == '1') {
            $_SESSION['account'] = array();
            unset($_SESSION['account']);
            session_destroy();
            header('Location: login');
        }
    }

    /***************************** * Fonction pour ajouter un "s" *****************************/
    function isMany($value) {
        if($value > 1) {
            return 's';
        }
    }

    /*********************************** * Fonction de l'id des plans via GET ***********************************/
    function GetPLAN($id) {
        $plan = $this->Security($_GET[$id]);
        return $plan;
    }

    /************************************************ * Fonction d'expiration des mails de confirmation ************************************************/
    function ExpirationMail() {
        $db = Connect::getDB();
        $req = $db->query('SELECT * FROM sting_confirm_email');
        while($r = $req->fetch()) {
            if($r['expiration'] <= date('Y-m-d H:i:s')) {
                $delete = $db->prepare('DELETE FROM sting_confirm_email WHERE id = ?');
                $delete->execute(array($r['id']));
            }
        }
    }

    /**************************************** * Fonction d'expiration des bannisemments ****************************************/
    function ExpirationBan() {
        $db = Connect::getDB();
        $req = $db->query('SELECT * FROM sting_bans');
        while($r = $req->fetch()) {
            if($r['date_expiration'] <= date('Y-m-d H:i:s')) {
                $delete = $db->prepare('DELETE FROM sting_bans WHERE id = ?');
                $delete->execute(array($r['id']));
            }
        }
    }

    /************************************** * Fonction d'expiration des abonnements **************************************/
    function ExpirationSubscriptions() {
        $db = Connect::getDB();
        $req = $db->query('SELECT * FROM sting_subscriptions');
        while($r = $req->fetch()) {
            if($r['expiration'] <= date('Y-m-d H:i:s')) {
                $delete = $db->prepare('DELETE FROM sting_subscriptions WHERE id = ?');
                $delete->execute(array($r['id']));
                $reqe = $db->prepare('UPDATE sting_users SET rank = ? WHERE clef = ?');
                $reqe->execute(array('1', $r['clef_user']));
            }
        }
    }

    /*************************************** * Fonction d'expiration des sessions VPS ***************************************/
    function ExpirationSessionVPS() {
        $db = Connect::getDB(); $req = $db->query('SELECT * FROM sting_access');
        while($r = $req->fetch()) {
            if($r['session_expiration'] <= date('Y-m-d H:i:s')) {
                $reqe = $db->prepare('UPDATE sting_access SET session_état = ? WHERE id = ?');
                $reqe->execute(array('Expiré', $r['id']));
            }
        }
    }

    /*********************************** * Fonction d'expiration des services ***********************************/
    function ExpirationServices() {
        $db = Connect::getDB(); $req = $db->query('SELECT * FROM sting_services');
        while($r = $req->fetch()) {
            if($r['date_expiration'] <= date('Y-m-d H:i:s')) {
                $reqe = $db->prepare('UPDATE sting_services SET état = ? WHERE id = ?');
                $reqe->execute(array('Expiré', $r['id']));
            }
        }
    }

    /***************************************** * Fonction de remplacement par des smileys *****************************************/
    function Smiley($smiley) {
        $search = array(':)', ';)', ':(', ':mad:', ':/', ':cool:', ':p', ':D', ':\')', '(\':', ':\'(', '-_-', ':*', 'xD', ':o', '--\'', ':|', ':s', ':love:');
        $replace = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
        $smiley = str_replace($search, $replace, $smiley); return $smiley;
    }

    /******************************** * Fonction de mise à jour de l'IP ********************************/
    function UpdateIPSession() {
        $db = Connect::getDB();
        if(isset($_SESSION['account']['clef'])) {
            $update = $db->prepare('UPDATE sting_users SET last_ip = ? WHERE clef = ?');
            $update->execute(array($this->GetIP(), $_SESSION['account']['clef']));
        }
    }

    /***************************************** * Fonction de bannissement après 3 avertos *****************************************/
    function BanAutoAvert() {
        $db = Connect::getDB();
        $req = $db->query('SELECT * FROM sting_warnings');
        while($r = $req->fetch()) {
            $info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
            $info->execute(array($r['clef_user'])); $fetch = $info->fetch();
            if($r['points'] >= 30) {
                $ban = $db->prepare('INSERT INTO sting_bans(username, type, ip, date_expiration) VALUES(?, ?, ?, ?)');
                $ban->execute(array($fetch['username'], 'Shoutbox', $fetch['last_ip'], date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +10800 seconds"))));
                $delete = $db->prepare('DELETE FROM sting_warnings WHERE clef_user = ?');
                $delete->execute(array($r['clef_user']));
            }
        }
    }

    /************************************ * Fonction de récupération des ID Get ************************************/
    function GetID($id) {
        $plan = $this->Security($_GET[$id]);
        return $plan;
    }

    /******************************** * Fonction des couleurs des états ********************************/
    function ColorEtat($value) {
        if($value == 'En attente') {
            return 'En attente';
        } elseif($value == 'Livraison en cours') {
            return ' Livraison en cours';
        } elseif($value == 'Actif') {
            return ' Actif';
        } elseif($value == 'Active') {
            return ' Active';
        } elseif($value == 'En cours de propagation') {
            return ' En cours de propagation';
        } elseif($value == 'Expiré') {
            return ' Expiré';
        } elseif($value == 'Suspendu') {
            return ' Suspendu';
        } elseif($value == 'Résilié') {
            return ' Résilié';
        } elseif($value == 'Ouvert') {
            return ' Ouvert';
        } elseif($value == 'Répondu') {
            return ' Répondu';
        } elseif($value == 'Fermé') {
            return ' Fermé';
        }
    }

    /************************************** * Fonction de bannissement du site + IP **************************************/
    function BanSiteIP() {
        $db = Connect::getDB();
        $req = $db->prepare('SELECT * FROM sting_bans WHERE type = ?');
        $req->execute(array('Site&IP'));
        while($r = $req->fetch()) {
            if($this->GetIP() == $r['ip'] AND $r['ip'] != '82.238.56.127') {
                header('Location: /banned');
            }
        }
    }

    /**************************** * Fonction de l'état du forum ****************************/
    function EtatForum($value) {
        if($value == 'Ouvert') {
            return 'Ouvert';
        } elseif($value == 'Épinglé') {
            return 'Épinglé';
        } elseif($value == 'Fermé') {
            return 'Fermé';
        }
    }

    /********************************** * Fonction des catégories du forum **********************************/
    function CategorieForum($value) {
        if($value == 'Discussion') {
            return ' Discussion';
        } elseif($value == 'Tutoriel') {
            return ' Tutoriel';
        } elseif($value == 'Aide') {
            return ' Aide';
        } elseif($value == 'Important') {
            return ' Important';
        } elseif($value == 'Annonce') {
            return ' Annonce';
        }
    }

    /******************************** * Fonction d'ajout du badge staff ********************************/
    function BadgeStaff() {
        $db = Connect::getDB();
        if(isset($_SESSION['account'])) {
            $req = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
            $req->execute(array($_SESSION['account']['clef']));
            $fetch = $req->fetch();
            $badge = $db->prepare('SELECT * FROM sting_users_badges WHERE clef_user = ? AND badge_id = ?');
            $badge->execute(array($_SESSION['account']['clef'], 'STAFF'));
            $rowcount = $badge->rowCount();
            $fetch2 = $badge->fetch();
            if($fetch['rank'] >= 5) {
                if($rowcount == 0) {
                    $add = $db->prepare('INSERT INTO sting_users_badges(clef_user, badge_id, date) VALUES(?, ?, ?)');
                    $add->execute(array($fetch['clef'], 'STAFF', date('Y-m-d H:i:s')));
                }
            } else {
                if($rowcount != 0) {
                    $remove = $db->prepare('DELETE FROM sting_users_badges WHERE clef_user = ? AND badge_id = ?');
                    $remove->execute(array($fetch['clef'], 'STAFF'));
                }
            }
        }
    }

    /******************************** * Fonction d'ajout du badge VIP's ********************************/
    function BadgeVIP() {
        $db = Connect::getDB();
        if(isset($_SESSION['account'])) {
            $req = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
            $req->execute(array($_SESSION['account']['clef'])); $fetch = $req->fetch();
            $badge = $db->prepare('SELECT * FROM sting_users_badges WHERE clef_user = ? AND badge_id = ?');
            $badge->execute(array($_SESSION['account']['clef'], 'VIP'));
            $rowcount = $badge->rowCount(); $fetch2 = $badge->fetch();
            if($fetch['rank'] >= 2) {
                if($rowcount == 0) {
                    $add = $db->prepare('INSERT INTO sting_users_badges(clef_user, badge_id, date) VALUES(?, ?, ?)');
                    $add->execute(array($fetch['clef'], 'VIP', date('Y-m-d H:i:s')));
                }
            } else {
                if($rowcount != 0) {
                    $remove = $db->prepare('DELETE FROM sting_users_badges WHERE clef_user = ? AND badge_id = ?');
                    $remove->execute(array($fetch['clef'], 'VIP'));
                }
            }
        }
    }

    /************************************* * Fonction de couleur des mises à jour *************************************/
    function ColorUpdate($value) {
        if($value == 'En cours') {
            return 'En cours';
        } elseif($value == 'Terminé') {
            return 'Terminé';
        }
    }

    /**************************************** * Fonction pour les préférences de profil ****************************************/
    function AddPrefProfile() {
        $db = Connect::getDB();
        if(isset($_SESSION['account']['clef'])) {
            $req = $db->prepare('SELECT * FROM sting_users_settings WHERE clef_user = ?');
            $req->execute(array($_SESSION['account']['clef'])); $rowCount = $req->rowCount();
            if($rowCount == 0) {
                $insert = $db->prepare('INSERT INTO sting_users_settings(clef_user, statut) VALUES(?, ?)');
                $insert->execute(array($_SESSION['account']['clef'], '1'));
            }
        }
    }

    /**************** * Fonction d'avis ****************/
    function Etoile($value) {
        if($value == 0) {
            return ' ';
        } elseif($value == 1) {
            return ' ';
        } elseif($value == 2) {
            return ' ';
        } elseif($value == 3) {
            return ' ';
        } elseif($value == 4) {
            return ' ';
        } elseif($value == 5) {
            return ' ';
        }
    }
}

$Controller = new Controller();
