<?php include 'zz1.php'; ?>
<title>Kalender | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php
if (isset($_GET['team'])) {
	$chosenTeamID = mysql_real_escape_string(trim(strip_tags($_GET['team'])));
	$sql1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$chosenTeamID."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) {
		$chosenTeam = $cookie_teamname;
	}
	else {
		$sql3 = mysql_fetch_assoc($sql2);
		$chosenTeam = $sql3['name'];
	}
}
else {
	$chosenTeamID = $cookie_team;
	$chosenTeam = $cookie_teamname;
}
?>
<h1>Saison <?php echo $cookie_saison; ?> - Spiele von <?php echo $chosenTeam; ?></h1>
<?php if ($loggedin == 1) { ?>
<?php
setTaskDone('team_calender');
// TYPEN-FILTER ANFANG
$filterSQL = "";
$filterTyp = '';
if (isset($_GET['typ'])) {
	$filterTyp = mysql_real_escape_string(trim(strip_tags($_GET['typ'])));
	if ($filterTyp == 'Cup' OR $filterTyp == 'Liga' OR $filterTyp == 'Pokal' OR $filterTyp == 'Test') {
		$filterSQL = " AND typ = '".$filterTyp."'";
	}
}
echo '<p style="text-align:right">';
$standardLink = '<a href="/kalender.php?team='.$chosenTeamID.'&amp;typ=';
echo $standardLink.'" class="pagenava'; if ($filterTyp == '') { echo ' aktiv'; } echo '">Alle</a> '.$standardLink.'Cup" class="pagenava'; if ($filterTyp == 'Cup') { echo ' aktiv'; } echo '">Cup</a> '.$standardLink.'Liga" class="pagenava'; if ($filterTyp == 'Liga') { echo ' aktiv'; } echo '">Liga</a> '.$standardLink.'Pokal" class="pagenava'; if ($filterTyp == 'Pokal') { echo ' aktiv'; } echo '">Pokal</a> '.$standardLink.'Test" class="pagenava'; if ($filterTyp == 'Test') { echo ' aktiv'; } echo '">Test</a>';
echo ' <a href="/team.php?id='.$chosenTeamID.'" class="pagenava">Zum Teamprofil</a>';
echo '</p>';
// TYPEN FILTER ENDE
?>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">Datum</th>
<th scope="col">Anstoß</th>
<th scope="col">Gegner</th>
<th scope="col">Ergebnis</th>
</tr>
</thead>
<tbody>
<?php
$spieltagDescription = array(	4 => array('Pokal: Vorrunde (Hinspiel)', 'Cup: Qualifikation'),
								5 => array('Pokal: Vorrunde (Rückspiel)'),
								7 => array('Cup: Vorrunde'),
								8 => array('Pokal: Achtelfinale (Hinspiel)'),
								9 => array('Pokal: Achtelfinale (Rückspiel)'),
								10 => array('Cup: Achtelfinale'),
								12 => array('Pokal: Viertelfinale (Hinspiel)'),
								13 => array('Pokal: Viertelfinale (Rückspiel)', 'Cup: Viertelfinale'),
								16 => array('Pokal: Halbfinale (Hinspiel)', 'Cup: Halbfinale'),
								17 => array('Pokal: Halbfinale (Rückspiel)'),
								19 => array('Cup: Finale'),
								20 => array('Pokal: Finale')
);
$sql1 = "SELECT id, datum, team1, team2, ergebnis, typ FROM ".$prefix."spiele WHERE (team1 = '".$chosenTeam."' OR team2 = '".$chosenTeam."')".$filterSQL." ORDER BY datum ASC";
$sql2 = mysql_query($sql1);
$counter = 1;
$lastDate = '';
$currentSpieltag = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 1) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	$currentDate = date('d.m.Y', getTimestamp('-1 hour', $sql3['datum']));
	if ($currentDate == $lastDate) { // nächstes Spiel an gleichem Tag
		echo '<td>&nbsp;</td>';
	}
	else { // neuer Tag
		$currentSpieltag = $cookie_spieltag-round((time()-$sql3['datum'])/86400);
		echo '<td style="font-weight:bold;">'.$currentDate.'</td>';
		echo '<td colspan="3" style="font-weight:bold;">Spieltag '.$currentSpieltag.'</td></tr><tr><td>&nbsp;</td>'; // Zeile mit Spieltag einschieben
		$counter++; // Zeilen-Counter erhöhen
		if (isset($spieltagDescription[$currentSpieltag]) && is_array($spieltagDescription[$currentSpieltag])) { // Zeile mit Spieltags-Beschreibung einschieben
            foreach ($spieltagDescription[$currentSpieltag] as $special_date) {
                echo '<td colspan="3" style="font-weight:bold;">'.$special_date.'</td></tr><tr><td>&nbsp;</td>';
                $counter++; // Zeilen-Counter erhöhen
            }
		}
	}
	echo '<td>'.date('H:i', getTimestamp('-1 hour', $sql3['datum'])).' Uhr</td><td>'.substr($sql3['typ'], 0, 1).': ';
	if ($sql3['team1'] != $chosenTeam) { $ergebnis = ergebnis_drehen($sql3['ergebnis']); $gegner = $sql3['team1']; $zusatz = ' (A)'; } else { $ergebnis = $sql3['ergebnis']; $gegner = $sql3['team2']; $zusatz = ' (H)'; }
	$sql4 = "SELECT ids FROM ".$prefix."teams WHERE name = '".$gegner."'";
	$sql5 = mysql_query($sql4);
	$sql6 = mysql_fetch_assoc($sql5);
	$sql6 = $sql6['ids'];
    // LIVE ODER ERGEBNIS ANFANG
    if ($sql3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $sql3['datum'])) {
        $ergebnis_live = 'LIVE';
    }
    else {
        $ergebnis_live = $ergebnis;
    }
    // LIVE ODER ERGEBNIS ENDE
	echo '<a href="/team.php?id='.$sql6.'">'.$gegner.'</a></td><td class="link"><a href="/spielbericht.php?id='.$sql3['id'].'">'.$ergebnis_live.' '.$zusatz.'</a></td>';
	echo '</tr>';
	$lastDate = $currentDate;
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>