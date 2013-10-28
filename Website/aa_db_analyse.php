<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$tabellen = array('chats', 'forum_beitraege', 'forum_themen', 'ligen', 'namen_pool', 'protokoll', 'pn', 'spiele', 'spieler', 'spielplan', 'sponsoren', 'stadien', 'teams', 'transfers', 'users', 'zeitrechnung');
shuffle($tabellen);
$laenge = 4;
$temp = count($tabellen)-$laenge-1;
$versatz = rand(0, $temp);
$tabellen = array_slice($tabellen, $versatz, $laenge);
foreach ($tabellen as $tabelle) {
	$tabellen_name = $prefix.$tabelle;
	$sql1 = "ANALYZE TABLE ".$tabellen_name;
	$sql2 = mysql_query($sql1);
	$sql3 = "OPTIMIZE TABLE ".$tabellen_name;
	$sql4 = mysql_query($sql3);
}
?>
