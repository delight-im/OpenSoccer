<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$a1 = "SELECT ids, vorname, nachname, staerke, talent, team, leiher, wiealt, position FROM ".$prefix."spieler WHERE spiele_gesamt > 8 ORDER BY RAND() LIMIT 0, 200";
$a2 = mysql_query($a1);
$counter = 0;
while ($a3 = mysql_fetch_assoc($a2)) {
	$counter++;
	if ($a3['wiealt'] >= 11315) { // Spieler ab 31 werden schwaecher
		$minusP = floor($a3['wiealt']/365-28)/70; // floor() weil es das Alter nur ganz gibt und nicht 31,5 oder so
		if ($a3['position'] == 'T') {
			$minus = mt_rand(1, 2)/10;
			$neu = $a3['staerke']-$minus;
		}
		else {
			$minus = round($a3['staerke']*$minusP, 1);
			if ($minus < 0.1) { $minus = 0.1; }
			$neu = $a3['staerke']-$minus;
		}
		if ($neu < 0.1) { $neu = 0.1; }
		$a4 = "UPDATE ".$prefix."spieler SET staerke = ".$neu.", talent = ".$neu.", spiele_gesamt = 0 WHERE ids = '".$a3['ids']."'";
		$a5 = mysql_query($a4);
		// PROTOKOLL ANFANG
		$formulierung = 'Dein ';
		if ($a3['leiher'] != 'keiner') { $formulierung .= 'ausgeliehener '; }
		$formulierung .= 'Spieler <a href="/spieler.php?id='.$a3['ids'].'">'.$a3['vorname'].' '.$a3['nachname'].'</a> hat sich wegen seines Alters um '.number_format($minus, 1, ',', '.').' Stärkepunkte verschlechtert.';
		$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$a3['team']."', '".$formulierung."', 'Spieler', '".time()."')";
		$sql8 = mysql_query($sql7);
		$entwLog1 = "INSERT INTO ".$prefix."spielerEntwicklung (team, spieler, zeit, staerkeNeu, staerkeAlt) VALUES ('".$a3['team']."', '".$a3['ids']."', ".time().", ".$neu.", ".$a3['staerke'].")";
		$entwLog2 = mysql_query($entwLog1);
		if ($a3['leiher'] != 'keiner') {
			$formulierung = 'Dein verliehener Spieler <a href="/spieler.php?id='.$a3['ids'].'">'.$a3['vorname'].' '.$a3['nachname'].'</a> hat sich wegen seines Alters um '.number_format($minus, 1, ',', '.').' Stärkepunkte verschlechtert.';
			$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$a3['leiher']."', '".$formulierung."', 'Spieler', '".time()."')";
			$sql8 = mysql_query($sql7);
			$entwLog1 = "INSERT INTO ".$prefix."spielerEntwicklung (team, spieler, zeit, staerkeNeu, staerkeAlt) VALUES ('".$a3['leiher']."', '".$a3['ids']."', ".time().", ".$neu.", ".$a3['staerke'].")";
			$entwLog2 = mysql_query($entwLog1);
		}
		// PROTOKOLL ENDE
	}
	else { // staerker werden (junge Spieler)
		$zufall = (mt_rand(0, 5)+1)/10;
		$plus = $zufall/$a3['staerke']*5;
		$plus = ceil($plus*10)/10;
		if ($plus < 0.1) { $plus = 0.1; }
		if ($plus > 1.2) { $plus = 1.2; }
		$neu = $a3['staerke']+$plus;
		if ($neu > $a3['talent']) { // wenn das volle Plus nicht moeglich ist nur Teil nehmen
			$plus = abs($a3['talent']-$a3['staerke']);
			$neu = $a3['staerke']+$plus;
		}
		if ($a3['staerke'] == $a3['talent']) {
			$a4 = "UPDATE ".$prefix."spieler SET spiele_gesamt = 0 WHERE ids = '".$a3['ids']."'";
			$a5 = mysql_query($a4);
			// PROTOKOLL ANFANG
			$formulierung = 'Dein ';
			if ($a3['leiher'] != 'keiner') { $formulierung .= 'ausgeliehener '; }
			$formulierung .= 'Spieler <a href="/spieler.php?id='.$a3['ids'].'">'.$a3['vorname'].' '.$a3['nachname'].'</a> hat seinen Höhepunkt erreicht und entwickelt sich nicht mehr weiter.';
			$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$a3['team']."', '".$formulierung."', 'Spieler', '".time()."')";
			$sql8 = mysql_query($sql7);
			$entwLog1 = "INSERT INTO ".$prefix."spielerEntwicklung (team, spieler, zeit, staerkeNeu, staerkeAlt) VALUES ('".$a3['team']."', '".$a3['ids']."', ".time().", ".$neu.", ".$a3['staerke'].")";
			$entwLog2 = mysql_query($entwLog1);
			if ($a3['leiher'] != 'keiner') {
				$formulierung = 'Dein verliehener Spieler <a href="/spieler.php?id='.$a3['ids'].'">'.$a3['vorname'].' '.$a3['nachname'].'</a> hat seinen Höhepunkt erreicht und entwickelt sich nicht mehr weiter.';
				$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$a3['leiher']."', '".$formulierung."', 'Spieler', '".time()."')";
				$sql8 = mysql_query($sql7);
				$entwLog1 = "INSERT INTO ".$prefix."spielerEntwicklung (team, spieler, zeit, staerkeNeu, staerkeAlt) VALUES ('".$a3['leiher']."', '".$a3['ids']."', ".time().", ".$neu.", ".$a3['staerke'].")";
				$entwLog2 = mysql_query($entwLog1);
			}
			// PROTOKOLL ENDE
		}
		else {
			$a4 = "UPDATE ".$prefix."spieler SET staerke = ".$neu.", spiele_gesamt = 0 WHERE ids = '".$a3['ids']."'";
			$a5 = mysql_query($a4);
			// PROTOKOLL ANFANG
			$formulierung = 'Dein ';
			if ($a3['leiher'] != 'keiner') { $formulierung .= 'ausgeliehener '; }
			$formulierung .= 'Spieler <a href="/spieler.php?id='.$a3['ids'].'">'.$a3['vorname'].' '.$a3['nachname'].'</a> hat sich durch Spielpraxis um '.number_format($plus, 1, ',', '.').' Stärkepunkte verbessert.';
			$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$a3['team']."', '".$formulierung."', 'Spieler', '".time()."')";
			$sql8 = mysql_query($sql7);
			$entwLog1 = "INSERT INTO ".$prefix."spielerEntwicklung (team, spieler, zeit, staerkeNeu, staerkeAlt) VALUES ('".$a3['team']."', '".$a3['ids']."', ".time().", ".$neu.", ".$a3['staerke'].")";
			$entwLog2 = mysql_query($entwLog1);
			if ($a3['leiher'] != 'keiner') {
				$formulierung = 'Dein verliehener Spieler <a href="/spieler.php?id='.$a3['ids'].'">'.$a3['vorname'].' '.$a3['nachname'].'</a> hat sich durch Spielpraxis um '.number_format($plus, 1, ',', '.').' Stärkepunkte verbessert.';
				$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$a3['leiher']."', '".$formulierung."', 'Spieler', '".time()."')";
				$sql8 = mysql_query($sql7);
				$entwLog1 = "INSERT INTO ".$prefix."spielerEntwicklung (team, spieler, zeit, staerkeNeu, staerkeAlt) VALUES ('".$a3['leiher']."', '".$a3['ids']."', ".time().", ".$neu.", ".$a3['staerke'].")";
				$entwLog2 = mysql_query($entwLog1);
			}
			// PROTOKOLL ENDE
		}
	}
}
echo $counter;
?>
