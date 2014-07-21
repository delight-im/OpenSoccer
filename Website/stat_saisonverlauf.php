<?php include 'zz1.php'; ?>
<title><?php echo _('Saisonverlauf'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Saisonverlauf'); ?></h1>
<p><?php echo _('Dieses Liniendiagramm zeigt die Entwicklung Deines Tabellenplatzes in der aktuellen Saison. Hinweis: Die Daten können erst nach zwei Spielen angezeigt werden.'); ?></p>
<p>
<?php
$torj1 = "SELECT spieltag, platz FROM ".$prefix."geschichte_tabellen WHERE liga = '".$cookie_liga."' AND saison = ".GameTime::getSeason()." AND team = '".$cookie_teamname."' ORDER BY spieltag ASC";
$torj2 = mysql_query($torj1);
if (mysql_num_rows($torj2) >= 2) {
	$wertemenge = '';
	$tage = array();
	while ($torj3 = mysql_fetch_assoc($torj2)) {
		$wertemenge .= intval(12-$torj3['platz']).',';
		$tage[] = $torj3['spieltag'];
	}
	if (count($tage) >= 2) {
		// LABELS ANFANG
		$p0 = min($tage);
		$p100 = max($tage);
		$p50 = round(($p0+$p100)/2);
		$p33 = round(($p0+$p50)/2);
		$p66 = round(($p50+$p100)/2);
		// LABELS ENDE
		$wertemenge = substr($wertemenge, 0, -1);
		echo '<img src="http://chart.apis.google.com/chart?cht=lc&amp;chco=76A4FB&amp;chls=2.0&amp;chs=450x450&amp;';
		echo 'chg=25,0,1,5&amp;chxt=x,y&amp;chxl=0:|'.$p0.'|'.$p33.'|'.$p50.'|'.$p66.'|'.$p100.'|1:|12|11|10|9|8|7|6|5|4|3|2|1&amp;'; // Labels
		echo 'chd=t:'.$wertemenge.'&amp;chco=0000FF&amp;';
		echo 'chdl=Tabellenplatz-Entwicklung&amp;'; // Legende
		echo 'chds=0,11&amp;chxr=1,1,12,1"';
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
<h1><?php echo _('Saisonverlauf'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
