<?php include 'zz1.php'; ?>
<title><?php echo _('Verträge'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Verträge'); ?></h1>
<?php if ($loggedin == 1) { ?>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Spieler'); ?></th>
<th scope="col"><?php echo _('AL'); ?></th>
<th scope="col"><?php echo _('MO'); ?></th>
<th scope="col"><?php echo _('Stärke'); ?></th>
<th scope="col"><?php echo _('GE'); ?></th>
<th scope="col"><?php echo _('Vertrag'); ?></th>
<th scope="col">&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
setTaskDone('overview_contracts');
function moralToGraphic($intValue) {
	$outputStr = '<span';
	if ($intValue > 75) {
		$outputStr .= ' style="color:green"';
	}
	elseif ($intValue > 60) {
		$outputStr .= '';
	}
	else {
		$outputStr .= ' style="color:red"';
	}
	$outputStr .= '>'.round($intValue).'%</span>';
	return $outputStr;
}
// SPIELER-MARKIERUNGEN ANFANG
$gf1 = "SELECT spieler, farbe FROM ".$prefix."spieler_mark WHERE team = '".$cookie_team."'";
$gf2 = mysql_query($gf1);
$markierungen = array();
while ($gf3 = mysql_fetch_assoc($gf2)) {
	$markierungen[$gf3['spieler']] = $gf3['farbe'];
}
// SPIELER-MARKIERUNGEN ENDE
$sql1 = "SELECT ids, vorname, nachname, wiealt, marktwert, gehalt, vertrag, leiher, moral, staerke, talent FROM ".$prefix."spieler WHERE team = '".$cookie_team."' ORDER BY vertrag ASC";
$sql2 = mysql_query($sql1);
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	// FARBE ANFANG
	$farbcode = '';
	if (isset($markierungen[$sql3['ids']])) {
		switch ($markierungen[$sql3['ids']]) {
			case 'Blau': $farbcode = ' style="background-color:#00f"'; break;
			case 'Gelb': $farbcode = ' style="background-color:#ff0"'; break;
			case 'Rot': $farbcode = ' style="background-color:#f00"'; break;
			case 'Gruen': $farbcode = ' style="background-color:#0f0"'; break;
			case 'Pink': $farbcode = ' style="background-color:#f0f"'; break;
			case 'Aqua': $farbcode = ' style="background-color:#0ff"'; break;
			case 'Silber': $farbcode = ' style="background-color:#c0c0c0"'; break;
			case 'Lila': $farbcode = ' style="background-color:#800080"'; break;
			case 'Oliv': $farbcode = ' style="background-color:#808000"'; break;
			default: $farbcode = ''; break;
		}
	}
	// FARBE ENDE
	if ($counter % 2 == 0) { echo '<tr>'; }	else { echo '<tr class="odd">'; }
	echo '<td'.$farbcode.'>&nbsp;</td>';
	echo '<td class="link"><a href="/spieler.php?id='.$sql3['ids'].'">'.mb_substr($sql3['vorname'], 0, 1, 'utf-8').'. '.$sql3['nachname'].'</a></td>';
	echo '<td>'.floor($sql3['wiealt']/365).'</td>';
	echo '<td>'.moralToGraphic($sql3['moral']).'</td>';
	$schaetzungVomScout = schaetzungVomScout($cookie_team, $cookie_scout, $sql3['ids'], $sql3['talent'], $sql3['staerke'], $cookie_team);
	echo '<td>'.number_format($sql3['staerke'], 1, ',', '.').' <span style="color:#999">('.number_format($schaetzungVomScout, 1, ',', '.').')</span></td>';
	echo '<td>'.number_format($sql3['gehalt']/1000000, 2, ',', '.').'</td>';
	echo '<td>'.floor(($sql3['vertrag']-time())/86400).' Tage</td>';
	if ($sql3['marktwert'] > 0 && $sql3['leiher'] == 'keiner') {
		echo '<td class="link"><a href="/vertrag_verlaengern.php?id='.$sql3['ids'].'">'._('Verlängern').'</a></td>';
	}
	else {
		echo '<td>&nbsp;</td>';
	}
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<p><strong><?php echo _('Überschriften:').'</strong> '._('AL: Alter, MO: Moral, GE: Gehalt pro Saison in Millionen €'); ?></p>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
