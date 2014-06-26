<?php include 'zz1.php'; ?>
<title><?php echo _('Neue Accounts'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
	$sql1 = "SELECT a.ids, a.username, a.regdate, a.last_login, a.welcomedByStaff, b.werber, c.username AS werberName FROM ".$prefix."users AS a LEFT JOIN ".$prefix."referrals AS b ON a.ids = b.geworben LEFT JOIN ".$prefix."users AS c ON b.werber = c.ids ORDER BY a.regdate DESC LIMIT ".$start.", ".$eintraege_pro_seite;
	$sql2 = mysql_query($sql1);
	$blaetter3 = anzahl_datensaetze_gesamt($sql1);
	echo '<h1>'._('Neue Accounts').'</h1>';
	echo '<p>'._('Die folgenden Accounts wurden zuletzt neu registriert. Nicht Manager haben schon ein Team bekommen.').'</p>';
	echo '<table><thead><tr class="odd"><th scope="col">'._('Manager').'</th><th scope="col">'._('Registriert').'</th><th scope="col">'._('Geworben von').'</th><th scope="col">'._('Letzter Login').'</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
	$counter = 0;
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
		$username = displayUsername($sql3['username'], $sql3['ids']);
		echo '<td';
		if ($username != 'Gelöschter User') {
			echo ' class="link"';
		}
		echo '>'.$username.'</td><td>'.date('d.m.Y', $sql3['regdate']).'</td>';
		if (is_null($sql3['werberName']) || is_null($sql3['werber'])) {
			echo '<td>&nbsp;-&nbsp;';
		}
		else {
			echo '<td class="link">'.displayUsername($sql3['werberName'], $sql3['werber']);
		}
		echo '</td><td>';
		if ($sql3['regdate'] > $sql3['last_login']) {
			echo '&nbsp;-&nbsp;';
		}
		else {
			echo date('d.m.Y H:i', $sql3['last_login']);
		}
		echo '</td>';
		if ($sql3['welcomedByStaff'] == 1) {
			echo '<td><img src="/images/erfolg.png" alt="+" title="'._('Dieser Manager wurde bereits angeschrieben').'" /></td>';
		}
		else {
			echo '<td><img src="/images/fehler.png" alt="-" title="'._('Dieser Manager wurde bisher noch nicht angeschrieben').'" /></td>';
		}
		echo '</tr>';
		$counter++;
	}
	echo '</tbody></table>';
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
}
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
