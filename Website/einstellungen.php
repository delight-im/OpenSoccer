<?php include 'zz1.php'; ?>
<title><?php echo _('Einstellungen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if (isset($_POST['accDelPlus']) && isset($_POST['accDelMinus']) && $cookie_id != CONFIG_DEMO_USER) {
	$accDelPlus = mysql_real_escape_string(trim(strip_tags($_POST['accDelPlus'])));
	$accDelMinus = mysql_real_escape_string(trim(strip_tags($_POST['accDelMinus'])));
	$sql1 = "INSERT INTO ".$prefix."accDel (user, zeit, plus, minus) VALUES ('".$cookie_username."', ".time().", '".$accDelPlus."', '".$accDelMinus."')";
	$sql2 = mysql_query($sql1);
	$getEmail1 = "SELECT email FROM ".$prefix."users WHERE ids = '".$cookie_id."' LIMIT 0, 1";
	$getEmail2 = mysql_query($getEmail1);
	if (mysql_num_rows($getEmail2) > 0) {
		$getEmail3 = mysql_fetch_assoc($getEmail2);
		$getEmail4 = explode('@', $getEmail3['email'], 2);
		if (count($getEmail4) == 2) {
			$getEmailHost = mysql_real_escape_string(trim(strip_tags($getEmail4[1])));
			$putEmail1 = "INSERT INTO ".$prefix."blacklist (email, until, host) VALUES ('".md5($getEmail3['email'])."', ".getTimestamp('+28 days').", '".$getEmailHost."')";
			$putEmail2 = mysql_query($putEmail1);
		}
	}
	if ($_SESSION['status'] != 'Bigpoint') {
		$sql1 = "UPDATE ".$prefix."users SET last_login = 1, last_urlaub_kurz = 0, last_urlaub_lang = 0, last_uagent = '', infotext = '', email = CONCAT('GELOESCHT', id), username = CONCAT('GELOESCHT', id), password = REVERSE(password) WHERE ids = '".$cookie_id."'";
		$sql2 = mysql_query($sql1);
	}
	else {
		$sql1 = "UPDATE ".$prefix."users SET last_login = 1, last_urlaub_kurz = 0, last_urlaub_lang = 0, last_uagent = '', infotext = '', username = CONCAT('GELOESCHT', id) WHERE ids = '".$cookie_id."'";
		$sql2 = mysql_query($sql1);
	}
	$sql11 = "DELETE FROM ".$prefix."pn WHERE von = '".$cookie_id."' OR an = '".$cookie_id."'";
	$sql12 = mysql_query($sql11);
	$sql11 = "DELETE FROM ".$prefix."freunde WHERE f1 = '".$cookie_id."' OR f2 = '".$cookie_id."'";
	$sql12 = mysql_query($sql11);
	$sql11 = "DELETE FROM ".$prefix."freunde_anfragen WHERE von = '".$cookie_id."' OR an = '".$cookie_id."'";
	$sql12 = mysql_query($sql11);
	$howLong1 = "SELECT regdate FROM ".$prefix."users WHERE ids = '".$cookie_id."'";
	$howLong2 = mysql_query($howLong1);
	if (mysql_num_rows($howLong2) == 1) {
		$howLong3 = mysql_fetch_assoc($howLong2);
		$wielange1 = "INSERT INTO ".$prefix."abmeldungen (zeit, username, liga, dabei, ip) VALUES (".time().", '".$cookie_username."', '".$cookie_liga."', ".intval(time()-$howLong3['regdate']).", '".getUserIP()."')";
		$wielange2 = mysql_query($wielange1);
	}
	header('Location: /logout.php');
	exit;
}
$get_urlaub1 = "SELECT urlaub, email FROM ".$prefix."users WHERE ids = '".$cookie_id."'";
$get_urlaub2 = mysql_query($get_urlaub1);
$get_urlaub3 = mysql_fetch_assoc($get_urlaub2);
$noch_urlaub = $get_urlaub3['urlaub'];
$mailAdresse = $get_urlaub3['email'];
$get_urlaub4 = "SELECT ende FROM ".$prefix."urlaub WHERE user = '".$cookie_id."'";
$get_urlaub5 = mysql_query($get_urlaub4);
if (mysql_num_rows($get_urlaub5) > 0) {
	$get_urlaub6 = mysql_fetch_assoc($get_urlaub5);
	if ($get_urlaub6['ende'] > time()) {
        $aktueller_urlaub = '<p>'.__('Du hast zurzeit Urlaub, und zwar bis zum %s.', date('d.m.Y', $get_urlaub6['ende'])).'</p>';
        $aktueller_urlaub .= '<form action="/einstellungen.php" method="post" accept-charset="utf-8"><input type="hidden" name="urlaub_abbrechen" value="1" /><input type="submit" value="'._('Urlaub abbrechen').'" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /></form>';
	}
	else {
		$aktueller_urlaub = '';
	}
}
else {
	$aktueller_urlaub = '';
}
if (isset($_POST['urlaub_abbrechen']) && $cookie_id != CONFIG_DEMO_USER) {
	if ($_POST['urlaub_abbrechen'] == '1') {
		$cancelUrlaub1 = "DELETE FROM ".$prefix."urlaub WHERE user = '".$cookie_id."'";
		$cancelUrlaub2 = mysql_query($cancelUrlaub1);
		addInfoBox(_('Dein Urlaub wurde abgebrochen. Du hast nun wieder die volle Kontrolle über Dein Team.'));
	}
}
if (isset($_POST['pw_alt']) && isset($_POST['pw_neu1']) && isset($_POST['pw_neu2']) && $cookie_id != CONFIG_DEMO_USER) {
$pw_meldung = _('Dein Passwort konnte leider nicht geändert werden. Bitte versuche es noch einmal!');
	$pw_alt = trim($_POST['pw_alt']);
	$pw_neu1 = trim($_POST['pw_neu1']);
	$pw_neu2 = trim($_POST['pw_neu2']);
    if ($pw_neu1 == $pw_neu2) {
        if (mb_strlen($pw_neu1) >= 6) {
            if ($pw_neu1 != $pw_alt) {
                $pw_alt = md5('1'.$pw_alt.'29');
                $pw_neu = md5('1'.$pw_neu1.'29');
                $sql1 = "UPDATE ".$prefix."users SET password = '".$pw_neu."' WHERE password = '".$pw_alt."' AND ids = '".$cookie_id."'";
                $sql2 = mysql_query($sql1);
                if ($sql2 != FALSE) {
                    if (mysql_affected_rows() > 0) {
                        setTaskDone('change_pw');
                        $pw_meldung = _('Dein Passwort wurde erfolgreich geändert!');
                    }
                }
            }
            else {
                $pw_meldung = _('Dein altes und neues Passwort dürfen nicht übereinstimmen!');
            }
        }
        else {
            $pw_meldung = _('Bitte gib mindestens 6 Zeichen für das Passwort ein!');
        }
    }
}
if (isset($_POST['urlaub_ende']) && $cookie_id != CONFIG_DEMO_USER) {
	if ($cookie_team != '__'.$cookie_id) {
		if (!isset($_SESSION['urlaub_min'])) { $_SESSION['urlaub_min'] = 0; }
		if (!isset($_SESSION['urlaub_max'])) { $_SESSION['urlaub_max'] = 0; }
        $ul_meldung = _('Du kannst leider keinen Urlaub beantragen, der so lange dauert.');
		$urlaub_ende = bigintval($_POST['urlaub_ende']);
		$temp = ceil(($urlaub_ende-time())/86400);
		if ($temp >= 1 && $temp <= 30 && $aktueller_urlaub == '') {
			// ART DES URLAUBS ANFANG
			if ($temp >= 1 && $temp <= 10 && $_SESSION['urlaub_min'] <= $temp && $_SESSION['urlaub_max'] >= $temp) {
				$sql1 = "UPDATE ".$prefix."users SET last_urlaub_kurz = ".time()." WHERE ids = '".$cookie_id."'";
				$sql2 = mysql_query($sql1);
			}
			elseif ($temp >= 11 && $temp <= 30 && $_SESSION['urlaub_min'] <= $temp && $_SESSION['urlaub_max'] >= $temp) {
				$sql1 = "UPDATE ".$prefix."users SET last_urlaub_lang = ".time()." WHERE ids = '".$cookie_id."'";
				$sql2 = mysql_query($sql1);
			}
			else {
				exit;
			}
			// ART DES URLAUBS ENDE
			$sql3 = "INSERT INTO ".$prefix."urlaub (user, team, ende) VALUES ('".$cookie_id."', '".$cookie_team."', '".$urlaub_ende."')";
			$sql4 = mysql_query($sql3);
			$sql5 = "DELETE FROM ".$prefix."urlaub WHERE ende < ".time();
			$sql6 = mysql_query($sql5);
			$ul_meldung = _('Dein Urlaub wurde genehmigt!');
			$_SESSION['urlaub_min'] = 0;
			$_SESSION['urlaub_max'] = 0;
		}
		$get_urlaub4 = "SELECT ende FROM ".$prefix."urlaub WHERE user = '".$cookie_id."'";
		$get_urlaub5 = mysql_query($get_urlaub4);
		if (mysql_num_rows($get_urlaub5) > 0) {
			$get_urlaub6 = mysql_fetch_assoc($get_urlaub5);
			if ($get_urlaub6['ende'] > time()) {
                $aktueller_urlaub = '<p>'.__('Du hast zurzeit Urlaub, und zwar bis zum %s.', date('d.m.Y', $get_urlaub6['ende'])).'</p>';
			}
			else {
				$aktueller_urlaub = '';
			}
		}
		else {
			$aktueller_urlaub = '';
		}
	}
}
?>
<?php if (isset($pw_meldung)) { echo '<h1>'._('Hinweis').'</h1><p style="color:red">'.$pw_meldung.'</p>'; } ?>
<?php if (isset($ul_meldung)) { echo '<h1>'._('Hinweis').'</h1><p style="color:red">'.$ul_meldung.'</p>'; } ?>

