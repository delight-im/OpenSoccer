<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$sql1 = "SELECT ids, team, leiher, praemienAbrechnung, spiele, praemieProEinsatz FROM ".$prefix."spieler WHERE leiher != 'keiner' AND praemieProEinsatz > 0 AND spiele != praemienAbrechnung LIMIT 0, 50";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$spielerID = $sql3['ids'];
	$zuZahlen = $sql3['praemieProEinsatz']*($sql3['spiele']-$sql3['praemienAbrechnung']);
	if ($zuZahlen == 0) { continue; }
	$zahlungAn = $sql3['team'];
	$zahlungVon = $sql3['leiher'];
	// ZAHLUNGEN ANFANG
	$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$zahlungAn."', 'Leihprämie', ".$zuZahlen.", ".time().")";
	$buch2 = mysql_query($buch1);
	$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$zahlungVon."', 'Prämienzahlung', -".$zuZahlen.", ".time().")";
	$buch2 = mysql_query($buch1);
	$praemienabzug1 = "UPDATE ".$prefix."teams SET konto = konto-".$zuZahlen." WHERE ids = '".$zahlungVon."'";
	$praemienabzug2 = mysql_query($praemienabzug1);
	$praemienabzug1 = "UPDATE ".$prefix."teams SET konto = konto+".$zuZahlen." WHERE ids = '".$zahlungAn."'";
	$praemienabzug2 = mysql_query($praemienabzug1);
	// ZAHLUNGEN ENDE
	$sql4 = "UPDATE ".$prefix."spieler SET praemienAbrechnung = spiele WHERE ids = '".$spielerID."'";
	$sql5 = mysql_query($sql4);
}
?>