<?php
/*********************************************
*  Class obligatoire au fonctionnement du site
*********************************************/
class StingCMS extends Controller

{
	/**********************
	* Fonction de connexion
	**********************/
	function Login()
	{
		$db = Connect::getDB();
		$email = $this->Security($_POST['email']);
		$password = $this->Security($_POST['password']);
		$password_crypt = $this->Encryption($_POST['password']);
		if (!isset($_SESSION['account'])) {
			if (!empty($email)) {
				if (!empty($password)) {
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$req = $db->prepare('SELECT * FROM sting_users WHERE email = ?');
						$req->execute(array(
							$email
						));
						$fetch = $req->fetch();
						$rowCount = $req->rowCount();
						if ($rowCount == 1) {
							if ($fetch['password'] == $password_crypt) {
									if ($fetch['account_confirmed'] == '2') {
										$response = 'Votre compte est désactivé';
									}
									else {
										if ($this->Settings('maintenance') == 0 OR $fetch['rank'] >= 5) {
											$req = $db->prepare('SELECT * FROM sting_users WHERE email = ? AND password = ?');
											$req->execute(array(
												$email,
												$password_crypt
											));
											$fetch = $req->fetch();
											$_SESSION['account'] = array(
												'id' => $fetch['id'],
												'username' => $fetch['username'],
												'password' => $fetch['password'],
												'email' => $fetch['email'],
												'clef' => $fetch['clef'],
												'rank' => $fetch['rank'],
												'gold' => $fetch['gold'],
												'avatar' => $fetch['avatar'],
												'registration_ip' => $fetch['registration_ip'],
												'last_ip' => $fetch['last_ip'],
												'last_connection' => date('Y-m-d H:i:s') ,
												'registration' => $fetch['registration'],
												'account_confirmed' => $fetch['account_confirmed'],
											);
											$edit = $db->prepare('UPDATE sting_users SET last_connection = ? WHERE email = ? AND password = ?');
											$edit->execute(array(
												date('Y-m-d H:i:s') ,
												$email,
												$password_crypt
											));
											$req = $db->prepare('INSERT INTO sting_logs(clef_user, action, date, ip) VALUES(?, ?, ?, ?)');
														$req->execute(array(
															$fetch['clef'],
															'Connexion à ' . $this->Settings('name') . '',
															date('Y-m-d H:i:s') ,
															$this->GetIP()
														));
											
											$badge = $db->prepare('SELECT * FROM sting_users_badges WHERE badge_id = ? AND clef_user = ?');
											$badge->execute(array('WELCOME', $fetch['clef']));
											$rowc = $badge->rowCount();
											if($rowc == 0) {
												$ins = $db->prepare('INSERT INTO sting_users_badges(clef_user, badge_id, date) VALUES(?, ?, ?)');
												$ins->execute(array($fetch['clef'], 'WELCOME', date('Y-m-d H:i:s')));
											}
											
											$response = 'success';
										}
										else {
											$response = 'Nous sommes en maintenance';
										}
									}
								
							}
							else {
								$response = 'Votre mot de passe est incorrect';
							}
						}
						else {
							$response = 'Cette email est incorrect';
						}
					}
					else {
						$response = 'Votre email est invalide';
					}
				}
				else {
					$response = 'Veuillez entrer un mot de passe';
				}
			}
			else {
				$response = 'Veuillez entrer une email';
			}
		}
		else {
			$response = 'Vous êtes déjà connecté';
		}

