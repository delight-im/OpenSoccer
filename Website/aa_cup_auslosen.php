<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$landToSimulate1 = "SELECT land FROM ".$prefix."ligen ORDER BY lastCupSelection ASC LIMIT 0, 1";
$landToSimulate2 = mysql_query($landToSimulate1);
$landToSimulate3 = mysql_fetch_assoc($landToSimulate2);
$landToSimulate4 = mysql_real_escape_string($landToSimulate3['land']);
$sql1 = "UPDATE ".$prefix."ligen SET lastCupSelection = ".time()." WHERE land = '".$landToSimulate4."'";
mysql_query($sql1);
if ($cookie_spieltag < 2 OR $cookie_spieltag > 22) { exit; }
if (date('H', time()) == 10 OR date('H', time()) == 11) { exit; } // Live-Spiele
$landCnt1 = "SELECT simuliert FROM ".$prefix."spiele WHERE typ = 'Cup' AND land = '".$landToSimulate4."'";
$landCnt2 = mysql_query($landCnt1);
$landCnt = array(0=>0, 1=>0); // Counter für simuliert-Werte 0 und 1
while ($landCnt3 = mysql_fetch_assoc($landCnt2)) {
	$landCnt[$landCnt3['simuliert']]++;
}
if ($landCnt[0] > 0) { exit; } // wenn noch ausstehende Spiele da sind, abbrechen
$startzeit = mktime(11, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
$startzeit = getTimestamp('+'.intval(4-$cookie_spieltag).' days', $startzeit); // erster Cup-Spieltag ist der 4. Liga-Spieltag
// WELCHE RUNDE ANFANG
switch ($landCnt[1]) {
	case 0: $naechste_runde = 1; break; // noch kein Spiel
	case 16: $naechste_runde = 2; break; // 1xFreilosRunde
	case 32: $naechste_runde = 3; break; // 1x16Finale
	case 40: $naechste_runde = 4; break; // 1x8Finale
	case 44: $naechste_runde = 5; break; // 1x4Finale
	case 46: $naechste_runde = 6; break; // 1x2Finale
	case 47: $naechste_runde = 7; break; // 1x1Finale
	default: exit;
}
// WELCHE RUNDE ENDE
// TEAMS HOLEN ANFANG
$teamliste = array();
if ($naechste_runde == 1) {
	$freiLos = array();
	$freiLosCounter = 0;
	$teams1 = "SELECT name FROM ".$prefix."teams WHERE liga IN (SELECT ids FROM ".$prefix."ligen WHERE land = '".$landToSimulate4."') ORDER BY elo DESC LIMIT 0, 48";
	$teams2 = mysql_query($teams1);
	while ($teams3 = mysql_fetch_assoc($teams2)) {
		if ($freiLosCounter < 16) {
			$freiLos[] = $teams3['name'];
		}
		else {
			$teamliste[] = $teams3['name'];
		}
		$freiLosCounter++;
	}
    // CUPERGEBNIS LETZTES JAHR ZURÜCKSETZEN ANFANG
    $sddfhooph1 = "UPDATE ".$prefix."teams SET cuprunde = 0 WHERE liga IN (SELECT ids FROM man_ligen WHERE land = '".$landToSimulate4."')";
    $sddfhooph2 = mysql_query($sddfhooph1);
    // CUPERGEBNIS LETZTES JAHR ZURÜCKSETZEN ENDE
    // FREILOSE VERTEILEN ANFANG
	foreach ($freiLos as $freiLosGewinner) {
		$sddfhooph1 = "UPDATE ".$prefix."teams SET cuprunde = 2 WHERE name = '".mysql_real_escape_string($freiLosGewinner)."'";
		$sddfhooph2 = mysql_query($sddfhooph1);
	}
    // FREILOSE VERTEILEN ENDE
}
elseif ($naechste_runde < 7) {
	$temp = $naechste_runde-1;
	$temp = $landToSimulate4.'_Runde_'.$temp;
	$teams1 = "SELECT team1, team2, ergebnis FROM ".$prefix."spiele WHERE liga = '".$temp."'";
    $teams2 = mysql_query($teams1);
    while ($teams3 = mysql_fetch_assoc($teams2)) {
        $tore = explode(':', $teams3['ergebnis']);
		if (intval($tore[0]) > intval($tore[1])) {
			$teamliste[] = $teams3['team1'];
		}
		elseif (intval($tore[1]) > intval($tore[0])) {
			$teamliste[] = $teams3['team2'];
		}
		else {
			exit;
		}
    }
	if ($naechste_runde == 2) {
		$freiLosVerteilung1 = "SELECT name FROM ".$prefix."teams WHERE liga IN (SELECT ids FROM man_ligen WHERE land = '".$landToSimulate4."') AND cuprunde = 2 LIMIT 0, 16";
		$freiLosVerteilung2 = mysql_query($freiLosVerteilung1);
		while ($freiLosVerteilung3 = mysql_fetch_assoc($freiLosVerteilung2)) {
			$teamliste[] = $freiLosVerteilung3['name'];
		}
	}
}
else { // Finale
	$temp = $naechste_runde-1;
	$temp = $landToSimulate4.'_Runde_'.$temp;
	$teams1 = "SELECT team1, team2, ergebnis FROM ".$prefix."spiele WHERE liga = '".$temp."' LIMIT 0, 1";
    $teams2 = mysql_query($teams1);
    $teams3 = mysql_fetch_assoc($teams2);
    $temp = explode(':', $teams3['ergebnis']);
    if (intval($temp[0]) > intval($temp[1])) {
    	$sieger = $teams3['team1'];
    	$finalgegner = $teams3['team2'];
    }
    elseif (intval($temp[1]) > intval($temp[0])) {
    	$sieger = $teams3['team2'];
    	$finalgegner = $teams3['team1'];
    }
	else {
		exit;
	}
    $teams4 = "UPDATE ".$prefix."teams SET cupsiege = cupsiege+1 WHERE name = '".$sieger."'";
    mysql_query($teams4); // Cupsieger einen Sieg dazuschreiben
    $siegLoga = "SELECT saison FROM ".$prefix."zeitrechnung";
    $siegLogb = mysql_query($siegLoga);
    $siegLogc = mysql_fetch_assoc($siegLogb);
    $siegLog1 = "INSERT INTO ".$prefix."cupsieger (saison, land, sieger, finalgegner) VALUES (".$siegLogc['saison'].", '".$landToSimulate4."', '".mysql_real_escape_string($sieger)."', '".mysql_real_escape_string($finalgegner)."')";
    mysql_query($siegLog1);
    $teams6 = "INSERT INTO ".$prefix."spiele (team1, team2, liga, typ, simuliert, datum, land) VALUES ('keins', 'keins', '".$landToSimulate4."_Runde_7', 'Cup', 1, ".time().", '".$landToSimulate4."')";
    mysql_query($teams6); // 1 leeres Spiel in die DB schreiben, damit Cup-Auslosung immer abgebrochen wird
}
// TEAMS HOLEN ENDE
// SPIELPAARUNGEN ANFANG
$startzeit = getTimestamp('+'.intval(($naechste_runde-1)*3).' days', $startzeit); // 1 abziehen, weil fuer den 1. Spieltag 0x86400 addiert werden muss
shuffle($teamliste);
for ($r1 = 0; $r1 <= round(count($teamliste)/2-1); $r1++) { // for-Schleife fuer Spielpaarungen: bei 32 Teams gibt es [0..15]
    $temporaer_liga = $landToSimulate4.'_Runde_'.$naechste_runde;
    $temporaer_team1 = $teamliste[$r1];
    $temporaer_team2 = $teamliste[count($teamliste)-$r1-1]; // 0vs32, 1vs31, ..., 15vs16
	$kennung = md5($temporaer_team1.$temporaer_team2);
	$ins1 = "INSERT INTO ".$prefix."spiele (liga, datum, team1, team2, typ, kennung, land) VALUES ('".$temporaer_liga."', '".$startzeit."', '".$temporaer_team1."', '".$temporaer_team2."', 'Cup', '".$kennung."', '".$landToSimulate4."')";
	mysql_query($ins1);
}
// SPIELPAARUNGEN ENDE
// CUPERGEBIS SETZEN ANFANG
foreach ($teamliste as $teamliste_pr) {
    $yxcvjhsd1 = "UPDATE ".$prefix."teams SET cuprunde = ".$naechste_runde." WHERE name = '".$teamliste_pr."'";
    mysql_query($yxcvjhsd1); // aktualisiere die erreichte Cuprunde
}
// CUPERGEBIS SETZEN ENDE
?>