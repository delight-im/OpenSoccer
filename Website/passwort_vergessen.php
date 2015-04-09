<?php include 'zz1.php'; ?>
<?php if ($loggedin == 1) { exit; } ?>
<title><?php echo _('Passwort vergessen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php
$showPasswordResetForm = true;
$timeout = getTimestamp('-5 hours');
$ou1 = "DELETE FROM ".$prefix."users_newpw WHERE zeit < ".$timeout;
$ou2 = mysql_query($ou1);
if (isset($_GET['e']) && isset($_GET['k'])) {
	$user = mysql_real_escape_string(trim(strip_tags($_GET['e'])));
	$key = md5(mysql_real_escape_string(trim(strip_tags($_GET['k']))));
	$ou1 = "SELECT user, newpw FROM ".$prefix."users_newpw WHERE user = '".$user."' AND keywert = '".$key."' AND zeit > ".$timeout;
	$ou2 = mysql_query($ou1);
	if (mysql_num_rows($ou2) != 0) {
		$ou3 = mysql_fetch_assoc($ou2);
		$in1 = "UPDATE ".$prefix."users SET password = '".$ou3['newpw']."' WHERE ids = '".$ou3['user']."'";
		$in2 = mysql_query($in1);
		$in1 = "DELETE FROM ".$prefix."users_newpw WHERE user = '".$ou3['user']."'";
		$in2 = mysql_query($in1);

        $showPasswordResetForm = false;
		addInfoBox(_('Dein neues Passwort wurde aktiviert. Du kannst Dich jetzt damit einloggen.'));
	}
	else {
		addInfoBox(_('Das Passwort konnte nicht aktiviert werden. Bitte rufe den Link noch einmal auf oder fordere ein neues Passwort an.'));
	}
}
elseif (isset($_POST['email'])) {
	$email = mysql_real_escape_string(trim(strip_tags($_POST['email'])));
	$ou1 = "SELECT ids, username, regdate FROM ".$prefix."users WHERE email = '".$email."'";
	$ou2 = mysql_query($ou1);
	if (mysql_num_rows($ou2) != 0) {
        $ou3 = mysql_fetch_assoc($ou2);
    	$user = $ou3['ids'];
		if ($user != CONFIG_DEMO_USER) {
			$username = $ou3['username'];
			$key = md5(md5($ou3['regdate']).md5(time()).'29');
			$key_db = md5($key);
			$newpw = mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
			$newpw_db = md5('1'.$newpw.'29');
			$in1 = "INSERT INTO ".$prefix."users_newpw (user, zeit, keywert, newpw) VALUES ('".$user."', '".time()."', '".$key_db."', '".$newpw_db."')";
			$in2 = mysql_query($in1);
			if ($in2 == FALSE) {
				addInfoBox(_('Für diese E-Mail-Adresse wurde in den letzten 5 Stunden schon ein Passwort angefordert.'));
			}
			else {
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$aip = _('Wenn Du kein neues Passwort angefordert hast, wurde diese Funktion von jemand anderem missbraucht. Die Anfrage kam von der folgenden IP-Adresse:').' '.$_SERVER['REMOTE_ADDR'];
				}
				else {
					$aip = '';
				}
// E-MAIL VERSENDEN
$empfaenger = $email;
$betreff = CONFIG_SITE_NAME.': Passwort vergessen';
$nachricht = '
Hallo '.$username.',

Du hast auf ein neues Passwort angefordert.
Dein Neues Passwort lautet: '.$newpw.'
Du musst das neue Passwort aber noch aktivieren, indem Du den folgenden Link anklickst:

http://'.CONFIG_SITE_DOMAIN.'/passwort_vergessen.php?e='.$user.'&k='.$key.'

Wir wünschen Dir noch viel Spaß beim Managen.

Sportliche Grüße
'.CONFIG_SITE_NAME.'
'.CONFIG_SITE_DOMAIN;
if ($aip != '') {
$nachricht .= '

-----------
'.$aip;
}
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
	$header = "From: ".CONFIG_SITE_NAME." <".CONFIG_SITE_EMAIL.">\r\nContent-type: text/plain; charset=UTF-8";
	mail($empfaenger, $betreff, $nachricht, $header);
}
// E-MAIL VERSENDEN
                $showPasswordResetForm = false;
				addInfoBox(_('Der Vorgang war erfolgreich. Wir senden Dir jetzt eine E-Mail mit weiteren Informationen zu.'));
			} // if in2 == FALSE
		}
	}
	else {
		addInfoBox(_('Es konnte kein User mit der angegebenen E-Mail-Adresse gefunden werden. Bitte versuche es noch einmal.'));
	}
}
?>
<h1><?php echo _('Passwort vergessen'); ?></h1>
<?php if ($showPasswordResetForm) { ?>
<p><?php echo _('Du hast Dein Passwort vergessen? Dann gib hier bitte einfach die E-Mail-Adresse ein, mit der Du Dich registriert hast. Wir schicken Dir dann eine E-Mail mit weiteren Informationen, damit Du ein neues Passwort wählen kannst.'); ?><br />
<i><?php echo _('Wichtig:').'</i> '._('Der Link in der E-Mail, die wir Dir senden, ist nur fünf Stunden lang gültig. Danach musst Du die E-Mail erneut anfordern.'); ?></p>
<form method="post" action="/passwort_vergessen.php" accept-charset="utf-8">
<p><?php echo _('E-Mail-Adresse:'); ?><br /><input type="text" name="email" id="email" style="width:200px" /></p>
<p><input type="submit" value="<?php echo _('Anfordern'); ?>" /></p>
</form>
<?php } ?>
<?php include 'zz3.php'; ?>
