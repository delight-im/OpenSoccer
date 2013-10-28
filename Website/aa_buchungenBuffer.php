<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
if ($live_scoring_spieltyp_laeuft != '') { exit; }
$sql1 = "SELECT id, teamID, verwendungszweck, betrag FROM ".$prefix."buchungenBuffer WHERE ausfuehren < ".time()." LIMIT 0, 80";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$loeschen1 = "DELETE FROM ".$prefix."buchungenBuffer WHERE id = ".$sql3['id'];
	$loeschen2 = mysql_query($loeschen1);
	if (mysql_affected_rows() > 0) {
		$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['teamID']."', '".$sql3['verwendungszweck']."', ".$sql3['betrag'].", ".time().")";
		$buch2 = mysql_query($buch1);
		$zahlung1 = "UPDATE ".$prefix."teams SET konto = konto+".$sql3['betrag']." WHERE ids = '".$sql3['teamID']."'";
		$zahlung2 = mysql_query($zahlung1);
	}
}
$sql1 = "SELECT id, teamID, pointsGained FROM ".$prefix."eloBuffer WHERE ausfuehren < ".time()." LIMIT 0, 80";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$loeschen1 = "DELETE FROM ".$prefix."eloBuffer WHERE id = ".$sql3['id'];
	$loeschen2 = mysql_query($loeschen1);
	if (mysql_affected_rows() > 0) {
		$eloChange1 = "UPDATE ".$prefix."teams SET elo = elo+".$sql3['pointsGained']." WHERE ids = '".$sql3['teamID']."'";
		$eloChange2 = mysql_query($eloChange1);
	}
}
?>