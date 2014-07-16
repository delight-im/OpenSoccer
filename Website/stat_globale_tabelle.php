<?php include 'zz1.php'; ?>
<title><?php echo _('Globale Tabelle'); ?> | Ballmanager.de</title>
<style type="text/css">
<!--
.team_<?php echo $cookie_team; ?> {
	font-weight: bold;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if ($live_scoring_spieltyp_laeuft != '') {
	echo '<h1>'.('Globale Tabelle').'</h1>';
	echo '<p>'.('Zurzeit laufen %s spiele. Deshalb kannst Du leider die globale Tabelle nicht ansehen. Bitte warte, bis die Spiele beendet sind.', $live_scoring_spieltyp_laeuft).'</p>';
	include 'zz3.php';
	exit;
}
?>
<h1><?php echo _('Globale Tabelle'); ?></h1>
<p><?php echo _('In dieser Tabelle sind die erfolgreichsten Teams der aktuellen Saison aufgelistet, nach ihren Punkten und Toren aus der Liga sortiert. Dabei zählen alle Siege gleich viel - und Drittligisten können schon einmal vor Erstligisten stehen.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('S-U-N'); ?></th>
<th scope="col"><?php echo _('TO'); ?></th>
<th scope="col"><?php echo _('DI'); ?></th>
<th scope="col"><?php echo _('PT'); ?></th>
</tr>
</thead>
<tbody>
<?php
$torj1 = "SELECT ids, name, punkte, tore, gegentore, (tore-gegentore) AS differenz, sunS, sunU, sunN FROM ".$prefix."teams ORDER BY punkte DESC, differenz DESC, tore DESC LIMIT 0, 25";
$torj2 = mysql_query($torj1);
$counter = 1;
while ($torj3 = mysql_fetch_assoc($torj2)) {
	if ($counter % 2 == 1) { echo '<tr class="team_'.$torj3['ids'].'">'; } else { echo '<tr class="team_'.$torj3['ids'].' odd">'; }
	echo '<td>'.$counter.'</td>';
	echo '<td class="link"><a href="/team.php?id='.$torj3['ids'].'">'.$torj3['name'].'</a></td>';
	echo '<td>'.$torj3['sunS'].'-'.$torj3['sunU'].'-'.$torj3['sunN'].'</td>';
	echo '<td>'.$torj3['tore'].':'.$torj3['gegentore'].'</td>';
	echo '<td>'.$torj3['differenz'].'</td>';
	echo '<td>'.$torj3['punkte'].'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<p><?php echo _('<strong>Überschriften:</strong> S-U-N: Siege/Unentschieden/Niederlagen, TO: Tore, DI: Differenz, PT: Punkte'); ?></p>
</p>
<?php } else { ?>
<h1><?php echo _('Globale Tabelle'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
