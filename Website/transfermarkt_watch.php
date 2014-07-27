<?php
if (!isset($_GET['id'])) { exit; }
include 'zzserver.php';
include 'zzcookie.php';
if ($loggedin == 0) { exit; }
$spieler = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
if ($cookie_id != CONFIG_DEMO_USER) {
	$sql1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$spieler."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) != 1) { exit; }
	$sql3 = mysql_fetch_assoc($sql2);
	$name = mysql_real_escape_string($sql3['vorname'].' '.$sql3['nachname']);
	$sql4 = "INSERT INTO ".$prefix."transfermarkt_watch (team, spieler_id, spieler_name) VALUES ('".$cookie_team."', '".$spieler."', '".$name."')";
	$sql5 = mysql_query($sql4);
	if ($sql5 == FALSE) {
		$sql4 = "DELETE FROM ".$prefix."transfermarkt_watch WHERE team = '".$cookie_team."' AND spieler_id = '".$spieler."'";
		$sql5 = mysql_query($sql4);
	}
}
$hadresse = 'Location: /spieler.php?id='.$spieler.'&action=setWatching';
if (isset($_SERVER['HTTP_REFERER'])) {
	if (stripos($_SERVER['HTTP_REFERER'], '/beobachtung.php') !== false) {
		$hadresse = 'Location: /beobachtung.php';
	} 
}
header($hadresse);
?>