		echo json_encode(['response' => $response]);
	}

	/***********************
	* Fonction d'inscription
	***********************/
	function Register()
	{
		$db = Connect::getDB();
		$username = $this->Security($_POST['username']);
		$password = $this->Security($_POST['password']);
		$password_crypt = $this->Encryption($_POST['password']);
		$email = $this->Security($_POST['email']);
		$secret = '6Lcm4iITAAAAAP7itK6rAhl7L8BEv2_IT-qwJpiU';
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
			$responseData = json_decode($verifyResponse);
			$responseData->success = true;
			if($responseData->success) {
				if (!empty($username)) {
					if (!empty($password)) {
						if (!empty($email)) {
							if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$req = $db->prepare('SELECT last_ip FROM sting_users WHERE last_ip = ?');
								$req->execute(array($this->GetIP()));
								$rowCount = $req->rowCount();
								if($rowCount == 0) {
								$req = $db->prepare('SELECT username FROM sting_users WHERE username = ?');
								$req->execute(array(
									$username
								));
								$rowCount = $req->rowCount();
								if ($rowCount == 0) {
									$req = $db->prepare('SELECT email FROM sting_users WHERE email = ?');
									$req->execute(array(
										$email
									));
									$rowCount = $req->rowCount();
									if ($rowCount == 0) {
										if (preg_match('#^[\p{L}\p{Nd}\s]+$#u', $username)) {
											if (iconv_strlen($username) <= 25) {
												if ($this->Settings('maintenance') == 0) {
													if ($this->Settings('register') == 1) {
														$clef = mt_rand(1000, 99999999);
														$req = $db->prepare('INSERT INTO sting_users(username, password, email, clef, rank, gold, avatar, registration_ip, last_ip, last_connection, registration, account_confirmed) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
														$req->execute(array(
															$username,
															$password_crypt,
															$email,
															$clef,
															'1',
															'0',
															'/avatar/welcome.jpg',
															$this->GetIP() ,
															$this->GetIP() ,
															date('Y-m-d H:i:s') ,
															date('Y-m-d H:i:s') ,
															'0'
														));
														$req = $db->prepare('INSERT INTO sting_logs(clef_user, action, date, ip) VALUES(?, ?, ?, ?)');
														$req->execute(array(
															$clef,
															'Inscription à ' . $this->Settings('name') . '',
															date('Y-m-d H:i:s') ,
															$this->GetIP()
														));
														$req = $db->prepare('INSERT INTO sting_confirm_email(email, clef, date, expiration) VALUES(?, ?, ?, ?)');
														$req->execute(array(
															$email,
															$clef,
															date('Y-m-d H:i:s') ,
															date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 hour"))
														));
														$req = $db->prepare('INSERT INTO sting_users_badges(clef_user, badge_id, date) VALUES(?, ?, ?)');
														$req->execute(array(
															$clef, 
															'WELCOME', 
															date('Y-m-d H:i:s')
														));
									
														$bot = $db->prepare('INSERT INTO sting_profile_messages(clef_user, profile_id, message, date) VALUES(?, ?, ?, ?)');
														$bot->execute(array('2', $clef, 'Bienvenue sur YourHabbo <b>'.$username.'</b> :)', date('Y-m-d H:i:s')));
														$notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, open, date) VALUES(?, ?, ?, ?, ?)');
														$notif->execute(array($clef, '/avatar/bot.png', '<b>BOT</b> vient de publier un message sur votre profil', '0', date('Y-m-d H:i:s')));
														
														$_SESSION['account'] = array(
															'clef' => $clef
														);
														$response = 'success';
													}
													else {
														$response = 'Les inscriptions sont désactivé';
													}
												}
												else {
													$response = 'Nous sommes en maintenance';
												}
											}
											else {
												$response = 'Votre nom d\'utilisateur est trop long';
											}
										}
										else {
											$response = 'Votre nom d\'utilisateur contient des caractères interdit';
										}
										
									}
									else {
										$response = 'Cette email est déjà prise';
									}
								}
								else {
									$response = 'Ce nom d\'utilisateur est déjà pris';
								}
									}
									else {
										$response = 'Votre IP possède déjà un compte';
									}
							}
							else {
								$response = 'Veuillez entrer une email valide';
							}
						}
						else {
							$response = 'Veuillez entrer une email';
						}
					}
					else {
						$response = 'Veuillez entrer un mot de passe';
					}
				}
				else {
					$response = 'Veuillez entrer un nom d\'utilisateur';
				}
			}
			else {
				$response = 'Le Captcha est incorrect';
			}

		echo json_encode(['response' => $response]);
	}
	
	/*****************************
	* Fonction mot de passe oublié
	*****************************/
	function Forgot()
	{
		$db = Connect::getDB();
		$email = $this->Security($_POST['email']);
		$captcha = $this->Security($_POST['captcha']);
		if (!empty($email)) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$secret = '6Lcm4iITAAAAAP7itK6rAhl7L8BEv2_IT-qwJpiU';
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
			$responseData = json_decode($verifyResponse);
			if($responseData->success) {
							$req = $db->prepare('SELECT email, clef, username FROM sting_users WHERE email = ?');
							$req->execute(array(
								$email
							));
							$rowCount = $req->rowCount();
							$fetch = $req->fetch();
							if ($rowCount == 1) {
								$req = $db->prepare('INSERT INTO sting_logs(clef_user, action, date, ip) VALUES(?, ?, ?, ?)');
								$req->execute(array(
									$fetch['clef'],
									'Réinitialisation du mot de passe',
									date('Y-m-d H:i:s') ,
									$this->GetIP()
								));
								$newpass = $this->GeneratorLetters(8);
								$req = $db->prepare('UPDATE sting_users SET password = ? WHERE email = ?');
								$req->execute(array(
									$this->Encryption($newpass) ,
									$email
								));
								$header = "MIME-Version: 1.0\r\n";
								$header.= 'From:"' . $this->Settings('name') . '"<service@' . $this->Settings('url') . '>' . "\n";
								$header.= 'Content-Type:text/html; charset="uft-8"' . "\n";
								$header.= 'Content-Transfert-Encoding: 8bit';
								$message = '<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>' . $this->Settings('name') . ' - Nouveau mot de passe</title>
</head>
<body">
	<div style="margin:0 auto;max-width:700px;">
		<table class=""  style="width:100%;font-size:0px;" >
			<tbody>
				<tr>
					<td style="text-align:center;vertical-align:top;font-size:0;padding:20px 0;padding-top:0px;padding-bottom:24px;"></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div style="margin:0 auto;max-width:700px;">
		<table class=""  style="width:100%;font-size:0px;background:#00a8ff;" >
			<tbody>
				<tr>
					<td style="text-align:center;vertical-align:top;font-size:0;padding:1px;">
						<div style="vertical-align:top;display:inline-block;font-size:13px;text-align:left;width:100%;">
							<table style="background:white;width:100%;">
								<tbody>
									<tr>
										<td style="font-size:0;padding:30px 30px 18px;" >
											<div style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:22px;line-height:22px;">
												Réinitialisation du mot de passe
											</div>
										</td>
									</tr>
									<tr>
										<td style="font-size:0;padding:0 30px 16px;" >
											<div style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:14px;line-height:22px;">
												<b>' . $fetch['username'] . '</b>, votre nouveau mot de passe est <b>' . $newpass . '</b>
											</div>
										</td>
									</tr>
									<tr>
										<td style="font-size:0;padding:0 30px 6px;" >
											<div style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:14px;line-height:22px;">
												Si vous n\'avez pas demander la réinitialisation de votre mot de passe, merci de nous contacter.
											</div>
										</td>
									</tr>
									<tr>
										<td style="font-size:0;padding:0 30px 30px 30px;" >
											<div style="cursor:auto;color:#000000;font-family:Proxima Nova, Arial, Arial, Helvetica, sans-serif;font-size:14px;line-height:22px;">
												Merci<br/>
												- L\'équipe ' . $this->Settings('name') . '
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>';
								mail($email, 'Nouveau mot de passe', $message, $header);
								$response = 'success';
							}
							else {
								$response = 'Cette email n\'existe pas';
							}
						}
						else {
							$response = 'Le Captcha est incorrect';
						}
				}
				else {
					$response = 'Cette email est incorrect';
				}
		}
		else {
			$response = 'Veuillez entrer une email';
		}

		echo json_encode(['response' => $response]);
	}

	/****************************
	* Fonction confirmation email
	****************************/
	function ConfirmEmail()
	{
		$db = Connect::getDB();
		$clef = $this->Security($_GET['key']);
		$email = $this->Security($_GET['email']);
		if (!empty($clef)) {
			if (!empty($email)) {
				$req = $db->prepare('SELECT * FROM sting_confirm_email WHERE clef = ?');
				$req->execute(array(
					$clef
				));
				$rowCount = $req->rowCount();
				$fetch = $req->fetch();
				if ($rowCount == 1) {
					if ($email == $fetch['email']) {
						$req = $db->prepare('UPDATE sting_users SET account_confirmed = ? WHERE email = ?');
						$req->execute(array(
							'1',
							$email
						));
						$req = $db->prepare('DELETE FROM sting_confirm_email WHERE email = ?');
						$req->execute(array(
							$email
						));
						$_SESSION['response'] = '<div class="erreur">Votre email a été vérifié</div>';
					}
					else {
						$_SESSION['response'] = '<div class="erreur">Cette email ne correspond pas avec la key</div>';
					}
				}
				else {
					$_SESSION['response'] = '<div class="erreur">Cette key n\'existe pas</div>';
				}
			}
			else {
				$_SESSION['response'] = '<div class="erreur">Veuillez saisir une email</div>';
			}
		}
		else {
			$_SESSION['response'] = '<div class="erreur">Veuillez saisir une key</div>';
		}

		echo $_SESSION['response'];
	}

	/**************************************
	* Fonction de chargement de la shoutbox
	**************************************/
	function ChargeShoutbox()
	{
		$db = Connect::getDB();
		if (isset($_SESSION['account'])) {
			$Shoutbox = $db->prepare('SELECT * FROM sting_shoutbox WHERE date <= ? ORDER BY date DESC LIMIT 30');
			$Shoutbox->execute(array(date('Y-m-d H:i:s')));
			while ($shout = $Shoutbox->fetch()) {
				$InfoAccount = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				$InfoAccount->execute(array(
					$shout['clef_user']
				));
				$fetch = $InfoAccount->fetch();
				echo '<li class="' . ($shout['clef_user'] != $_SESSION['account']['clef'] ? 'chat__bubble chat__bubble--rcvd chat__bubble--stop"' : '') . '' . ($shout['clef_user'] == $_SESSION['account']['clef'] ? 'chat__bubble chat__bubble--sent"' : '') . '">' . ($shout['clef_user'] != $_SESSION['account']['clef'] ? '<a href="' . $fetch['username'] . '" class="' . $this->RankColor($fetch['rank']) . '">' . $fetch['username'] . '' : '') . '</a> ' . ($shout['clef_user'] != $_SESSION['account']['clef'] ? '-' : '') . ' <small>' . $this->ConvertTime($shout['date']) . '</small> ' . ($shout['clef_user'] != $_SESSION['account']['clef'] ? '- <span style="opacity: 1;"  onclick="Tag(\'' . $fetch['username'] . '\')" id="tagShoutbox"></span>' : '') . ' :  ' . wordwrap($shout['message'], 45, "\n", true) . '</li>';
			}
		}
	}
	
	/************************************************
	* Fonction de suppression des caractères spéciaux
	************************************************/
	function KickSpeciaux($in) 
	{
		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[ ]@i','@[^a-zA-Z0-9_]@');
		$replace = array ('e','a','i','u','o','c','_','');
		return preg_replace($search, $replace, $in);
	}

	/***************************************************
	* Fonction d'insertion d'un message dans la shoutbox
	***************************************************/
	function InsertMessage()
	{
		$db = Connect::getDB();
		$message = $this->Security($_POST['message']);
		if (isset($_SESSION['account'])) {
			if (!empty($message)) {
				$VerifDate = $db->prepare('SELECT * FROM sting_shoutbox WHERE clef_user = ? ORDER by date DESC');
				$VerifDate->execute(array(
					$_SESSION['account']['clef']
				));
				$fetch = $VerifDate->fetch();
				$rowCount = $VerifDate->rowCount();
				if($_SESSION['account']['rank'] == 1) {
					$DateLimite = date("Y-m-d H:i:s", strtotime('' . $fetch['date'] . '' . " +10 seconds"));
				} elseif($_SESSION['account']['rank'] == 2) {
					$DateLimite = date("Y-m-d H:i:s", strtotime('' . $fetch['date'] . '' . " +5 seconds"));
				} elseif($_SESSION['account']['rank'] == 3) {
					$DateLimite = date("Y-m-d H:i:s", strtotime('' . $fetch['date'] . '' . " +4 seconds"));
				} elseif($_SESSION['account']['rank'] == 4) {
					$DateLimite = date("Y-m-d H:i:s", strtotime('' . $fetch['date'] . '' . " +3 seconds"));
				} elseif($_SESSION['account']['rank'] >= 5) {
					$DateLimite = date("Y-m-d H:i:s", strtotime('' . $fetch['date'] . '' . " +2 seconds"));
				}
				$dateactuel = date('Y-m-d H:i:s');
				if ($DateLimite <= $dateactuel OR $rowCount == 0) {
					if (iconv_strlen($message) <= 300) {
						if($this->Settings('shoutbox') == '1') {
						$Banned = $db->prepare('SELECT username, type FROM sting_bans WHERE username = ? AND type = ?');
						$Banned->execute(array(
							$_SESSION['account']['username'],
							'Shoutbox'
						));
						$rowCountBanned = $Banned->rowCount();
						if ($rowCountBanned == 0 OR $_SESSION['account']['username'] == 'Mathis') {
							$BannedIP = $db->prepare('SELECT ip, type FROM sting_bans WHERE ip = ? AND type = ?');
							$BannedIP->execute(array(
								$this->GetIP(),
								'IP'
							));
							$rowCountBannedIP = $BannedIP->rowCount();
							if($rowCountBannedIP == 0 OR $this->GetIP() == '82.238.56.127') {
							$response = 'success';
								$bot = explode('@BOT', $message);
							if ($_SESSION['account']['rank'] >= 5) {
								$vider = explode('/vider', $message);
								$ban = explode('/ban', $message);
								$parl = explode('/bot', $message);
								$ipban = explode('/ipban', $message);
								$averto = explode('/avert', $message);
								$unban = explode('/unban', $message);
							}
							if($_SESSION['account']['rank'] >= 8) {
								$kdo = explode('/kdo', $message);
							}

							if (isset($vider['1'])) {
								$CommandeVide = $db->query('TRUNCATE sting_shoutbox');
								$CommandeVide = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$CommandeVide->execute(array(
									$_SESSION['account']['clef'],
									'<i>vient de vider la shoutbox.</i>',
									date('Y-m-d H:i:s')
								));
							} elseif ($unban['1']) {
								$user = $unban['1'];
								$CommandeBan = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$CommandeBan->execute(array(
									$_SESSION['account']['clef'],
									'<i>vient de débannir <strong style="color: black">' . $user . '</strong> de la Shoutbox.</i>',
									date('Y-m-d H:i:s')
								));
								$Delete = $db->prepare('DELETE FROM sting_bans WHERE username = ?');
								$Delete->execute(array(trim($user)));
							} elseif ($ban['1']) {
								$user = $ban['1'];
								$ip = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
								$ip->execute(array(trim($user)));
								$fetch = $ip->fetch();
								$DateExpiration = date("Y-m-d H:i:s", strtotime('' . date('Y-m-d H:i:s') . '' . " +10800 seconds"));
								$GoBan = $db->prepare('INSERT INTO sting_bans(username, type, ip, date_expiration) VALUES(?, ?, ?, ?)');
								$GoBan->execute(array(
									trim($user) ,
									'Shoutbox',
									$fetch['last_ip'],
									$DateExpiration
								));
								$CommandeBan = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$CommandeBan->execute(array(
									$_SESSION['account']['clef'],
									'<i>vient de bannir <strong style="color: black">' . $user . '</strong> de la Shoutbox.</i>',
									date('Y-m-d H:i:s')
								));
								$Delete = $db->prepare('DELETE FROM sting_shoutbox WHERE clef_user = ?');
								$Delete->execute(array($fetch['clef']));
							} elseif ($parl['1']) {
								$msg = $parl['1'];
								$CommandeBan = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$CommandeBan->execute(array(
									2,
									$msg,
									date('Y-m-d H:i:s')
								));
							} elseif($ipban['1']) {
								$user = $ipban['1'];
								$ip = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
								$ip->execute(array(trim($user)));
								$fetch = $ip->fetch();
									$DateExpiration = date("Y-m-d H:i:s", strtotime('' . date('Y-m-d H:i:s') . '' . " +86400 seconds"));
									$GoBan = $db->prepare('INSERT INTO sting_bans(username, type, ip, date_expiration) VALUES(?, ?, ?, ?)');
									$GoBan->execute(array(
										trim($user) ,
										'IP',
										$fetch['last_ip'],
										$DateExpiration
									));
									$CommandeBan = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBan->execute(array(
										$_SESSION['account']['clef'],
										'<i>vient de bannir l\'IP de <strong style="color: black">' . $user . '</strong>.</i>',
										date('Y-m-d H:i:s')
									));
									$Delete = $db->prepare('DELETE FROM sting_shoutbox WHERE clef_user = ?');
									$Delete->execute(array($fetch['clef']));
							}
							elseif($bot['1']) {
								$text = $bot['1'];
								$msg = strtolower(trim($text));
								$Message = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$Message->execute(array(
									$_SESSION['account']['clef'],
									$this->Smiley($message) ,
									date('Y-m-d H:i:s')
								));
								
								$bjr = $db->prepare('SELECT phrase, type FROM sting_bots WHERE type = ?');
								$bjr->execute(array('Bonjour'));
								$fetch = $bjr->fetchAll(PDO::FETCH_COLUMN);
								
								$heure = $db->prepare('SELECT phrase, type FROM sting_bots WHERE type = ?');
								$heure->execute(array('Heure'));
								$fetch2 = $heure->fetchAll(PDO::FETCH_COLUMN);
								
								$recru = $db->prepare('SELECT phrase, type FROM sting_bots WHERE type = ?');
								$recru->execute(array('Recrutement'));
								$fetch3 = $recru->fetchAll(PDO::FETCH_COLUMN);

								if(in_array($msg, $fetch)) {
									$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBot->execute(array(
										'2',
										'Salut '.$_SESSION['account']['username'].' '.$this->Smiley(':)').'',
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
									));
								}  elseif(in_array($msg, $fetch2)) {
									$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBot->execute(array(
										'2',
										'Il est actuellement '.date('H:i').'',
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
									));
								} elseif(in_array($msg, $fetch3)) {
									if($this->Settings('recrutement') == 0) {
										$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
										$CommandeBot->execute(array(
											'2',
											'Les recrutements sont actuellement fermés.',
											date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
										));	
									} elseif($this->Settings('recrutement') == 1) {
										$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
										$CommandeBot->execute(array(
											'2',
											'Les recrutements sont ouvert ! Envoie ton C.V à <a href="mailto:izikore@gmail.com" style="color: black">izikore@gmail.com</a>. Bonne chance !',
											date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
										));
									}
								} elseif($msg == 'hacintmfdp62') {
									$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBot->execute(array(
										'2',
										'Bravo <b>'.$_SESSION['account']['username'].'</b>, tu viens de trouver la commande caché qui sert à bannir Haci.',
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
									));
									
									$info = $db->prepare('SELECT last_ip, username FROM sting_users WHERE username = ?');
									$info->execute(array('Haci'));
									$fetch = $info->fetch();
									
									$GoBan = $db->prepare('INSERT INTO sting_bans(username, type, raison, ip, date_expiration) VALUES(?, ?, ?, ?, ?)');
									$GoBan->execute(array(
										'Haci',
										'Site&IP',
										'Aucune',
										$fetch['last_ip'],
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +3600 minutes"))
									));
									$GoBan = $db->prepare('INSERT INTO sting_bans(username, type, raison, ip, date_expiration) VALUES(?, ?, ?, ?, ?)');
									$GoBan->execute(array(
										'Haci',
										'Shoutbox',
										'Aucune',
										$fetch['last_ip'],
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +3600 minutes"))
									));
								} elseif($msg == 'point' OR $msg == 'points') {
									$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBot->execute(array(
										'2',
										''.$_SESSION['account']['username'].' possède '.$_SESSION['account']['gold'].' point'.$this->isMany($_SESSION['account']['gold']).'',
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
									));
								} elseif($msg == 'ok' OR $msg == 'OK' OR $msg == 'Ok') {
									$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBot->execute(array(
										'2',
										'Je refuse que tu me dises "'.trim($msg).'" '.$_SESSION['account']['username'].'',
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
									));
								} else {
									$CommandeBot = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeBot->execute(array(
										'2',
										'Désolé <i>'.$_SESSION['account']['username'].'</i> mais je n\'ai pas compris.',
										date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 second"))
									));
								}
							}
								elseif($averto['1']) {
								$user = $averto['1'];
								$Avert = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
								$Avert->execute(array(trim($user)));
								$fetch = $Avert->fetch();
									$check = $db->prepare('SELECT * FROM sting_warnings WHERE clef_user = ?');
									$check->execute(array($fetch['clef']));
									$rowCount = $check->rowCount();
									if($rowCount == 0) {
									$Av = $db->prepare('INSERT INTO sting_warnings(clef_user, points, date) VALUES(?, ?, ?)');
									$Av->execute(array(
										 $fetch['clef'],
											10,
										date('Y-m-d H:i:s')
									));
									} elseif($rowCount != 0) {
										$Av = $db->prepare('UPDATE sting_warnings SET points = points + ? WHERE clef_user = ?');
										$Av->execute(array(10, $fetch['clef']));
									}
									
									$CommandeMsg = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$CommandeMsg->execute(array(
										$_SESSION['account']['clef'],
										'<i>a donné un avertissement à <strong style="color: black">' . $user . '</strong>.</i>',
										date('Y-m-d H:i:s')
									));
							} elseif (isset($kdo['1'])) {
								
								$gold = mt_rand(10, 50);
								$time = mt_rand(10, 120);
								$code = $this->CodeKDOGo(8);
								
								$Kdo = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$Kdo->execute(array(
									2,
									'Préparation d\'un code KDO de '.$gold.' points en cours ...',
									date('Y-m-d H:i:s')
								));
								
								$Kdo = $db->prepare('INSERT INTO sting_codes_kdo(code, gold) VALUES(?, ?)');
								$Kdo->execute(array($code, $gold));
									
								$Kdo = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
								$Kdo->execute(array(
									2,
									'Voici le code KDO <i><b>'.$code.'</b></i>',
									date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +".$time." seconds"))
								));
							}
							else {
									$Message = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
									$Message->execute(array(
										$_SESSION['account']['clef'],
										$this->Smiley($message) ,
										date('Y-m-d H:i:s')
									));
									}
								}
						else {
							$response = 'Votre IP a été banni de la Shoutbox';
						}
						}
						else {
							$response = 'Vous &ecirc;tes banni de la Shoutbox';
						}
						} else {
							$response = 'La shoutbox est désactivé';
						}
					}
					else {
						$response = 'Votre message est trop long';
					}
				}
				else {
					if($_SESSION['account']['rank'] == 1) {
						$response = 'Un message toutes les 10 secondes';
					} elseif($_SESSION['account']['rank'] == 2) {
						$response = 'Un message toutes les 5 secondes';
					} elseif($_SESSION['account']['rank'] == 3) {
						$response = 'Un message toutes les 4 secondes';
					} elseif($_SESSION['account']['rank'] == 4) {
						$response = 'Un message toutes les 3 secondes';
					} elseif($_SESSION['account']['rank'] >= 5) {
						$response = 'Un message toutes les 2 secondes';
					}
				}
			}
			else {
				$response = 'Veuillez entrer un message';
			}
		}
		else {
			$response = 'Vous devez &ecirc;tre connect&eacute;';
		}

		echo json_encode(['response' => $response]);
	}

	/*******************
	* Fonction d'annonce
	*******************/
	function Annonce()
	{
		$db = Connect::getDB();
		if (isset($_SESSION['account'])) {
			$req = $db->query('SELECT * FROM sting_notice');
			$fetch = $req->fetch();
			echo $fetch['content'];
		}
	}

	/****************************************************
	* Fonction de récupération des utilisateurs connectés
	****************************************************/
	function UsersOnline()
	{
		$db = Connect::getDB();
		if (isset($_SESSION['account'])) {
			$time_session = 7;
			$time_actuel = date('U');
			$UserID = $_SESSION['account']['clef'];
			$CheckID = $db->prepare('SELECT * FROM sting_users_online WHERE clef_user = ?');
			$CheckID->execute(array(
				$UserID
			));
			$rowCount = $CheckID->rowCount();
			if ($rowCount == 0) {
				$AddUser = $db->prepare('INSERT INTO sting_users_online(clef_user, time, last_connection) VALUES(?, ?, ?)');
				$AddUser->execute(array(
					$UserID,
					$time_actuel,
					date('Y-m-d H:i:s')
				));
			}
			else {
				$UpdateUser = $db->prepare('UPDATE sting_users_online SET time = ?, last_connection = ? WHERE clef_user = ?');
				$UpdateUser->execute(array(
					$time_actuel,
					date('Y-m-d H:i:s') ,
					$UserID
				));
			}

			$ExpireSession = $time_actuel - $time_session;
			$Online = $db->prepare('SELECT COUNT(*) AS nb FROM sting_users_online WHERE time > ?');
			$Online->execute(array(
				$ExpireSession
			));
			$fetch = $Online->fetch();
			$ProfileID = $db->prepare('SELECT * FROM sting_users_online WHERE time > ?');
			$ProfileID->execute(array(
				$ExpireSession
			));
			if ($fetch['nb'] <= 1) {
				echo '<div class="panel panel-primary">
							<div class="panel-heading">
								<h5 class="panel-title"><i class="fa fa-user"></i> Membre connecté</h5>
								<div class="elements">
									Il y a ' . $fetch['nb'] . ' connecté
								</div>
							</div>

							<div class="panel-body">';
				while ($a = $ProfileID->fetch()) {
					$Account1 = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
					$Account1->execute(array(
						$a['clef_user']
					));
					$fetch6 = $Account1->fetch();
					echo '<span class="' . $this->RankColor($fetch6['rank']) . '">' . $fetch6['username'] . '</span>';
				}

				echo '</div>
						</div>';
			}
			else {
				echo '<div class="panel panel-primary">
							<div class="panel-heading">
								<h5 class="panel-title"><i class="fa fa-users"></i> Membres connectés</h5>
								<div class="elements">
									Il y a ' . $fetch['nb'] . ' connectés
								</div>
							</div>

							<div class="panel-body">';
				while ($a = $ProfileID->fetch()) {
					$Account1 = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
					$Account1->execute(array(
						$a['clef_user']
					));
					$fetch6 = $Account1->fetch();
					echo '<span class="' . $this->RankColor($fetch6['rank']) . '">' . $fetch6['username'] . '</span>, ';
				}

				echo '</div>
						</div>';
			}
		}
	}
	
	/****************
	* Fonction de pub
	****************/
	function PubVIP()
	{
		$db = Connect::getDB();
        
        if(isset($_SESSION['account'])) {
            
            $req = $db->query('SELECT * FROM sting_pubs');
            $rowCount = $req->rowCount();
            
            if($rowCount == 0) {
                echo '<center><span style="font-size: 14px">Aucun abonnement VIP n\'est disponible</span></center>';
            } else {
                $req = $db->query('SELECT id FROM sting_pubs');
            
            $subs = $req->fetchAll(PDO::FETCH_COLUMN);
            $subs_id = mt_rand(1,(count($subs)));
            
            $req = $db->prepare('SELECT * FROM sting_pubs WHERE id = ?');
            $req->execute(array($subs_id));
            $fetch = $req->fetch();
            
            echo '<div class="jumbotron text-center bg-white">
                        <h1 class="text-inverse" data-step="1" data-position="bottom-right-aligned">'.$fetch['title'].'</h1>
                        <p class="lead m-b-20" data-step="2" data-hintposition="top-middle" data-position="bottom-right-aligned"><b class="groupeVIPBasic">'.$_SESSION['account']['username'].'</b> '.$fetch['description'].'</p>
                        <p><a class="btn btn-lg btn-primary" href="'.$fetch['bouton_lien'].'">'.$fetch['bouton_name'].'</a></p>
                    </div>
            ';
            }
            
        }
		
	}
	
	/*******************************
	* Fonction d'affichage des plans
	*******************************/
	function Plans()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
		
		$req = $db->query('SELECT * FROM sting_plans');
		
		while($r = $req->fetch()) {
			echo '<div class="col-md-3 col-sm-3">
						<div class="pricingTable bg-blue">
							<div class="pricingTable-header">
								<div class="heading">
									<h3>'.$r['name'].'</h3>
								</div>
								<span class="price-value">'.$r['price'].'P <span>/mois</span></span>
							</div>
							<div class="pricingContent">
								<ul>
									<li>'.$r['cms'].' <i class="icon-check"></i></li>
									<li>'.$r['émulateur'].' <i class="icon-check"></i></li>
									<li>'.$r['access'].' <i class="icon-check"></i></li>
								</ul>
							</div>
							<div class="pricingTable-sign-up">
								<a href="?id='.$r['id'].'" class="btn bg-blue btn-block">Commander</a>
								<div style="height: 5px"></div>
								<a href="'.$r['demo'].'" class="btn btn-danger btn-block">Démo</a>
							</div>
						</div>
					</div>';
			}
		}
	}
	
	/****************************
	* Fonction d'achat d'un rétro
	****************************/
	function Order()
	{
		$db = Connect::getDB();
		
		$id = (int) $_GET['id'];
		
		if(isset($_SESSION['account'])) {
			echo '<div class="col-md-8">
			<div class="panel panel-primary">
							<div class="panel-heading">
								<h5 class="panel-title"><i class="fa fa-shopping-cart"></i> Commande d\'un rétro Plan '.$_GET['id'].'</h5>
								<div class="elements">
									<span class="heading-text" data-toggle="modal" data-target="#prix"><i class="icon-circle-right2"></i>&nbsp; Voir les prix des noms de domaine</span>
								</div>
							</div>

							<div class="panel-body">
							<form method="post" id="order" action="/core/action/action.php?to=order">
								<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Nom du rétro</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Nom du rétro">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Extension</label>
											<select name="extension" id="extension" class="form-control">
												<option value=".fr">.fr</option>
												<option value=".eu">.eu</option>
												<option value=".com">.com</option>
												<option value=".net">.net</option>
												<option value=".org">.org</option>
												<option value=".xyz">.xyz</option>
                                                <option value=".ga">.ga</option>
                                                <option value".cf">.cf</option>
                                                <option value=".tk">.tk</option>
                                                <option value=".ml">.ml</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label class="control-label">Options</label>
											<select name="option" id="option" class="form-control">
												<option value="Aucune">Aucune</option>
												<option value="SWF">SWF (200 Points)</option>
												<option value="Session">Session (200 Points)</option>
												<option value="SWF + Session">SWF + Session (400 Points)</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label class="control-label">Type de livraison</label>
											<select name="type" id="option" class="form-control">
												<option value="Normal">Normal</option>
												<option value="Premium">Premium (100 Points)</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<button class="btn btn-primary btn-block">Commander mon rétro-habbo</button>
										</div>
									</div>
								</div>
								
							</div>
							</form>
					</div>
					<div class="col-md-4">
						<div class="panel panel-flat">
									<div class="panel-body no-padding">
										<div class="team bg-flatie-700 no-border border-flat">
											<div class="header-bg"><img src="'.$_SESSION['account']['avatar'].'" class="header-blur"></div>
											<div class="avatar">
												<img src="'.$_SESSION['account']['avatar'].'" style="height: 180px; width: 180px">
											</div>
											<div class="content m-b-20">
												<h4 class="no-margin text-uppercase m-t-10">'.$_SESSION['account']['username'].'</h4>
												<p class="text-uppercase">Vous avez '.$_SESSION['account']['gold'].' Point'.$this->isMany($_SESSION['account']['gold']).'</p>
											</div>
										</div>
									</div>
								</div>
					</div>';
			
		}
	}
	
	/***********************************
	* Fonction de validation de commande
	***********************************/
	function TermineOrder()
	{
		$db = Connect::getDB();
		
		$name = $this->Security($_POST['name']);
		$extension = $this->Security($_POST['extension']);
		$option = $this->Security($_POST['option']);
		$type = $this->Security($_POST['type']);
		
		if(isset($_SESSION['account'])) {
			if(!empty($name)) {
				if (preg_match('#^[a-zA-Z0-9 \-]+$#', $name)) {
					$req = $db->prepare('SELECT * FROM sting_orders WHERE name = ? AND extension = ?');
					$req->execute(array($name, $extension));
					$rowCount = $req->rowCount();
					$req2 = $db->prepare('SELECT * FROM sting_services WHERE name = ? AND extension = ?');
					$req2->execute(array($name, $extension));
					$rowCount2 = $req2->rowCount();
					if($rowCount == 0 AND $rowCount2 == 0) {
						if($extension == '.fr' OR $extension == '.eu' OR $extension == '.com' OR $extension == '.net' OR $extension == '.xyz' OR $extension == '.org' OR $extension == '.ga' OR $extension == '.cf' OR $extension == '.tk' OR $extension == '.ml') {
							if($option == 'SWF' OR $option == 'Session' OR $option == 'SWF + Session' OR $option == 'Aucune') {
								if($type == 'Normal' OR $type == 'Premium') {
									if($type == 'Normal') {
										$req = $db->prepare('SELECT * FROM sting_plans WHERE id = ?');
										$req->execute(array($_SESSION['getid']));
										$fetch = $req->fetch();
								
										$req2 = $db->prepare('SELECT * FROM sting_extensions WHERE extension = ?');
										$req2->execute(array($extension));
										$fetch2 = $req2->fetch();
								
										$req3 = $db->prepare('SELECT * FROM sting_options WHERE content = ?');
										$req3->execute(array($option));
										$fetch3 = $req3->fetch();
								
										$price = $fetch['price'] + $fetch2['price'] + $fetch3['price'];
								
										if($_SESSION['account']['gold'] >= $price) {
										
											/* Ajout de la commande */
											$req = $db->prepare('INSERT INTO sting_orders(clef_user, name, extension, plan, options, date_order, état, type) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
											$req->execute(array($_SESSION['account']['clef'], $name, $extension, 'Plan '.$_SESSION['getid'].'', $option, date('Y-m-d H:i:s'), 'En attente', 'Normal'));
											/* Fin d'ajout de la commande */
									
											/* On retire les points */
											$req = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
											$req->execute(array($price, $_SESSION['account']['clef']));
											/* Fin du retirage des points */
									
											/* Ajout du log */
											$req = $db->prepare('INSERT INTO sting_logs(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
											$req->execute(array($_SESSION['account']['clef'], 'Commande de '.$name.''.$extension.'', $this->GetIP(), date('Y-m-d H:i:s')));
											/* Fin d'ajout du log */
									
											$response = 'success';
										

										} else {
											$response = 'Vous n\'avez pas assez de points';
										}
										
									} elseif($type == 'Premium') {
										$verif = $db->prepare('SELECT COUNT(*) AS nb FROM sting_orders WHERE type = ?');
										$verif->execute(array('Premium'));
										$veriffetch = $verif->fetch();
										if($veriffetch['nb'] >= 5) {
											$response = 'Aucune place Premium n\'est disponible';
										} else {
											
										
										$req = $db->prepare('SELECT * FROM sting_plans WHERE id = ?');
										$req->execute(array($_SESSION['getid']));
										$fetch = $req->fetch();
								
										$req2 = $db->prepare('SELECT * FROM sting_extensions WHERE extension = ?');
										$req2->execute(array($extension));
										$fetch2 = $req2->fetch();
								
										$req3 = $db->prepare('SELECT * FROM sting_options WHERE content = ?');
										$req3->execute(array($option));
										$fetch3 = $req3->fetch();
								
										$price = $fetch['price'] + $fetch2['price'] + $fetch3['price'] + 100;
								
										if($_SESSION['account']['gold'] >= $price) {
										
											/* Ajout de la commande */
											$req = $db->prepare('INSERT INTO sting_orders(clef_user, name, extension, plan, options, date_order, état, type) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
											$req->execute(array($_SESSION['account']['clef'], $name, $extension, 'Plan '.$_SESSION['getid'].'', $option, date('Y-m-d H:i:s'), 'En attente', 'Premium'));
											/* Fin d'ajout de la commande */
									
											/* On retire les points */
											$req = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
											$req->execute(array($price, $_SESSION['account']['clef']));
											/* Fin du retirage des points */
									
											/* Ajout du log */
											$req = $db->prepare('INSERT INTO sting_logs(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
											$req->execute(array($_SESSION['account']['clef'], 'Commande de '.$name.''.$extension.'', $this->GetIP(), date('Y-m-d H:i:s')));
											/* Fin d'ajout du log */
									
											$response = 'success';
										

										} else {
											$response = 'Vous n\'avez pas assez de points';
										}
										}
									}
									
								} else {
									$response = 'Veuillez choisir un type de commande';
								}
								
							} else {
								$response = 'Veuillez choisir une option';
							}
							
						} else {
							$response = 'Veuillez choisir une extension';
						}
						
					} else {
						$response = 'Ce nom de rétro ainsi que l\'extension sont déjà utilisé';
					}
					
				} else {
					$response = 'Votre nom de rétro comporte des caractères interdit';
				}
				
			} else {
				$response = 'Veuillez entrer un nom de rétro';
			}
			
		} else {
			$response = 'Veuillez vous connecter';
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/********************************************
	* Fonction d'affichage des prix des extension
	********************************************/
	function PriceExtensions()
	{
		$db = Connect::getDB();
		
		$req = $db->query('SELECT * FROM sting_extensions');
		
		while($r = $req->fetch()) {
			echo '<tr>
											<td>'.$r['extension'].'</td>
											<td>'.$r['price'].' Point'.$this->isMany($r['price']).'</td>
											<td>1 an</td>
										</tr>';
		}
	}
	
	/*************************************************
	* Fonction de récupération des dernières commandes
	*************************************************/
	function CurrentOrders()
	{
		$db = Connect::getDB();
		
		$DPP = 10;
        $TD = $db->prepare('SELECT * FROM sting_orders WHERE clef_user = ?');
        $TD->execute(array($_SESSION['account']['clef']));
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $MesCommandes = $db->prepare('SELECT * FROM sting_orders WHERE clef_user = ? ORDER BY date_order DESC LIMIT '.$go.','.$DPP);
        $MesCommandes->execute(array($_SESSION['account']['clef']));
        $rowCount = $MesCommandes->rowCount();
        
        if($rowCount == 0) {
            $_SESSION['aucunecommande'] = "<script>new PNotify({
			title: '<i class=\"fa fa-info-circle\"></i> Information',
			text: 'Vous n\'avez aucune commande en attente.',
			addclass: 'bg-info'
		});</script>";
        } else {
             while($ms = $MesCommandes->fetch()) {
            echo '<tr>
											<td>'.$ms['id'].'</td>
											<td>'.$ms['name'].''.$ms['extension'].'</td>
											<td>'.$this->ColorEtat($ms['état']).'</td>
											<td>'.$ms['type'].'</td>
                                            <td>' . date("d-m-Y à H:i", strtotime($ms['date_order'])) . '</td>
										</tr>';
            }
        
        }
	}
	
	/******************************************
	* Fonction de récupération des rétros-habbo
	******************************************/
	function RetroHabbo()
	{
		$db = Connect::getDB();
		
		$DPP = 10;
        $TD = $db->prepare('SELECT * FROM sting_services WHERE clef_user = ?');
        $TD->execute(array($_SESSION['account']['clef']));
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $MesServices = $db->prepare('SELECT * FROM sting_services WHERE clef_user = ? ORDER BY date_order DESC LIMIT '.$go.','.$DPP);
        $MesServices->execute(array($_SESSION['account']['clef']));
        $rowCount = $MesServices->rowCount();
        
        if($rowCount == 0) {
            $_SESSION['aucunservice'] = "<script>new PNotify({
			title: '<i class=\"fa fa-info-circle\"></i> Information',
			text: 'Vous n\'avez aucun rétro-habbo.',
			addclass: 'bg-info'
		});</script>";
        } else {
             while($ms = $MesServices->fetch()) {
            echo '<tr>
											<td>'.$ms['id'].'</td>
											<td>'.$ms['name'].''.$ms['extension'].'</td>
											<td>'.$this->ColorEtat($ms['état']).'</td>
                                            <td>' . $this->ConvertTime($ms['date_order']) . '</td>
											<td>' . date("d-m-Y à H:i", strtotime($ms['date_expiration'])) . '</td>
											<td><a href="?id='.$ms['id'].'">Gérer</a></td>
										</tr>';
            }
        
        }
	}
	
	/**************************************
	* Fonction de récupération des domaines
	**************************************/
	function Domains()
	{
		$db = Connect::getDB();
		
		$DPP = 10;
        $TD = $db->prepare('SELECT * FROM sting_domains WHERE clef_user = ?');
        $TD->execute(array($_SESSION['account']['clef']));
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $MesDomains = $db->prepare('SELECT * FROM sting_domains WHERE clef_user = ? ORDER BY date_order DESC LIMIT '.$go.','.$DPP);
        $MesDomains->execute(array($_SESSION['account']['clef']));
        $rowCount = $MesDomains->rowCount();
        
        if($rowCount == 0) {
            $_SESSION['aucundomaine'] = "<script>new PNotify({
			title: '<i class=\"fa fa-info-circle\"></i> Information',
			text: 'Vous n\'avez aucun nom de domaine.',
			addclass: 'bg-info'
		});</script>";
        } else {
             while($md = $MesDomains->fetch()) {
            echo '<tr>
											<td>'.$md['id'].'</td>
											<td>'.$md['name'].'</td>
											<td>'.$md['extension'].'</td>
											<td>'.$this->ColorEtat($md['état']).'</td>
                                            <td>' . date("d-m-Y à H:i", strtotime($md['date_order'])) . '</td>
											<td>' . date("d-m-Y à H:i", strtotime($md['date_expiration'])) . '</td>
											<td><a href="?id='.$md['id'].'">Gérer</a></td>
										</tr>';
            }
        
        }
	}
	
	/*****************
	* Fonction CodeKDO
	*****************/
	function CodeKDO()
	{
		$db = Connect::getDB();
		
		$code = $this->Security($_POST['code']);
		
		if(isset($_SESSION['account'])) {
			if(!empty($code)) {
				if (preg_match('#^[a-zA-Z0-9 \-]+$#', $code)) {
					if(iconv_strlen($code) <= 8) {
				$req = $db->prepare('SELECT * FROM sting_codes_kdo WHERE code = ?');
				$req->execute(array($code));
				$rowCount = $req->rowCount();
				$fetch = $req->fetch();
				if($rowCount != 0) {
					$add = $db->prepare('UPDATE sting_users SET gold = gold + ? WHERE clef = ?');
					$add->execute(array($fetch['gold'], $_SESSION['account']['clef']));
					
					$delete = $db->prepare('DELETE FROM sting_codes_kdo WHERE code = ?');
					$delete->execute(array($code));
					
					$msgcode = ''.$fetch['gold'].' Point'.$this->isMany($fetch['gold']).' ont été ajouté à votre compte';
					
					$response = 'success';
					
				} else {
					$response = 'Ce code KDO est invalide ou a été utilisé';
				}
					} else {
						$response = 'Ce code est incorrect';
					}
				} else {
					$response = 'Ce code KDO est incorrect';
				}
			} else {
				$response = 'Veuillez entrer un code KDO';
			}
			
		}
		
		echo json_encode(['response' => $response, 'msgcode' => $msgcode]);
	}
	
	/************************************
	* Fonction de récupération des staffs
	************************************/
	function Staffs()
	{
		$db = Connect::getDB();
		
		$req = $db->query('SELECT * FROM sting_users WHERE rank >= 5 AND rank <= 9 ORDER BY rank DESC');
		while($r = $req->fetch()) {
			echo '<div class="col-md-3 col-sm-3 col-xs-3">
								<div class="panel panel-flat">
									<div class="panel-body no-padding">
										<div class="team bg-flatie-700 no-border border-flat">
											<div class="header-bg"><img src="'.$r['avatar'].'" class="header-blur" alt=""></div>
											<div class="avatar">
												<img src="'.$r['avatar'].'" style="height: 150px; width: 150px" alt="">
											</div>
											<div class="content m-b-20">
												<a href="'.$r['username'].'"><h4 class="no-margin text-uppercase m-t-10 '.$this->RankColor($r['rank']).'">'.$r['username'].'</h4></a>
												<p style="border-radius: 0px;" class="text-uppercase '.$this->BannerColor($r['rank']).'"></p>
											</div>
										</div>
									</div>
								</div>
							</div>';
		}
	}
	
	/***************************************
	* Fonction de changement de mot de passe
	***************************************/
	function ChangePassword()
	{
		$db = Connect::getDB();
		
		$passwordactuel = $this->Security($_POST['actuel']);
		$newpassword = $this->Security($_POST['new']);
		$confirmpass = $this->Security($_POST['confirm']);
		$passwordactuel_crypt = $this->Encryption($_POST['actuel']);
		$newpassword_crypt = $this->Encryption($_POST['new']);
		$confirmpass_crypt = $this->Encryption($_POST['confirm']);
		
		if(isset($_SESSION['account'])) {
			if(!empty($passwordactuel) && !empty($newpassword) && !empty($confirmpass)) {
				$req = $db->prepare('SELECT clef, password FROM sting_users WHERE clef = ?');
				$req->execute(array($_SESSION['account']['clef']));
				$fetch = $req->fetch();
				if($passwordactuel_crypt == $fetch['password']) {
					if(iconv_strlen($newpassword) >= 6 AND iconv_strlen($confirmpass) >= 6) {
					if($newpassword == $confirmpass) {
						$update = $db->prepare('UPDATE sting_users SET password = ? WHERE clef = ?');
						$update->execute(array($confirmpass_crypt, $_SESSION['account']['clef']));
						
						$response = 'success';
						
					} else {
						$response = 'Vos nouveaux mot de passe ne correspondent pas';
					}
					
					} else {
						$response = 'Votre nouveau mot de passe est trop court';
					}
				} else {
					$response = 'Le mot de passe actuel est incorrect';
				}
				
			} else {
				$response = 'Veuillez remplir tous les champs';
			}
			
		} else {
			$response = 'Vous n\'&eacute;tes pas connect&eacute;';
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/********************************
	* Fonction de changement d'avatar
	********************************/
	function ChangeAvatar()
	{
		$db = Connect::getDB();
		if(isset($_POST['changeavatar'])) {
			if(!empty($_FILES['avatar']['name']) && !empty($_FILES['avatar'])) {
				
				$max = 2097152;
				$extensions = array('jpg', 'jpeg', 'png', 'gif');
				if($_FILES['avatar']['size'] <= $max) {
					$extension = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
					if(in_array($extension, $extensions)) {
						$chemin = "avatars/".$_SESSION['account']['clef'].".".$extension;
						$url = "/client/avatars/".$_SESSION['account']['clef'].".".$extension;
						$go = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
						if($go) {
							
							$update = $db->prepare('UPDATE sting_users SET avatar = ? WHERE clef = ?');
							$update->execute(array($url, $_SESSION['account']['clef']));
							
							$_SESSION['avatar'] = '<div class="alert bg-success"><span class="text-semibold">Changement effectué.</span> Votre avatar a bien été modifié</div>';
						} else {
							$_SESSION['avatar'] = '<div class="alert bg-danger"><span class="text-semibold">Attention!</span> Une erreur est survenue lors de l\'importation</div>';
 						}
						
					} else {
						$_SESSION['avatar'] = '<div class="alert bg-danger"><span class="text-semibold">Attention!</span> Votre avatar n\'est pas au bon format</div>';
					}
					
				} else {
					$_SESSION['avatar'] = '<div class="alert bg-danger"><span class="text-semibold">Attention!</span> Votre avatar est supérieur à 2Mo</div>';
				}
				
			} else {
				$_SESSION['avatar'] = '<div class="alert bg-danger"><span class="text-semibold">Attention!</span> Veuillez choisir un avatar</div>';
			}
		}
	}
	
	/**********************************
	* Fonction de visianage des tickets
	**********************************/
	function ViewDiscussions() 
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			
			$req = $db->prepare('SELECT * FROM sting_support WHERE id = ?');
			$req->execute(array($_GET['id']));
			$fetch = $req->fetch();
			$rowCount = $req->rowCount();
			if($rowCount == 1) {
				if($_SESSION['account']['clef'] == $fetch['clef_user']) {
					echo '<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading ">
								<h5 class="panel-title"><i class="fa fa-ticket"></i> Ticket #'.$_GET['id'].'</h5>
								<div class="elements">
									<span class="heading-text">État: '.$fetch['état'].'</span>
								</div>
							</div>
							<div class="panel-body">
							<form method="post" action="/core/action/action.php?to=addmsg" id="sendmsg">
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control" id="reponse" name="reponse" placeholder="Exprimez-vous...">
									<span class="input-group-btn"><button class="btn btn-primary">Envoyer</button></span>
								</div>
							</div>
                            </form>
								<ul class="chat ticketconv" id="repr">';
								$rep = $db->prepare('SELECT * FROM sting_support_responses WHERE ticket_id = ? ORDER BY date_response DESC');
								$rep->execute(array($_GET['id']));
								while($r = $rep->fetch()) {
									$InfoAccount = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
									$InfoAccount->execute(array(
										$r['clef_user']
									));
									$fetche = $InfoAccount->fetch();
									echo '<li class="' . ($r['clef_user'] != $_SESSION['account']['clef'] ? 'chat__bubble chat__bubble--rcvd chat__bubble--stop"' : '') . '' . ($r['clef_user'] == $_SESSION['account']['clef'] ? 'chat__bubble chat__bubble--sent"' : '') . '"><strong>' . ($r['clef_user'] != $_SESSION['account']['clef'] ? '<a href="profile?id=' . $r['clef_user'] . '" class="' . $this->RankColor($fetche['rank']) . '">' . $fetche['username'] . '' : '') . '</a></strong> ' . ($r['clef_user'] != $_SESSION['account']['clef'] ? '-' : '') . ' <small>' . date("H:i", strtotime($r['date_response'])) . '</small> :  ' . wordwrap($r['response'], 45, "\n", true) . '</li>';
								}
								echo '<li class="chat__bubble chat__bubble--sent"><strong><small>' . date("H:i", strtotime($fetch['date_open'])) . '</small> :  ' . wordwrap($fetch['contenu'], 45, "\n", true) . '</li>';
					echo '</ul>
						</div>
					</div>
				</div>';
				
				} else {
					echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="support"</SCRIPT>';
				}
			} else {
				echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="support"</SCRIPT>';
			}
		}
 	}
	
	/******************************************
	* Fonction d'ajout d'un message à un ticket
	******************************************/
	function AddMsg()
	{
		$db = Connect::getDB();
		
		$msg = $this->Security($_POST['reponse']);
		
		if(isset($_SESSION['account'])) {
			if(!empty($msg)) {
				
				$req = $db->prepare('SELECT * FROM sting_support WHERE id = ?');
				$req->execute(array($_SESSION['idt']));
				$fetch = $req->fetch();
				
				if($fetch['état'] != 'Fermé') {
					
					$add = $db->prepare('INSERT INTO sting_support_responses(ticket_id, response, clef_user, date_response) VALUES(?, ?, ?, ?)');
				$add->execute(array($_SESSION['idt'], $msg, $_SESSION['account']['clef'], date('Y-m-d H:i:s')));
					
					$upd = $db->prepare('UPDATE sting_support SET last_response = ?, last_clef = ? WHERE id = ?');
				$upd->execute(array(date('Y-m-d H:i:s'), $_SESSION['account']['clef'], $_SESSION['idt']));
				
				if($fetch['état'] == 'Répondu') {
					$update = $db->prepare('UPDATE sting_support SET état = ? WHERE id = ?');
					$update->execute(array('En attente', $_SESSION['idt']));
				}
				
				$repons = '<li class="chat__bubble chat__bubble--sent"><strong>' . date("H:i") . '</strong> : '.$msg.'</li>';
				
				$response = "success";
				} else {
					$response = 'Votre ticket est fermé';
				}
				
			} else {
				$response = 'Veuillez entrer une r&eacute;ponse';
			}
		}
		
		echo json_encode(['response' => $response, 'repons' => $repons]);
	}
	
	/***********************************************
	* Fonction de récupération d'un ticket avec id x
	***********************************************/
	function ViewTickets()
	{
		$db = Connect::getDB();
		
		$DPP = 10;
        $TD = $db->prepare('SELECT * FROM sting_support WHERE clef_user = ?');
        $TD->execute(array($_SESSION['account']['clef']));
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $MesTickets = $db->prepare('SELECT * FROM sting_support WHERE clef_user = ? ORDER BY date_open DESC LIMIT '.$go.','.$DPP);
        $MesTickets->execute(array($_SESSION['account']['clef']));
        $rowCount = $MesTickets->rowCount();
        
        if($rowCount == 0) {
            $_SESSION['aucunticket'] = "<script>new PNotify({
			title: '<i class=\"fa fa-info-circle\"></i> Information',
			text: 'Vous n\'avez aucun ticket.',
			addclass: 'bg-info'
		});</script>";
        } else {
             while($t = $MesTickets->fetch()) {
            echo '<tr>
											<td>'.$t['id'].'</td>
											<td>'.$t['sujet'].'</td>
											<td>'.$t['département'].'</td>
											<td>'.$this->ColorEtat($t['état']).'</td>
                                            <td>' . date("d-m-Y à H:i", strtotime($t['date_open'])) . '</td>
											<td>' . date("d-m-Y à H:i", strtotime($t['last_response'])) . '</td>
											<td><a href="?id='.$t['id'].'">Voir</a></td>
										</tr>';
            }
        
        }
	}
	
	/*********************************
	* Fonction de création d'un ticket
	*********************************/
	function CreateTicket()
	{
		$db = Connect::getDB();
		
		$sujet = $this->Security($_POST['sujet']);
		$departement = $this->Security($_POST['departement']);
		$contenu = $this->Security($_POST['contenu']);
		
		if(isset($_SESSION['account'])) {
			if(!empty($sujet)) {
				if(iconv_strlen($sujet) <= 45) {
					if(!empty($departement)) {
						if($departement == 'Technique' OR $departement == 'Commercial') {
							if(!empty($contenu) && iconv_strlen($contenu) > 5) {
							$add = $db->prepare('INSERT INTO sting_support(clef_user, sujet, département, état, contenu, date_open, last_response, last_clef) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
							$add->execute(array($_SESSION['account']['clef'], $sujet, $departement, 'Ouvert', $contenu, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $_SESSION['account']['clef']));
							
							$response = 'success';
								
							} else {
								$response = 'Veuillez entrer un contenu ou entrez plus de 5 caractères';
							}
							
						} else {
							$response = 'Veuillez sélectionner un département';
						}
						
					} else {
						$response = 'Veuillez sélectionner un département';
					}
					
				} else {
					$response = 'Votre sujet est trop long';
				}
				
			} else {
				$response = 'Veuillez remplir le sujet';
			}
			
		} else {
			$response = 'Veuillez-vous connecter';
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/********************************
	* Fonction des commandes à livrer
	********************************/
	function OrderLivre()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->query('SELECT * FROM sting_orders ORDER BY type DESC LIMIT 3');
			
			
			
			echo '<div class="panel panel-primary">
										<div class="panel-heading">
											<h5 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group3" aria-expanded="false"><i class="fa fa-gamepad"></i> Rétros prochainement livrés</a>
											</h5>
											<h5 class="elements">
												<i class="fa fa-plus"></i>
											</h5>
										</div>
							<div id="accordion-control-right-group3" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
							<ul class="media-list media-list-linked">';
			
			while($r = $req->fetch()) {
				$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				$info->execute(array($r['clef_user']));
				$fetch = $info->fetch();
				echo '<li class="media">
									<a href="#" class="media-link">
										<div class="media-left">
											<img src="'.$fetch['avatar'].'" class="img-circle" alt="">
										</div>
										
										<div class="media-body">
											<h6 class="media-heading">'.$r['name'].''.$r['extension'].' <span class="media-annotation dotted"> '.$this->ConvertTime($r['date_order']).'</span></h6>
										Livraison: <b>'.$r['type'].'</b> | <b>'.$r['plan'].'</b>
										</div>
									</a>
								</li>';
			}
			
			echo '</ul></div>
							
						</div>';
		}
	}
	
	/***********************************
	* Fonctions de gestion de rétro-habbo
	***********************************/
	function ManagementRetro()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_services WHERE id = ?');
			$req->execute(array($_GET['id']));
			$fetch = $req->fetch();
			$rowCount = $req->rowCount();
			if($rowCount != 0) {
				if($_SESSION['account']['clef'] == $fetch['clef_user']) {
					$_SESSION['retro'] = array(
						'idservice' => $fetch['id_service'],
						'name' => $fetch['name'],
						'extension' => $fetch['extension'],
						'plan' => $fetch['plan'],
						'état' => $fetch['état'],
						'options' => $fetch['options'],
						'date_order' => $fetch['date_order'],
						'date_validation' => $fetch['date_validation'],
						'date_expiration' => $fetch['date_expiration']
					);
					
					$access = $db->prepare('SELECT * FROM sting_access WHERE clef_user = ? AND id_service = ?');
					$access->execute(array($_SESSION['account']['clef'], $fetch['id_service']));
					$ac = $access->fetch();
					$_SESSION['access'] = array(
						'mysql_hôte' =>	$ac['mysql_hôte'],
						'mysql_user' => $ac['mysql_user'],
						'mysql_pass' => $ac['mysql_pass'],
						'ftp_hôte' => $ac['ftp_hôte'],
						'ftp_user' => $ac['ftp_user'],
						'ftp_pass' => $ac['ftp_pass'],
						'session_ip' => $ac['session_ip'],
						'session_user' => $ac['session_user'],
						'session_pass' => $ac['session_pass'],
						'session_expiration' => $ac['session_expiration'],
						'session_etat' => $ac['session_état'],
						'swf_variables' => $ac['swf_variables'],
						'swf_text' => $ac['swf_text'],
						'swf_override_variables' => $ac['swf_override_variables'],
						'swf_furnidata' => $ac['swf_furnidata'],
						'swf_figuredata' => $ac['swf_figuredata'],
						'swf_productdata' => $ac['swf_productdata'],
						'swf_game' => $ac['swf_game'],
						'swf_habbo_swf' => $ac['swf_habbo_swf']
					);
				
				} else {
					echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="retros_habbo"</SCRIPT>';
				}
			} else {
				echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="retros_habbo"</SCRIPT>';
			}
		}
	}
	
	function SessionVPS()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['retro']['options'] != 'SWF' OR $_SESSION['retro']['options'] != 'SWF + Session') {
				if($_SESSION['account']['gold'] >= 200) {
					$insert = $db->prepare('INSERT INTO sting_orders_options(id_service, retro, options, date) VALUES(?, ?, ?, ?)');
					$insert->execute(array($_SESSION['retro']['idservice'], ''.$_SESSION['retro']['name'].''.$_SESSION['retro']['extension'].'', 'Session', date('Y-m-d H:i:s')));
					if($_SESSION['retro']['options'] == 'Aucune') {
						$update = $db->prepare('UPDATE sting_services SET options = ? WHERE id_service = ? AND clef_user = ?');
						$update->execute(array('Session', $_SESSION['retro']['idservice'], $_SESSION['account']['clef']));
					} elseif($_SESSION['retro']['options'] == 'SWF') {
						$update = $db->prepare('UPDATE sting_services SET options = ? WHERE id_service = ? AND clef_user = ?');
						$update->execute(array('SWF + Session', $_SESSION['retro']['idservice'], $_SESSION['account']['clef']));
					}
					$update = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
					$update->execute(array(200, $_SESSION['account']['clef']));
					$response = 'success';
				} else {
					$response = 'Nous n\'avez pas assez de points';
				}
				
				echo json_encode(['response' => $response]);
			}
		}
	}
	
	function RenouvVPS()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['account']['gold'] >= 200) {
				$edit = $db->prepare('UPDATE sting_access SET session_expiration = ?, session_état = ? WHERE id = ? AND clef_user = ?');
				$edit->execute(array(date('Y-m-d H:i:s', strtotime('' . $_SESSION['access']['session_expiration'] . '' . " +1 month")), 'Active', $_SESSION['ids'], $_SESSION['account']['clef']));
				$gold = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
				$gold->execute(array('200', $_SESSION['account']['clef']));
				$response = 'success';
			} else {
				$response = 'Vous n\'avez pas assez de points';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function PackSWF()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['retro']['options'] != 'SWF' OR $_SESSION['retro']['options'] != 'SWF + Session') {
			if($_SESSION['account']['gold'] >= 200) {
				$insert = $db->prepare('INSERT INTO sting_orders_options(id_service, retro, options, date) VALUES(?, ?, ?, ?)');
				$insert->execute(array($_SESSION['retro']['idservice'], ''.$_SESSION['retro']['name'].''.$_SESSION['retro']['extension'].'', 'SWF', date('Y-m-d H:i:s')));
					if($_SESSION['retro']['options'] == 'Aucune') {
						$update = $db->prepare('UPDATE sting_services SET options = ? WHERE id_service = ? AND clef_user = ?');
						$update->execute(array('SWF', $_SESSION['retro']['idservice'], $_SESSION['account']['clef']));
					} elseif($_SESSION['retro']['options'] == 'Session') {
						$update = $db->prepare('UPDATE sting_services SET options = ? WHERE id_service = ? AND clef_user = ?');
						$update->execute(array('SWF + Session', $_SESSION['retro']['idservice'], $_SESSION['account']['clef']));
					}
				$gold = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
				$gold->execute(array(200, $_SESSION['account']['clef']));
				$response = 'success';
			}
			
			echo json_encode(['response' => $response]);
			}
		}
	}
	
	function Renouvellement()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if(isset($_SESSION['retro'])) {
				$req = $db->prepare('SELECT * FROM sting_plans WHERE name = ?');
				$req->execute(array($_SESSION['retro']['plan']));
				$fetch = $req->fetch();
				if($_SESSION['account']['gold'] >= $fetch['price']) {
					$update = $db->prepare('UPDATE sting_services SET date_expiration = ?, état = ? WHERE clef_user = ? AND id_service = ?');
					$update->execute(array(date('Y-m-d H:i:s', strtotime('' . $_SESSION['retro']['date_expiration'] . '' . " +1 month")), 'Actif', $_SESSION['account']['clef'], $_SESSION['retro']['idservice']));
					$gold = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
					$gold->execute(array($fetch['price'], $_SESSION['account']['clef']));
					$date = date('d-m-Y à H:i', strtotime('' . $_SESSION['retro']['date_expiration'] . '' . " +1 month"));
					$response = 'success'; 
				} else {
					$response = 'Vous n\'avez pas assez de points';	
				}
				
				echo json_encode(['response' => $response, 'date' => $date]);
			}
		}
	}
	
	function Resilier()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if(isset($_SESSION['retro'])) {
					$update = $db->prepare('UPDATE sting_services SET état = ? WHERE clef_user = ? AND id_service = ?');
					$update->execute(array('Résilié', $_SESSION['account']['clef'], $_SESSION['retro']['idservice']));
					$etat = $this->ColorEtat('Résilié');
					$response = 'success'; 
				
				echo json_encode(['response' => $response, 'etat' => $etat]);
			}
		}
	}
	
	function Reboot()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if(isset($_SESSION['retro'])) {
				$req = $db->prepare('SELECT * FROM sting_repairs WHERE id_service = ? AND type = ?');
				$req->execute(array($_SESSION['retro']['idservice'], 'Redémarrage'));
				$fetch = $req->fetch();
				$rowCount = $req->rowCount();
				if($rowCount == 0) {
					$insert = $db->prepare('INSERT INTO sting_repairs(id_service, type, date) VALUES(?, ?, ?)');
					$insert->execute(array($_SESSION['retro']['idservice'], 'Redémarrage', date('Y-m-d H:i:s')));
					$response = 'success';
				} else {
					$response = 'Vous avez déjà fais une demande de redémarrage';
				}
				
				
			}
			echo json_encode(['response' => $response]);
		}
	
	
	}
	
	function ReinstallCMS()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if(isset($_SESSION['retro'])) {
				if($_SESSION['account']['gold'] >= 50) {
					$req = $db->prepare('SELECT * FROM sting_repairs WHERE id_service = ? AND type = ?');
					$req->execute(array($_SESSION['retro']['idservice'], 'CMS'));
					$fetch = $req->fetch();
					$rowCount = $req->rowCount();
					if($rowCount == 0) {
						$go = $db->prepare('INSERT INTO sting_repairs(id_service, type, date) VALUES(?, ?, ?)');
						$go->execute(array($_SESSION['retro']['idservice'], 'CMS', date('Y-m-d H:i:s')));
						$vire = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
						$vire->execute(array('50', $_SESSION['account']['clef']));
						$response = 'success';
					} else {
						$response = 'Vous avez déjà fais une demande de réinstallation';	
					}
				} else {
					$response = 'Vous n\'avez pas assez de points, il vous faut 50 points.';
				}
				
				echo json_encode(['response' => $response]);
			}
		}
	}
	
	function ReinstallBDD()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if(isset($_SESSION['retro'])) {
				if($_SESSION['account']['gold'] >= 50) {
					$req = $db->prepare('SELECT * FROM sting_repairs WHERE id_service = ? AND type = ?');
					$req->execute(array($_SESSION['retro']['idservice'], 'BDD'));
					$fetch = $req->fetch();
					$rowCount = $req->rowCount();
					if($rowCount == 0) {
						$go = $db->prepare('INSERT INTO sting_repairs(id_service, type, date) VALUES(?, ?, ?)');
						$go->execute(array($_SESSION['retro']['idservice'], 'BDD', date('Y-m-d H:i:s')));
						$vire = $db->prepare('UPDATE sting_users SET gold = gold - ? WHERE clef = ?');
						$vire->execute(array('50', $_SESSION['account']['clef']));
						$response = 'success';
					} else {
						$response = 'Vous avez déjà fais une demande de réinstallation';	
					}
				} else {
					$response = 'Vous n\'avez pas assez de points, il vous faut 50 points.';
				}
				
				echo json_encode(['response' => $response]);
			}
		}
	}
	
	function ADMOptionsGo()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$req = $db->prepare('SELECT * FROM sting_orders_options WHERE id = ?');
				$req->execute(array($_SESSION['intid']));
				$rowCount = $req->rowCount();
				$fetch = $req->fetch();
				if($rowCount != 0) {
					$rr = $db->prepare('SELECT * FROM sting_access WHERE id_service = ?');
					$rr->execute(array($fetch['id_service']));
					$ro = $rr->rowCount();
					if($ro != 0) {
					if($fetch['options'] == 'Session') {
						$ip = $this->Security($_POST['ip']);
						$user = $this->Security($_POST['user']);
						$pass = $this->Security($_POST['pass']);
						
						if(!empty($ip) && !empty($user) && !empty($pass)) {
						
						$insert = $db->prepare('UPDATE sting_access SET session_ip = ?, session_user = ?, session_pass = ?, session_état = ?, session_expiration = ? WHERE id_service = ?');
						$insert->execute(array($ip, $user, $pass, 'Active', date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month")), $fetch['id_service']));
						
						$delete = $db->prepare('DELETE FROM sting_orders_options WHERE id = ?');
						$delete->execute(array($_SESSION['intid']));
						
						$response = 'success';
						} else {
							$response = 'Veuillez remplir tous les champs';
						}
						
					} elseif($fetch['options'] == 'SWF') {
						$variables = $_POST['variables'];
						$text = $_POST['text'];
						$override = $_POST['override'];
						$furni = $_POST['furnidata'];
						$figure = $_POST['figuredata'];
						$product = $_POST['productdata'];
						$game = $_POST['game'];
						$swf = $_POST['swf'];
						
						if(!empty($variables) && !empty($text) && !empty($override) && !empty($furni) && !empty($figure) && !empty($product) && !empty($game) && !empty($swf)) {
							$up = $db->prepare('UPDATE sting_access SET swf_variables = ?, swf_text = ?, swf_override_variables = ?, swf_figuredata = ?, swf_productdata = ?, swf_furnidata = ?, swf_game = ?, swf_habbo_swf = ? WHERE id_service = ?');
							$up->execute(array($variables, $text, $override, $figure, $figure, $product, $game, $swf, $fetch['id_service']));
							
							$del = $db->prepare('DELETE FROM sting_orders_options WHERE id = ?');
							$del->execute(array($_SESSION['intid']));
							
							$response = 'success';
						} else {
							$response = 'Veuillez remplir tous les champs';
						}
					}
						
					} else {
						$response = 'Les accès de se rétro ne sont pas ajouté';
					}
					
				} else {
					$response = 'Erreur';
				}
				
				echo json_encode(['response' => $response]);
			}
		}
	}
	
	function ADMOptionsValide()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$id = (int) $_GET['id'];
				$_SESSION['intid'] = $id;
				$req = $db->prepare('SELECT * FROM sting_orders_options WHERE id = ?');
				$req->execute(array($id));
				$rowCount = $req->rowCount();
				$fetch = $req->fetch();
				if($rowCount != 0) {
					if($fetch['options'] == 'Session') {
						echo '<div class="col-md-12">
						<div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Information de commande</h2>
                        </div><div class="card-block">
						<form method="post" id="options" action="/core/action/action?to=admopt">
						<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>IP Session</label>
                                <input type="text" class="form-control" name="ip" placeholder="IP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Utilisateur Session</label>
                                <input type="text" class="form-control" name="user" placeholder="Utilisateur">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Mot de passe Session</label>
                                <input type="text" class="form-control" name="pass" placeholder="Mot de passe">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block waves-effect">Valider</button>
						</div>
						</div>
						</form></div>
						
						</div></div>											</div>';
					} elseif($fetch['options'] == 'SWF') {
						echo '<div class="col-md-12">
						<div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Information de commande</h2>
                        </div><div class="card-block">
						<form method="post" id="options" action="/core/action/action?to=admopt">
						<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Variables</label>
                                <input type="text" class="form-control" name="variables" placeholder="Variables">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Text</label>
                                <input type="text" class="form-control" name="text" placeholder="Text">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Override Variables</label>
                                <input type="text" class="form-control" name="override" placeholder="Override Variables">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Furnidata</label>
                                <input type="text" class="form-control" name="furnidata" placeholder="Furnidata">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Figuredata</label>
                                <input type="text" class="form-control" name="figuredata" placeholder="Figuredata">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Productdata</label>
                                <input type="text" class="form-control" name="productdata" placeholder="Productdata">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Game</label>
                                <input type="text" class="form-control" name="game" placeholder="Game">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>.SWF</label>
                                <input type="text" class="form-control" name="swf" placeholder=".SWF">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block waves-effect">Valider</button>
						</div>
						</div>
						</form></div>
						
						</div></div>											</div>';	
					} 
					
				} else {
					echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="optionsc"</SCRIPT>';
				}
				
			}
		}
	}
		
	/*****************************************
	* Fonction de connexion à l'administration
	*****************************************/
	function ADMLogin()
	{
		$db = Connect::getDB();
		
		$pass = $this->Security($_POST['password']);
		
		if($_SESSION['account']['rank'] >= 5) {
			$adm = $db->prepare('SELECT * FROM sting_adm_accounts WHERE clef = ? AND username = ?');
			$adm->execute(array($_SESSION['account']['clef'], $_SESSION['account']['username']));
			$fetch = $adm->fetch();
			$row = $adm->rowCount();
			if($row != 0) {
			if($pass == $fetch['password']) {
				$_SESSION['admin'] = 'active';
				$response = 'success';
			} else {
				$response = 'Votre mot de passe est incorrect';
			}
			} else {
				$response = 'Votre compte administration est introuvable';
			}
		} else {
			$response = 'Vous n\'êtes pas autorisé';
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/**********************************************
	* Fonction de récupération du nombre de clients
	**********************************************/
	function ADMNbClient()
	{
		$db = Connect::getDB();

			$req = $db->query('SELECT COUNT(id) AS nb FROM sting_users');
			$fetch = $req->fetch();
			
			$value = $fetch['nb'];
			
			return $value;
	}
	
	/************************************************
	* Fonction de récupération du nombre de commandes
	************************************************/
	function ADMNbOrders()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			$req = $db->query('SELECT COUNT(*) AS nb FROM sting_orders');
			$fetch = $req->fetch();
			
			$value = $fetch['nb'];
			
			return $value;
		}
	}
	
	/*************************************************
	* Fonction de récupération du nombre d'euro gagnés
	*************************************************/
	function ADMNbEuros()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			$req = $db->query('SELECT SUM(payout) AS nb FROM sting_logs_payments');
			$fetch = $req->fetch();
			
			$value = $fetch['nb'];
			
			return $value;
		}
	}
	
	/**********************************************
	* Fonction de récupération du nombre de tickets
	**********************************************/
	function ADMNbTickets()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			$req = $db->prepare('SELECT COUNT(*) AS nb FROM sting_support WHERE état = ? OR état = ?');
			$req->execute(array('Ouvert', 'En attente'));
			$fetch = $req->fetch();
			
			$value = $fetch['nb'];
			
			return $value;
		}
	}
	
	/**************************************************
	* Fonction de récupération du nombre de rétro actif
	**************************************************/
	function ADMNbRetroActif()
	{
		$db = Connect::getDB();

			$req = $db->prepare('SELECT COUNT(id) AS nb FROM sting_services WHERE état = ?');
			$req->execute(array('Actif'));
			$fetch = $req->fetch();
			
			$value = $fetch['nb'];
			
			return $value;
	}
	
	/**********************************************
	* Fonction de changement des paramètres général
	**********************************************/
	function ADMChangeGeneral()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 8) {
				$name = $this->Security($_POST['name']);
				$description = $this->Security($_POST['description']);
				$url = $this->Security($_POST['url']);
			
				$update = $db->prepare('UPDATE sting_settings SET name = ?, description = ?, url = ?');
				$update->execute(array($name, $description, $url));
				
				$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
				$log->execute(array($_SESSION['account']['clef'], 'Changement des paramètres général', $this->GetIP(), date('Y-m-d H:i:s')));
			
				$response = 'success';
			} else {
				$response = 'Vous n\'&ecirc;tes pas autorisé à changer cela';
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/*************************************************
	* Fonction de changement des paramètres optionnels
	*************************************************/
	function ADMChangeOptions()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 8) {
				$maintenance = $this->Security($_POST['maintenance']);
				$inscription = $this->Security($_POST['inscription']);
				$recrutement = $this->Security($_POST['recrutement']);
				
				if($maintenance == 1 OR $maintenance == 0) {
					if($inscription == 1 OR $inscription == 0) {
						if($recrutement == 1 OR $recrutement == 0) {
				
							$update = $db->prepare('UPDATE sting_settings SET maintenance = ?, register = ?, recrutement = ?');
							$update->execute(array($maintenance, $inscription, $recrutement));
							
							$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
							$log->execute(array($_SESSION['account']['clef'], 'Changement des paramètres optionnels', $this->GetIP(), date('Y-m-d H:i:s')));
							
							$response = 'success';
							
						} else {
							$response = 'Veuillez sélectionner une option';
						}
					} else {
						$response = 'Veuillez sélectionner une option';
					}
				} else {
					$response = 'Veuillez sélectionner une option';
				}
				
			} else {
				$response = 'Vous n\'&ecirc;tes pas autorisé à changer cela';
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/**************************************
	* Fonction de configuration du dedipass
	**************************************/
	function ADMPayment()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 8) {
				$public = $this->Security($_POST['public']);
				$private = $this->Security($_POST['private']);
				
				$update = $db->prepare('UPDATE sting_settings SET dedipass_public_key = ?, dedipass_private_key = ?');
				$update->execute(array($public, $private));
				
				$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
				$log->execute(array($_SESSION['account']['clef'], 'Changement de configuration de paiement', $this->GetIP(), date('Y-m-d H:i:s')));
				
				$response = 'success';
				
			} else {
				$response = 'Vous n\'&ecirc;tes pas autorisé à changer cela';
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/******************************
	* Fonction des logs de paiement
	******************************/
	function ADMHisPayments()
	{
		$db = Connect::getDB();
		if($_SESSION['account']['rank'] >= 8) {
			$DPP = 7;
			$TD = $db->query('SELECT * FROM sting_logs_payments');
			$rc = $TD->rowCount();
  	      
			$_SESSION['tt'] = ceil($rc/$DPP);
   	     
			if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
				$_GET['d'] = intval($_GET['d']);
				$PA = $_GET['d'];
				$_SESSION['PA'] = $_GET['d'];
			} else {
				$PA = 1;
				$_SESSION['PA'] = 1;
			}
        
			$go = ($PA-1)*$DPP;
        
			$PaiementLog = $db->query('SELECT * FROM sting_logs_payments ORDER BY id DESC LIMIT '.$go.','.$DPP);
			$rowCount = $PaiementLog->rowCount();
        
			if($rowCount == 0) {
				$_SESSION['aucunlogpayment'] = "<script>$.notify({message: 'Aucun historique de paiment disponible',});</script>";
			} else {
             while($pl = $PaiementLog->fetch()) {
				 $info = $db->prepare('SELECT username, clef FROM sting_users WHERE clef = ?');
				 $info->execute(array($pl['clef_user']));
				 $f = $info->fetch();
				 echo '<tr>
			 								<td>'.$pl['id'].'</td>
											<td>'.$f['username'].'</td>
											<td>'.$pl['code'].'</td>
											<td>'.$pl['rate'].'</td>
											<td>'.$pl['gold'].'</td>
											<td>'.$pl['payout'].'</td>
											<td>' . date("d-m-Y à H:i", strtotime($pl['date'])) . '</td>
										</tr>';
			 	}
        
			}
		}
	}
	
	/**************************
	* Fonction des logs général
	**************************/
	function ADMHisGeneral()
	{
		$db = Connect::getDB();
		if($_SESSION['account']['rank'] >= 8) {
				
			$DPP = 7;
			$TD = $db->query('SELECT * FROM sting_logs');
			$rc = $TD->rowCount();
        
			$_SESSION['tt'] = ceil($rc/$DPP);
        
			if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
				$_GET['d'] = intval($_GET['d']);
				$PA = $_GET['d'];
				$_SESSION['PA'] = $_GET['d'];
			} else {
				$PA = 1;
				$_SESSION['PA'] = 1;
			}
        
			$go = ($PA-1)*$DPP;
        
			$Log = $db->query('SELECT * FROM sting_logs ORDER BY id DESC LIMIT '.$go.','.$DPP);
			$rowCount = $Log->rowCount();
        
			if($rowCount == 0) {
				$_SESSION['aucunlog'] = "<script>$.notify({message: 'Aucun historique de paiment disponible',});</script>";
			} else {
				while($l = $Log->fetch()) {
					$info = $db->prepare('SELECT username, clef FROM sting_users WHERE clef = ?');
					$info->execute(array($l['clef_user']));
					$f = $info->fetch();
					echo '<tr>
			 								<td>'.$l['id'].'</td>
											<td>'.$f['username'].'</td>
											<td>'.$l['action'].'</td>
											<td>'.$l['ip'].'</td>
											<td>' . date("d-m-Y à H:i", strtotime($l['date'])) . '</td>
										</tr>';
				}
        
			}
		}
	}
	
	/**************************
	* Fonction des logs général
	**************************/
	function ADMHisAdmin()
	{
		$db = Connect::getDB();
		if($_SESSION['account']['rank'] >= 8) {
			
			$DPP = 7;
			$TD = $db->query('SELECT * FROM sting_logs_admin');
			$rc = $TD->rowCount();
        
			$_SESSION['tt'] = ceil($rc/$DPP);
        
			if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
				$_GET['d'] = intval($_GET['d']);
				$PA = $_GET['d'];
				$_SESSION['PA'] = $_GET['d'];
			} else {
				$PA = 1;
				$_SESSION['PA'] = 1;
			}
        
			$go = ($PA-1)*$DPP;
        
			$Log = $db->query('SELECT * FROM sting_logs_admin ORDER BY id DESC LIMIT '.$go.','.$DPP);
			$rowCount = $Log->rowCount();
        
			if($rowCount == 0) {
				$_SESSION['aucunlog'] = "<script>$.notify({message: 'Aucun historique de paiment disponible',});</script>";
			} else {
				while($l = $Log->fetch()) {
					$info = $db->prepare('SELECT username, clef FROM sting_users WHERE clef = ?');
					$info->execute(array($l['clef_user']));
					$f = $info->fetch();
					echo '<tr>
			 								<td>'.$l['id'].'</td>
											<td>'.$f['username'].'</td>
											<td>'.$l['action'].'</td>
											<td>'.$l['ip'].'</td>
											<td>' . date("d-m-Y à H:i", strtotime($l['date'])) . '</td>
										</tr>';
				}
        
			}
		}
	}
	
	/**********************************
	* Fonction de recherche des clients
	**********************************/
	function ADMSearch()
	{
		$db = Connect::getDB();
		
		$client = $this->Security($_POST['client']);
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$req = $db->query('SELECT * FROM sting_users WHERE username LIKE "%'.$client.'%" ORDER BY id');
				if(!empty($client)) {
					
					$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
					$log->execute(array($_SESSION['account']['clef'], 'Recherche avec le mot clé <b>'.$client.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					
					while($fetch = $req->fetch()) {
						echo '<div class="col-xl-2 col-lg-3 col-sm-4 col-6"><div class="contacts__item"><a href="" class="contacts__img"><img src="'.$fetch['avatar'].'" alt=""></a><div class="contacts__info"><strong>'.$fetch['username'].'</strong><small>'.$this->Rank($fetch['rank']).'</small></div><br><a href="?id='.$fetch['clef'].'" class="contacts__btn">Voir</a></div></div>';
					}
				} else {
					echo '<h4 class="text-center" style="color: #707070">Veuillez entrer un nom d\'utilisateur</h4>';
				}
				
			} else {
				echo 'erreur';
			}
			
		} else {
			echo 'erreur';
		}
	}
	
	/*************************************
	* Fonction de modification d'un client
	*************************************/
	function ADMModifClient()
	{
		$db = Connect::getDB();
		
		if($_SESSION['admin']) {
			if($_SESSION['account']['rank'] >= 6) {
				$req = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				$req->execute(array($_GET['id']));
				$fetch = $req->fetch();
				echo '<div class="col-md-6">
						<div class="card-demo">
							<div class="card">

								<div class="card-header">
									<h2 class="card-title">Profil de <b>'.$fetch['username'].'</b></h2>
								</div>
						
								<div class="card-block">
									<form method="post" action="/core/action/action?to=admaccount1" id="account1">
									<div class="form-group">
										<label>Nom d\'utilisateur</label>
										<input type="text" name="username" class="form-control" placeholder="Nom d\'utilisateur" value="'.$fetch['username'].'">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Mot de passe (Laisser vide pour ne pas le modifier)</label>
										<input type="password" name="password" class="form-control" placeholder="Mot de passe">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Email</label>
										<input type="text" name="email" class="form-control" placeholder="Email" value="'.$fetch['email'].'">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Grade</label>
										<select name="rank" class="form-control">
											<option ' . ($fetch['rank'] == '1' ? 'selected' : '') . ' value="1">Membre</option>
											<option ' . ($fetch['rank'] == '2' ? 'selected' : '') . ' value="2">VIP Basic</option>
											<option ' . ($fetch['rank'] == '3' ? 'selected' : '') . ' value="3">VIP Plus</option>
											<option ' . ($fetch['rank'] == '4' ? 'selected' : '') . ' value="4">VIP Ultime</option>
											<option ' . ($fetch['rank'] == '5' ? 'selected' : '') . ' value="5">Agent</option>
											<option ' . ($fetch['rank'] == '6' ? 'selected' : '') . ' value="6">Technicien</option>
											<option ' . ($fetch['rank'] == '7' ? 'selected' : '') . ' value="7">Supérieur</option>
											<option ' . ($fetch['rank'] == '8' ? 'selected' : '') . ' value="8">Développeur</option>
											<option ' . ($fetch['rank'] == '9' ? 'selected' : '') . ' value="9">Administrateur</option>
										</select>
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
									<h2 class="card-title">Options</h2>
								</div>
						
								<div class="card-block">
									<form method="post" action="/core/action/action?to=admaccount2" id="account2">
									<div class="form-group">
										<label>Avatar</label>
										<input type="text" name="avatar" class="form-control" placeholder="Avatar" value="'.$fetch['avatar'].'">
										<i class="form-group__bar"></i>
									</div>
									<div class="form-group">
										<label>Compte</label>
										<select name="compte" class="form-control">
											<option ' . ($fetch['account_confirmed'] == '0' ? 'selected' : '') . ' value="0">Non validé</option>
											<option ' . ($fetch['account_confirmed'] == '1' ? 'selected' : '') . ' value="1">Validé</option>
											<option ' . ($fetch['account_confirmed'] == '2' ? 'selected' : '') . ' value="2">Désactivé</option>
										</select>
									</div>
									<button class="btn btn-primary btn-block waves-effect">Valider</button>
									</form>
								</div>

							</div>
						</div>
					</div>';
			}
		}
	}
	
	/***********************************
	* Fonction de modification du profil
	***********************************/
	function ADMAccount1()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
			
				$username = $this->Security($_POST['username']);
				$password_crypt = $this->Encryption($_POST['password']);
				$password = $this->Security($_POST['password']);
				$email = $this->Security($_POST['email']);
				$rank = $this->Security($_POST['rank']);
				
				if(!empty($password)) {
					if($_SESSION['getid'] == 1 AND $_SESSION['account']['clef'] != 1) {
						$response = 'Il est interdit de modifier ce compte';
						$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
						$log->execute(array($_SESSION['account']['clef'], 'Tentative de modification de <b>'.$username.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					} else {
					
					$update = $db->prepare('UPDATE sting_users SET username = ?, password = ?, email = ?, rank = ? WHERE clef = ?');
					$update->execute(array($username, $password_crypt, $email, $rank, $_SESSION['getid']));
					$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
					$log->execute(array($_SESSION['account']['clef'], 'Modification du profil de <b>'.$username.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					$response = 'success';
		
					}
				} else {
					if($_SESSION['getid'] == 1 AND $_SESSION['account']['clef'] != 1) {
						$response = 'Il est interdit de modifier ce compte';
						$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
						$log->execute(array($_SESSION['account']['clef'], 'Tentative de modification de <b>'.$username.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					} else {
					$update = $db->prepare('UPDATE sting_users SET username = ?, email = ?, rank = ? WHERE clef = ?');
					$update->execute(array($username, $email, $rank, $_SESSION['getid']));
					$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
					$log->execute(array($_SESSION['account']['clef'], 'Modification du profil de <b>'.$username.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					$response = 'success';
					}
				}
				
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/***********************************************
	* Fonction de modification des options du profil
	***********************************************/
	function ADMAccount2()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
			
				$avatar = $this->Security($_POST['avatar']);
				$compte = $this->Security($_POST['compte']);
				
				if($_SESSION['getid'] == 1 AND $_SESSION['account']['clef'] != 1) {
						$response = 'Il est interdit de modifier ce compte';
						$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
					$log->execute(array($_SESSION['account']['clef'], 'Tentative de modification de <b>'.$username.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					} else {
					$update = $db->prepare('UPDATE sting_users SET avatar = ?, account_confirmed = ? WHERE clef = ?');
					$update->execute(array($avatar, $compte, $_SESSION['getid']));
					$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
					$log->execute(array($_SESSION['account']['clef'], 'Modification des options de profil de <b>'.$username.'</b>', $this->GetIP(), date('Y-m-d H:i:s')));
					$response = 'success';
					}
				
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/*************************
	* Fonctions de bannissement
	*************************/
	function ADMBan()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$username = $this->Security($_POST['username']);
				$type = $_POST['type'];
				$temps = $this->Security($_POST['temps']);
				$raison = $this->Security($_POST['raison']);
				
				if(!empty($username) && !empty($type) && !empty($temps) && !empty($raison)) {
					
					$recup = $db->prepare('SELECT last_ip, username FROM sting_users WHERE username = ?');
					$recup->execute(array($username));
					$fetch = $recup->fetch();
					
					$ban = $db->prepare('INSERT INTO sting_bans(username, type, raison, ip, date_expiration) VALUES(?, ?, ?, ?, ?)');
					$ban->execute(array($username, $type, $raison, $fetch['last_ip'], date("Y-m-d H:i:s", strtotime('' . date('Y-m-d H:i:s') . '' . " +".$temps." minutes"))));
					$response = 'success';
					
				} else {
					$response = 'Veuillez remplir tous les champs';
				}
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	function ADMListeBan()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				echo '<div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Liste des bannis</h2>
                    </div>

                    <div class="card-block">
                        <table class="table table-bordered mb-0">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Raison</th>
								<th>Fin</th>
                            </tr>
                            </thead>
                            <tbody>';
							$req = $db->query('SELECT * FROM sting_bans ORDER BY id DESC');
                            while($r = $req->fetch()) {
								$info = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
								$info->execute(array($r['username']));
								$f = $info->fetch();
								echo '<tr>
                                <th class="text-center">'.$r['id'].'</th>
                                <td>'.$f['username'].'</td>
								<td>'.$r['type'].'</td>
                                <td>'.$r['raison'].'</td>
                                <td>' . date("d-m-Y à H:i", strtotime($r['date_expiration'])) . '</td>
                            </tr>';
							}
                            '</tbody>
                        </table>
                    </div>
                </div>';
			}
		}
	}
	
	/*************************
	* Fonctions de la boutique
	*************************/
	function VIPBasic()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['account']['rank'] == 1) {
				if($_SESSION['account']['gold'] >= 100) {
					
					$req = $db->prepare('UPDATE sting_users SET rank = ?, gold = gold - ? WHERE clef = ?');
					$req->execute(array('2', '100', $_SESSION['account']['clef']));
					
					$req = $db->prepare('INSERT INTO sting_subscriptions(clef_user, expiration) VALUES(?, ?)');
					$req->execute(array($_SESSION['account']['clef'], date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month"))));
					
					$response = 'success';
					
				} else {
					$response = 'Vous n\'avez pas assez de points';
				}
				
			} else {
				if($_SESSION['account']['rank'] == 2 OR $_SESSION['account']['rank'] == 3 OR $_SESSION['account']['rank'] == 4) {
					$response = 'Vous êtes déjà abonné au VIP';
				} elseif($_SESSION['account']['rank'] >= 5) {
					$response = 'Les Staffs ne peuvent pas adhérer au VIP';
				}
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	function VIPPlus()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['account']['rank'] == 1) {
				if($_SESSION['account']['gold'] >= 200) {
					
					$req = $db->prepare('UPDATE sting_users SET rank = ?, gold = gold - ? WHERE clef = ?');
					$req->execute(array('3', '200', $_SESSION['account']['clef']));
					
					$req = $db->prepare('INSERT INTO sting_subscriptions(clef_user, expiration) VALUES(?, ?)');
					$req->execute(array($_SESSION['account']['clef'], date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month"))));
					
					$response = 'success';
					
				} else {
					$response = 'Vous n\'avez pas assez de points';
				}
				
			} else {
				if($_SESSION['account']['rank'] == 2 OR $_SESSION['account']['rank'] == 3 OR $_SESSION['account']['rank'] == 4) {
					$response = 'Vous êtes déjà abonné au VIP';
				} elseif($_SESSION['account']['rank'] >= 5) {
					$response = 'Les Staffs ne peuvent pas adhérer au VIP';
				}
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	function VIPUltime()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['account']['rank'] == 1) {
				if($_SESSION['account']['gold'] >= 400) {
					
					$req = $db->prepare('UPDATE sting_users SET rank = ?, gold = gold - ? WHERE clef = ?');
					$req->execute(array('4', '400', $_SESSION['account']['clef']));
					
					$req = $db->prepare('INSERT INTO sting_subscriptions(clef_user, expiration) VALUES(?, ?)');
					$req->execute(array($_SESSION['account']['clef'], date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month"))));
					
					$response = 'success';
					
				} else {
					$response = 'Vous n\'avez pas assez de points';
				}
				
			} else {
				if($_SESSION['account']['rank'] == 2 OR $_SESSION['account']['rank'] == 3 OR $_SESSION['account']['rank'] == 4) {
					$response = 'Vous êtes déjà abonné au VIP';
				} elseif($_SESSION['account']['rank'] >= 5) {
					$response = 'Les Staffs ne peuvent pas adhérer au VIP';
				}
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/************************
	* Fonctions des commandes
	************************/
	function ADMListWaiting()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$DPP = 5;
				$TD = $db->query('SELECT * FROM sting_orders');
				$rc = $TD->rowCount();
    	    
				$_SESSION['tt'] = ceil($rc/$DPP);
        
				if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
					$_GET['d'] = intval($_GET['d']);
					$PA = $_GET['d'];
					$_SESSION['PA'] = $_GET['d'];
				} else {
					$PA = 1;
					$_SESSION['PA'] = 1;
				}
        
				$go = ($PA-1)*$DPP;
      	  
				$CommandesEnAttente = $db->query('SELECT * FROM sting_orders ORDER BY type DESC LIMIT '.$go.','.$DPP);
				$rowCount = $CommandesEnAttente->rowCount();
        
				if($rowCount == 0) {
					$_SESSION['aucunecommande'] = "No commande";
				} else {
					while($cea = $CommandesEnAttente->fetch()) {
				 echo '<tr>
											<td>'.$cea['id'].'</td>
											<td>'.$cea['name'].'</td>
											<td>'.$cea['extension'].'</td>
											<td>'.$cea['plan'].'</td>
											<td>'.$cea['options'].'</td>
											<td>'.$cea['type'].'</td>
                                            <td>' . date("d-m-Y à H:i", strtotime($cea['date_order'])) . '</td>
											<td><a href="orders?p=waiting&id='.$cea['id'].'">Gérer</a></td>
										</tr>';
					}
        
				}
			}
		}
	}
	
	function ADMGoOrders()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				
				$req = $db->prepare('SELECT * FROM sting_orders WHERE id = ?');
				$req->execute(array($_GET['id']));
				$fetch = $req->fetch();
				$rowCount = $req->rowCount();
				
				if($rowCount != 0) {
				
				echo '<div class="col-md-4">
					<div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Information de commande</h2>
                        </div>

                        <div class="card-block">
							<i>Information de commande</i><br>
							Nom: <b>'.$fetch['name'].'</b><br>
							Extension: <b>'.$fetch['extension'].'</b><br>
							Plan: <b>'.$fetch['plan'].'</b><br>
							Options: <b>'.$fetch['options'].'</b>
                        </div>
                    </div>
					<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal_name">Changer le nom</button>';
					
					if($fetch['options'] == 'SWF' OR $fetch['options'] == 'SWF + Session') {
					echo '<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal_swf">Configurer les liens des SWF\'s</button>
					<div id="modal_swf" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">Configurer les SWF\'s</h4>
									</div>
									<div class="modal-body">	
									<form method="post" id="changer" action="/core/action/action?to=admswf">
										<input name="variables" class="form-control" placeholder="Variables">
										<input name="text" class="form-control" placeholder="Text">
										<input name="override" class="form-control" placeholder="Override Variables">
										<input name="furnidata" class="form-control" placeholder="Furnidata">
										<input name="figuredata" class="form-control" placeholder="Figuredata">
										<input name="productdata" class="form-control" placeholder="Productdata">
										<input name="game" class="form-control" placeholder="Game">
										<input name="swf" class="form-control" placeholder=".SWF">
										<button class="btn btn-primary btn-block">Valider</button>
										</form>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
									</div>
								</div>
							</div>
						</div>
					';
						
					}
					
					echo '<div id="modal_name" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">Modifier le nom du rétro</h4>
									</div>
									<div class="modal-body">	
									<form method="post" id="changer" action="/core/action/action?to=admchangenamer">
									<label>Nom du rétro</label>
										<input name="namer" class="form-control" placeholder="Nom du rétro">
										<label>Extension</label>
										<select name="extensionr" class="form-control">
											<option value=".fr">.fr</option>
											<option value=".eu">.eu</option>
											<option value=".com">.com</option>
											<option value=".net">.net</option>
											<option value=".org">.org</option>
											<option value=".xyz">.xyz</option>
                                            <option value=".ga">.ga</option>
                                            <option value".cf">.cf</option>
									    	<option value=".tk">.tk</option>
                                            <option value=".ml">.ml</option>
										</select>
										<button class="btn btn-primary btn-block">Valider</button>
										</form>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
									</div>
								</div>
							</div>
						</div>
                </div>
				</div>
				<div class="col-md-8"><div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Information de commande</h2>
                        </div>';
						
						if($fetch['options'] == 'Session' OR $fetch['options'] == 'SWF + Session') {
							
							echo '<div class="card-block">
						<form method="post" id="order" action="/core/action/action?to=admvalidorder">
						<div class="row">
						<div class="col-sm-4">
							<i>Information MySQL</i>
							<div class="form-group">
                                <input type="text" class="form-control" name="hotemysql" placeholder="Hôte MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="usermysql" placeholder="Utilisateur MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="passmysql" placeholder="Mot de passe MySQL">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-4">
							<i>Information FTP</i>
							<div class="form-group">
                                <input type="text" class="form-control" name="hoteftp" placeholder="Hôte FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="userftp" placeholder="Utilisateur FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="passftp" placeholder="Mot de passe FTP">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-4">
							<i>Information Session</i>
							<div class="form-group">
                                <input type="text" class="form-control" name="ipsession" placeholder="IP Session">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="usersession" placeholder="Utilisateur Session">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="passsession" placeholder="Mot de passe Session">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block">Valider la commande</button>
						</div>
						</div>
						</div>
						</form>
						</div></div>';
							
						} else {
							echo '<div class="card-block">
						<form method="post" id="order" action="/core/action/action?to=admvalidorder">
						<div class="row">
						<div class="col-sm-6">
							<i>Information MySQL</i>
							<div class="form-group">
                                <input type="text" class="form-control" name="hotemysql" placeholder="Hôte MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="usermysql" placeholder="Utilisateur MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="passmysql" placeholder="Mot de passe MySQL">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-6">
							<i>Information FTP</i>
							<div class="form-group">
                                <input type="text" class="form-control" name="hoteftp" placeholder="Hôte FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="userftp" placeholder="Utilisateur FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" name="passftp" placeholder="Mot de passe FTP">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block">Valider la commande</button>
						</div>
						</div>
						</div>
						</form>
						</div></div>';
						}
                        
					'</div>
				</div></div></div>';
					
				} else {
					echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="orders?p=waiting"</SCRIPT>';
				}
				
			}
		}
	
	}
	
	function ADMValidOrder()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$req = $db->prepare('SELECT * FROM sting_orders WHERE id = ?');
				$req->execute(array($_SESSION['idorder']));
				$fetch = $req->fetch();
				$rowCount = $req->rowCount();
				
				$hoteftp = $this->Security($_POST['hoteftp']);
				$userftp = $this->Security($_POST['userftp']);
				$passftp = $this->Security($_POST['passftp']);
				
				$hotemysql = $this->Security($_POST['hotemysql']);
				$usermysql = $this->Security($_POST['usermysql']);
				$passmysql = $this->Security($_POST['passmysql']);
				
				$ipsession = $this->Security($_POST['ipsession']);
				$usersession = $this->Security($_POST['usersession']);
				$passsession = $this->Security($_POST['passsession']);
				
				if($rowCount != 0) {
				
				if($fetch['options'] == 'Session' OR $fetch['options'] == 'SWF + Session') {
					
					if(!empty($hoteftp) && !empty($userftp) && !empty($passftp) && !empty($hotemysql) && !empty($usermysql) && !empty($passmysql) && !empty($ipsession) && !empty($usersession) && !empty($passsession)) {
						
						$idservice = mt_rand(1000, 99999999);
						
						$_SESSION['idids'] = $idservice;
						$_SESSION['clefuser'] = $fetch['clef_user'];
						
						$service = $db->prepare('INSERT INTO sting_services(id_service, clef_user, name, extension, plan, état, options, date_order, date_validation, date_expiration) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
						$service->execute(array($idservice, $fetch['clef_user'], $fetch['name'], $fetch['extension'], $fetch['plan'], 'Actif', $fetch['options'], $fetch['date_order'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month"))));
						
							$ndd = $db->prepare('INSERT INTO sting_domains(clef_user, name, extension, état, date_order, date_expiration, dns_1, dns_2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
							$ndd->execute(array($fetch['clef_user'], $fetch['name'], $fetch['extension'], 'Actif', $fetch['date_order'], date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 year")), 'ns1.yourhabbo.fr', 'ns2.yourhabbo.fr'));
						
						$access = $db->prepare('INSERT INTO sting_access(mysql_hôte, mysql_user, mysql_pass, ftp_hôte, ftp_user, ftp_pass, session_ip, session_user, session_pass, clef_user, id_service, session_expiration, session_état) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
						$access->execute(array($hotemysql, $usermysql, $passmysql, $hoteftp, $userftp, $passftp, $ipsession, $usersession, $passsession, $fetch['clef_user'], $idservice, date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month")), 'Active'));
						
						$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
						$log->execute(array($_SESSION['account']['clef'], 'Livraison de '.$fetch['name'].''.$fetch['extension'].'', $this->GetIP(), date('Y-m-d H:i:s')));
						
						$shoutbox = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
						$shoutbox->execute(array(2, '<i>Le rétro <b style="color: black">'.$fetch['name'].''.$fetch['extension'].'</b> vient d\'être livré.</i>', date('Y-m-d H:i:s')));
						
						$remove = $db->prepare('DELETE FROM sting_orders WHERE id = ?');
						$remove->execute(array($_SESSION['idorder']));
						
						$response = 'Livraison effectué';
						
					} else {
						$response = 'Veuillez remplir tous les champs';
					}
					
				} else {
					if(!empty($hoteftp) && !empty($userftp) && !empty($passftp) && !empty($hotemysql) && !empty($usermysql) && !empty($passmysql)) {
						
						$idservice = mt_rand(1000, 999999999);
						
						$service = $db->prepare('INSERT INTO sting_services(id_service, clef_user, name, extension, plan, état, options, date_order, date_validation, date_expiration) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
						$service->execute(array($idservice, $fetch['clef_user'], $fetch['name'], $fetch['extension'], $fetch['plan'], 'Actif', $fetch['options'], $fetch['date_order'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 month"))));
						
							$ndd = $db->prepare('INSERT INTO sting_domains(clef_user, name, extension, état, date_order, date_expiration, dns_1, dns_2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
							$ndd->execute(array($fetch['clef_user'], $fetch['name'], $fetch['extension'], 'Actif', $fetch['date_order'], date('Y-m-d H:i:s', strtotime('' . date('Y-m-d H:i:s') . '' . " +1 year")), 'ns1.yourhabbo.fr', 'ns2.yourhabbo.fr'));
						
						$access = $db->prepare('INSERT INTO sting_access(mysql_hôte, mysql_user, mysql_pass, ftp_hôte, ftp_user, ftp_pass, session_ip, session_user, session_pass, clef_user, id_service) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
						$access->execute(array($hotemysql, $usermysql, $passmysql, $hoteftp, $userftp, $passftp, 'Aucun', 'Aucun', 'Aucun', $fetch['clef_user'], $idservice));
						
						$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
						$log->execute(array($_SESSION['account']['clef'], 'Livraison de '.$fetch['name'].''.$fetch['extension'].'', $this->GetIP(), date('Y-m-d H:i:s')));
						
						$shoutbox = $db->prepare('INSERT INTO sting_shoutbox(clef_user, message, date) VALUES(?, ?, ?)');
						$shoutbox->execute(array(2, '<i>Le rétro <b style="color: black">'.$fetch['name'].''.$fetch['extension'].'</b> vient d\'être livré.</i>', date('Y-m-d H:i:s')));
						
						$remove = $db->prepare('DELETE FROM sting_orders WHERE id = ?');
						$remove->execute(array($_SESSION['idorder']));
						
						$response = 'Livraison effectué';
						
					} else {
						$response = 'Veuillez remplir tous les champs';
					}
				}
					
				} else {
					$response = 'Vous avez déjà livré cette commande ou la commande n\'existe plus';
				}
				
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	function ADMListActives()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$DPP = 5;
				$TD = $db->prepare('SELECT * FROM sting_services WHERE état = ?');
				$TD->execute(array('Actif'));
				$rc = $TD->rowCount();
    	    
				$_SESSION['ttt'] = ceil($rc/$DPP);
        
				if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
					$_GET['d'] = intval($_GET['d']);
					$PA = $_GET['d'];
					$_SESSION['PAA'] = $_GET['d'];
				} else {
					$PA = 1;
					$_SESSION['PAA'] = 1;
				}
        
				$go = ($PA-1)*$DPP;
      	  
				$CommandesActives = $db->prepare('SELECT * FROM sting_services WHERE état = ? LIMIT '.$go.','.$DPP);
				$CommandesActives->execute(array('Actif'));
				$rowCount = $CommandesActives->rowCount();
        
				if($rowCount == 0) {
					$_SESSION['aucunecommande'] = "No commande";
				} else {
					while($cea = $CommandesActives->fetch()) {
				 echo '<tr>
											<td>'.$cea['id'].'</td>
											<td>'.$cea['name'].'</td>
											<td>'.$cea['extension'].'</td>
											<td><a href="orders?p=active&id='.$cea['id'].'">Gérer</a></td>
										</tr>';
					}
        
				}
			}
		}
	}
	
	function ADMManageValid()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 6) {
			$req = $db->prepare('SELECT * FROM sting_services WHERE id = ?');
			$req->execute(array($_SESSION['idv']));
			$fetch = $req->fetch();
			$rowCount = $req->rowCount();
			if($rowCount != 0) {
				
				$infio = $db->prepare('SELECT * FROM sting_access WHERE id_service = ?');
				$infio->execute(array($fetch['id_service']));
				$fetcha = $infio->fetch();
				
				$_SESSION['idddd'] = $fetch['id_service'];
				
				echo '<div class="col-md-4"><div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Modification par défaut</h2>
                        </div><div class="card-block">
						<form method="post" id="changevalid1" action="/core/action/action?to=admmodifvalide">
						<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Nom</label>
                                <input type="text" class="form-control" name="name" placeholder="Nom" value="'.$fetch['name'].'">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>Extension</label>
                                <select name="extension" class="form-control">
									<option ' . ($fetch['extension'] == '.fr' ? 'selected' : '') . ' value=".fr">.fr</option>
									<option ' . ($fetch['extension'] == '.eu' ? 'selected' : '') . ' value=".eu">.eu</option>
									<option ' . ($fetch['extension'] == '.com' ? 'selected' : '') . ' value=".com">.com</option>
									<option ' . ($fetch['extension'] == '.net' ? 'selected' : '') . ' value=".net">.net</option>
									<option ' . ($fetch['extension'] == '.org' ? 'selected' : '') . ' value=".org">.org</option>
									<option ' . ($fetch['extension'] == '.xyz' ? 'selected' : '') . ' value=".xyz">.xyz</option>
                                    <option ' . ($fetch['extension'] == '.ga' ? 'selected' : '') . ' value=".ga">.ga</option>
                                    <option ' . ($fetch['extension'] == '.cf' ? 'selected' : '') . ' value".cf">.cf</option>
                                    <option ' . ($fetch['extension'] == '.tk' ? 'selected' : '') . ' value=".tk">.tk</option>
                                    <option ' . ($fetch['extension'] == '.ml' ? 'selected' : '') . ' value=".ml">.ml</option>
								</select>
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
								<label>État</label>
                                <select name="etat" class="form-control">
									<option ' . ($fetch['extension'] == 'Actif' ? 'selected' : '') . ' value="Actif">Actif</option>
									<option ' . ($fetch['extension'] == 'Expiré' ? 'selected' : '') . ' value="Expiré">Expiré</option>
									<option ' . ($fetch['extension'] == 'Résilié' ? 'selected' : '') . ' value="Résilié">Résilié</option>
									<option ' . ($fetch['extension'] == 'Suspendu' ? 'selected' : '') . ' value="Suspendu">Suspendu</option>
								</select>
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block waves-effect">Modifier</button>
						</div>
						</div>
						</form></div>
						
						</div></div>											</div>
						';
							
						echo '<div class="col-md-8"><div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Information de commande</h2>
                        </div>';
						
						if($fetch['options'] == 'Session' OR $fetch['options'] == 'SWF + Session') {
							
							echo '<div class="card-block">
						<form method="post" id="order" action="/core/action/action?to=admmodifaccess">
						<div class="row">
						<div class="col-sm-4">
							<i>Information MySQL</i>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['mysql_hôte'].'" name="hotemysql" placeholder="Hôte MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['mysql_user'].'" name="usermysql" placeholder="Utilisateur MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['mysql_pass'].'" name="passmysql" placeholder="Mot de passe MySQL">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-4">
							<i>Information FTP</i>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['ftp_hôte'].'" name="hoteftp" placeholder="Hôte FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['ftp_user'].'" name="userftp" placeholder="Utilisateur FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['ftp_pass'].'" name="passftp" placeholder="Mot de passe FTP">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-4">
							<i>Information Session</i>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['session_ip'].'" name="ipsession" placeholder="IP Session">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['session_user'].'" name="usersession" placeholder="Utilisateur Session">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['session_pass'].'" name="passsession" placeholder="Mot de passe Session">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block">Modifier</button>
						</div>
						</div>
						</div>
						</form>
						</div></div>';
							
						} else {
							echo '<div class="card-block">
						<form method="post" id="order" action="/core/action/action?to=admmodifaccess">
						<div class="row">
						<div class="col-sm-6">
							<i>Information MySQL</i>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['mysql_hôte'].'" name="hotemysql" placeholder="Hôte MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['mysql_user'].'" name="usermysql" placeholder="Utilisateur MySQL">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['mysql_pass'].'" name="passmysql" placeholder="Mot de passe MySQL">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-6">
							<i>Information FTP</i>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['ftp_hôte'].'" name="hoteftp" placeholder="Hôte FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['ftp_user'].'" name="userftp" placeholder="Utilisateur FTP">
                                <i class="form-group__bar"></i>
                            </div>
							<div class="form-group">
                                <input type="text" class="form-control" value="'.$fetcha['ftp_pass'].'" name="passftp" placeholder="Mot de passe FTP">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block">Valider la commande</button>
						</div>
						</div>
						</div>
						</form>
						</div></div>';
						}
                        
					'</div>
				</div></div></div>';
					
			} else {
				echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="orders"</SCRIPT>';
			}
		}
	}
	
	function ADMModifValide()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 6) {
			$name = $this->Security($_POST['name']);
			$extension = $this->Security($_POST['extension']);
			$etat = $this->Security($_POST['etat']);
			
			$modif = $db->prepare('UPDATE sting_services SET name = ?, extension = ?, état = ? WHERE id = ?');
			$modif->execute(array($name, $extension, $etat, $_SESSION['idv']));
			
			$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
			$log->execute(array($_SESSION['account']['clef'], 'Modification du nom de rétro '.$name.''.$extension.'', $this->GetIP(), date('Y-m-d H:i:s')));
			
			$response = 'Modification effectué';
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function ADMModifAccess()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 6) {
			
			$hoteftp = $this->Security($_POST['hoteftp']);
			$userftp = $this->Security($_POST['userftp']);
			$passftp = $this->Security($_POST['passftp']);
				
			$hotemysql = $this->Security($_POST['hotemysql']);
			$usermysql = $this->Security($_POST['usermysql']);
			$passmysql = $this->Security($_POST['passmysql']);
				
			$ipsession = $this->Security($_POST['ipsession']);
			$usersession = $this->Security($_POST['usersession']);
			$passsession = $this->Security($_POST['passsession']);
			$expirationsession = $this->Security($_POST['expirationsession']);
			$sessionetat = $this->Security($_POST['etatsession']);
			
			if($fetch['options'] == 'Session' OR $fetch['options'] == 'SWF + Session') {
				if(!empty($hoteftp) && !empty($userftp) && !empty($passftp) && !empty($hotemysql) && !empty($usermysql) && !empty($passmysql) && !empty($ipsession) && !empty($usersession) && !empty($passsession)) {
						$req = $db->prepare('SELECT * FROM sting_access WHERE id_service = ?');
						$req->execute(array($_SESSION['idddd']));
						$rowCount = $req->rowCount();
						if($rowCount == 0) {
							$access = $db->prepare('INSERT INTO sting_access(mysql_hôte, mysql_user, mysql_pass, ftp_hôte, ftp_user, ftp_pass, session_ip, session_user, session_pass, clef_user, id_service, session_expiration, session_état) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
							$access->execute(array($hotemysql, $usermysql, $passmysql, $hoteftp, $userftp, $passftp, $ipsession, $usersession, $passsession, $ZR['clef_user'], $_SESSION['idddd'], $expirationsession, $sessionetat));
							$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
							$log->execute(array($_SESSION['account']['clef'], 'Livraison des accès de #'.$_SESSION['idddd'].'', $this->GetIP(), date('Y-m-d H:i:s')));
							$response = 'Ajout affectué';
						} else {
							$info = $db->prepare('SELECT * FROM sting_services WHERE id_service = ?');
							$info->execute(array($_SESSION['idddd']));
							$ZR = $info->fetch();
							
							$update = $db->prepare('UPDATE sting_access SET mysql_hôte = ?, mysql_user = ?, mysql_pass = ?, ftp_hôte = ?, ftp_user = ?, ftp_pass = ?, session_ip = ?, session_user = ?, session_pass = ? WHERE clef_user = ? AND id_service = ?');
							$update->execute(array($hotemysql, $usermysql, $passmysql, $hoteftp, $userftp, $passftp, $ipsession, $usersession, $passsession, $ZR['clef_user'], $_SESSION['idddd']));
							$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
							$log->execute(array($_SESSION['account']['clef'], 'Livraison des accès de #'.$_SESSION['idddd'].'', $this->GetIP(), date('Y-m-d H:i:s')));
							$response = 'Modification effectué';
						}
						
					} else {
						$response = 'Veuillez remplir tous les champs';
					}
				
				} else {
					if(!empty($hoteftp) && !empty($userftp) && !empty($passftp) && !empty($hotemysql) && !empty($usermysql) && !empty($passmysql)) {
						$req = $db->prepare('SELECT * FROM sting_access WHERE id_service = ?');
						$req->execute(array($_SESSION['idddd']));
						$rowCount = $req->rowCount();
						if($rowCount == 0) {
							
							$info = $db->prepare('SELECT * FROM sting_services WHERE id_service = ?');
							$info->execute(array($_SESSION['idddd']));
							$ZR = $info->fetch();
							
							$access = $db->prepare('INSERT INTO sting_access(mysql_hôte, mysql_user, mysql_pass, ftp_hôte, ftp_user, ftp_pass, clef_user, id_service) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
							$access->execute(array($hotemysql, $usermysql, $passmysql, $hoteftp, $userftp, $passftp, $ZR['clef_user'], $_SESSION['idddd']));
							
							$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
							$log->execute(array($_SESSION['account']['clef'], 'Livraison des accès de #'.$_SESSION['idddd'].'', $this->GetIP(), date('Y-m-d H:i:s')));
							
							$response = 'Ajout effectué';
						} else {
							$info = $db->prepare('SELECT * FROM sting_services WHERE id_service = ?');
							$info->execute(array($_SESSION['idddd']));
							$ZR = $info->fetch();
							
							$update = $db->prepare('UPDATE sting_access SET mysql_hôte = ?, mysql_user = ?, mysql_pass = ?, ftp_hôte = ?, ftp_user = ?, ftp_pass = ? WHERE clef_user = ? AND id_service = ?');
							$update->execute(array($hotemysql, $usermysql, $passmysql, $hoteftp, $userftp, $passftp, $ZR['clef_user'], $_SESSION['idddd']));
							
							$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
							$log->execute(array($_SESSION['account']['clef'], 'Livraison des accès de #'.$_SESSION['idddd'].'', $this->GetIP(), date('Y-m-d H:i:s')));
							$response = 'Modification effectué';
						}
					} else {
						$response = 'Veuillez remplir tous les champs';
					}
				}
			
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function ADMSWF()
	{
		
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 6) {
			
			$variables = $this->Security($_POST['variables']);
			$text = $this->Security($_POST['text']);
			$override = $this->Security($_POST['override']);
			$furnidata = $this->Security($_POST['furnidata']);
			$figuredata = $this->Security($_POST['figuredata']);
			$productdata = $this->Security($_POST['productdata']);
			$game = $this->Security($_POST['game']);
			$swf = $this->Security($_POST['swf']);
			
			if((!empty($variables) && !empty($text) && !empty($override) && !empty($furnidata) && !empty($figuredata) && !empty($productdata) && !empty($game) && !empty($swf))) {
				$req = $db->prepare('SELECT * FROM sting_access WHERE id_service = ? AND clef_user = ?');
				$req->execute(array($_SESSION['idids'], $_SESSION['clefuser']));
				$rowCount = $req->rowCount();
				if($rowCount == 0) {
					$response = 'Veuillez configurer le rétro avant';
				} else {
					$update = $db->prepare('UPDATE sting_access SET swf_variables = ?, swf_text = ?, swf_override_variables = ?, swf_furnidata = ?, swf_figuredata = ?, swf_productdata = ?, swf_game = ?, swf_habbo_swf = ? WHERE id_service = ? AND clef_user = ?');
					$update->execute(array($variables, $text, $override, $furnidata, $figuredata, $productdata, $game, $swf, $_SESSION['idids'], $_SESSION['clefuser']));
					$response = 'SWF livré';
					$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
					$log->execute(array($_SESSION['account']['clef'], 'Livrasion SWF de #'.$_SESSION['idids'].'', $this->GetIP(), date('Y-m-d H:i:s')));
				}
				
				
			} else {
				$response = 'Veuillez remplir tous les champs';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function ADMRedemReinstall()
	{
		
		
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 5) {
				$DPP = 5;
				$TD = $db->query('SELECT * FROM sting_repairs');
				$rc = $TD->rowCount();
    	    
				$_SESSION['tt'] = ceil($rc/$DPP);
        
				if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
					$_GET['d'] = intval($_GET['d']);
					$PA = $_GET['d'];
					$_SESSION['PA'] = $_GET['d'];
				} else {
					$PA = 1;
					$_SESSION['PA'] = 1;
				}
        
				$go = ($PA-1)*$DPP;
      	  
				$Operator = $db->query('SELECT * FROM sting_repairs ORDER BY date LIMIT '.$go.','.$DPP);
				$rowCount = $Operator->rowCount();
        
				if($rowCount == 0) {
					$_SESSION['aucunecommande'] = "No commande";
				} else {
					while($cea = $Operator->fetch()) {
						$info = $db->prepare('SELECT * FROM sting_services WHERE id_service = ?');
						$info->execute(array($cea['id_service']));
						$fefe = $info->fetch();
				 echo '<tr>
											<td>'.$fefe['name'].'</td>
											<td>'.$fefe['extension'].'</td>
											<td>'.$cea['type'].'</td>
											<td>'.$fefe['plan'].'</td>
											<td>' . date("d-m-Y à H:i", strtotime($cea['date'])) . '</td>
											<td><a href="?ok='.$cea['id'].'">Effectué</a></td>
										</tr>';
					}
        
				}
			}
		}
	}
	
	function ADMOperator()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 5) {
				if((int) $_GET['ok']) {
					$id = (int) $_GET['ok'];
					$req = $db->prepare('SELECT * FROM sting_repairs WHERE id = ?');
					$req->execute(array($id));
					$rowCount = $req->rowCount();
					$fetch = $req->fetch();
					if($rowCount == 1) {
						$inf = $db->prepare('SELECT * FROM sting_services WHERE id_service = ?');
						$inf->execute(array($fetch['id_service']));
						$ee = $inf->fetch();
						$ok = $db->prepare('INSERT INTO sting_notifications(clef_user, content, date, open, avatar) VALUES(?, ?, ?, ?, ?)');
						$ok->execute(array($ee['clef_user'], 'L\'opération "'.$fetch['type'].'" a bien été effectué', date('Y-m-d H:i:s'), '0', $_SESSION['account']['avatar']));
						$delete = $db->prepare('DELETE FROM sting_repairs WHERE id = ?');
						$delete->execute(array($id));
						$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
						$log->execute(array($_SESSION['account']['clef'], 'Opération '.$fetch['type'].' du service #'.$fetch['id_service'].'', $this->GetIP(), date('Y-m-d H:i:s')));
					} 
				}
				
			}
		}
	}
	
	function ADMOptionsOrder()
	{
		
		
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 5) {
				$DPP = 5;
				$TD = $db->query('SELECT * FROM sting_orders_options');
				$rc = $TD->rowCount();
    	    
				$_SESSION['tt'] = ceil($rc/$DPP);
        
				if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
					$_GET['d'] = intval($_GET['d']);
					$PA = $_GET['d'];
					$_SESSION['PA'] = $_GET['d'];
				} else {
					$PA = 1;
					$_SESSION['PA'] = 1;
				}
        
				$go = ($PA-1)*$DPP;
      	  
				$opt = $db->query('SELECT * FROM sting_orders_options ORDER BY date LIMIT '.$go.','.$DPP);
				$rowCount = $opt->rowCount();
        
				if($rowCount == 0) {
					$_SESSION['aucunecommande'] = "No commande";
				} else {
					while($cea = $opt->fetch()) {
						$info = $db->prepare('SELECT * FROM sting_services WHERE id_service = ?');
						$info->execute(array($cea['id_service']));
						$fefe = $info->fetch();
				 echo '<tr>
											<td>'.$fefe['name'].'</td>
											<td>'.$fefe['extension'].'</td>
											<td>'.$cea['options'].'</td>
											<td>' . date("d-m-Y à H:i", strtotime($cea['date'])) . '</td>
											<td><a href="?id='.$cea['id'].'">Livrer</a></td>
										</tr>';
					}
        
				}
			}
		}
	}
	
	/*****************************************************
	* Fonctions pour récupérer les badges de l'utilisateur
	*****************************************************/
	function MyBadges()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_users_badges WHERE clef_user = ?');
			$req->execute(array($_SESSION['account']['clef']));
			$rowCount = $req->rowCount();
			
			if($rowCount == 0) {
				echo '<center>Vous n\'avez aucun badge</center>';
			} else {
			
			while($r = $req->fetch()) {
				$info = $db->prepare('SELECT * FROM sting_badges WHERE badge_id = ?');
				$info->execute(array($r['badge_id']));
				$fet = $info->fetch();
				echo '<img data-popup="popover" class="badgestyle" data-placement="bottom" title="" data-content="'.$fet['description'].'" data-original-title="'.$fet['title'].'" src="/badges/'.$r['badge_id'].'.gif">';
			}
			}
		}
	}
	
	function Badges()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			echo '<center>Bientôt disponible</center>';
		}
	}
	
	function OrderBadge()
	{
		$response = $post;
		
		echo json_encode(['response' => $response]);
	}
	
	/********************************************
	* Fonction d'affichage du plans sur la racine
	********************************************/
	function PlansRacine()
	{
		$db = Connect::getDB();
		
		$req = $db->query('SELECT * FROM sting_plans');
		
		while($r = $req->fetch()) {
			echo '<div class="col-12 col-md-3">
              <div class="pricing-1">
                <p class="plan-name">'.$r['name'].'</p>
                <br>
                <h2 class="price">'.$r['price'].'P</h2>
                <br>

                <small>'.$r['cms'].' <i class="fa fa-check" style="color: green"></i></small><br>
                <small>'.$r['émulateur'].' <i class="fa fa-check" style="color: green"></i></small><br>
                <small>'.$r['access'].' <i class="fa fa-check" style="color: green"></i></small><br>
                <br>
                <p class="text-center py-3"><a class="btn btn-primary" href="/client/orders?id='.$r['id'].'">Commander</a><div style="height: 1px"></div><a class="btn btn-primary btn-primary" href="'.$r['demo'].'">Démo</a></p>
              </div>
            </div>';
		}
	}
	
	/**********************
	* Fonctions des tickets
	**********************/
	function ADMListTickets()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			
			$DPP = 10;
        $TD = $db->prepare('SELECT * FROM sting_support WHERE état = ? OR état = ?');
		$TD->execute(array('Ouvert', 'En attente'));
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;

		$req = $db->prepare('SELECT * FROM sting_support WHERE état = ? OR état = ? ORDER BY date_open LIMIT '.$go.','.$DPP);
		$req->execute(array('Ouvert', 'En attente'));
        $rowCount = $req->rowCount();
        
        if($rowCount == 0) {
            $_SESSION['aucunticket'] = "<script>new PNotify({
			title: '<i class=\"fa fa-info-circle\"></i> Information',
			text: 'Il y a aucun ticket.',
			addclass: 'bg-info'
		});</script>";
        } else {
             while($r = $req->fetch()) {
            echo '<tr>
						<td>'.$r['id'].'</td>
						<td>'.$r['sujet'].'</td>
						<td>'.$r['département'].'</td>
						<td>'.$r['état'].'</td>
						<td>'.$this->ConvertTime($r['date_open']).'</td>
						<td><a href="?id='.$r['id'].'">Répondre</a></td>
					  </td>';
            }
        
        }
			
		}
	}
	
	function ADMLookTicket()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			
			$req = $db->prepare('SELECT * FROM sting_support WHERE id = ?');
			$req->execute(array($_GET['id']));
			$fetch = $req->fetch();
			
			$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
			$info->execute(array($fetch['clef_user']));
			$fet = $info->fetch();
			
			echo '<div class="messages">
                        <div class="messages__body">
                            <div class="messages__header">
                                <div class="toolbar toolbar--inner mb-0">
                                    <div class="toolbar__label"><b>'.$fet['username'].'</b> a ouvert <i>'.$fetch['sujet'].'</i></div>
									<div class="actions toolbar__actions">
                                        <form method="post" id="closete" action="/core/action/action?to=admcloseticket">
											<button class="btn btn-danger waves-effect"><i class="zmdi zmdi-close"></i> Fermer</button>
										</form>
                                    </div>
                                </div>
                            </div>

                            <div class="messages__content" id="repo">';
                                
								$rep = $db->prepare('SELECT * FROM sting_support_responses WHERE ticket_id = ? ORDER BY date_response DESC');
								$rep->execute(array($_GET['id']));
								while($p = $rep->fetch()) {
									$in = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
									$in->execute(array($p['clef_user']));
									$f = $in->fetch();
									echo '<div class="messages__item ' . ($p['clef_user'] == $_SESSION['account']['clef'] ? 'messages__item--right' : '') . '">
									' . ($p['clef_user'] != $_SESSION['account']['clef'] ? '<img src="'.$f['avatar'].'" class="messages__avatar">' : '') . '

                                    <div class="messages__details">
                                        <p>' . wordwrap($p['response'], 57, "\n", true) . '</p>
                                        <small><i class="zmdi zmdi-time"></i> ' . date("d-m-Y à H:i", strtotime($p['date_response'])) . '</small>
                                    </div>
                                </div>';
								}
								
							echo	'<div class="messages__item">
                                    <img src="'.$fet['avatar'].'" class="messages__avatar" alt="">

                                    <div class="messages__details">
                                        <p>' . wordwrap($fetch['contenu'], 57, "\n", true) . '</p>
                                        <small><i class="zmdi zmdi-time"></i> ' . date("d-m-Y à H:i", strtotime($fetch['date_open'])) . '</small>
                                    </div>
                                </div>

                                
                            </div>
							<form method="post" id="addrept" action="/core/action/action?to=admaddrept">
                            <div class="messages__reply">
                                <textarea class="messages__reply__text" name="rep" id="rep" placeholder="Votre réponse..."></textarea>
                                <button class="btn btn-success btn--icon messages__reply__btn waves-effect"><i class="zmdi zmdi-mail-send"></i></button>
                            </div>
							</form>
                        </div>
                    </div>';
			
		}
	}
	
	function ADMAddRepT()
	{
		$db = Connect::getDB();
		
		$rep = $this->Security($_POST['rep']);
		
		if(isset($_SESSION['admin'])) {
			
			if(!empty($rep)) {
				
				$add = $db->prepare('INSERT INTO sting_support_responses(ticket_id, clef_user, response, date_response) VALUES(?, ?, ?, ?)');
				$add->execute(array($_SESSION['idte'], $_SESSION['account']['clef'], $rep, date('Y-m-d H:i:s')));
				
				$update = $db->prepare('UPDATE sting_support SET état = ?, last_response = ?, last_clef = ? WHERE id = ?');
				$update->execute(array('Répondu', date('Y-m-d H:i:s'), $_SESSION['account']['clef'], $_SESSION['idte']));
				
				$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
				$log->execute(array($_SESSION['account']['clef'], 'Ajout d\'une réponse au ticket #'.$_SESSION['idte'].'', $this->GetIP(), date('Y-m-d H:i:s')));
				
				$repo = '<div class="messages__item messages__item--right">
                                    <div class="messages__details">
                                        <p>' . wordwrap($rep, 57, "\n", true) . '</p>
                                        <small><i class="zmdi zmdi-time"></i> ' . date("d-m-Y à H:i", strtotime(date('Y-m-d H:i:s'))) . '</small>
                                    </div>
                                </div>';
			$response = 'success';
			
			} else {
				$response = 'Veuillez entrer une réponse';
			}
			
			echo json_encode(['response' => $response, 'repo' => $repo]);
		}
	}
	
	function ADMCloseTicket()
	{
		$db = Connect::getDB();
			
		if(isset($_SESSION['admin'])) {
			$req = $db->prepare('SELECT * FROM sting_support WHERE id = ?');
			$req->execute(array($_SESSION['idte']));
			$fetch = $req->fetch();
			if($fetch['état'] != 'Fermé') {
				$update = $db->prepare('UPDATE sting_support SET état = ? WHERE id = ?');
				$update->execute(array('Fermé', $_SESSION['idte']));
				$log = $db->prepare('INSERT INTO sting_logs_admin(clef_user, action, ip, date) VALUES(?, ?, ?, ?)');
				$log->execute(array($_SESSION['account']['clef'], 'Fermeture du ticket #'.$_SESSION['idte'].'', $this->GetIP(), date('Y-m-d H:i:s')));
				$response = 'success';
			} else {
				$response = 'Ce ticket est déjà fermé';
			}
		}
		
		echo json_encode(['response' => $response]);
	}

	/*****************************************
	* Fonction de changement de nom d'un rétro
	*****************************************/
	function ADMChangeNameR() 
	{
		
		$db = Connect::getDB();
		
		if(isset($_SESSION['admin'])) {
			if($_SESSION['account']['rank'] >= 6) {
				$name = $this->Security($_POST['namer']);
				$extension = $this->Security($_POST['extensionr']);
				if(!empty($name) && !empty($extension)) {
					$update = $db->prepare('UPDATE sting_orders SET name = ?, extension = ? WHERE id = ?');
					$update->execute(array($name, $extension, $_SESSION['idorder']));
					$response = 'Le nom a bien été modifié';
				} else {
					$response = 'Veuillez remplir tous les champs';
				}
			}
		}
		echo json_encode(['response' => $response]);
	}
	
	/*******************
	* Fonctions du forum
	*******************/
	function Forum()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 2) {
			
		$DPP = 12;
        $TD = $db->query('SELECT * FROM sting_forum');
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $forum = $db->query('SELECT * FROM sting_forum ORDER BY last_comment DESC LIMIT '.$go.','.$DPP);
		$epingle = $db->prepare('SELECT * FROM sting_forum WHERE état = ? OR état = ?');
		$epingle->execute(array('Épinglé', 'Important'));
			while($e = $epingle->fetch()) {
				echo '<tr>
											<td>'.$this->CategorieForum($e['catégorie']).' - <a style="color: black" href="?post='.$e['id'].'">'.$e['sujet'].'</a></td>
											<td>'.$this->ConvertTime($e['last_comment']).'</td>
											<td>' . date("d-m-Y à H:i", strtotime($e['date_open'])) . '</td>
											<td>'.$this->EtatForum($e['état']).'</td>
											<td class="text-center">
												<ul class="icons-list">
													<li><a href="?post='.$e['id'].'"><i class="icon-eye2"></i></a></li>
												</ul>
											</td>
										</tr>';
			}

             while($r = $forum->fetch()) {
				 if($r['état'] == 'Ouvert' OR $r['état'] == 'Fermé') {
					 echo '<tr>
											<td>'.$this->CategorieForum($r['catégorie']).' - <a style="color: black" href="?post='.$r['id'].'">'.$r['sujet'].'</a></td>
											<td>'.$this->ConvertTime($r['last_comment']).'</td>
											<td>' . date("d-m-Y à H:i", strtotime($r['date_open'])) . '</td>
											<td>'.$this->EtatForum($r['état']).'</td>
											<td class="text-center">
												<ul class="icons-list">
													<li><a href="?post='.$r['id'].'"><i class="icon-eye2"></i></a></li>
												</ul>
											</td>
										</tr>';
				 }
				 
			 	}
        
		}
	}
	
	function ForumPost()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 2) {
			$req = $db->prepare('SELECT * FROM sting_forum WHERE id = ?');
			$req->execute(array($_GET['post']));
			$fetch = $req->fetch();
			$rowcount = $req->rowcount();
			$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
			$info->execute(array($fetch['clef_user']));
			$fetch2 = $info->fetch();
			
			if($rowcount != 0) {
			
			$req5 = $db->prepare('SELECT * FROM sting_forum_likes WHERE clef_user = ? AND forum_id = ?');
			$req5->execute(array($_SESSION['account']['clef'], $_SESSION['getidf']));
			$rowCount5 = $req5->rowCount();
			
			$total = $db->prepare('SELECT COUNT(id) AS nb FROM sting_forum_likes WHERE forum_id = ?');
			$total->execute(array($_SESSION['getidf']));
			$fetch5 = $total->fetch();
				
			echo '<div class="col-md-12">
						<div class="panel panel-flat">
							<div class="panel-heading">
								<h5 class="panel-title">'.$this->CategorieForum($fetch['catégorie']).' - '.$fetch['sujet'].'</h5>
								</div>
								<div class="panel-body">
									<div class="pull-right">Posté '.strtolower($this->ConvertTime($fetch['date_open'])).'</div>
									<div class="row m-b-20 p-l-15 p-r-15">
										<div class="col-md-1 col-xs-3" style="width: 6%">
											<img src="'.$fetch2['avatar'].'" height="50px" width="50px" class="img-circle" alt="">
										</div>
										<h4 class="no-margin text-semibold '.$this->RankColor($fetch2['rank']).'">'.$fetch2['username'].'</h4>
										<small class="no-margin">'.$this->Rank($fetch2['rank']).'</small>
									</div>
										'.$this->Smiley($fetch['contenu']).'
									</div>
									<div class="panel-footer">
										<form method="post" id="likeordislike" action="/core/action/action?to=likeordislike">
											<button id="but" class="btn-link">' . ($rowCount5 == 0 ? '<i class="fa fa-thumbs-o-up"></i> J\'aime ('.$fetch5['nb'].')' : '') . '' . ($rowCount5 != 0 ? '<i class="fa fa-thumbs-o-down"></i> Je n\'aime plus ('.$fetch5['nb'].')' : '') . '</button>
										</form>
									</div>
								</div>
							</div>';
			echo '<div class="col-md-12">
							<ul class="media-list">
								<li class="media">
									<a href="#" class="pull-left"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-sm pull-right"></a>
									<div class="media-body">
										<form action="/core/action/action?to=forumcomment" id="commente" method="post">
											<textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Entrez votre commentaire"></textarea>
											<div class="row m-t-15">
												<div class="col-xs-12 text-right">
													<button class="btn bg-blue btn-sm btn-labeled">
														<b><i class="icon-paperplane"></i></b>
														 Envoyer 
													</button>
												</div>
											</div>
										</form>
									</div>
								</li>
							</ul>
						</div>';
			
				$com = $db->prepare('SELECT COUNT(id) AS nb FROM sting_forum_comments WHERE forum_id = ?');
				$com->execute(array($_GET['post']));
				$fe = $com->fetch();
			
			echo '<div class="col-md-12">
							Commentaire'.$this->isMany($fe['nb']).'
							<hr>
							<ul class="media-list" id="commenting">';
                                
                                  $reqe = $db->prepare('SELECT * FROM sting_forum_comments WHERE forum_id = ? ORDER BY date DESC');
								  $reqe->execute(array($_GET['post']));
								while($r = $reqe->fetch()) {
									
									$in = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
									$in->execute(array($r['clef_user']));
									$f = $in->fetch();
									echo '<li class="media">
									<a href="#" class="pull-left"><img src="'.$f['avatar'].'" class="img-circle img-sm pull-right"></a>
									<div class="media-body">
										<h5 class="no-margin-top">
											<span class="text-muted pull-right"><small>' . date("d-m-Y à H:i", strtotime($r['date'])) . '</small></span>
											<a href="'.$f['username'].'" class="'.$this->RankColor($f['rank']).'">'.$f['username'].'</a>
										</h5>
                                        <font size="2px">
										
											'.$this->Smiley($r['comment']).'
										
                                        </font>
									</div>
								</li>';
								}
                                                                
					echo	'</ul>
						</div>'	;
				
			} else {
				echo '<SCRIPT LANGUAGE="JavaScript">
document.location.href="forum"
</SCRIPT>';
			}
		}
	}
	
	function ForumComment()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 2) {
			
			$comment = $this->Security($_POST['comment']);
			if(!empty($comment)) {
				$req = $db->prepare('INSERT INTO sting_forum_comments(forum_id, clef_user, comment, date) VALUES(?, ?, ?, ?)');
				$req->execute(array($_SESSION['getidf'], $_SESSION['account']['clef'], $comment, date('Y-m-d H:i:s')));
				$addrep = '<li class="media">
									<a href="#" class="pull-left"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-sm pull-right"></a>
									<div class="media-body">
										<h5 class="no-margin-top">
											<span class="text-muted pull-right"><small>' . date("d-m-Y à H:i", strtotime(date('Y-m-d H:i:s'))) . '</small></span>
											<a href="'.$_SESSION['account']['username'].'" class="'.$this->RankColor($_SESSION['account']['rank']).'">'.$_SESSION['account']['username'].'</a>
										</h5>
                                        <font size="2px">
										
											'.$this->Smiley($comment).'
										
                                        </font>
									</div>
								</li>';
				$update = $db->prepare('UPDATE sting_forum SET last_comment = ? WHERE id = ?');
				$update->execute(array(date('Y-m-d H:i:s'), $_SESSION['getidf']));
				$response = 'success';
			} else {
				$response = 'Veuillez entrer une réponse';
			}
			
			echo json_encode(['response' => $response, 'addrep' => $addrep]);
		}
		
	}
	
	function LikeOrDislike()
	{
		
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 2) {
			
			$req = $db->prepare('SELECT * FROM sting_forum_likes WHERE clef_user = ? AND forum_id = ?');
			$req->execute(array($_SESSION['account']['clef'], $_SESSION['getidf']));
			$rowCount = $req->rowCount();
			
			$total = $db->prepare('SELECT COUNT(id) AS nb FROM sting_forum_likes WHERE forum_id = ?');
			$total->execute(array($_SESSION['getidf']));
			$fetch = $total->fetch();
			
			if($rowCount == 0) {
				$te = $fetch['nb'] + 1;
				$insert = $db->prepare('INSERT INTO sting_forum_likes(clef_user, forum_id, date) VALUES(?, ?, ?)');
				$insert->execute(array($_SESSION['account']['clef'], $_SESSION['getidf'], date('Y-m-d H:i:s')));
				$modif = '<i class="fa fa-thumbs-o-down"></i> Je n\'aime plus ('.$te.')';
				$response = 'success';
			} else {
				$te = $fetch['nb'] - 1;
				$delete = $db->prepare('DELETE FROM sting_forum_likes WHERE clef_user = ? AND forum_id = ?');
				$delete->execute(array($_SESSION['account']['clef'], $_SESSION['getidf']));
				$modif = '<i class="fa fa-thumbs-o-up"></i> J\'aime ('.$te.')';
				$response = 'success';
			}
		
			echo json_encode(['response' => $response, 'modif' => $modif]);
		}
	}
	
	function CreateForum()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 2) {
			
			$sujet = $this->Security($_POST['sujet']);
			$prefixe = $this->Security($_POST['prefixe']);
			$contenu = nl2br($_POST['contenu'], ENT_QUOTES);
			
			if(!empty($sujet) && !empty($prefixe) && !empty($contenu)) {
				if(iconv_strlen($sujet) <= 50) {
					if($prefixe == 'Discussion' OR $prefixe == 'Aide' OR $prefixe == 'Tutoriel') {
						$req = $db->prepare('INSERT INTO sting_forum(clef_user, sujet, contenu, catégorie, état, last_comment, date_open) VALUES(?, ?, ?, ?, ?, ?, ?)');
						$req->execute(array($_SESSION['account']['clef'], $sujet, $contenu, $prefixe, 'Ouvert', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));
						
						$response = 'success';
						
					} elseif($prefixe == 'Discussion' OR $prefixe == 'Aide' OR $prefixe == 'Tutoriel' OR $prefixe == 'Important' OR $prefixe == 'Annoce' AND $_SESSION['account']['rank'] >= 7) {
						$req = $db->prepare('INSERT INTO sting_forum(clef_user, sujet, contenu, catégorie, état, last_comment, date_open) VALUES(?, ?, ?, ?, ?, ?, ?)');
						$req->execute(array($_SESSION['account']['clef'], $sujet, $contenu, $prefixe, 'Ouvert', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));
						
						$response = 'success';
					} else {
						$response = 'Veuillez choisir un préfixe';
					} 
					
				} else {
					$response = 'Le sujet est trop long';
				}
				
			} else {
				$response = 'Veuillez remplir tous les champs';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	/***************************
	* Fonctions des mises à jour
	***************************/
	function Update()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$DPP = 12;
        $TD = $db->query('SELECT * FROM sting_updates');
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $update = $db->query('SELECT * FROM sting_updates ORDER BY date DESC LIMIT '.$go.','.$DPP);

             while($u = $update->fetch()) {
					 echo '<tr>
											<td>' . date("d-m-Y à H:i", strtotime($u['date'])) . '</td>
											<td>'.$u['type_de_tâche'].'</td>
											<td>'.$u['catégorie'].'</td>
											<td>'.$u['système_exploitation'].'</td>
											<td>'.$u['sévérité'].'</td>
											<td>'.$u['résumé'].'</td>
											<td>'.$this->ColorUpdate($u['état']).'</td>
											<td class="text-center">
												<ul class="icons-list">
													<li><a href="?id='.$u['id'].'"><i class="icon-eye2"></i></a></li>
												</ul>
											</td>
										</tr>';
				 
			 	}
		}
	}
	
	function UpdateDetail()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			
			$req = $db->prepare('SELECT * FROM sting_updates WHERE id = ?');
			$req->execute(array($_GET['id']));
			$fetch = $req->fetch();
			$rowcount = $req->rowCount();
			
			if($rowcount == 0) {
				echo '<SCRIPT LANGUAGE="JavaScript">
document.location.href="updates"
</SCRIPT>';
			}
			
			$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
			$info->execute(array($fetch['clef_user']));
			$fetch2 = $info->fetch();
			
			echo '<div class="row">
					<div class="col-md-8">
						<div class="panel panel-default no-margin-bottom no-border-bottom">
							<div class="panel-heading">
								<h3 class="panel-title">'.$fetch['résumé'].'</h3>						
							</div>
							<div class="panel-body">								
								<div class="row">
									<div class="col-md-12">
										'.$fetch['contenu'].'
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="panel panel-flat">							
							<div class="panel-body">
								<div class="row">
									<div class="col-md-6">
										<h5 class="no-margin">Date d\'ouverture:</h5>
									</div>
									
									<div class="col-md-6 text-right">
										<h5 class="no-margin">' . date("d-m-Y à H:i", strtotime($fetch['date'])) . '</h5>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-12 m-t-5">
										<h6 class="no-margin m-b-10">Progression</h6>
										<div class="progress m-t-5 m-b-10">
											<div class="progress-bar progress-bar-success progress-bar-striped active" style="width: '.$fetch['progression'].'%">
												<span class="sr-only">'.$fetch['progression'].'% terminé</span>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-12">
										<h6 class="no-margin">Détail</h6>
										<div class="row">
											<div class="col-md-6 col-xs-6">
												<h5>Type de tâche:</h5>
												<h5>Catégorie:</h5>
												<h5>Système d\'exploitation:</h5>
												<h5>Sévérité:</h5>
												<h5>État:</h5>
											</div>
											
											<div class="col-md-6 col-xs-6 text-right">
												<h5>'.$fetch['type_de_tâche'].'</h5>
												<h5>'.$fetch['catégorie'].'</h5>
												<h5>'.$fetch['système_exploitation'].'</h5>
												<h5>'.$fetch['sévérité'].'</h5>
												<h5>'.$this->ColorUpdate($fetch['état']).'</h5>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-md-12">
										<h6 class="no-margin">Information</h6>
										<div class="row">
											<div class="col-md-6 col-xs-6">
												<h5>Ouverte par:</h5>
												<h5>Grade:</h5>
											</div>
											
											<div class="col-md-6 col-xs-6 text-right">
												<h5 class="'.$this->RankColor($fetch2['rank']).'">'.$fetch2['username'].'</h5>
												<h5>'.$this->Rank($fetch2['rank']).'</h5>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
		}
	}
	
	function ADMUpdatesList()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 8) {
			
		$DPP = 5;
        $TD = $db->query('SELECT * FROM sting_updates');
        $rc = $TD->rowCount();
        
        $_SESSION['tt'] = ceil($rc/$DPP);
        
        if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
            $_GET['d'] = intval($_GET['d']);
            $PA = $_GET['d'];
            $_SESSION['PA'] = $_GET['d'];
        } else {
            $PA = 1;
            $_SESSION['PA'] = 1;
        }
        
        $go = ($PA-1)*$DPP;
        
        $maj = $db->query('SELECT * FROM sting_updates ORDER BY date DESC LIMIT '.$go.','.$DPP);

			while($m = $maj->fetch()) {
				echo '<tr>
											<td>'.$m['type_de_tâche'].'</td>
											<td>'.$m['catégorie'].'</td>
											<td>' . date("d-m-Y à H:i", strtotime($m['date'])) . '</td>
											<td>'.$m['résumé'].'</td>
											<td><a href="?id='.$m['id'].'">Voir</a></td>
										</tr>';
					}

				 
			 	}
        
		}
	
	function ADMCreateUpdate()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 8) {
			
			$title = $this->Security($_POST['title']);
			$tache = $this->Security($_POST['type_tache']);
			$severite = $this->Security($_POST['severite']);
			$categorie = $this->Security($_POST['categorie']);
			$exploitation = $this->Security($_POST['exploitation']);
			$explication = nl2br($_POST['explication'], ENT_QUOTES);
			$etat = $this->Security($_POST['etat']);
			$progression = $this->Security($_POST['progression']);
			
			if(!empty($title) && !empty($tache) && !empty($severite) && !empty($categorie) && !empty($explication) && !empty($etat) && !empty($progression)) {
				
				if($categorie == 'Site' OR $categorie == 'Web' OR $categorie == 'MySQL' OR $categorie == 'VPS' OR $categorie == 'Serveur dédié' OR $categorie == 'Plesk') {
					if($tache == 'Maintenance' OR $tache =='Amélioration' OR $tache == 'Incident') {
						if($severite == 'Très basse' OR $severite == 'Basse' OR $severite == 'Moyenne' OR $severite == 'Haute' OR $severite == 'Très haute' OR $severite == 'Critique') {
							if($exploitation == 'Linux' OR $exploitation == 'Plesk' OR $exploitation == 'Windows' OR $exploitation == 'Proxmox') {
								if($etat == 'En cours' OR $etat == 'Terminé') {
									if(ctype_digit($progression) AND $progression <= 100) {
										
										$add = $db->prepare('INSERT INTO sting_updates(clef_user, type_de_tâche, catégorie, système_exploitation, sévérité, résumé, état, progression, contenu, date) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
										$add->execute(array($_SESSION['account']['clef'], $tache, $categorie, $exploitation, $severite, $title, $etat, $progression, $explication, date('Y-m-d H:i:s')));
										
										$response = 'success';
										
									} else {
										$response = 'Vous avez mal remplis la progression';
									}
									
								} else {
									$response = 'Veuillez entrer l\'état';
								}
								
							} else {
								$response = 'Veuillez choisir un système d\'exploitation';
							}
							
						} else {
							$response = 'Veuillez choisir une sévérité';
						}
						
					} else {
						$response = 'Veuillez choisir un type de tâche';
					}
					
				} else {
					$response = 'Veuillez choisir une catégorie';
				}
				
			} else {
				$response = 'Veuillez remplir tous les champs';
			}
			
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function ADMUpdateUpdate()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 8) {
			$req = $db->prepare('SELECT * FROM sting_updates WHERE id = ?');
			$req->execute(array($_GET['id']));
			$rowCount = $req->rowCount();
			$fetch = $req->fetch();
			if($rowCount == 0) {
				echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="updates"</SCRIPT>';
			} else {
				echo '<div class="row">
					
					<div class="col-md-12">
					<div class="card-demo">
                    <div class="card">

                        <div class="card-header">
                            <h2 class="card-title">Modifier une mise à jour</h2>
                        </div><div class="card-block">
						<form method="post" id="updateupdate" action="/core/action/action?to=admupdateupdate">
						<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>État</label>
                                <select name="etat" class="form-control">
									<option ' . ($fetch['état'] == 'En cours' ? 'selected"' : '') . ' value="En cours">En cours</option>
									<option ' . ($fetch['état'] == 'Terminé' ? 'selected"' : '') . ' value="Terminé">Terminé</option>
								</select>
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Progression</label>
                                <input name="progression" type="number" value="'.$fetch['progression'].'" class="form-control" placeholder="Progression">
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label>Explication</label> 
                                <textarea class="form-control" rows="5" name="explication" placeholder="Explique-ici">'.$fetch['contenu'].'</textarea>
                                <i class="form-group__bar"></i>
                            </div>
						</div>
						<div class="col-md-12">
							<button class="btn btn-primary btn-block waves-effect">Modifier la mise à jour</button>
						</div>
						</div>
						</form>
						<div style="height: 5px"></div>
						<form method="post" action="/core/action/action?to=admsupprupdate" id="supprupdate">
							<button class="btn btn-danger btn-block waves-effect">Supprimer la mise à jour</button>
						</form>
						</div>
						
						</div></div></div></div>';
			}
		}
	}
	
	function ADMUpdateUpdateScript()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 8) {
			$etat = $this->Security($_POST['etat']);
			$progression = $this->Security($_POST['progression']);
			$explication = nl2br($_POST['explication'], ENT_QUOTES);
			
			if(!empty($etat) && !empty($progression) && !empty($explication)) {
				if(ctype_digit($progression) AND $progression <= 100) {
					
					$req = $db->prepare('SELECT * FROM sting_updates WHERE id = ?');
					$req->execute(array($_SESSION['getidu']));
					$rowcount = $req->rowCount();
					if($rowcount == 0) {
						$response = 'Cette mise à jour n\'existe pas ou plus';
					} else { 
					
					$update = $db->prepare('UPDATE sting_updates SET état = ?, progression = ?, contenu = ? WHERE id = ?');
					$update->execute(array($etat, $progression, $explication, $_SESSION['getidu']));
					
					$response = 'success';
						
					}
					
				} else {
					
				}
				
			} else {
				$response = 'Veuillez remplir tous les champs';
			}
			
		}
		
		echo json_encode(['response' => $response]);
	}
	
	function ADMSupprUpdate()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 8) {
			$req = $db->prepare('SELECT * FROM sting_updates WHERE id = ?');
			$req->execute(array($_SESSION['getidu']));
			$rowcount = $req->rowCount();
			if($rowcount == 0) {
				$response = 'Cette mise à jour n\'existe pas ou plus';
			} else {
				$delete = $db->prepare('DELETE FROM sting_updates WHERE id = ?');
				$delete->execute(array($_SESSION['getidu']));
				$response = 'success';
			}
		}
		
		echo json_encode(['response' => $response]);
	}
	
	/**********************
	* Fonctions des profils
	**********************/
	function Profile()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$namep = explode('/client/', $_SERVER['REQUEST_URI']);
			$req = $db->prepare('SELECT * FROM sting_users WHERE username = ? OR clef = ?');
			$req->execute(array($namep['1'], $_GET['id']));
			$rowCount = $req->rowCount();
			$fetch = $req->fetch();
			
			if($rowCount == 0) {
				echo '<SCRIPT LANGUAGE="JavaScript">document.location.href="index"</SCRIPT>';
			} else {
				$_SESSION['profile'] = array(
						'id' => $fetch['id'],
						'username' => $fetch['username'],
						'password' => $fetch['password'],
						'email' => $fetch['email'],
						'clef' => $fetch['clef'],
						'rank' => $fetch['rank'],
						'gold' => $fetch['gold'],
						'avatar' => $fetch['avatar'],
						'registration_ip' => $fetch['registration_ip'],
						'last_ip' => $fetch['last_ip'],
						'last_connection' => date('Y-m-d H:i:s') ,
						'registration' => $fetch['registration'],
						'account_confirmed' => $fetch['account_confirmed'],
					);
			}
		}
	}
	
	function ProfileNetwork()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_users_settings WHERE clef_user = ?');
			$req->execute(array($_SESSION['profile']['clef']));
			$rowCount = $req->rowCount();
			$fetch = $req->fetch();
			
			if($rowCount != 0 AND $fetch['facebook'] != '' OR $fetch['twitter'] != '' OR $fetch['snapchat'] != '' OR $fetch['instagram'] != '') {
				echo '<ul class="icons-list m-t-15">';
				if($fetch['facebook'] != '') {
					echo '<li><a href="'.$fetch['facebook'].'" data-popup="tooltip" title="" data-original-title="Facebook"><i class="fa fa-facebook-square"></i></a></li>';
				}
				if($fetch['twitter'] != '') {
					echo '<li><a href="'.$fetch['twitter'].'" data-popup="tooltip" title="" data-original-title="Twitter"><i class="fa fa-twitter-square"></i></a></li>';
				}
				if($fetch['instagram'] != '') {
					echo '<li><a href="'.$fetch['instagram'].'" data-popup="tooltip" title="" data-original-title="Instagram"><i class="fa fa-instagram"></i></a></li>';
				}
				if($fetch['snapchat'] != '') {
					echo '<li><a href="'.$fetch['snapchat'].'" data-popup="tooltip" title="" data-original-title="Snapchat"><i class="fa fa-snapchat-square"></i></a></li>';
				}
				
				echo '</ul>';
			}
		}
	}
	
	function ProfileStatut()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			
			$req = $db->prepare('SELECT statut, clef_user FROM sting_users_settings WHERE clef_user = ?');
			$req->execute(array($_SESSION['profile']['clef']));
			$rowCount = $req->rowCount();
			$fetch = $req->fetch();
			
			if($rowCount != 0) {
				if($fetch['statut'] == 1) {
					echo '<div class="panel panel-flat bg-slate-white">
							<div class="panel-heading">
								<h5 class="panel-title" style="color: black">Publier un message</h5>
							</div>

							<div class="panel-body">
								<form id="sendmessage" method="post" action="/core/action/action?to=profilemsg">
									<div class="form-group">
										<textarea name="message" id="message" class="form-control m-b-15" rows="3" cols="1" placeholder="Exprimez-vous..."></textarea>
									</div>

									<div class="row">
										<div class="col-sm-6"></div>

										<div class="col-sm-6 text-right">
											<button class="btn btn-primary btn-labeled btn-labeled-right">Publier <b><i class="icon-checkmark3"></i></b></button>
										</div>
									</div>
								</form>
							</div>
						</div>';
					
				} elseif($fetch['statut'] == 0 AND $fetch['clef_user'] == $_SESSION['account']['clef']) {
					echo '<div class="panel panel-flat bg-slate-white">
							<div class="panel-heading">
								<h5 class="panel-title" style="color: black">Publier un message</h5>
							</div>

							<div class="panel-body">
								<form id="sendmessage" method="post" action="/core/action/action?to=profilemsg">
									<div class="form-group">
										<textarea name="message" id="message" class="form-control m-b-15" rows="3" cols="1" placeholder="Exprimez-vous..."></textarea>
									</div>

									<div class="row">
										<div class="col-sm-6"></div>

										<div class="col-sm-6 text-right">
											<button class="btn btn-primary btn-labeled btn-labeled-right">Publier <b><i class="icon-checkmark3"></i></b></button>
										</div>
									</div>
								</form>
							</div>
						</div>';
				}
				
			}
			
		}
	}
	
	function SendMsgProfile()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			
			$msg = $this->Security($_POST['message']);
			
			$req = $db->prepare('SELECT statut, clef_user FROM sting_users_settings WHERE clef_user = ?');
			$req->execute(array($_SESSION['profile']['clef']));
			$rowCount = $req->rowCount();
			$fetch = $req->fetch();
			
			if($rowCount != 0) {
				if($fetch['statut'] == 1) {
					 if($_SESSION['explodeid']['1'] == $_SESSION['profile']['username']) {
						 if(!empty($msg)) {

							 $req2 = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
							 $req2->execute(array($_SESSION['explodeid']['1']));
							 $fetch2 = $req2->fetch();
							 $add = $db->prepare('INSERT INTO sting_profile_messages(clef_user, profile_id, message, date) VALUES(?, ?, ?, ?)');
							 $add->execute(array($_SESSION['account']['clef'], $fetch2['clef'], $msg, date('Y-m-d H:i:s')));
							 $response = 'success';
							 if($_SESSION['account']['username'] != $_SESSION['profile']['username']) {
								 $notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, date, open) VALUES(?, ?, ?, ?, ?)');
								 $notif->execute(array($_SESSION['profile']['clef'], $_SESSION['account']['avatar'], '<b>'.$_SESSION['account']['username'].'</b> vient de publier un message sur votre profil', date('Y-m-d H:i:s'), '0'));
							 }
							 $last = $db->query('SELECT MAX(id) AS nb FROM sting_profile_messages');
							 $feet = $last->fetch();
							 $maxid = $feet['nb'];
							 $post = '<div class="timeline-content">
							<div class="media">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle" alt=""></a>
								</div>

								<div class="media-body">
									<h6 class="media-heading '.$this->RankColor($_SESSION['account']['rank']).'">'.$_SESSION['account']['username'].'</h6>
									<div class="media-annotation"><i class="icon-alarm position-left"></i> '.$this->ConvertTime(date('Y-m-d H:i:s')).'</div>
									<p>' . $this->Smiley(wordwrap($msg, 45, "\n", true)) . '</p>
									
								</div>
								<ul class="quick-actions">										
									<li class="idlike" id="'.$maxid.'"><a href="#" id="likeordislike"><i class="icon-thumbs-up3 position-left"></i>J\'aime</a> <span class="text-muted" id="nblike">(0)</span></li>
								</ul><div id="comment'.$maxid.'">
								

								</div><div class="media-actions pull-right">
									<button type="button" class="btn btn-link btn-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<i class="icon-more2"></i>
									</button>									
									<ul class="dropdown-menu dropdown-menu-right bg-slate-light">
										<li class="signal" id="'.$r['id'].'"><a href="#"><i class="icon-spam"></i> Signaler</a></li>											
									</ul>									
								</div>
							</div>
							<div class="media no-margin m-t-20">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-50" alt=""></a>
								</div>
								<div class="media-body">
									<div class="form-group has-feedback no-margin">
										<input type="text" class="form-control input-xs" id="'.$maxid.'" name="content" placeholder="Exprimez-vous...">
									</div>
								</div>
							</div> 
						</div>
						<div style="height: 5px"></div>';
						   	 } else {
							 	$response = 'Veuillez entrer un message';
						 	}
					 	} elseif($_SESSION['idbasic'] == $_SESSION['profile']['clef']) {
						 	if(!empty($msg)) {
						 	$req2 = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
						 	$req2->execute(array($_SESSION['idbasic']));
						 	$fetch2 = $req2->fetch();
						 	$add = $db->prepare('INSERT INTO sting_profile_messages(clef_user, profile_id, message, date) VALUES(?, ?, ?, ?)');
						 	$add->execute(array($_SESSION['account']['clef'], $fetch2['clef'], $msg, date('Y-m-d H:i:s')));
							if($_SESSION['account']['clef'] != $_SESSION['profile']['clef']) {
								 $notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, date, open) VALUES(?, ?, ?, ?, ?)');
								 $notif->execute(array($_SESSION['profile']['clef'], $_SESSION['account']['avatar'], '<b>'.$_SESSION['account']['username'].'</b> vient de publier un message sur votre profil', date('Y-m-d H:i:s'), '0'));
							 }
						 	$response = 'success';
								$last = $db->query('SELECT MAX(id) AS nb FROM sting_profile_messages');
							 $feet = $last->fetch();
							 $maxid = $feet['nb'];
							 $post = '<div class="timeline-content">
							<div class="media">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle" alt=""></a>
								</div>

								<div class="media-body">
									<h6 class="media-heading '.$this->RankColor($_SESSION['account']['rank']).'">'.$_SESSION['account']['username'].'</h6>
									<div class="media-annotation"><i class="icon-alarm position-left"></i> '.$this->ConvertTime(date('Y-m-d H:i:s')).'</div>
									<p>' . $this->Smiley(wordwrap($msg, 45, "\n", true)) . '</p>
									
								</div>
								<ul class="quick-actions">										
									<li class="idlike" id="'.$maxid.'"><a href="#" id="likeordislike"><i class="icon-thumbs-up3 position-left"></i>J\'aime</a> <span class="text-muted" id="nblike">(0)</span></li>
								</ul><div id="comment'.$maxid.'">
								

								</div><div class="media-actions pull-right">
									<button type="button" class="btn btn-link btn-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<i class="icon-more2"></i>
									</button>									
									<ul class="dropdown-menu dropdown-menu-right bg-slate-light">
										<li class="signal" id="'.$r['id'].'"><a href="#"><i class="icon-spam"></i> Signaler</a></li>											
									</ul>									
								</div>
							</div>
							<div class="media no-margin m-t-20">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-50" alt=""></a>
								</div>
								<div class="media-body">
									<div class="form-group has-feedback no-margin">
										<input type="text" class="form-control input-xs" id="'.$maxid.'" name="content" placeholder="Exprimez-vous...">
									</div>
								</div>
							</div> 
						</div>
						<div style="height: 5px"></div>';
							} else {
								$response = 'Veuillez entrer un message';
							}
					 	}
					 
				} elseif($fetch['statut'] == 0 AND $fetch['clef_user'] == $_SESSION['account']['clef']) {
					if($_SESSION['explodeid']['1'] == $_SESSION['profile']['username']) {
						 if(!empty($msg)) {

							 $req2 = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
							 $req2->execute(array($_SESSION['explodeid']['1']));
							 $fetch2 = $req2->fetch();
							 $add = $db->prepare('INSERT INTO sting_profile_messages(clef_user, profile_id, message, date) VALUES(?, ?, ?, ?)');
							 $add->execute(array($_SESSION['account']['clef'], $fetch2['clef'], $msg, date('Y-m-d H:i:s')));
							 $response = 'success';
							 $last = $db->query('SELECT MAX(id) AS nb FROM sting_profile_messages');
							 $feet = $last->fetch();
							 $maxid = $feet['nb'];
							 $post = '<div class="timeline-content">
							<div class="media">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle" alt=""></a>
								</div>

								<div class="media-body">
									<h6 class="media-heading '.$this->RankColor($_SESSION['account']['rank']).'">'.$_SESSION['account']['username'].'</h6>
									<div class="media-annotation"><i class="icon-alarm position-left"></i> '.$this->ConvertTime(date('Y-m-d H:i:s')).'</div>
									<p>' . $this->Smiley(wordwrap($msg, 45, "\n", true)) . '</p>
									
								</div>
								<ul class="quick-actions">										
									<li class="idlike" id="'.$maxid.'"><a href="#" id="likeordislike"><i class="icon-thumbs-up3 position-left"></i>J\'aime</a> <span class="text-muted" id="nblike">(0)</span></li>
								</ul><div id="comment'.$maxid.'">
								

								</div><div class="media-actions pull-right">
									<button type="button" class="btn btn-link btn-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<i class="icon-more2"></i>
									</button>									
									<ul class="dropdown-menu dropdown-menu-right bg-slate-light">
										<li class="signal" id="'.$r['id'].'"><a href="#"><i class="icon-spam"></i> Signaler</a></li>											
									</ul>									
								</div>
							</div>
							<div class="media no-margin m-t-20">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-50" alt=""></a>
								</div>
								<div class="media-body">
									<div class="form-group has-feedback no-margin">
										<input type="text" class="form-control input-xs" id="'.$maxid.'" name="content" placeholder="Exprimez-vous...">
									</div>
								</div>
							</div> 
						</div>
						<div style="height: 5px"></div>';
						   	 } else {
							 	$response = 'Veuillez entrer un message';
						 	}
					 	} elseif($_SESSION['idbasic'] == $_SESSION['profile']['clef']) {
						 	if(!empty($msg)) {
						 	$req2 = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
						 	$req2->execute(array($_SESSION['idbasic']));
						 	$fetch2 = $req2->fetch();
						 	$add = $db->prepare('INSERT INTO sting_profile_messages(clef_user, profile_id, message, date) VALUES(?, ?, ?, ?)');
						 	$add->execute(array($_SESSION['account']['clef'], $fetch2['clef'], $msg, date('Y-m-d H:i:s')));
						 	$response = 'success';
								$last = $db->query('SELECT MAX(id) AS nb FROM sting_profile_messages');
							 $feet = $last->fetch();
							 $maxid = $feet['nb'];
							 $post = '<div class="timeline-content">
							<div class="media">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle" alt=""></a>
								</div>

								<div class="media-body">
									<h6 class="media-heading '.$this->RankColor($_SESSION['account']['rank']).'">'.$_SESSION['account']['username'].'</h6>
									<div class="media-annotation"><i class="icon-alarm position-left"></i> '.$this->ConvertTime(date('Y-m-d H:i:s')).'</div>
									<p>' . $this->Smiley(wordwrap($msg, 45, "\n", true)) . '</p>
									
								</div>
								<ul class="quick-actions">										
									<li class="idlike" id="'.$maxid.'"><a href="#" id="likeordislike"><i class="icon-thumbs-up3 position-left"></i>J\'aime</a> <span class="text-muted" id="nblike">(0)</span></li>
								</ul><div id="comment'.$maxid.'">
								

								</div><div class="media-actions pull-right">
									<button type="button" class="btn btn-link btn-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<i class="icon-more2"></i>
									</button>									
									<ul class="dropdown-menu dropdown-menu-right bg-slate-light">
										<li class="signal" id="'.$r['id'].'"><a href="#"><i class="icon-spam"></i> Signaler</a></li>											
									</ul>									
								</div>
							</div>
							<div class="media no-margin m-t-20">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-50" alt=""></a>
								</div>
								<div class="media-body">
									<div class="form-group has-feedback no-margin">
										<input type="text" class="form-control input-xs" id="'.$maxid.'" name="content" placeholder="Exprimez-vous...">
									</div>
								</div>
							</div> 
						</div>
						<div style="height: 5px"></div>';
							} else {
								$response = 'Veuillez entrer un message';
							}
					 	}
				} else {
					$response = 'Cet utilisateur refuse les messages de profil';
				}
			
				echo json_encode(['response' => $response, 'post' => $post]);
			}
		}
	}
	
	/**********************************
	* Fonctions de paramètres de profil 
	**********************************/
	function SettingsProfile()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_users_settings WHERE clef_user = ?');
			$req->execute(array($_SESSION['account']['clef']));
			$fetch = $req->fetch();
			echo '<div id="error3"></div>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h5 class="panel-title"><i class="fa fa-cogs"></i> Paramètres de profil</h5>
								
							</div>

							<div class="panel-body">
								<form method="post" id="settingsp" action="/core/action/action?to=settingsprofile">
									<div class="form-group">
											<label class="control-label">Lien Facebook</label>
											<input type="text" name="facebook" id="facebook" class="form-control" ' . ($fetch['facebook'] != '' ? 'value="'.$fetch['facebook'].'"' : '') . ' placeholder="Exemple: https://fb.com/Yourhabbofr">
									</div>
									<div class="form-group">
											<label class="control-label">Lien Twitter</label>
											<input type="text" name="twitter" id="twitter" class="form-control" ' . ($fetch['twitter'] != '' ? 'value="'.$fetch['twitter'].'"' : '') . ' placeholder="Exemple: https://twitter.com/MathisLts">
									</div>
									<div class="form-group">
											<label class="control-label">Lien Instagram</label>
											<input type="text" name="instagram" id="instagram" class="form-control" ' . ($fetch['instagram'] != '' ? 'value="'.$fetch['instagram'].'"' : '') . ' placeholder="Exemple: https://instagram.com/MathisLts">
									</div>
									<div class="form-group">
											<label class="control-label">Lien Snapchat</label>
											<input type="text" name="snapchat" id="snapchat" class="form-control" ' . ($fetch['snapchat'] != '' ? 'value="'.$fetch['snapchat'].'"' : '') . ' placeholder="Exemple: https://snapchat.com/add/mathislts">
									</div>
									<div class="form-group">
										<label class="control-label">Message de profil</label>
										<select name="msgp" class="form-control">
											<option ' . ($fetch['statut'] == '1' ? 'selected' : '') . ' value="1">Activer</option>
											<option ' . ($fetch['statut'] == '0' ? 'selected' : '') . ' value="0">Désactiver</option>
										</select>
									</div>
									<button class="btn btn-primary btn-block">Valider mes paramètres</button>
								</form>
							</div>
						</div>';
		}
	}
	
	function ChangeSettingsProfile()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			
			$facebook = $this->Security($_POST['facebook']);
			$twitter = $this->Security($_POST['twitter']);
			$instagram = $this->Security($_POST['instagram']);
			$snapchat = $this->Security($_POST['snapchat']);
			$msg = $this->Security($_POST['msgp']);
			
			if($msg == '1' OR $msg == '0') {
				$update = $db->prepare('UPDATE sting_users_settings SET facebook = ?, twitter = ?, instagram = ?, snapchat = ?, statut = ? WHERE clef_user = ?');
				$update->execute(array($facebook, $twitter, $instagram, $snapchat, $msg, $_SESSION['account']['clef']));
				$response = 'success';
			} else {
				$response = 'Une erreur est survenue';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function ProfileStatutList()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['explodeid']['1'] == $_SESSION['profile']['username']) {
				
				$info = $db->prepare('SELECT * FROM sting_users WHERE username = ?');
				$info->execute(array($_SESSION['explodeid']['1']));
				$fet = $info->fetch();
				
				$DPP = 6;
				$TD = $db->prepare('SELECT * FROM sting_profile_messages WHERE profile_id = ?');
				$TD->execute(array($fet['clef']));
				$rc = $TD->rowCount();
       	 
				$_SESSION['tt'] = ceil($rc/$DPP);
        
				if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
					$_GET['d'] = intval($_GET['d']);
					$PA = $_GET['d'];
            	$_SESSION['PA'] = $_GET['d'];
				} else {
					$PA = 1;
					$_SESSION['PA'] = 1;
				}
        
				$go = ($PA-1)*$DPP;
				$req = $db->prepare('SELECT * FROM sting_profile_messages WHERE profile_id = ? ORDER BY date DESC LIMIT '.$go.','.$DPP);
				$req->execute(array($fet['clef']));
					while($r = $req->fetch()) {
						
						$_SESSION['idcomment'] = array(
							$r['id'] => $r['id']
						);
						
						$account = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
						$account->execute(array($r['clef_user']));
						$fff = $account->fetch();
						
						$like = $db->prepare('SELECT * FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ? AND user_clef = ?');
						$like->execute(array($r['id'], $_SESSION['profile']['clef'], $_SESSION['account']['clef']));
						$rowcountt = $like->rowCount();
						
						$liketotal = $db->prepare('SELECT COUNT(id) AS nb FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ?');
						$liketotal->execute(array($r['id'], $_SESSION['profile']['clef']));
						$ft = $liketotal->fetch();
						
						echo '<div class="timeline-content">
							<div class="media">
								<div class="media-left">
									<a href="#"><img src="'.$fff['avatar'].'" class="img-circle" alt=""></a>
								</div>

								<div class="media-body">
									<a href="'.$fff['username'].'"><h6 class="media-heading '.$this->RankColor($fff['rank']).'">'.$fff['username'].'</h6></a>
									<div class="media-annotation"><i class="icon-alarm position-left"></i> '.$this->ConvertTime($r['date']).'</div>
									<p>' . $this->Smiley(wordwrap($r['message'], 45, "\n", true)) . '</p>
									
								</div>
								<ul class="quick-actions">										
									<li class="idlike" id="'.$r['id'].'"><a href="#" id="likeordislike">' . ($rowcountt == 0 ? '<i class="icon-thumbs-up3 position-left"></i>J\'aime' : '') . '' . ($rowcountt != 0 ? '<i class="icon-thumbs-down3 position-left"></i>Je n\'aime plus' : '') . '</a> <span class="text-muted" id="nblike">('.$ft['nb'].')</span></li>
								</ul><div id="comment'.$r['id'].'">';
						
								$comm = $db->prepare('SELECT * FROM sting_profile_comments WHERE statut_id = ? AND profile_clef = ? ORDER BY date DESC LIMIT 10');
								$comm->execute(array($r['id'], $_SESSION['profile']['clef']));
						
								while($c = $comm->fetch()) {
									$ifn = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
									$ifn->execute(array($c['user_clef']));
									$ffe = $ifn->fetch();
									echo '<div class="comments media media-list media-list-bordered">
									<div class="media-left">
									  <a href="#">
										<img src="'.$ffe['avatar'].'" class="img-circle" alt="">
									  </a>
									</div>
									<div class="media-body">
									  <h4 class="media-heading '.$this->RankColor($ffe['rank']).'">'.$ffe['username'].'</h4>									
									   ' . wordwrap($this->Smiley($c['content']), 45, "\n", true) . '									 									  
									</div>
								</div>';
								}
						
								echo '</div><div class="media-actions pull-right">
									<button type="button" class="btn btn-link btn-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<i class="icon-more2"></i>
									</button>									
									<ul class="dropdown-menu dropdown-menu-right bg-slate-light">
										<li class="signal" id="'.$r['id'].'"><a href="#"><i class="icon-spam"></i> Signaler</a></li>											
									</ul>									
								</div>
							</div>
							<div class="media no-margin m-t-20">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-50" alt=""></a>
								</div>
								<div class="media-body">
									<div class="form-group has-feedback no-margin">
										<input type="text" class="form-control input-xs" id="'.$r['id'].'" name="content" placeholder="Exprimez-vous...">
									</div>
								</div>
							</div> 
						</div>
						<div style="height: 5px"></div>';
						}
				 		
			 	} elseif($_SESSION['idbasic'] == $_SESSION['profile']['clef']) {
				$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				$info->execute(array($_SESSION['idbasic']));
				$fet = $info->fetch();
					$DPP = 6;
				$TD = $db->prepare('SELECT * FROM sting_profile_messages WHERE profile_id = ?');
				$TD->execute(array($fet['clef']));
				$rc = $TD->rowCount();
       	 
				$_SESSION['tt'] = ceil($rc/$DPP);
        
				if(isset($_GET['d']) AND !empty($_GET['d']) AND $_GET['d'] > 0) {
					$_GET['d'] = intval($_GET['d']);
					$PA = $_GET['d'];
            	$_SESSION['PA'] = $_GET['d'];
				} else {
					$PA = 1;
					$_SESSION['PA'] = 1;
				}
        
				$go = ($PA-1)*$DPP;
				$req = $db->prepare('SELECT * FROM sting_profile_messages WHERE profile_id = ? ORDER BY date DESC LIMIT '.$go.','.$DPP);
				$req->execute(array($fet['clef']));
					while($r = $req->fetch()) {
						$account = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
						$account->execute(array($r['clef_user']));
						$fff = $account->fetch();
						$liketotal = $db->prepare('SELECT COUNT(id) AS nb FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ?');
						$liketotal->execute(array($r['id'], $_SESSION['profile']['clef']));
						$ft = $liketotal->fetch();
						echo '<div class="timeline-content">
							<div class="media">
								<div class="media-left">
									<a href="#"><img src="'.$fff['avatar'].'" class="img-circle" alt=""></a>
								</div>

								<div class="media-body">
									<a href="'.$fff['username'].'"><h6 class="media-heading '.$this->RankColor($fff['rank']).'">'.$fff['username'].'</h6></a>
									<div class="media-annotation"><i class="icon-alarm position-left"></i> '.$this->ConvertTime($r['date']).'</div>
									<p>' . $this->Smiley(wordwrap($r['message'], 45, "\n", true)) . '</p>
									
								</div>
								<ul class="quick-actions">										
									<li class="idlike" id="'.$r['id'].'"><a href="#" id="likeordislike">' . ($rowcountt == 0 ? '<i class="icon-thumbs-up3 position-left"></i>J\'aime' : '') . '' . ($rowcountt != 0 ? '<i class="icon-thumbs-down3 position-left"></i>Je n\'aime plus' : '') . '</a> <span class="text-muted" id="nblike">('.$ft['nb'].')</span></li>
								</ul><div id="comment'.$r['id'].'">';
						
								$comm = $db->prepare('SELECT * FROM sting_profile_comments WHERE statut_id = ? AND profile_clef = ? ORDER BY date DESC LIMIT 10');
								$comm->execute(array($r['id'], $_SESSION['profile']['clef']));
						
								while($c = $comm->fetch()) {
									$ifn = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
									$ifn->execute(array($c['user_clef']));
									$ffe = $ifn->fetch();
									echo '<div class="comments media media-list media-list-bordered">
									<div class="media-left">
									  <a href="#">
										<img src="'.$ffe['avatar'].'" class="img-circle" alt="">
									  </a>
									</div>
									<div class="media-body">
									  <h4 class="media-heading '.$this->RankColor($ffe['rank']).'">'.$ffe['username'].'</h4>									
									   ' . wordwrap($c['content'], 45, "\n", true) . '									 									  
									</div>
								</div>';
								}
						
								echo '</div><div class="media-actions pull-right">
									<button type="button" class="btn btn-link btn-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<i class="icon-more2"></i>
									</button>									
									<ul class="dropdown-menu dropdown-menu-right bg-slate-light">
										<li class="signal" id="'.$r['id'].'"><a href="#"><i class="icon-spam"></i> Signaler</a></li>											
									</ul>									
								</div>
							</div>
							<div class="media no-margin m-t-20">
								<div class="media-left">
									<a href="#"><img src="'.$_SESSION['account']['avatar'].'" class="img-circle img-50" alt=""></a>
								</div>
								<div class="media-body">
									<div class="form-group has-feedback no-margin">
										<input type="text" class="form-control input-xs" id="'.$r['id'].'" name="content" placeholder="Exprimez-vous...">
									</div>
								</div>
							</div> 
						</div>
						<div style="height: 5px"></div>';
						}
			}
		}
	}
	
	function FriendsList()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_users_friends WHERE user_clef = ?');
			$req->execute(array($_SESSION['profile']['clef']));
			
			while($r = $req->fetch()) {
				$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				$info->execute(array($r['friend_clef']));
				$fetch = $info->fetch();
				echo '<li class="media" id="'.$r['user_clef'].'">
									<a href="'.$fetch['username'].'" class="media-link">
										<div class="media-left"><img src="'.$fetch['avatar'].'" class="img-circle" alt=""></div>
										<div class="media-body">
											<span class="media-heading '.$this->RankColor($fetch['rank']).'">'.$fetch['username'].'</span>
											<span class="media-annotation">'.$this->Rank($fetch['rank']).'</span>
										</div>
										<!--<div class="media-right media-middle">
											<span class="status-mark bg-success"></span>
										</div>-->
									</a>
								</li>';
			}
		}
	}
	
	function ButtonAddFriend()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req1 = $db->prepare('SELECT * FROM sting_users_friends WHERE user_clef = ? AND friend_clef = ?');
			$req1->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
			$rowCount1 = $req1->rowCount();
			if($rowCount1 == 0) {
				$req2 = $db->prepare('SELECT * FROM sting_friends_requests WHERE demandeur_clef = ? AND receveur_clef = ?');
				$req2->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
				$rowCount2 = $req2->rowCount();
				if($rowCount2 == 0) {
					if($_SESSION['account']['clef'] != $_SESSION['profile']['clef']) {
							echo '<form method="post" id="addfriend" action="/core/action/action?to=addfriend" class="text-center">
								<div id="friend">
									<button class="btn btn-primary btn-xs btn-block">Demander en ami</button>
								</div>
							</form>';

					}
					
				} else {
					echo '<center>Vous avez envoyé une demande d\'ami</center>';
				}
				
			} else {
				$req3 = $db->prepare('SELECT * FROM sting_users_friends WHERE friend_clef = ?');
						$req3->execute(array($_SESSION['profile']['clef']));
						$rowCount3 = $req3->rowCount();
				if($rowCount3 != 0) {
					echo '<form method="post" id="deletefriend" action="/core/action/action?to=deletefriend" class="text-center">
								<div id="deletefriend">
									<button class="btn btn-danger btn-xs btn-block">Supprimer de mes amis</button>
								</div>
							</form>';
				}
			}
		}
		
	}
	
	function AddFriend()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req1 = $db->prepare('SELECT * FROM sting_users_friends WHERE user_clef = ? AND friend_clef = ?');
			$req1->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
			$rowCount1 = $req1->rowCount();
			if($rowCount1 == 0) {
				$req2 = $db->prepare('SELECT * FROM sting_friends_requests WHERE demandeur_clef = ? AND receveur_clef = ?');
				$req2->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
				$rowCount2 = $req2->rowCount();
				if($rowCount2 == 0) {
					if($_SESSION['account']['clef'] != $_SESSION['profile']['clef']) {
						$add = $db->prepare('INSERT INTO sting_friends_requests(demandeur_clef, receveur_clef, date) VALUES(?, ?, ?)');
						$add->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef'], date('Y-m-d H:i:s')));
						$notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, open, date) VALUES(?, ?, ?, ?, ?)');
						$notif->execute(array($_SESSION['profile']['clef'], $_SESSION['account']['avatar'], '<b>'.$_SESSION['account']['username'].'</b> vous a envoyé une demande d\'ami', '0', date('Y-m-d H:i:s')));
						$response = 'success';
					} else {
						$response = 'Vous ne pouvez pas être ami avec vous même';
					}
					
				} else {
					$response = '<center>Vous avez envoyé une demande d\'ami</center>';
				}
				
			} else {
				$response = 'Vous êtes déjà ami';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function ListAddFriend()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_friends_requests WHERE receveur_clef = ?');
			$req->execute(array($_SESSION['account']['clef']));
			
			while($r = $req->fetch()) {
				$info = $db->prepare('SELECT * FROM sting_users WHERE clef = ?');
				$info->execute(array($r['demandeur_clef']));
				$fetch = $info->fetch();
				echo '<li class="media">
												<div class="media-link">
													<div class="media-left">
														<img src="'.$fetch['avatar'].'" class="img-circle" alt="">
													</div>

													<div class="media-body">
														<h6 class="media-heading '.$this->RankColor($fetch['rank']).'">'.$fetch['username'].'</h6>
														'.$this->Rank($fetch['rank']).'
													</div>
													
													<div class="media-right">
														<button id="'.$fetch['clef'].'" class="btn btn-primary">Accepter</button>
													</div>
												</div>
											</li>';
			}
		}
	}
	
	function FriendsReq()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_GET['id']) {
				$id = (int) $_GET['id'];
				
				$req = $db->prepare('SELECT clef FROM sting_users WHERE clef = ?');
				$req->execute(array($id));
				$rowCount = $req->rowCount();
				if($rowCount != 0) {
					$req2 = $db->prepare('SELECT * FROM sting_friends_requests WHERE demandeur_clef = ? AND receveur_clef = ?');
					$req2->execute(array($id, $_SESSION['account']['clef']));
					$rowCount2 = $req2->rowCount();
					if($rowCount2 != 0) {
						
						$req3 = $db->prepare('SELECT * FROM sting_users_friends WHERE user_clef = ? AND friend_clef = ?');
						$req3->execute(array($_SESSION['account']['clef'], $id));
						$rowCount3 = $req3->rowCount();
						if($rowCount3 == 0) {
						
						$insert = $db->prepare('INSERT INTO sting_users_friends(user_clef, friend_clef) VALUES(?, ?)');
						$insert->execute(array($_SESSION['account']['clef'], $id));
						
						$insert = $db->prepare('INSERT INTO sting_users_friends(friend_clef, user_clef) VALUES(?, ?)');
						$insert->execute(array($_SESSION['account']['clef'], $id));
						
						$delete = $db->prepare('DELETE FROM sting_friends_requests WHERE demandeur_clef = ? AND receveur_clef = ?');
						$delete->execute(array($id, $_SESSION['account']['clef']));
						
						$delete = $db->prepare('DELETE FROM sting_friends_requests WHERE receveur_clef = ? AND demandeur_clef = ?');
						$delete->execute(array($id, $_SESSION['account']['clef']));
							
						$notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, open, date) VALUES(?, ?, ?, ?, ?)');
						$notif->execute(array($id, $_SESSION['account']['avatar'], '<b>'.$_SESSION['account']['username'].'</b> a accepté votre demande d\'ami', '0', date('Y-m-d H:i:s')));
						
						$response = 'accept';
							
						} else {
							$response = 'Vous êtes déjà ami';
						}
						
					} else {
						$response = 'Aucune demande d\'ami';
					}
					
				} else {
					$response ='Ce membre n\'existe pas';
				}
				
			} else {
				$response = 'Veuillez entrer un id';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function DeleteFriend()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_users_friends WHERE user_clef = ? AND friend_clef = ?');
			$req->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
			$rowCount = $req->rowCount();
			if($rowCount != 0) {
				$req = $db->prepare('DELETE FROM sting_users_friends WHERE user_clef = ? AND friend_clef = ?');
				$req->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
				
				$req = $db->prepare('DELETE FROM sting_users_friends WHERE friend_clef = ? AND user_clef = ?');
				$req->execute(array($_SESSION['account']['clef'], $_SESSION['profile']['clef']));
				
				$notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, open, date) VALUES(?, ?, ?, ?, ?)');
				$notif->execute(array($_SESSION['profile']['clef'], $_SESSION['account']['avatar'], '<b>'.$_SESSION['account']['username'].'</b> vous a supprimé de ses amis', '0', date('Y-m-d H:i:s')));
				
				$response = 'success';
				
				$iduser = $_SESSION['profile']['clef'];
			} else {
				$response = 'Vous n\'êtes pas ami';
			}
			
			echo json_encode(['response' => $response, 'iduser' => $iduser]);
		}
	}
	
	function LikeStatut()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_GET['id']) {
				
				$id = (int) $_GET['id'];
				
				$req = $db->prepare('SELECT * FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ? AND user_clef = ?');
				$req->execute(array($id, $_SESSION['profile']['clef'], $_SESSION['account']['clef']));
				$rowCount = $req->rowCount();
				
				if($rowCount == 0) {
					$insert = $db->prepare('INSERT INTO sting_profile_likes(statut_id, profile_clef, user_clef) VALUES(?, ?, ?)');
					$insert->execute(array($id, $_SESSION['profile']['clef'], $_SESSION['account']['clef']));
					$likemax = $db->prepare('SELECT COUNT(id) AS nb FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ?');
					$likemax->execute(array($id, $_SESSION['profile']['clef']));
					$fetch = $likemax->fetch();
					$lik = $fetch['nb'];
					$likeordislike = '<a href="#" id="likeordislike"><i class="icon-thumbs-down3 position-left"></i>Je n\'aime plus</a> <span class="text-muted" id="nblike">('.$lik.')</span>';
					$response = 'like';
				} else {
					$delete = $db->prepare('DELETE FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ? AND user_clef = ?');
					$delete->execute(array($id, $_SESSION['profile']['clef'], $_SESSION['account']['clef']));
					$likemax = $db->prepare('SELECT COUNT(id) AS nb FROM sting_profile_likes WHERE statut_id = ? AND profile_clef = ?');
					$likemax->execute(array($id, $_SESSION['profile']['clef']));
					$fetch = $likemax->fetch();
					$lik = $fetch['nb'];
					$likeordislike = '<a href="#" id="likeordislike"><i class="icon-thumbs-up3 position-left"></i>J\'aime</a> <span class="text-muted" id="nblike">('.$lik.')</span>';
					$response = 'dislike';
				}
				
				
				
			} else {
				$response = 'Veuillez entrer un ID';
			}
			
			echo json_encode(['response' => $response, 'likeordislike' => $likeordislike]);
		}
	}
	
	function ReportStatut()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			
			if($_GET['id']) {
				
				$id = (int) $_GET['id'];
				
				$req = $db->prepare('SELECT * FROM sting_profile_reports WHERE statut_id = ? AND profile_clef = ? AND user_clef = ?');
				$req->execute(array($id, $_SESSION['profile']['clef'], $_SESSION['account']['clef']));
				$fetch = $req->fetch();
				$rowCount = $req->rowCount();
				
				$DateLimite = date("Y-m-d H:i:s", strtotime('' . $fetch['date'] . '' . " +3600 seconds"));
				$dateactuel = date('Y-m-d H:i:s');
				
				if($DateLimite <= $dateactuel OR $rowCount == 0) {
					$insert = $db->prepare('INSERT INTO sting_profile_reports(statut_id, profile_clef, user_clef, date) VALUES(?, ?, ?, ?)');
					$insert->execute(array($id, $_SESSION['profile']['clef'], $_SESSION['account']['clef'], date('Y-m-d H:i:s')));
					$response = 'send';
				} else {
					$response = 'Vous avez déjà reporté ce statut';
				}
				
				
			} else {
				$response = 'Veuillez entrer un ID';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	function AddComment()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if((int) $_GET['id'] && $this->Security($_GET['text'])) {
				if(iconv_strlen($_GET['text']) >= 1) {
					$id = (int) $_GET['id'];
					$insert = $db->prepare('INSERT INTO sting_profile_comments(statut_id, profile_clef, user_clef, content, date) VALUES(?, ?, ?, ?, ?)');
					$insert->execute(array($id, $_SESSION['profile']['clef'], $_SESSION['account']['clef'], $this->Security($_GET['text']), date('Y-m-d H:i:s')));
					$notif = $db->prepare('INSERT INTO sting_notifications(clef_user, avatar, content, open, date) VALUES(?, ?, ?, ?, ?)');
					$notif->execute(array($_SESSION['profile']['clef'], $_SESSION['account']['avatar'], ''.$_SESSION['account']['username'].' vient d\'ajouter un commentaire à votre statut', '0', date('Y-m-d H:i:s')));
					$rep = '<div class="comments media media-list media-list-bordered">
									<div class="media-left">
									  <a href="#">
										<img src="'.$_SESSION['account']['avatar'].'" class="img-circle" alt="">
									  </a>
									</div>
									<div class="media-body">
									  <h4 class="media-heading '.$this->RankColor($_SESSION['account']['rank']).'">'.$_SESSION['account']['username'].'</h4>									
									   ' . wordwrap($this->Smiley($this->Security($_GET['text'])), 45, "\n", true) . '									 									  
									</div>
								</div>';
					$response = 'send';
				} else {
					$response = 'Votre commentaire est trop court';
				}
			}
		}
		
		echo json_encode(['response' => $response, 'rep' => $rep]);
	}

	/***********************************************************
	* Fonction de changement du mot de passe de l'administration
	***********************************************************/
	function PasswordADMChange()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 5) {
			$password = $this->Security($_POST['newadm']);
			
			$req = $db->prepare('SELECT * FROM sting_adm_accounts WHERE clef = ?');
			$req->execute(array($_SESSION['account']['clef']));
			$rowCount = $req->rowCount();
			
			$req2 = $db->prepare('SELECT rank, clef FROM sting_users WHERE clef = ?');
			$req2->execute(array($_SESSION['account']['clef']));
			$fetch = $req2->fetch();
			
			if($rowCount == 0) {
				if($fetch['rank'] >= 5) {
					$insert = $db->prepare('INSERT INTO sting_adm_accounts(clef, username, password) VALUES(?, ?, ?)');
					$insert->execute(array($_SESSION['account']['clef'], $_SESSION['account']['username'], $password));
					$response = 'success';
				} else {
					$response = 'Nous n\'êtes pas assez gradé';
				}
				
			} else {
				$update = $db->prepare('UPDATE sting_adm_accounts SET password = ? WHERE clef = ? AND username = ?');
				$update->execute(array($password, $_SESSION['account']['clef'], $_SESSION['account']['username']));
				$response = 'success';
			}
			
			echo json_encode(['response' => $response]);
		}
	}
	
	/****************************
	* Fonctions des notifications
	****************************/
	function ViewNotif()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_notifications WHERE clef_user = ? AND open = ?');
			$req->execute(array($_SESSION['account']['clef'], '0'));
			$rowCount = $req->rowCount();
			if($rowCount != 0) {
				$edit = $db->prepare('UPDATE sting_notifications SET open = ? WHERE clef_user = ?');
				$edit->execute(array('1', $_SESSION['account']['clef']));
			}
		}
	}
	
	function NBNotifications()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT COUNT(id) AS nb FROM sting_notifications WHERE clef_user = ? AND open = ?');
			$req->execute(array($_SESSION['account']['clef'], '0'));
			$fetch = $req->fetch();
			
			echo $fetch['nb'];
		}
	}
	
	function Notifications()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			$req = $db->prepare('SELECT * FROM sting_notifications WHERE clef_user = ? ORDER BY date DESC');
			$req->execute(array($_SESSION['account']['clef']));
			$rowCount = $req->rowCount();
			if($rowCount != 0) {
			while($r = $req->fetch()) {
				echo '<li class="clearfix new">
											<a href="#" class="media-thumb"><img src="'.$r['avatar'].'" alt="image">
											</a><a href="#" class="media-title">'.$r['content'].'<span class="media-time">Le ' . date("d-m-Y à H:i", strtotime($r['date'])) . '</span></a>
										</li>';
			}
			} else {
				echo '<center><i class="fa fa-info-circle"></i> Tu n\'as aucune notification</center>';
			}
		}
		
 	}
	
	/******************
	* Fonctions du loto
	******************/
	function Loto()
	{
		$db = Connect::getDB();
		
		if($_SESSION['account']['rank'] >= 3) {
			echo '<div class="panel panel-primary">
							<div class="panel-heading">
								<h5 class="panel-title"><i class="fa fa-star"></i> Loto</h5>
								
							</div>

							<div class="panel-body">
								<p align="center">Bienvenue sur le <b>Loto</b> de YourHabbo</p>
								<p align="center">Les règles sont simple, vous avez juste à appuyer sur le bouton pour lancer le loto. Cependant, vous avez le droit à seulement 3 lotos par jour. Vous pouvez gagner diverses chose, nous mettons à jour régulièrement les lots à gagner</p> 
								<hr>
								<center><div id="loto1"><i class="fa fa-spinner fa-pulse fa-fw"></i> En attente du lancement du loto</div></center>
								<center><div id="loto2"></div></center>
								<div style="height: 11px"></div>
								<button id="lancementloto" type="submit" style="color: #F9A600 !important" class="btn border-warning btn-flat btn-xlg btn-block"><i class="fa fa-star"></i> Lancer le loto <i class="fa fa-star"></i></button>
							</div>
						</div>';
		}
	}
	
	function LotoScript()
	{
		$db = Connect::getDB();
		
		if(isset($_SESSION['account'])) {
			if($_SESSION['account']['rank'] >= 3) {
				$req = $db->prepare('SELECT COUNT(*) AS nb FROM sting_lotos WHERE clef_user = ? AND date = ?');
				$req->execute(array($_SESSION['account']['clef'], date('Y-m-d')));
				$fetch = $req->fetch();
				if($fetch['nb'] < 3) {
					$det = mt_rand(0, 1);
					if($det == 1) {
						$gold = mt_rand(1, 10);
						$response = '['.date('H:i:s').'] <span style="color: blue">Lancement du loto ...</span><br>['.date('H:i:s').'] <span style="color: green">Vous avez gagné '.$gold.' point'.$this->isMany($gold).' <i class="fa fa-check"></i></span><br>';
						
						$up = $db->prepare('UPDATE sting_users SET gold = gold + ? WHERE clef = ?');
						$up->execute(array($gold, $_SESSION['account']['clef']));
						
						$insert = $db->prepare('INSERT INTO sting_lotos(clef_user, lot, date) VALUES(?, ?, ?)');
						$insert->execute(array($_SESSION['account']['clef'], ''.$gold.' point'.$this->isMany($gold).'', date('Y-m-d')));
					} elseif($det == 0) {
						$response = '['.date('H:i:s').'] <span style="color: blue">Lancement du loto ...</span><br>['.date('H:i:s').'] <span style="color: red">Vous avez perdu <i class="fa fa-close"></i></span><br>';
						$insert = $db->prepare('INSERT INTO sting_lotos(clef_user, lot, date) VALUES(?, ?, ?)');
						$insert->execute(array($_SESSION['account']['clef'], 'Perdu', date('Y-m-d')));
					}
					
				} else {
					$response = 'Oh non! Vous avez déjà jouer 3 fois aujourd\'hui. Repassez demain pour 3 nouveaux lotos!';
				}
				
				echo json_encode(['response' => $response]);
			}
		}
	}
	
	/**************************
	* Fonction de suivis google
	**************************/
	function SuiviGoogle()
	{
		echo "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-101988476-1', 'auto');
  ga('send', 'pageview');

</script>";
	}
	
}

$StingCMS = new StingCMS();