<?php
require '../../global.php';

$code = isset($_POST['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['code']) : '';

if (empty($code)) {
	$_SESSION['payment'] = '<script>swal("Information", "Veuillez saisir un code", "info")</script>';
	header('Location: ../reloading');
}
else {
	$dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?public_key='.$Controller->Settings('dedipass_public_key').'&private_key='.$Controller->Settings('dedipass_private_key').'&code=' . $code);
	$dedipass = json_decode($dedipass);
	if ($dedipass->status == 'success') {
		$virtual_currency = $dedipass->virtual_currency;
		$rate = $dedipass->rate;
		$payout = $dedipass->payout;
		$db = Connect::getDB();
		$req = $db->prepare('INSERT INTO sting_logs_payments(clef_user, code, payout, rate, gold, ip, date, navigator) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$req->execute(array(
			$_SESSION['account']['clef'],
			$code,
			$payout,
			$rate,
			$virtual_currency,
			$Controller->GetIP() ,
			date('Y-m-d H:i:s') ,
			$_SERVER['HTTP_USER_AGENT']
		));
		$req = $db->prepare('UPDATE sting_users SET gold = gold + ? WHERE clef = ?');
		$req->execute(array(
			$virtual_currency,
			$_SESSION['account']['clef']
		));
		$_SESSION['payment'] = '<script>swal({
		title: "Information",
		text: "Les points sont utilisable sur l\'ensemble du site",
		type: "info",
		showCancelButton: false,
		closeOnConfirm: false,
		showLoaderOnConfirm: true,
		},
		function(){
		setTimeout(function(){
		swal("Vos ' . $virtual_currency . ' Point' . $Controller->isMany($virtual_currency) . ' ont été crédité");
		}, 2000);
		});</script>';
		header('Location: ../reloading');
	}
	else {
		$_SESSION['payment'] = '<script>swal("Attention", "Le code ' . $code . ' est incorrect", "danger")</script>';
		header('Location: ../reloading');
	}
}

?>