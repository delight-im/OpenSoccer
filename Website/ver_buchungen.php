<?php include 'zz1.php'; ?>
<title><?php echo _('Buchungen'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Buchungen'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><?php echo _('In dieser Tabelle sind alle Buchungen aufgelistet, die in der aktuellen Saison durchgeführt wurden.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Buchungstag'); ?></th>
<th scope="col"><?php echo _('Verwendungszweck'); ?></th>
<th scope="col"><?php echo _('Betrag'); ?></th>
</tr>
</thead>
<tbody>
<?php
setTaskDone('finance_transactions');
$delOld_Timeout = getTimestamp('-28 days');
$delOld1 = "DELETE FROM ".$prefix."buchungen WHERE team = '".$cookie_team."' AND zeit < ".$delOld_Timeout;
$delOld2 = mysql_query($delOld1);
$sql1 = "SELECT verwendungszweck, betrag, zeit FROM ".$prefix."buchungen WHERE team = '".$cookie_team."' ORDER BY zeit DESC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	echo '<tr';
	if ($counter % 2 == 0) { echo ' class="odd"'; }
	/*// SPONSOREN-GELDER ERST AM NAECHSTEN TAG ANFANG
	if ($sql3['verwendungszweck'] == 'Sponsoring') {
		if (date('d', $sql3['zeit']) == date('d', time())) {
			continue;
		}
	}
	// SPONSOREN-GELDER ERST AM NAECHSTEN TAG ENDE*/
	echo '><td>'.date('d.m.Y', $sql3['zeit']).'</td><td>'.$sql3['verwendungszweck'].'</td><td style="text-align:right">'.number_format($sql3['betrag'], 2, ',', '.').' €</td></tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
echo '</div>';
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
