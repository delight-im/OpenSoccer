<?php
if (!isset($_GET['id'])) { exit; }
include 'zzserver.php';
include 'zzcookie.php';
$spieler = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
$sql1 = "SELECT gehalt, vertrag, jugendTeam, team FROM ".$prefix."spieler WHERE ids = '".$spieler."' AND team = '".$cookie_team."' AND leiher = 'keiner'";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) > 0 && $cookie_id != CONFIG_DEMO_USER) {
    $sql3 = mysql_fetch_assoc($sql2);
	if ($sql3['vertrag'] > time()) {
		if ($sql3['jugendTeam'] == $sql3['team'] && $sql3['gehalt'] % 100000 == 0) {
			$entlassungskosten = 0; // Jugendspieler umsonst entlassen
		}
		else {
			$entlassungskosten = $sql3['gehalt']*ceil(($sql3['vertrag']-time())/86400/22)/2;
		}
		$sql4 = "UPDATE ".$prefix."teams SET konto = konto-".$entlassungskosten." WHERE ids = '".$cookie_team."'";
		$sql5 = mysql_query($sql4);
		$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Entlassungskosten', -".$entlassungskosten.", '".time()."')";
		$buch2 = mysql_query($buch1);
		$sql6 = "UPDATE ".$prefix."spieler SET team = 'frei', liga = 'frei', transfermarkt = 0 WHERE ids = '".$spieler."'";
		$sql7 = mysql_query($sql6);
		$sql6 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$spieler."'";
		$sql7 = mysql_query($sql6);
		// PROTOKOLL ANFANG
		$getmanager1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$spieler."'";
		$getmanager2 = mysql_query($getmanager1);
		$getmanager3 = mysql_fetch_assoc($getmanager2);
		$getmanager4 = $getmanager3['vorname'].' '.$getmanager3['nachname'];
		$formulierung = 'Du hast den Spieler <a href="/spieler.php?id='.$spieler.'">'.$getmanager4.'</a> entlassen.';
		$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Spieler', '".time()."')";
		$sql8 = mysql_query($sql7);
		// PROTOKOLL ENDE
	}
}
header('Location: /kader.php');
?>
