<?php include 'zz1.php'; ?>
<title>Transferliste | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Transferliste</h1>
<?php if ($loggedin == 1) { ?>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">Spieler</th>
<th scope="col">Gebote</th>
<th scope="col">Höchstgebot</th>
<th scope="col">Ende</th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT a.ids, a.vorname, a.nachname, a.position, a.wiealt, b.bieter_highest, b.betrag_highest, b.gebote, b.ende FROM ".$prefix."spieler AS a JOIN ".$prefix."transfermarkt AS b ON a.ids = b.spieler WHERE a.team = '".$cookie_team."' ORDER BY b.ende ASC";
$sql2 = mysql_query($sql1);
$counter = 0;
if (mysql_num_rows($sql2) == 0) {
	echo '<tr><td colspan="4">Du bietest im Moment keinen Spieler zum Verkauf an.</td></tr>';
}
else {
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
		echo '<td class="link"><a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a></td>';
		echo '<td>'.$sql3['gebote'].'</td>';
		echo '<td class="link"><a href="/transfermarkt_auktion.php?id='.$sql3['ids'].'">'.number_format($sql3['betrag_highest'], 0, ',', '.').' €</a></td>';
		$noch_zeit = intval(($sql3['ende']-time())/60);
		echo '<td><span title="'.date('d.m.Y H:i', $sql3['ende']).'">'.$noch_zeit.'min</span></td>';
		//echo '<td>'.number_format($sql3['marktwert']/1000000, 3, ',', '.').'</td>';
		echo '</tr>';
		$counter++;
	}
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>