<h1><?php echo _('E-Mail-Adresse'); ?></h1>
<p><?php echo _('Du bist mit der folgenden E-Mail-Adresse registriert:'); ?><br /><?php echo $mailAdresse; ?></p>

<?php if ($cookie_team != '__'.$cookie_id) { ?>
<h1><?php echo _('Vereinsnamen ändern'); ?></h1>
<p><?php echo __('Du möchtest den Namen Deines Vereins ändern? Dann kannst Du das %s tun!', '<a href="/namensaenderung.php">'._('auf dieser Seite').'</a>'); ?></p>
<p><?php echo __('Eine Liste mit %s findest Du hier. Diese Namen darfst Du leider nicht für Dein Team verwenden.', '<a href="/gesperrteTeamnamen.php">'._('geschützten Namen').'</a>'); ?></p>
<?php } ?>

<h1><?php echo _('Managernamen ändern'); ?></h1>
<p><?php echo __('Du möchtest Deinen eigenen Managernamen ändern? Schreibe bitte einfach einem %1$s eine kurze Nachricht. Dein Name wird dann so bald wie möglich geändert, wenn er nicht gegen die %2$s verstößt.', '<a href="/wio.php#teamList">'._('Mitglied des Support-Teams').'</a>', '<a href="/regeln.php">'._('Regeln').'</a>').'</p><p><strong>'._('Achtung:').'</strong> '._('Beim nächsten Login solltest Du Dich mit Deiner E-Mail-Adresse einloggen oder darauf achten, auch den neuen Namen zu versuchen. Das Einloggen mit dem alten Managernamen ist dann nicht mehr möglich.'); ?></p>

