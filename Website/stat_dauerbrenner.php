<?php include 'zz1.php'; ?>
<title><?php echo _('Dauerbrenner'); ?> | Ballmanager.de</title>
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
<h1><?php echo _('Dauerbrenner'); ?></h1>
<p><?php echo _('Welche Spieler hatten die meisten Einsätze in dieser Saison? Wer ist am häufigsten für sein Team aufgelaufen?'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Spieler'); ?></th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Einsätze'); ?></th>
</tr>
</thead>
<tbody>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/stat_dauerbrenner.html';
if (file_exists($tmp_dateiname)) {
	if (filemtime($tmp_dateiname) > getTimestamp('-1 day')) {
		$tmp_liga_cache = file_get_contents($tmp_dateiname);
		if (strlen($tmp_liga_cache) > 0) {
			echo $tmp_liga_cache;
            $temp = TRUE;
		}
	}
}
if ($temp == FALSE) {
$tmp_liga_cache = '';
    $torj1 = "SELECT a.ids, a.vorname, a.nachname, a.spiele, a.team, b.name FROM ".$prefix."spieler AS a JOIN ".$prefix."teams AS b ON a.team = b.ids ORDER BY a.spiele DESC LIMIT 0, 25";
    $torj2 = mysql_query($torj1);
    $counter = 1;
    while ($torj3 = mysql_fetch_assoc($torj2)) {
        if ($counter % 2 == 1) {
			$tmp_liga_cache .= '<tr class="team_'.$torj3['team'].'">';
		}
		else {
			$tmp_liga_cache .= '<tr class="odd team_'.$torj3['team'].'">';
		}
        $tmp_liga_cache .= '<td>'.$counter.'</td><td class="link"><a href="/spieler.php?id='.$torj3['ids'].'">'.$torj3['vorname'].' '.$torj3['nachname'].'</a></td><td class="link"><a href="/team.php?id='.$torj3['team'].'">'.$torj3['name'].'</a></td><td>'.$torj3['spiele'].'</td>';
        $tmp_liga_cache .= '</tr>';
        $counter++;
    }
    $datei = fopen($tmp_dateiname, 'w+');
    fwrite($datei, $tmp_liga_cache);
    fclose($datei);
    echo $tmp_liga_cache;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Meiste Zuschauer'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
