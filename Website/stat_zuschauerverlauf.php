<?php include 'zz1.php'; ?>
<title><?php echo _('Zuschauerverlauf'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php if ($cookie_team != '__'.$cookie_id) { ?>
<h1><?php echo _('Zuschauerverlauf'); ?></h1>
<p><?php echo _('Dieses Liniendiagramm zeigt die Entwicklung Deiner Zuschauerzahlen in der aktuellen Saison. Hinweis: Die Daten können erst nach dem zweiten Heimspiel angezeigt werden.'); ?></p>
<p>
<?php
$torj1 = "SELECT zuschauer, datum FROM ".$prefix."spiele WHERE team1 = '".$cookie_teamname."' AND simuliert = 1 ORDER BY datum ASC";
$torj2 = mysql_query($torj1);
if (mysql_num_rows($torj2) > 0) {
    $wertemenge = '';
    $wertearray = array();
	$tage = array();
    while ($torj3 = mysql_fetch_assoc($torj2)) {
        $wertemenge .= $torj3['zuschauer'].',';
        $wertearray[] = $torj3['zuschauer'];
		$tage[] = $torj3['datum'];
    }
	// LABELS ANFANG
	$p0 = min($tage);
	$p100 = max($tage);
	$p50 = round(($p0+$p100)/2);
	$p33 = round(($p0+$p50)/2);
	$p66 = round(($p50+$p100)/2);
	// LABELS ENDE
    $wertemenge = substr($wertemenge, 0, -1);
    echo '<img src="http://chart.apis.google.com/chart?cht=lc&amp;chco=76A4FB&amp;chls=2.0&amp;chs=450x450&amp;';
	echo 'chg=25,0,1,5&amp;chxt=x&amp;chxl=0:|'.date('d.m.', $p0).'|'.date('d.m.', $p33).'|'.date('d.m.', $p50).'|'.date('d.m.', $p66).'|'.date('d.m.', $p100).'&amp;'; // Labels
    echo 'chd=t:'.$wertemenge.'&amp;';
    echo 'chds='.min($wertearray).','.max($wertearray).'"';
    echo' alt="" />';
}
else {
	echo _('Noch keine Daten verfügbar!')';
}
?>
</p>
<?php } ?>
<?php } else { ?>
<h1><?php echo _('Zuschauerverlauf'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
