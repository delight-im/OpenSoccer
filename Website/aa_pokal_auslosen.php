<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
if ($cookie_spieltag < 2 OR $cookie_spieltag > 21) { exit; }
if (date('H', time()) == 18 OR date('H', time()) == 19) { exit; } // Live-Spiele
$plan1 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE typ = 'Pokal' AND simuliert = 0";
$plan2 = mysql_query($plan1);
$plan2a = mysql_result($plan2, 0);
if ($plan2a > 0) { exit; } // wenn noch ausstehende Spiele da sind, abbrechen
$startzeit = mktime(19, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
$startzeit = getTimestamp('+'.intval(4-$cookie_spieltag).' days', $startzeit); // erster Pokal-Spieltag ist der 4. Liga-Spieltag
// WELCHE RUNDE ANFANG
$wrij1 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE typ = 'Pokal' AND simuliert = 1";
$wrij2 = mysql_query($wrij1);
$wrij3 = mysql_result($wrij2, 0);
switch ($wrij3) {
	case 0: $naechste_runde = 1; break; // noch kein Spiel
	case 32: $naechste_runde = 2; break; // 2x16Finale
	case 48: $naechste_runde = 3; break; // 2x8Finale
	case 56: $naechste_runde = 4; break; // 2x4Finale
	case 60: $naechste_runde = 5; break; // 2x2Finale
	case 61: $naechste_runde = 6; break; // 1x1Finale
	default: exit;
}
// WELCHE RUNDE ENDE
// TEAMS HOLEN ANFANG
$teamliste = array();
if ($naechste_runde == 1) {
	// TEAM-AUSWAHL NACH 5-JAHRES-WERTUNG ANFANG
	$teams1 = "SELECT name, liga FROM ".$prefix."teams WHERE vorjahr_liga IN (SELECT ids FROM ".$prefix."ligen WHERE hoch = 'KEINE') ORDER BY vorjahr_platz ASC LIMIT 0, 39";
	$teams2 = mysql_query($teams1);
	$allTeams = array();
	while ($teams3 = mysql_fetch_assoc($teams2)) {
		if (!isset($allTeams[$teams3['liga']])) { $allTeams[$teams3['liga']] = array(); }
		$allTeams[$teams3['liga']][] = $teams3['name'];
	}
	foreach ($allTeams as $teamList) {
		$teamliste[] = $teamList[0]; // Erstplatzierten aus dieser Liga hinzufügen
		$teamliste[] = $teamList[1]; // Zweitplatzierten aus dieser Liga hinzufügen
	}
	$teams4 = "SELECT ids FROM ".$prefix."ligen WHERE hoch = 'KEINE' ORDER BY pkt_gesamt DESC, pkt_saison1 DESC, pkt_saison2 DESC, pkt_saison3 DESC, pkt_saison4 DESC, pkt_saison5 DESC LIMIT 0, 6";
	$teams5 = mysql_query($teams4);
	while ($teams6 = mysql_fetch_assoc($teams5)) {
		$teamliste[] = $allTeams[$teams6['ids']][2]; // Drittplatzierte aus 6 besten Ligen hinzufügen
	}
	// TEAM-AUSWAHL NACH 5-JAHRES-WERTUNG ENDE
	// POKALSIEGER AUTOMATISCH QUALIFIZIEREN ANFANG
	$oldWinner1 = "SELECT name FROM ".$prefix."teams ORDER BY vorjahr_pokalrunde DESC LIMIT 0, 1";
	$oldWinner2 = mysql_query($oldWinner1);
	if (mysql_num_rows($oldWinner2) == 1) {
		$oldWinner3 = mysql_fetch_assoc($oldWinner2);
		if (!in_array($oldWinner3['name'], $teamliste)) {
			$rausgeworfenesTeam = array_pop($teamliste); // letztes Team wieder rauswerfen
			if (isset($rausgeworfenesTeam)) {
				$teamliste[] = $oldWinner3['name'];
			}
		}
	}
	// POKALSIEGER AUTOMATISCH QUALIFIZIEREN ENDE
	// 5-JAHRES-WERTUNG ANFANG
	// pkt_saison3 = vor zwei Jahren, pkt_saison2 = vor einem Jahr, pkt_saison1 = jetzt
	$jahwer1 = "UPDATE ".$prefix."ligen SET pkt_saison5 = pkt_saison4, pkt_saison4 = pkt_saison3, pkt_saison3 = pkt_saison2, pkt_saison2 = pkt_saison1, pkt_saison1 = 0";
	$jahwer2 = mysql_query($jahwer1);
	// 5-JAHRES-WERTUNG ENDE
    // POKALERGEBNIS LETZTES JAHR ZURÜCKSETZEN ANFANG
    $sddfhooph1 = "UPDATE ".$prefix."teams SET pokalrunde = 0";
    $sddfhooph2 = mysql_query($sddfhooph1);
    // POKALERGEBNIS LETZTES JAHR ZURÜCKSETZEN ENDE
}
elseif ($naechste_runde < 6) {
	$temp = $naechste_runde-1;
	$temp = 'Pokal_Runde_'.$temp;
    $ergebnisse = array();
	$teams1 = "SELECT team1, team2, ergebnis, kennung FROM ".$prefix."spiele WHERE liga = '".$temp."' ORDER BY id ASC"; // ORDER wichtig, damit 1. Spiel immer vor 2. Spiel eingerechnet wird
    $teams2 = mysql_query($teams1);
    while ($teams3 = mysql_fetch_assoc($teams2)) {
        $tore = explode(':', $teams3['ergebnis']);
        if (!isset($ergebnisse[$teams3['kennung']])) { // 1. Spiel
            $ergebnisse[$teams3['kennung']] = array('team1'=>$teams3['team1'], 'team2'=>$teams3['team2'], 'tore1'=>0, 'tore2'=>0);
            $ergebnisse[$teams3['kennung']]['tore1'] += $tore[0];
            $ergebnisse[$teams3['kennung']]['tore2'] += $tore[1]*1.000001; // Auswaerts-Tore zaehlen mehr
        }
        else { // 2. Spiel
            $ergebnisse[$teams3['kennung']]['tore1'] += $tore[1]*1.000001; // Auswaerts-Tore zaehlen mehr
            $ergebnisse[$teams3['kennung']]['tore2'] += $tore[0];
            if ($ergebnisse[$teams3['kennung']]['tore1'] > $ergebnisse[$teams3['kennung']]['tore2']) {
                $teamliste[] = $ergebnisse[$teams3['kennung']]['team1'];
            }
            elseif ($ergebnisse[$teams3['kennung']]['tore2'] > $ergebnisse[$teams3['kennung']]['tore1']) {
                $teamliste[] = $ergebnisse[$teams3['kennung']]['team2'];
            }
			else {
				exit;
			}
        }
    }
}
else { // im Finale nur 1 Spiel, daher keine Auswaertstorregel
	$temp = $naechste_runde-1;
	$temp = 'Pokal_Runde_'.$temp;
	$teams1 = "SELECT team1, team2, ergebnis FROM ".$prefix."spiele WHERE liga = '".$temp."' LIMIT 0, 1";
    $teams2 = mysql_query($teams1);
    $teams3 = mysql_fetch_assoc($teams2);
    $temp = explode(':', $teams3['ergebnis']);
    if ($temp[0] > $temp[1]) {
    	$sieger = $teams3['team1'];
    	$finalgegner = $teams3['team2'];
    }
    elseif ($temp[1] > $temp[0]) {
    	$sieger = $teams3['team2'];
    	$finalgegner = $teams3['team1'];
    }
	else {
		exit;
	}
	$teamliste[] = $sieger; // damit der Wert pokalrunde unten erhöht wird und man den Sieger später erkennen kann
    $teams4 = "UPDATE ".$prefix."teams SET pokalsiege = pokalsiege+1 WHERE name = '".$sieger."'";
    $teams5 = mysql_query($teams4); // Pokalsieger einen Sieg dazuschreiben
    $siegLoga = "SELECT saison FROM ".$prefix."zeitrechnung";
    $siegLogb = mysql_query($siegLoga);
    $siegLogc = mysql_fetch_assoc($siegLogb);
    $siegLog1 = "INSERT INTO man_pokalsieger (saison, sieger, finalgegner) VALUES (".$siegLogc['saison'].", '".mysql_real_escape_string($sieger)."', '".mysql_real_escape_string($finalgegner)."')";
    $siegLog2 = mysql_query($siegLog1);
    $teams6 = "INSERT INTO ".$prefix."spiele (team1, team2, liga, typ, simuliert, datum) VALUES ('keins', 'keins', 'keine', 'Pokal', 1, '".time()."')";
    $teams7 = mysql_query($teams6); // 1 leeres Spiel in die DB schreiben, damit Pokal-Auslosung immer abgebrochen wird
}
// TEAMS HOLEN ENDE
// SPIELPAARUNGEN ANFANG
$startzeit = getTimestamp('+'.intval(($naechste_runde-1)*4).' days', $startzeit); // 1 abziehen, weil fuer den 1. Spieltag 0x86400 addiert werden muss
shuffle($teamliste);
for ($r1 = 0; $r1 <= round(count($teamliste)/2-1); $r1++) { // for-Schleife fuer Spielpaarungen: bei 32 Teams gibt es [0..15]
    $temporaer_liga = 'Pokal_Runde_'.$naechste_runde;
    $temporaer_team1 = $teamliste[$r1];
    $temporaer_team2 = $teamliste[count($teamliste)-$r1-1]; // 0vs32, 1vs31, ..., 15vs16
    $kennung = md5($temporaer_team1.$temporaer_team2);
	$ins1 = "INSERT INTO ".$prefix."spiele (liga, datum, team1, team2, typ, kennung) VALUES ('".$temporaer_liga."', '".$startzeit."', '".$temporaer_team1."', '".$temporaer_team2."', 'Pokal', '".$kennung."')";
	$ins2 = mysql_query($ins1);
	if ($naechste_runde < 5) {
        $ins1 = "INSERT INTO ".$prefix."spiele (liga, datum, team1, team2, typ, kennung) VALUES ('".$temporaer_liga."', '".getTimestamp('+1 day', $startzeit)."', '".$temporaer_team2."', '".$temporaer_team1."', 'Pokal', '".$kennung."')";
        $ins2 = mysql_query($ins1);
	}
}
// SPIELPAARUNGEN ENDE
// POKALERGEBIS SETZEN ANFANG
foreach ($teamliste as $teamliste_pr) {
    $yxcvjhsd1 = "UPDATE ".$prefix."teams SET pokalrunde = ".$naechste_runde." WHERE name = '".$teamliste_pr."'";
    $yxcvjhsd2 = mysql_query($yxcvjhsd1); // aktualisiere die erreichte Pokalrunde
}
// POKALERGEBIS SETZEN ENDE
?>