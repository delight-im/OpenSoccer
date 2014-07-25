<?php if (!isset($_GET['mode'])) { include 'zzserver.php'; } ?>
<?php
$frei1 = "SELECT ids, vorname, nachname, team, wiealt FROM ".$prefix."spieler WHERE vertrag < ".time()." AND team != 'frei'";
$frei2 = mysql_query($frei1);
while ($frei3 = mysql_fetch_assoc($frei2)) {
    $dl1 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$frei3['ids']."'";
    $dl2 = mysql_query($dl1);
    $dl1 = "DELETE FROM ".$prefix."transfermarkt_leihe WHERE spieler = '".$frei3['ids']."'";
    $dl2 = mysql_query($dl1);
    $dl1 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE ids = '".$frei3['ids']."'";
    $dl2 = mysql_query($dl1);
    $getmanager4 = $frei3['vorname'].' '.$frei3['nachname'];
    // PROTOKOLL ANFANG
	if (($frei3['wiealt']/365) > 34) {
		$jobsNachKarriere = array('Jugendtrainer', 'Fanbetreuer', 'Talent-Scout', 'PR-Manager', 'Platzwart', 'Kassierer im Fanshop', 'Schatzmeister', 'persönlicher Assistent des Managers', 'Hausmeister', 'Busfahrer');
		shuffle($jobsNachKarriere);
		$formulierung = 'Der Spieler <a href="/spieler.php?id='.$frei3['ids'].'">'.$getmanager4.'</a> hat seine aktive Zeit beendet und arbeitet nun als '.$jobsNachKarriere[0].' in Deinem Verein.';
	}
	else {
		$formulierung = 'Der Vertrag des Spielers <a href="/spieler.php?id='.$frei3['ids'].'">'.$getmanager4.'</a> ist ausgelaufen. Er spielt jetzt außerhalb Europas.';
	}
    $free1 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$frei3['team']."', '".$formulierung."', 'Spieler', '".time()."')";
    $free2 = mysql_query($free1);
    // PROTOKOLL ENDE
}
$free1 = "UPDATE ".$prefix."spieler SET liga = 'frei', team = 'frei', moral = 100, gehalt = 0, vertrag = 0 WHERE vertrag < ".time()." AND team != 'frei'";
$free2 = mysql_query($free1);
$datum = date('Y-m-d', time());
$sql3 = "UPDATE ".$prefix."zeitrechnung SET letzte_abbuchung = '".$datum."'";
$sql4 = mysql_query($sql3);
if (mysql_affected_rows() > 0) {
	// PREISE SENKEN ANFANG
	$sd1 = "UPDATE ".$prefix."supplyDemandPrices SET price = price*0.9 WHERE price >= 1111112";
	$sd2 = mysql_query($sd1);
	// PREISE SENKEN ENDE
	$sql1 = "SELECT ids, jugendarbeit, fanbetreuer, scout FROM ".$prefix."teams";
	$sql2 = mysql_query($sql1);
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		$sql4 = "SELECT SUM(gehalt) FROM ".$prefix."spieler WHERE team = '".$sql3['ids']."'";
		$sql5 = mysql_query($sql4);
		$sql6 = mysql_fetch_assoc($sql5);
		$sql6 = $sql6['SUM(gehalt)'];
		$abbuchung = round(($sql3['jugendarbeit']*8000000/22)+($sql3['fanbetreuer']*6000000/22)+($sql3['scout']*5000000/22)+($sql6/22));
		$sql7 = "UPDATE ".$prefix."teams SET konto = konto-".$abbuchung." WHERE ids = '".$sql3['ids']."'";
		$sql8 = mysql_query($sql7);
        $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['ids']."', 'Gehälter', -".$abbuchung.", '".time()."')";
        $buch2 = mysql_query($buch1);
    }
    // WERT DES GELDES ANFANG
    $wdgb1 = "SELECT AVG(konto) FROM ".$prefix."teams WHERE last_managed = 0";
    $wdgb2 = mysql_query($wdgb1);
    $sd_durchschnitt = mysql_result($wdgb2, 0);
	$wdgb1 = "SELECT konto FROM ".$prefix."teams WHERE last_managed = 0 ORDER BY konto DESC";
	$wdgb2 = mysql_query($wdgb1);
	$kontowerte = array();
	while ($wdgb3 = mysql_fetch_assoc($wdgb2)) {
		$kontowerte[] = $wdgb3['konto'];
	}
    $anzahl_kontowerte = count($kontowerte);
    if ($anzahl_kontowerte % 2 == 0 ) {
        $sd_median = (($kontowerte[($anzahl_kontowerte/2)-1])+($kontowerte[($anzahl_kontowerte/2)]))/2;
    }
    else {
        $sd_median = $kontowerte[($anzahl_kontowerte/2)];
    }
    $sd_durchschnitt_ou_temp = array_chunk($kontowerte, ceil(($anzahl_kontowerte/2)));
    $sd_durchschnitt_o = array_sum($sd_durchschnitt_ou_temp[0])/count($sd_durchschnitt_ou_temp[0]);
    $sd_durchschnitt_u = array_sum($sd_durchschnitt_ou_temp[1])/count($sd_durchschnitt_ou_temp[1]);
    $preisniveau1 = "SELECT ((SELECT AVG(konto)*468 FROM ".$prefix."teams WHERE last_managed = 0)/(SELECT SUM(marktwert) FROM ".$prefix."spieler WHERE team != 'frei')) AS preisniveau";
	$preisniveau2 = mysql_query($preisniveau1);
	$preisniveau3 = mysql_fetch_assoc($preisniveau2);
	$preisniveau = $preisniveau3['preisniveau']; // P=M/Q (M = Geldmenge, Q = Warenwert)
	$mwert1 = "SELECT AVG(marktwert), SUM(marktwert) FROM ".$prefix."spieler WHERE team != 'frei'";
	$mwert2 = mysql_query($mwert1);
	$mwert3 = mysql_fetch_assoc($mwert2);
	$mwert_avg = $mwert3['AVG(marktwert)'];
	$mwert_sum = $mwert3['SUM(marktwert)'];
	$wdg7 = "INSERT INTO ".$prefix."spielstatistik (datum, durchschnitt, median, durchschnitt_o, durchschnitt_u, preisniveau, marktwert_avg, marktwert_sum) VALUES ('".$datum."', '".$sd_durchschnitt."', '".$sd_median."', '".$sd_durchschnitt_o."', '".$sd_durchschnitt_u."', '".$preisniveau."', '".$mwert_avg."', '".$mwert_sum."')";
    $wdg8 = mysql_query($wdg7);
    // WERT DES GELDES ENDE
}
$zr1 = "UPDATE ".$prefix."zeitrechnung SET zeit = zeit+1";
$zr2 = mysql_query($zr1);
?>
