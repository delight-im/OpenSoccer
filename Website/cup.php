<?php include 'zz1.php'; ?>
<title><?php echo _('Nationaler Cup'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Land wählen'); ?></h1>
<form action="" method="get" accept-charset="utf-8">
<p><select name="land" size="1" style="width:200px">
    <?php
	$sql1 = "SELECT land FROM ".$prefix."ligen WHERE ids = '".$cookie_liga."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) {
		$meinLand = 'Österreich';
	}
	else {
		$sql3 = mysql_fetch_assoc($sql2);
		$meinLand = mysql_real_escape_string($sql3['land']);
	}
    if (isset($_GET['land'])) {
    	$temp_land = mysql_real_escape_string(trim(strip_tags($_GET['land'])));
    }
    else {
    	$temp_land = $meinLand;
    }
    $shsj1 = "SELECT land FROM ".$prefix."ligen GROUP BY land ORDER BY land ASC";
    $shsj2 = mysql_query($shsj1);
    while ($shsj3 = mysql_fetch_assoc($shsj2)) {
        echo '<option value="'.$shsj3['land'].'"';
        if ($shsj3['land'] == $temp_land) { echo ' selected="selected"'; }
        echo '>'.$shsj3['land'].'</option>';
    }
    ?>
</select>
<input type="submit" value="Auswählen" /></p>
</form>
<h1><?php echo _('Nationaler Cup'); ?></h1>
<p>Für diesen Wettbewerb qualifizieren sich die 32 besten Teams aus <?php echo $temp_land; ?>, 16 davon durch ein Freilos. Von Liga 1 bis 4 - jeder hat die Chance auf den Cup-Sieg.</p>
<p><?php echo _('Es gibt eine Qualifikation und danach fünf Runden, die jeweils ausgelost werden. In einem KO-Spiel pro Runde ermitteln die Teams den Sieger, der dann die nächste Runde erreicht.'); ?></p>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/cup_ergebnisse'.urlencode($temp_land).'.html';
if (file_exists($tmp_dateiname)) {
	if (filemtime($tmp_dateiname) > getTimestamp('+2 hours')) {
		$tmp_liga_cache = file_get_contents($tmp_dateiname);
		if (strlen($tmp_liga_cache) > 0) {
			$ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
			echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
            $temp = TRUE;
            include 'zz3.php';
            exit;
		}
	}
}
if ($temp == FALSE) {
?>
<?php
$tmp_liga_cache = '';
?>
<?php
// TEAM-IDS HOLEN ANFANG
$getteam1 = "SELECT ids, name, liga FROM ".$prefix."teams WHERE cuprunde > 0";
$getteam2 = mysql_query($getteam1);
$team_ids = array();
$team2liga = array();
while ($getteam3 = mysql_fetch_assoc($getteam2)) {
	$team_ids[$getteam3['name']] = $getteam3['ids'];
	$team2liga[$getteam3['name']] = $getteam3['liga'];
}
// TEAM-IDS HOLEN ENDE
// LIGA-ID TO LIGA-NAME ANFANG
$getliga1 = "SELECT ids, name FROM ".$prefix."ligen WHERE land = '".$temp_land."'";
$getliga2 = mysql_query($getliga1);
$ligaID2ligaName = array();
while ($getliga3 = mysql_fetch_assoc($getliga2)) {
	$ligaID2ligaName[$getliga3['ids']] = $getliga3['name'];
}
// LIGA-ID TO LIGA-NAME ENDE
$spiele = array();
$spiele['Qualifikation'] = array();
$spiele['Vorrunde'] = array();
$spiele['Achtelfinale'] = array();
$spiele['Viertelfinale'] = array();
$spiele['Halbfinale'] = array();
$spiele['Finale'] = array();
$wrij1 = "SELECT id, team1, team2, ergebnis, liga, kennung, typ, datum FROM ".$prefix."spiele WHERE typ = 'Cup' AND land = '".$temp_land."' ORDER BY datum ASC";
$wrij2 = mysql_query($wrij1);
while ($wrij3 = mysql_fetch_assoc($wrij2)) {
	$rundenStr = explode('_', $wrij3['liga'], 2);
	if (isset($rundenStr[1])) { $rundenStr = $rundenStr[1]; } else { $rundenStr = ''; }
	switch ($rundenStr) {
		case 'Runde_1': $rundenarray = 'Qualifikation'; break;
		case 'Runde_2': $rundenarray = 'Vorrunde'; break;
		case 'Runde_3': $rundenarray = 'Achtelfinale'; break;
		case 'Runde_4': $rundenarray = 'Viertelfinale'; break;
		case 'Runde_5': $rundenarray = 'Halbfinale'; break;
		case 'Runde_6': $rundenarray = 'Finale'; break;
		default: $rundenarray = ''; break;
	}
	if ($rundenarray != '') {
		// LIVE ODER ERGEBNIS ANFANG
		if ($wrij3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $wrij3['datum'])) {
			$ergebnis_live = 'LIVE';
		}
		else {
			$ergebnis_live = $wrij3['ergebnis'];
		}
		// LIVE ODER ERGEBNIS ENDE
		$ergebnis_string = '<a href="/spielbericht.php?id='.$wrij3['id'].'">'.$ergebnis_live.'</a>';
		$spiele[$rundenarray][$wrij3['kennung']] = array('team1'=>$wrij3['team1'], 'team2'=>$wrij3['team2'], 'ergebnis'=>$ergebnis_string);
	}
}
?>
<?php
$spiele = array_reverse($spiele); // aktuellste Runde ganz oben
for ($i = 1; $i <= 6; $i++) {
	$spiels = each($spiele);
	$spielliste = $spiels['value'];
	if (count($spielliste) == 0) { continue; }
	$tmp_liga_cache .= '<h1>'.$spiels['key'].'</h1>';
	$tmp_liga_cache .= '<p><table><thead><tr class="odd"><th scope="col">'._('Team 1').'</th><th scope="col">'._('Team 2').'</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
	foreach ($spielliste as $spiel) {
		$tmp_liga_cache .= '<tr><td>['.substr((isset($ligaID2ligaName[$team2liga[$spiel['team1']]]) ? $ligaID2ligaName[$team2liga[$spiel['team1']]] : ' '), -1).'] <a href="/team.php?id='.$team_ids[$spiel['team1']].'">'.$spiel['team1'].'</a></td><td>['.substr((isset($ligaID2ligaName[$team2liga[$spiel['team2']]]) ? $ligaID2ligaName[$team2liga[$spiel['team2']]] : ' '), -1).'] <a href="/team.php?id='.$team_ids[$spiel['team2']].'">'.$spiel['team2'].'</a></td><td>'.$spiel['ergebnis'].'</td></tr>';
	}
	$tmp_liga_cache .= '</tbody></table></p>';
}
?>
<?php
$datei = fopen($tmp_dateiname, 'w+');
fwrite($datei, $tmp_liga_cache);
fclose($datei);
$ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
}
?>
<p><strong><?php echo _('Hinweis:'); ?></strong> <?php echo _('Die Zahlen in den eckigen Klammern geben die Liga des jeweiligen Teams an.'); ?></p>
<?php } else { ?>
<h1><?php echo _('Nationaler Cup'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
