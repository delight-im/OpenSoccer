<?php include 'zz1.php'; ?>
<title><?php echo _('Treueste Spieler'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.team_<?php echo $cookie_team; ?> {
	font-weight: bold;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<h1><?php echo _('Treueste Spieler'); ?></h1>
<?php if ($loggedin == 1) { ?>
<?php
// NUR EIGENE SPIELER ODER ALLE ANFANG
$onlyShowOwnSQL = "";
$onlyShowOwnLink = '<a href="/stat_treuesteSpieler.php?own=1" class="pagenava">'._('Meine Spieler').'</a>';
$onlyShowOwnCache = '';
if (isset($_GET['own'])) {
	if ($_GET['own'] == '1') {
		$onlyShowOwnSQL = " WHERE a.team = '".$cookie_team."'";
		$onlyShowOwnLink = '<a href="/stat_treuesteSpieler.php?own=0" class="pagenava">'._('Alle Spieler').'</a>';
		$onlyShowOwnCache = $cookie_team;
	}
}
echo '<p style="text-align:right">'.$onlyShowOwnLink.'</p>';
// NUR EIGENE SPIELER ODER ALLE ENDE
?>
<p><?php echo _('Welche Spieler haben über mehrere Saisons die meisten Einsätze für ihren Verein absolviert? Welche Spieler sind die treuesten?'); ?></p>
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
$tmp_dateiname = 'cache/stat_treuesteSpieler'.$onlyShowOwnCache.'.html';
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
    $torj1 = "SELECT a.ids, a.vorname, a.nachname, a.spiele_verein, a.team, b.name FROM ".$prefix."spieler AS a JOIN ".$prefix."teams AS b ON a.team = b.ids".$onlyShowOwnSQL." ORDER BY a.spiele_verein DESC LIMIT 0, 25";
    $torj2 = mysql_query($torj1);
    $counter = 1;
    while ($torj3 = mysql_fetch_assoc($torj2)) {
        if ($counter % 2 == 1) {
			$tmp_liga_cache .= '<tr class="team_'.$torj3['team'].'">';
		}
		else {
			$tmp_liga_cache .= '<tr class="odd team_'.$torj3['team'].'">';
		}
        $tmp_liga_cache .= '<td>'.$counter.'</td><td class="link"><a href="/spieler.php?id='.$torj3['ids'].'">'.$torj3['vorname'].' '.$torj3['nachname'].'</a></td><td class="link"><a href="/team.php?id='.$torj3['team'].'">'.$torj3['name'].'</a></td><td>'.$torj3['spiele_verein'].'</td>';
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
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
