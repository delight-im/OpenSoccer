<?php
header("Expires: Mon, 24 Mar 2008 00:00:00 GMT");
header("Cache-Control: no-cache");
ini_set('session.use_trans_sid', 0);
include 'zzserver.php';
include 'zzcookie.php';
include 'zzfunctions.php';
ob_start();

// BLAETTERN ANFANG
if (isset($_GET['seite'])) { $seite = intval($_GET['seite']); }
else { $seite = 1; }
$eintraege_pro_seite = 15; // ANGEBEN DER BEITRAEGE PRO SEITE
$start = $seite*$eintraege_pro_seite-$eintraege_pro_seite; // ERMITTELN DER STARTZAHL FÜR DIE ABFRAGE
// BLAETTERN ENDE

require_once('./classes/I18N.php');
I18N::init('de_DE', 'messages', './i18n');

// INFO-BOXEN-ARRAY ANFANG
$showInfoBox = array();
function addInfoBox($text) {
	global $showInfoBox;
	$showInfoBox[] = trim($text);
}
function setTaskDone($shortName) {
	global $prefix, $cookie_id, $cookie_team;
	if ($_SESSION['hasLicense'] == 0 && $cookie_team != '__'.$cookie_id) {
		$taskDone1 = "INSERT INTO ".$prefix."licenseTasks_Completed (user, task) VALUES ('".$cookie_id."', '".mysql_real_escape_string(trim($shortName))."')";
		$taskDone2 = mysql_query($taskDone1);
		if ($taskDone2 != FALSE) {
			addInfoBox('Herzlichen Glückwunsch, Du hast gerade einen weiteren Teil deiner <a class="inText" href="/managerPruefung.php">Manager-Prüfung</a> abgeschlossen!');
			$getTaskMoney1 = "UPDATE ".$prefix."teams SET konto = konto+1000000 WHERE ids = '".$cookie_team."'";
			mysql_query($getTaskMoney1);
			$taskBuchung1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Manager-Prüfung', 1000000, ".time().")";
			mysql_query($taskBuchung1);
		}
	}
}
function getSpecialOffer() {
	$reason = 'Heute ist ein schöner Tag';
	$today = date('d.m');
	$ostern = date('d.m', easter_date());
	switch ($today) {
		case '13.07': $reason = 'Der Ballmanager hat Geburtstag'; break;
		case '24.12': $reason = 'Es ist Weihnachten'; break;
		case '01.01': $reason = 'Das neue Jahr hat begonnen'; break;
		case '01.05': $reason = 'Es ist Maifeiertag'; break;
		case '03.10': $reason = 'Es ist Tag der Deutschen Einheit'; break;
		case $ostern: $reason = 'Es ist Ostern'; break;
		default: return false;
	}
	return $reason.' und deshalb darf jeder Manager kostenlos <a class="inText" href="/ver_lotto.php">Lotto spielen</a>!';
}
// INFO-BOXEN-ARRAY ENDE
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="de" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<?php
if (isMobile()) {
	echo '<meta name="robots" content="noindex,follow" />';
}
else {
	echo '<meta name="robots" content="index,follow" />';
}
?>
<link rel="stylesheet" href="/images/Refresh.php?v=234927" type="text/css" />
<script type="text/javascript" src="/js/drop_down.js"></script>
<link rel="stylesheet" href="/css/drop_down.css" type="text/css" />
<link rel="icon" type="image/x-icon" href="/images/favicon.ico" />
<?php if (isMobile()) { ?><meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" /><?php } ?>