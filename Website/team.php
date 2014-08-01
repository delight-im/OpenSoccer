<?php include 'zz1.php'; ?>
<?php
if (!isset($_GET['id'])) { exit; }
function kontoToWort($konto) {
	if ($konto < -30000000) { return _('sehr schlecht'); }
	elseif ($konto < 0) { return _('schlecht'); }
	elseif ($konto < 30000000) { return _('solide'); }
	elseif ($konto < 60000000) { return _('gut'); }
	elseif ($konto < 90000000) { return _('sehr gut'); }
	else { return _('hervorragend'); }
}
$clearid = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
$sql1 = "SELECT name, rank, konto, staerke, pokalrunde, vorjahr_pokalrunde, cuprunde, vorjahr_cuprunde, vorjahr_platz, aufstellung, liga, vorjahr_elo, elo, meisterschaften, pokalsiege, cupsiege, friendlies, friendlies_ges, last_managed FROM ".$prefix."teams WHERE ids = '".$clearid."'";
$sql2 = mysql_query($sql1);
$sql2a = mysql_num_rows($sql2);
if ($sql2a == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
?>
<title><?php echo _('Team:').' '.$sql3['name']; ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.verletzter_spieler td {
	text-decoration: line-through;
}
-->
</style>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Team:').' '.$sql3['name']; ?></h1>
	<?php
	if ($loggedin == 1 && ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin')) {
		echo '<p style="text-align:right">';
		echo '<a href="/namensaenderung.php?team='.$clearid.'" class="pagenava">'._('Vereinsnamen ändern').'</a>';
		echo '<a href="/protokoll.php?team='.$clearid.'" class="pagenava">'._('Protokoll ansehen').'</a>';
		echo '</p>';
	}
	?>
<p style="text-align:right">
	<a href="/lig_transfers.php?team=<?php echo $clearid; ?>" class="pagenava"><?php echo _('Transfers dieses Teams'); ?></a>
	<a href="/kalender.php?team=<?php echo $clearid; ?>" class="pagenava"><?php echo _('Zum Spielplan'); ?></a>
</p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Bereich'); ?></th>
<th scope="col"><?php echo _('Wert'); ?></th>
</tr>
</thead>
<tbody>
<?php
if (isset($_GET['action'])) {
	if ($_GET['action'] == 'requestedFriendly') {
		setTaskDone('arrange_friendly');
	}
}
$tql1 = "SELECT name, gespielt FROM ".$prefix."ligen WHERE ids = '".$sql3['liga']."'";
$tql2 = mysql_query($tql1);
$tql3 = mysql_fetch_assoc($tql2);
$stadion1 = "SELECT plaetze FROM ".$prefix."stadien WHERE team = '".$clearid."'";
$stadion2 = mysql_query($stadion1);
$stadion3 = mysql_fetch_assoc($stadion2);
$uql1 = "SELECT saison FROM ".$prefix."zeitrechnung";
$uql2 = mysql_query($uql1);
$uql3 = mysql_fetch_assoc($uql2);
$vql1 = "SELECT ids, username, status FROM ".$prefix."users WHERE team = '".$clearid."'";
$vql2 = mysql_query($vql1);
$vql4 = mysql_num_rows($vql2);
if ($vql4 == 0) { $last_managed = date('d.m.Y H:i', $sql3['last_managed']); $strainer = '<td>Computer ('.$last_managed.')</td>'; } else { $vql3 = mysql_fetch_assoc($vql2); $strainer = '<td class="link">'.displayUsername($vql3['username'], $vql3['ids']).'</td>'; }
echo '<tr class="odd"><td>'._('Trainer').'</td>'.$strainer.'</tr>';
echo '<tr><td>'._('Manager').'</td>'.$strainer.'</tr>';
echo '<tr class="odd"><td>'._('Finanzlage').'</td><td>'.kontoToWort($sql3['konto']).'</td></tr>';
echo '<tr><td>'._('Land').'</td><td class="link"><a href="/lig_tabelle.php?liga='.$sql3['liga'].'">'.$tql3['name'].'</a></td></tr>';
echo '<tr class="odd"><td>'._('Liga (Vorjahr)').'</td><td>'.$sql3['rank'].'. ('.$sql3['vorjahr_platz'].'.)</td></tr>';
echo '<tr><td>'._('Pokal (Vorjahr)').'</td><td>'.pokalrunde_wort($sql3['pokalrunde']).' ('.pokalrunde_wort($sql3['vorjahr_pokalrunde']).')</td></tr>';
echo '<tr class="odd"><td>'._('Cup (Vorjahr)').'</td><td>'.cuprunde_wort($sql3['cuprunde']).' ('.cuprunde_wort($sql3['vorjahr_cuprunde']).')</td></tr>';
echo '<tr><td>'._('Kaderstärke').'</td><td>'.number_format($sql3['staerke'], 1, ',', '.').'</td></tr>';
echo '<tr class="odd"><td>'._('Aufstellungsstärke').'</td><td>'.number_format($sql3['aufstellung'], 1, ',', '.').'</td></tr>';
echo '<tr><td>'._('Meisterschaft').'</td><td>'.$sql3['meisterschaften'].'x</td></tr>';
echo '<tr class="odd"><td>'._('Pokalsieg').'</td><td>'.$sql3['pokalsiege'].'x</td></tr>';
echo '<tr><td>'._('Cupsieg').'</td><td>'.$sql3['cupsiege'].'x</td></tr>';
echo '<tr class="odd"><td>'._('RKP (Vorjahr)').'</td><td>'.__('%1$s (%2$s) Punkte', number_format($sql3['elo'], 0, ',', '.'), number_format($sql3['vorjahr_elo'], 0, ',', '.')).'</td></tr>';
echo '<tr><td>'._('Testspiele').'</td><td>';
if ($live_scoring_spieltyp_laeuft == 'Test') { echo '?'; } else { __('%1$s (%2$s Siege)', $sql3['friendlies_ges'], $sql3['friendlies']); }
echo '</td></tr>';
echo '<tr class="odd"><td>'._('Stadion').'</td><td>'.__('%s Plätze', number_format($stadion3['plaetze'], 0, ',', '.')).'</td></tr>';
?>
</tbody>
</table>
<?php
if (isset($vql3['username'])) {
	$mdsSiege1 = "SELECT COUNT(*) FROM ".$prefix."users_mds_sieger WHERE ids = '".mysql_real_escape_string($vql3['ids'])."'";
	$mdsSiege2 = mysql_query($mdsSiege1);
	$mdsSiege4 = mysql_result($mdsSiege2, 0);
}
else {
	$mdsSiege4 = 0;
}
// TROPHAEEN-RAUM ANFANG
if ($sql3['meisterschaften'] > 0 OR $sql3['pokalsiege'] > 0 OR $sql3['cupsiege'] > 0) {
	echo '<h1>'._('Trophäen-Raum').'</h1><p>';
	if ($sql3['meisterschaften'] > 0) {
		for ($i = 0; $i < $sql3['meisterschaften']; $i++) {
			echo '<img src="/images/trophaee_liga.png" alt="Meisterschaft" title="'._('Meisterschaft').'" /> ';
		}
	}
	if ($sql3['pokalsiege'] > 0) {
		for ($i = 0; $i < $sql3['pokalsiege']; $i++) {
			echo '<img src="/images/trophaee_pokal.png" alt="Pokalsieg" title="'._('Pokalsieg').'" /> ';
		}
	}
	if ($sql3['cupsiege'] > 0) {
		for ($i = 0; $i < $sql3['cupsiege']; $i++) {
			echo '<img src="/images/trophaee_cup.png" alt="Cupsieg" title="'._('Cupsieg').'" /> ';
		}
	}
	if ($mdsSiege4 > 0) {
		for ($i = 0; $i < $mdsSiege4; $i++) {
			echo '<img src="/images/trophaee_mds.png" alt="Manager der Saison" title="'._('Manager der Saison').'" /> ';
		}
	}
	echo '</p>';
}
// TROPHAEEN-RAUM ENDE
?>
<h1><?php echo _('Kader'); ?></h1>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('MT'); ?></th>
<th scope="col"><?php echo _('TS'); ?></th>
<th scope="col"><?php echo _('Name'); ?></th>
<th scope="col"><?php echo _('AL'); ?></th>
<th scope="col"><?php echo _('Stärke'); ?></th>
<th scope="col"><?php echo _('FR'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql4 = "SELECT ids, position, vorname, nachname, wiealt, staerke, talent, frische, verletzung, transfermarkt, marktwert FROM ".$prefix."spieler WHERE team = '".$clearid."' ORDER BY position DESC";
$sql5 = mysql_query($sql4);
$counter = 0;
$durchschnittsAlterWerte = array();
$gesamtMarktwertWerte = array();
while ($sql6 = mysql_fetch_assoc($sql5)) {
	$durchschnittsAlterWerte[] = $sql6['wiealt'];
	$gesamtMarktwertWerte[] = $sql6['marktwert'];
	$schaetzungVomScout = schaetzungVomScout($cookie_team, $cookie_scout, $sql6['ids'], $sql6['talent'], $sql6['staerke'], $clearid);
	if ($counter % 2 == 0) {
		if ($sql6['verletzung'] != 0) { echo '<tr class="verletzter_spieler">'; } else { echo '<tr>'; }
	}
	else {
		if ($sql6['verletzung'] != 0) { echo '<tr class="odd verletzter_spieler">'; } else { echo '<tr class="odd">'; }
	}
	echo '</td><td>'.$sql6['position'].'</td>';
	echo '<td>';
	if ($sql6['transfermarkt'] == 0) {
		echo '&nbsp;';
	}
	elseif ($sql6['transfermarkt'] == 1) {
		echo 'Kauf';
	}
	else {
		echo 'Leihe';
	}
	echo '</td>';
	echo '<td class="link"><a href="/spieler.php?id='.$sql6['ids'].'">'.$sql6['vorname'].' '.$sql6['nachname'].'</a></td><td>'.floor($sql6['wiealt']/365).'</td><td>'.number_format($sql6['staerke'], 1, ',', '.').' <span style="color:#999">('.number_format($schaetzungVomScout, 1, ',', '.').')</span></td><td>'.$sql6['frische'].'%</td>';
	echo '</tr>';
	$counter++;
}
if (count($durchschnittsAlterWerte) > 0) {
	$dAlter = array_sum($durchschnittsAlterWerte)/count($durchschnittsAlterWerte)/365;
}
else {
	$dAlter = 0;
}
if (count($gesamtMarktwertWerte) > 0) {
	$gMarktwert = array_sum($gesamtMarktwertWerte);
}
else {
	$gMarktwert = 0;
}
echo '<tr><td colspan="6">'.__('Team-Alter: %s Jahre', number_format($dAlter, 1, ',', '.')).'</td></tr>';
echo '<tr class="odd"><td colspan="6">'.__('Team-Marktwert: %s €', number_format($gMarktwert, 0, ',', '.')).'</td></tr>';
?>
</tbody>
</table>
<p><strong><?php echo _('Überschriften:').'</strong> '._('MT: Mannschaftsteil, TS: Transferstatus, AL: Alter, FR: Frische'); ?></p>
<p><strong><?php echo _('Mannschaftsteile:').'</strong> '._('T: Torwart, A: Abwehr, M: Mittelfeld, S: Sturm'); ?></p>
<p><strong><?php echo _('Durchgestrichen:').'</strong> '._('verletzte oder gesperrte Spieler'); ?></p>
<?php include 'zz3.php'; ?>
