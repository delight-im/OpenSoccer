<?php
if (!isset($_POST['reg_email']) OR !isset($_POST['reg_benutzername'])) { exit; }
?>
<?php include 'zz1.php'; ?>
<title><?php echo _('Registriert'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>

<h1><?php echo _('Registriert'); ?></h1>
<?php
function email_senden($email, $username, $password) {
	$empfaenger = $email;
	$betreff = CONFIG_SITE_NAME.': Willkommen';
	$nachricht = "Hallo ".$username.",\n\nDu hast Dich erfolgreich auf ".CONFIG_SITE_DOMAIN." registriert. Bitte logge Dich jetzt mit Deinen Benutzerdaten ein, um Deinen Account zu aktivieren. Und dann kann es auch schon losgehen ...\n\nDamit Du Dich anmelden kannst, findest Du hier noch einmal Deine Benutzerdaten:\n\nE-Mail: ".$email."\nBenutzername: ".$username."\nPasswort: ".$password."\n\nWir wünschen Dir noch viel Spaß beim Managen!\n\nSportliche Grüße\n".CONFIG_SITE_NAME."\n".CONFIG_SITE_DOMAIN."\n\n------------------------------\n\nDu erhältst diese E-Mail, weil Du Dich auf ".CONFIG_SITE_DOMAIN." mit dieser Adresse registriert hast. Du kannst Deinen Account jederzeit löschen, nachdem Du Dich eingeloggt hast, sodass Du anschließend keine E-Mails mehr von uns bekommst. Bei Missbrauch Deiner E-Mail-Adresse meldest Du Dich bitte per E-Mail unter ".CONFIG_SITE_EMAIL;
	if (CONFIG_EMAIL_PHP_MAILER) {
		require './phpmailer/PHPMailerAutoload.php';
		$mail = new PHPMailer(); // create a new object
		$mail->CharSet = CONFIG_EMAIL_CHARSET;
		$mail->IsSMTP();
		$mail->SMTPAuth = CONFIG_EMAIL_AUTH;
		$mail->SMTPSecure = CONFIG_EMAIL_SECURE;
		$mail->Host = CONFIG_EMAIL_HOST;
		$mail->Port = CONFIG_EMAIL_PORT;
		$mail->Username = CONFIG_EMAIL_USER;
		$mail->Password = CONFIG_EMAIL_PASS;
		$mail->SetFrom(CONFIG_EMAIL_FROM, CONFIG_SITE_NAME);
		$mail->Subject = $betreff;
		$mail->Body = $nachricht;
		$mail->AddAddress($empfaenger);
		$mail->Send();
	}
	else{
		$header = "From: ".CONFIG_SITE_NAME." <".CONFIG_SITE_EMAIL.">\r\nContent-type: text/plain; charset=utf-8";
		mail($empfaenger, $betreff, $nachricht, $header);
	}
}
$last_ip = getUserIP();
$fehler_gemacht = TRUE;
if (strlen($_POST['reg_email']) > 0 && strlen($_POST['reg_benutzername']) > 0) {
    $email = mysql_real_escape_string(trim(strip_tags($_POST['reg_email'])));
    $email_valide = preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
    if ($email_valide == TRUE) {
		$username = mysql_real_escape_string(trim(strip_tags($_POST['reg_benutzername'])));
		$username = str_replace('_', '', $username);
		$password = mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
		$password_db = md5('1'.$password.'29');
		$blackList1 = "SELECT COUNT(*) FROM ".$prefix."blacklist WHERE email = '".md5($email)."' AND until > ".time();
		$blackList2 = mysql_query($blackList1);
		$blackList3 = mysql_result($blackList2, 0);
		$schon_vorhandene_user = $blackList3;
		$sql1 = "SELECT COUNT(*) FROM ".$prefix."users WHERE email = '".$email."' OR username = '".$username."'";
		$sql2 = mysql_query($sql1);
		$sql3 = mysql_result($sql2, 0);
		$schon_vorhandene_user += $sql3;
		if ($schon_vorhandene_user == 0) {
			$uniqueIDHash = md5($email.time());
			$sql4 = "INSERT INTO ".$prefix."users (email, username, password, regdate, last_login, last_ip, ids, liga, team) VALUES ('".$email."', '".$username."', '".$password_db."', ".time().", ".bigintval(getTimestamp('-14 days')).", '".$last_ip."', '".$uniqueIDHash."', '', '__".$uniqueIDHash."')";
			$sql5 = mysql_query($sql4);
			if ($sql5 != FALSE) {
				if (isset($_SESSION['referralID'])) {
					$refID = mysql_real_escape_string(trim($_SESSION['referralID']));
					if (mb_strlen($refID) == 32) {
						$addReferral1 = "INSERT INTO ".$prefix."referrals (werber, geworben, zeit) VALUES ('".$refID."', '".$uniqueIDHash."', ".time().")";
						$addReferral2 = mysql_query($addReferral1);
					}
				}
				$fehler_gemacht = FALSE;
				if (CONFIG_IS_LOCAL_INSTALLATION) {
					echo '<p><strong>'._('Dein Passwort lautet:').'</strong> '.htmlspecialchars($password).'</p>';
					echo '<p>'._('Du brauchst dieses Passwort unbedingt für den ersten Login. Danach kannst Du es in den Einstellungen ändern, wenn Du möchtest.').'</p>';
				}
				else {
					echo '<p>'._('Vielen Dank, die Registrierung war erfolgreich! Wir senden Dir nun an die angegebene Adresse eine E-Mail mit Deinem Passwort zu. Mit dem Benutzernamen und dem zugeschickten Passwort kannst Du Dich danach einloggen.').'</p>';
					echo '<p>'._('Logge Dich am besten ganz schnell ein - dann kannst Du dir das beste Team sichern! Viel Spaß!').'</p>';
					email_senden($email, $username, $password, $last_ip);
				}
			}
		}
    }
}
if ($fehler_gemacht == TRUE) {
	echo '<p>'._('Die Registrierung konnte leider nicht abgeschlossen werden. Der Benutzername oder die E-Mail-Adresse ist ungültig oder schon vergeben.').' <a href="/index.php">'._('Bitte versuche es noch einmal.').'</a></p>';
}
?>
<?php include 'zz3.php'; ?>
