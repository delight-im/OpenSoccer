<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
// NUR MAXIMAL 2500 FREIE SPIELER ANFANG
$cn1 = "SELECT COUNT(*) FROM ".$prefix."transfermarkt WHERE besitzer = 'KEINER'";
$cn2 = mysql_query($cn1);
$cn3 = mysql_result($cn2, 0);
if ($cn3 > 2500) { exit; }
// NUR MAXIMAL 2500 FREIE SPIELER ENDE
$laengen = array(24, 36, 48, 60, 72);
$sql1 = "SELECT ids, vorname, nachname, staerke, marktwert, transfermarkt, FLOOR(wiealt/365) AS wiealt_jahre, FLOOR(staerke) AS staerke_punkte FROM man_spieler WHERE team = 'frei' AND wiealt < 11315 AND transfermarkt = 0 GROUP BY wiealt_jahre, staerke_punkte, position ORDER BY staerke DESC LIMIT 0, 100";
$sql2 = mysql_query($sql1);
$affectedCounter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	shuffle($laengen);
	$auk_ende = getTimestamp('+'.$laengen[0].' hours');
	$auk_startgebot = round($sql3['marktwert']*mt_rand(10, 14)/10);
	$auk_gehalt = round($sql3['marktwert']/11);
	$sql7 = "INSERT INTO ".$prefix."transfermarkt (spieler, besitzer, gehalt, ende, betrag_highest) VALUES ('".$sql3['ids']."', 'KEINER', '".$auk_gehalt."', '".$auk_ende."', '".$auk_startgebot."')";
	$sql8 = mysql_query($sql7);
	$sql9 = "UPDATE ".$prefix."spieler SET transfermarkt = 1 WHERE ids = '".$sql3['ids']."'";
	$sql10 = mysql_query($sql9);
	$affectedCounter += mysql_affected_rows();
}
$weg1 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE transfermarkt = 1 AND ids NOT IN (SELECT spieler FROM ".$prefix."transfermarkt)";
$weg2 = mysql_query($weg1);
echo $affectedCounter;
?>