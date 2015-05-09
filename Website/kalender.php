<?php include 'zz1.php'; ?>
<title><?php echo _('Kalender'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php
if (isset($_GET['team'])) {
	$chosenTeamID = mysql_real_escape_string(trim(strip_tags($_GET['team'])));
	$sql1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$chosenTeamID."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) {
		unset($_GET['team']);
        $chosenTeam = '';
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
<h1><?php echo __('Saison %d', GameTime::getSeason()); ?> - <?php echo __('Spiele von %s', $chosenTeam); ?></h1>
<?php if ($loggedin == 1 || isset($_GET['team'])) { ?>
<?php
if ($loggedin == 1) {
    setTaskDone('team_calender');
}
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
echo $standardLink.'" class="pagenava'; if ($filterTyp == '') { echo ' aktiv'; } echo '">'._('Alle').'</a> '.$standardLink.'Cup" class="pagenava'; if ($filterTyp == 'Cup') { echo ' aktiv'; } echo '">'._('Cup').'</a> '.$standardLink.'Liga" class="pagenava'; if ($filterTyp == 'Liga') { echo ' aktiv'; } echo '">'._('Liga').'</a> '.$standardLink.'Pokal" class="pagenava'; if ($filterTyp == 'Pokal') { echo ' aktiv'; } echo '">'._('Pokal').'</a> '.$standardLink.'Test" class="pagenava'; if ($filterTyp == 'Test') { echo ' aktiv'; } echo '">'._('Test').'</a>';
echo ' <a href="/team.php?id='.$chosenTeamID.'" class="pagenava">'._('Zum Teamprofil').'</a>';
echo '</p>';
// TYPEN FILTER ENDE
?>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Anstoß'); ?></th>
<th scope="col"><?php echo _('Gegner'); ?></th>
<th scope="col"><?php echo _('Ergebnis'); ?></th>
</tr>
</thead>
<tbody>
<?php
$spieltagDescription = array(	4 => array(_('Pokal: Vorrunde (Hinspiel)'), _('Cup: Qualifikation')),
								5 => array(_('Pokal: Vorrunde (Rückspiel)')),
								7 => array(_('Cup: Vorrunde')),
								8 => array(_('Pokal: Achtelfinale (Hinspiel)')),
								9 => array(_('Pokal: Achtelfinale (Rückspiel)')),
								10 => array(_('Cup: Achtelfinale')),
								12 => array(_('Pokal: Viertelfinale (Hinspiel)')),
								13 => array(_('Pokal: Viertelfinale (Rückspiel)'), _('Cup: Viertelfinale')),
								16 => array(_('Pokal: Halbfinale (Hinspiel)'), _('Cup: Halbfinale')),
								17 => array(_('Pokal: Halbfinale (Rückspiel)')),
								19 => array(_('Cup: Finale')),
								20 => array(_('Pokal: Finale'))
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
		$currentSpieltag = GameTime::getMatchDay()-round((time()-$sql3['datum'])/86400);
		echo '<td style="font-weight:bold;">'.$currentDate.'</td>';
		echo '<td colspan="3" style="font-weight:bold;">'.__('Spieltag %d', $currentSpieltag).'</td></tr><tr><td>&nbsp;</td>'; // Zeile mit Spieltag einschieben
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
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
