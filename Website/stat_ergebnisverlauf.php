<?php include 'zz1.php'; ?>
<title><?php echo _('Ergebnisverlauf'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Ergebnisverlauf'); ?></h1>
<p><?php echo _('Dieses Liniendiagramm zeigt die Entwicklung Deiner Ergebnisse in der aktuellen Saison. Hinweis: Die Daten können erst nach zwei Spielen angezeigt werden.'); ?></p>
<p>
<?php
$torj1 = "SELECT ergebnis, team1, datum, typ FROM ".$prefix."spiele WHERE (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."') AND simuliert = 1 ORDER BY datum ASC";
$torj2 = mysql_query($torj1);
if (mysql_num_rows($torj2) > 0) {
	$wertemenge = '';
	$onlyLiga = '';
	$lastLigaWert = 0;
	$tage = array();
	while ($torj3 = mysql_fetch_assoc($torj2)) {
		if (abs(time()-$torj3['datum']) < 3600) { continue; } // kein Ergebnis vorwegnehmen
		if ($torj3['team1'] == $cookie_teamname) { $ergebnis = $torj3['ergebnis']; } else { $ergebnis = strrev($torj3['ergebnis']); }
		$toreTeams = explode(':', $ergebnis, 2);
		if ($toreTeams[0] > $toreTeams[1]) { $ergebnis = 3; }
		elseif ($toreTeams[0] == $toreTeams[1]) { $ergebnis = 1; }
		elseif ($toreTeams[0] < $toreTeams[1]) { $ergebnis = 0; }
		$wertemenge .= $ergebnis.',';
		if ($torj3['typ'] == 'Liga') {
			$onlyLiga .= $ergebnis.',';
			$lastLigaWert = $ergebnis;
		}
		else {
			$onlyLiga .= $lastLigaWert.','; // wenn Pokal oder so noch mal vorheriges Liga-Ergebnis nehmen
		}
		$tage[] = $torj3['datum'];
	}
	if (count($tage) > 0) {
		// LABELS ANFANG
		$p0 = min($tage);
		$p100 = max($tage);
		$p50 = round(($p0+$p100)/2);
		$p33 = round(($p0+$p50)/2);
		$p66 = round(($p50+$p100)/2);
		// LABELS ENDE
		$wertemenge = substr($wertemenge, 0, -1);
		$onlyLiga = substr($onlyLiga, 0, -1);
		echo '<img src="http://chart.apis.google.com/chart?cht=lc&amp;chco=76A4FB&amp;chls=2.0&amp;chs=450x450&amp;';
		echo 'chg=25,0,1,5&amp;chxt=x&amp;chxl=0:|'.date('d.m.', $p0).'|'.date('d.m.', $p33).'|'.date('d.m.', $p50).'|'.date('d.m.', $p66).'|'.date('d.m.', $p100).'&amp;'; // Labels
		echo 'chd=t:'.$onlyLiga.'&amp;chco=0000FF&amp;';
		echo 'chdl=Ligaspiele&amp;'; // Legende
		echo 'chds=0,3.1"';
		echo' alt="" />';
		echo '</p><p>';
		echo '<img src="http://chart.apis.google.com/chart?cht=lc&amp;chco=76A4FB&amp;chls=2.0&amp;chs=450x450&amp;';
		echo 'chg=25,0,1,5&amp;chxt=x&amp;chxl=0:|'.date('d.m.', $p0).'|'.date('d.m.', $p33).'|'.date('d.m.', $p50).'|'.date('d.m.', $p66).'|'.date('d.m.', $p100).'&amp;'; // Labels
		echo 'chd=t:'.$wertemenge.'&amp;chco=0000FF&amp;';
		echo 'chdl=Alle%20Spiele&amp;'; // Legende
		echo 'chds=0,3.1"';
		echo' alt="" />';	
	}
	else {
		echo _('Noch keine Daten verfügbar!');
	}
}
else {
	echo _('Noch keine Daten verfügbar!');
}
?>
</p>
<?php } else { ?>
<h1><?php echo _('Ergebnisverlauf'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