<?php if ($cookie_team != '__'.$cookie_id) { ?>
<h1><?php echo _('Urlaub beantragen'); ?></h1>
<?php echo $aktueller_urlaub; ?>
<?php if ($aktueller_urlaub == '') { ?>
<p><?php echo _('Mit dem folgenden Formular kannst Du Urlaub beantragen. In Deiner Urlaubszeit kannst Du nicht automatisch gelöscht werden. Außerdem verwaltet der Computer Deine Aufstellung und verlängert alle Spieler-Verträge, die während Deines Urlaubs auslaufen würden.'); ?></p>
<?php
$get_urlaub6 = "SELECT last_urlaub_kurz, last_urlaub_lang FROM ".$prefix."users WHERE ids = '".$cookie_id."'";
$get_urlaub7 = mysql_query($get_urlaub6);
$get_urlaub8 = mysql_fetch_assoc($get_urlaub7);
$urlaub_erlaubt_kurz = FALSE;
$urlaub_erlaubt_lang = FALSE;
// KURZURLAUB ANFANG
$timeout = getTimestamp('-30 days');
if ($get_urlaub8['last_urlaub_kurz'] == 0) {
    $letzter_urlaub = _('noch keinen Urlaub');
}
else {
    $letzter_urlaub = 'zuletzt am '.date('d.m.Y', $get_urlaub8['last_urlaub_kurz']).' Kurzurlaub';
}
if ($get_urlaub8['last_urlaub_kurz'] < $timeout) {
	$naechste_moeglichkeit = _('Du kannst jetzt neuen Urlaub beantragen.');
	$urlaub_erlaubt_kurz = TRUE;
}
else {
	$days_to_wait = ceil(abs($get_urlaub8['last_urlaub_kurz']-$timeout)/3600/24);
	$naechste_moeglichkeit = 'Du musst noch '.$days_to_wait.' Tage warten, bis Du wieder Urlaub beantragen kannst.';
}
echo '<p><strong>'._('Kurzurlaub (1-10 Tage):').'</strong> '.__('Du hast %1$s beantragt. %2$s', $letzter_urlaub, $naechste_moeglichkeit);
// KURZURLAUB ENDE
// LANGER URLAUB ANFANG
$timeout = getTimestamp('-60 days');
if ($get_urlaub8['last_urlaub_lang'] == 0) {
    $letzter_urlaub = _('noch keinen Urlaub');
}
else {
    $letzter_urlaub = __('zuletzt am %s einen langen Urlaub', date('d.m.Y', $get_urlaub8['last_urlaub_lang']));
}
if ($get_urlaub8['last_urlaub_lang'] < $timeout) {
	$naechste_moeglichkeit = _('Du kannst jetzt neuen Urlaub beantragen.');
	$urlaub_erlaubt_lang = TRUE;
}
else {
	$days_to_wait = ceil(abs($get_urlaub8['last_urlaub_lang']-$timeout)/3600/24);
    $naechste_moeglichkeit = __('Du musst noch %d Tage warten, bis Du wieder Urlaub beantragen kannst.', $days_to_wait);
}
echo '<p><strong>'._('Langer Urlaub (11-30 Tage):').'</strong> '.__('Du hast %1$s beantragt. %2$s', $letzter_urlaub, $naechste_moeglichkeit);
// LANGER URLAUB ENDE
?>
</p>
<?php if ($urlaub_erlaubt_kurz == TRUE OR $urlaub_erlaubt_lang == TRUE) { ?>
<form action="/einstellungen.php" method="post" accept-charset="utf-8">
<p>Beginn:<br /><?php echo date('d.m.Y', time()); ?> (heute)</p>
<p>Ende:<br /><select name="urlaub_ende" size="1">
<?php
if ($urlaub_erlaubt_kurz == TRUE && $urlaub_erlaubt_lang == TRUE) {
	$start_urlaub = 1;
	$noch_urlaub = 30;
}
elseif ($urlaub_erlaubt_kurz == TRUE) {
	$start_urlaub = 1;
	$noch_urlaub = 10;
}
elseif ($urlaub_erlaubt_lang == TRUE) {
	$start_urlaub = 11;
	$noch_urlaub = 30;
}
else {
	$start_urlaub = 0;
	$noch_urlaub = 0;
}
$_SESSION['urlaub_min'] = $start_urlaub;
$_SESSION['urlaub_max'] = $noch_urlaub;
for ($i = $start_urlaub; $i <= $noch_urlaub; $i++) {
	$temp = getTimestamp('+'.$i.' days');
	echo '<option value="'.$temp.'">'.date('d.m.Y', $temp).'</option>';
}
?>
</select></p>
<p><input type="submit" value="<?php echo _('Beantragen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<?php } ?>
<?php } ?>
<?php } ?>

<h1><?php echo _('Passwort ändern'); ?></h1>
<p><?php echo _('Mit dem folgenden Formular kannst Du Dein Passwort ändern. Dazu musst Du alle Felder ausfüllen.'); ?></p>
<form action="/einstellungen.php" method="post" accept-charset="utf-8">
<p><?php echo _('Altes Passwort:'); ?><br /><input type="password" name="pw_alt" size="50" /></p>
<p><?php echo _('Neues Passwort:'); ?><br /><input type="password" name="pw_neu1" size="50" /></p>
<p><?php echo _('Neues Passwort (Bestätigung):'); ?><br /><input type="password" name="pw_neu2" size="50" /></p>
<p><input type="submit" value="<?php echo _('Passwort ändern'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>

<h1 id="accDel"><?php echo _('Account löschen'); ?></h1>
<p><?php echo _('Du bist Dir wirklich sicher, dass Du Deinen Account löschen möchtest? Das ist sehr schade, aber wir akzeptieren das natürlich.'); ?></p>
<p><?php echo _('Wir würden uns freuen, wenn Du uns noch mitteilen würdest, was Dir hier gefallen hat und was noch nicht so gut war.'); ?></p>
<form action="/einstellungen.php" method="post" accept-charset="utf-8">
<p><?php echo _('Das war gut:'); ?><br /><input type="text" name="accDelPlus" style="width:250px" /></p>
<p><?php echo _('Das hat mir nicht gefallen:'); ?><br /><input type="text" name="accDelMinus" style="width:250px" /></p>
<p><input type="submit" value="<?php echo _('Account endgültig löschen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<?php } else { ?>
<h1><?php echo _('Einstellungen'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
