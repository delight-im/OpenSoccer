<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$sql1 = "SELECT gespielt FROM ".$prefix."ligen LIMIT 0, 1";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
if ($sql3['gespielt'] != 0) { exit; }
$bql1 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE typ = 'Liga'";
$bql2 = mysql_query($bql1);
$bql3 = mysql_result($bql2, 0);
if ($bql3 > 0) { exit; }
$plan1 = "SELECT spieltag, team1, team2 FROM ".$prefix."spielplan";
$plan2 = mysql_query($plan1);
$spielplan = array();
while ($plan3 = mysql_fetch_assoc($plan2)) {
	$spielplan[$plan3['spieltag']][] = array('%'.$plan3['team1'].'%', '%'.$plan3['team2'].'%');
}
$platzhalter = array('%1%', '%2%', '%3%', '%4%', '%5%', '%6%', '%7%', '%8%', '%9%', '%10%', '%11%', '%12%', '%13%', '%14%', '%15%', '%16%', '%17%', '%18%', '%19%', '%20%');
$counterStart = mktime(15, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
$sql4 = "SELECT ids, land FROM ".$prefix."ligen";
$sql5 = mysql_query($sql4);
while ($sql6 = mysql_fetch_assoc($sql5)) {
	$counter = getTimestamp('+1 day', $counterStart);
	$liga_id = $sql6['ids'];
	$liga_land = mysql_real_escape_string($sql6['land']);
	$sql7 = "SELECT name FROM ".$prefix."teams WHERE liga = '".$liga_id."'";
	$sql8 = mysql_query($sql7);
	$mannschaften = array();
	while ($sql9 = mysql_fetch_assoc($sql8)) {
		$mannschaften[] = $sql9['name'];
	}
	shuffle($mannschaften);
	foreach ($spielplan as $spieltag) {
		foreach ($spieltag as $begegnung) {
			$team1 = str_replace($platzhalter, $mannschaften, $begegnung[0]);
			$team1 = mysql_real_escape_string($team1);
			$team2 = str_replace($platzhalter, $mannschaften, $begegnung[1]);
			$team2 = mysql_real_escape_string($team2);
			$rein1 = "INSERT INTO ".$prefix."spiele (liga, datum, team1, team2, typ, land) VALUES ('".$liga_id."', ".$counter.", '".$team1."', '".$team2."', 'Liga', '".$liga_land."')";
			$rein2 = mysql_query($rein1);
		}
		$counter = getTimestamp('+1 day', $counter);
	}
}
?>
