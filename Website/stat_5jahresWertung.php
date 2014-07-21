<?php include 'zz1.php'; ?>
<title><?php echo _('5-Jahres-Wertung'); ?> | Ballmanager.de</title>
<style type="text/css">
<!--
.tabelle_3startplaetze td, .tabelle_3startplaetze a {
	background: #79df39;
	color: #000;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<h1><?php echo _('5-Jahres-Wertung'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><?php echo _('Auf dieser Seite sind alle ersten Ligen aufgelistet, sortiert nach ihrem Erfolg im Pokal. Dabei zählen die Punkte, die die Teams in den letzten 5 Saisons geholt haben.'); ?></p>
<p><?php echo _('Für einen Sieg im Pokal bekommt jedes Land 2 Punkte, für ein Unentschieden 1 Punkt. Die Punktzahl wird am Ende durch die Anzahl der Teams für das Land geteilt.'); ?></p>
<p><?php echo _('Auf Grundlage dieser 5-Jahres-Wertung werden die Startplätze im Pokal vergeben: Die ersten 6 Länder bekommen 3 Startplätze, die restlichen Länder 2.'); ?></p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Liga'); ?></th>
<th scope="col"><?php echo _('GES'); ?></th>
<th scope="col"><?php echo GameTime::getSeason(); ?></th>
<th scope="col"><?php echo intval(GameTime::getSeason()-1); ?></th>
<th scope="col"><?php echo intval(GameTime::getSeason()-2); ?></th>
<th scope="col"><?php echo intval(GameTime::getSeason()-3); ?></th>
<th scope="col"><?php echo intval(GameTime::getSeason()-4); ?></th>
</tr>
</thead>
<tbody>
<?php
setTaskDone('check_5yearsranking');
$sql1 = "SELECT ids, name, pkt_saison1, pkt_saison2, pkt_saison3, pkt_saison4, pkt_saison5, pkt_gesamt FROM man_ligen WHERE hoch = 'KEINE' ORDER BY pkt_gesamt DESC, pkt_saison1 DESC, pkt_saison2 DESC, pkt_saison3 DESC, pkt_saison4 DESC, pkt_saison5 DESC LIMIT 0, 13";
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter <= 6) { echo '<tr class="tabelle_3startplaetze">'; }
	elseif ($counter % 2 == 1) { echo '<tr>'; }
	else { echo '<tr class="odd">'; }
	echo '<td>'.$counter.'.</td><td class="link"><a href="/lig_tabelle.php?liga='.$sql3['ids'].'">'.substr($sql3['name'], 0, -2).'</a></td>';
	if ($live_scoring_spieltyp_laeuft == 'Pokal') {
		echo '<td>?</td><td>?</td>';
	}
	else {
		echo '<td>'.number_format($sql3['pkt_gesamt'], 1, ',', '.').'</td><td>'.number_format($sql3['pkt_saison1'], 1, ',', '.').'</td>';
	}
	echo '<td>'.number_format($sql3['pkt_saison2'], 1, ',', '.').'</td>';
	echo '<td>'.number_format($sql3['pkt_saison3'], 1, ',', '.').'</td>';
	echo '<td>'.number_format($sql3['pkt_saison4'], 1, ',', '.').'</td>';
	echo '<td>'.number_format($sql3['pkt_saison5'], 1, ',', '.').'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<p><strong><?php echo _('Erklärungen:').'</strong> '._('Die erste Spalte gibt die Platzierung an, die zweite die Liga, die dritte (&quot;GES&quot;) die Gesamtpunktzahl der letzten 5 Saisons und die folgenden fünf Spalten die einzelnen Punktzahlen für die letzten Saisons.'); ?></p>
<p style="font-size:80%; color:#666;"><?php echo _('Bei Punktgleichheit (GES) zwischen zwei Ländern entscheidet das neueste Saisonergebnis, in dem es eine Differenz gibt.'); ?></p>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
