<?php include 'zz1.php'; ?>
<title><?php echo _('Protokoll'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
$showTeam = mysql_real_escape_string(trim(strip_tags($cookie_team)));
$showTeamName = mysql_real_escape_string(trim(strip_tags($cookie_teamname)));
if ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin') {
	if (isset($_GET['team'])) {
		$showTeam = mysql_real_escape_string(trim(strip_tags($_GET['team'])));
		$showTeamName1 = "SELECT name, liga FROM ".$prefix."teams WHERE ids = '".$showTeam."'";
		$showTeamName2 = mysql_query($showTeamName1);
		if (mysql_num_rows($showTeamName2) == 0) { exit; }
		$showTeamName3 = mysql_fetch_assoc($showTeamName2);
		$showTeamName = mysql_real_escape_string(trim(strip_tags($showTeamName3['name'])));
	}
}

if ($showTeam == $cookie_team) { // if the event log for one's own club is to be shown
	echo '<h1>'._('Protokoll').'</h1>';
}
else { // if the event log for another user's club is to be shown by the support staff
	echo '<h1>Protokoll für '.htmlspecialchars($showTeamName).'</h1>';
}

setTaskDone('check_logs');

$filterTypes = array('Spieler', 'Finanzen', 'Termine', 'Transfers', 'Verletzung', 'Stadion', 'Assistenten');
$filterSQL = "";
$filterGET = '';
if (isset($_GET['filter'])) {
	if (in_array($_GET['filter'], $filterTypes)) {
		$filterGET = mysql_real_escape_string($_GET['filter']);
		$filterSQL = " AND typ = '".$filterGET."'";
	}
}
?>
<form action="/protokoll.php" method="get" accept-charset="utf-8">
<p><select name="filter" size="1" style="width:200px">
	<option value="">&nbsp;-&nbsp;</option>
	<?php
	foreach ($filterTypes as $filterType) {
		echo '<option';
		if ($filterType == $filterGET) { echo ' selected="selected"'; }
		echo '>'.$filterType.'</option>';
	}
	?>
</select> <input type="submit" value="<?php echo _('Filtern'); ?>" /></p>
</form>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Ereignis'); ?></th>
</tr>
</thead>
<tbody>
<?php
$delOld_Timeout = getTimestamp('-28 days');
$delOld1 = "DELETE FROM ".$prefix."protokoll WHERE team = '".$showTeam."' AND zeit < ".$delOld_Timeout;
$delOld2 = mysql_query($delOld1);
$sql1 = "SELECT text, typ, zeit FROM ".$prefix."protokoll WHERE team = '".$showTeam."'".$filterSQL." ORDER BY zeit DESC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td><img src="/images/protokoll/'.$sql3['typ'].'.png" alt="'.$sql3['typ'].'" title="'.$sql3['typ'].'" /></td>';
	echo '<td><span title="'.date('d.m.Y H:i', $sql3['zeit']).' Uhr">'.date('d.m.Y', $sql3['zeit']).'</span></td><td>'.$sql3['text'].'</a></td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<p><strong><?php echo _('Hinweis:').'</strong> '._('Die Ereignisse sind nach Datum geordnet, d.h. die neueste Meldung steht ganz oben.'); ?></p>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?filter='.$filterGET.'&amp;team='.htmlspecialchars($showTeam).'&amp;seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
echo '</div>';
?>
<?php } else { ?>
<h1><?php echo _('Protokoll'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
