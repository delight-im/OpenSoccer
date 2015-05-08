<?php include 'zz1.php'; ?>
<title><?php echo _('Kader'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.verletzt td {
	text-decoration: line-through;
}
.verliehen td {
	background-color: #ddd;
}
-->
</style>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Kader'); ?></h1>
<?php if ($loggedin == 1) { ?>
<?php
if (isset($_POST['positionToSearch'])) {
	$positionToSearch = mysql_real_escape_string(trim(strip_tags($_POST['positionToSearch'])));
	if ($positionToSearch == 'T' OR $positionToSearch == 'A' OR $positionToSearch == 'M' OR $positionToSearch == 'S') {
		$up1 = "UPDATE ".$prefix."teams SET posToSearch = '".$positionToSearch."' WHERE ids = '".$cookie_team."'";
		$up2 = mysql_query($up1);
		switch ($positionToSearch) {
			case 'T': $whatIsSearched = 'Torhüter'; break;
			case 'A': $whatIsSearched = 'Abwehrspieler'; setTaskDone('instruct_youthcoach'); break;
			case 'M': $whatIsSearched = 'Mittelfeldspieler'; break;
			case 'S': $whatIsSearched = 'Stürmer'; break;
		}
		addInfoBox(__('Dein Jugendtrainer sucht ab sofort %s.', $whatIsSearched));
	}
}
?>
<?php
// SPIELER-MARKIERUNGEN ANFANG
if (isset($_POST['auswahl']) && isset($_POST['farbe'])) {
	$farbe = mysql_real_escape_string(trim(strip_tags($_POST['farbe'])));
	if (is_array($_POST['auswahl'])) {
		foreach ($_POST['auswahl'] as $markierter_spieler) {
			$sql1 = "INSERT INTO ".$prefix."spieler_mark (team, spieler, farbe) VALUES ('".$cookie_team."', '".$markierter_spieler."', '".$farbe."') ON DUPLICATE KEY UPDATE farbe = '".$farbe."'";
			$sql2 = mysql_query($sql1);
		}
	}
}
$gf1 = "SELECT spieler, farbe FROM ".$prefix."spieler_mark WHERE team = '".$cookie_team."'";
$gf2 = mysql_query($gf1);
$markierungen = array();
while ($gf3 = mysql_fetch_assoc($gf2)) {
	$markierungen[$gf3['spieler']] = $gf3['farbe'];
}
// SPIELER-MARKIERUNGEN ENDE
?>
<form action="/kader.php" method="post" accept-charset="utf-8">
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('MT'); ?></th>
<th scope="col"><?php echo _('TS'); ?></th>
<th scope="col"><?php echo _('Name'); ?></th>
<th scope="col"><?php echo _('AL'); ?></th>
<th scope="col"><?php echo _('Stärke'); ?></th>
<th scope="col"><?php echo _('PS'); ?></th>
<th scope="col"><?php echo _('MW'); ?></th>
</tr>
</thead>
<tbody>
<?php
// LIGA-WERT UND MORAL DER SPIELER AKTUALISIEREN ANFANG
$lu1 = "UPDATE ".$prefix."spieler SET liga = '".$cookie_liga."' WHERE team = '".$cookie_team."'";
$lu2 = mysql_query($lu1);
// LIGA-WERT UND MORAL DER SPIELER AKTUALISIEREN ENDE
$sql1 = "SELECT ids, team, position, vorname, nachname, wiealt, staerke, tore, spiele, marktwert, gehalt, talent, transfermarkt, verletzung, leiher FROM ".$prefix."spieler WHERE team = '".$cookie_team."' OR leiher = '".$cookie_team."' ORDER BY position DESC, staerke DESC";
$sql2 = mysql_query($sql1);
$durchschnittsAlterWerte = array();
$gesamtMarktwertWerte = array();
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$durchschnittsAlterWerte[] = $sql3['wiealt'];
	$gesamtMarktwertWerte[] = $sql3['marktwert'];
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
	if ($sql3['transfermarkt'] == 0) {
		$transferstatus = '&nbsp;';
	}
	elseif ($sql3['transfermarkt'] == 1) {
		$transferstatus = _('Kauf');
	}
	else {
		$transferstatus = _('Leihe');
	}
	// CSS-KLASSEN BESTIMMEN ANFANG
	$trCSS = '';
	if ($sql3['verletzung'] != 0) { $trCSS .= 'verletzt'; }
	if ($sql3['team'] != $cookie_team OR $sql3['leiher'] != 'keiner') { $trCSS .= 'verliehen'; }
	$trCSS = trim($trCSS);
	echo '<tr';
	if ($trCSS != '') { echo ' class="'.$trCSS.'"'; }
	echo '>';
	// CSS-KLASSEN BESTIMMEN ENDE
	echo '<td'.$farbcode.'><input type="checkbox" name="auswahl[]" value="'.$sql3['ids'].'" /></td>';
	$schaetzungVomScout = schaetzungVomScout($cookie_team, $cookie_scout, $sql3['ids'], $sql3['talent'], $sql3['staerke'], $cookie_team);
	echo '<td>'.$sql3['position'].'</td><td>'.$transferstatus.'</td><td class="link"><a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a></td><td>'.floor($sql3['wiealt']/365).'</td><td>'.number_format($sql3['staerke'], 1, ',', '.').' <span style="color:#999">('.number_format($schaetzungVomScout, 1, ',', '.').')</span></td>';
	if ($sql3['team'] != $cookie_team) {
		echo '<td colspan="2">'._('Verliehen').'</td>';
	}
	elseif ($sql3['leiher'] != 'keiner') {
		echo '<td colspan="2">'._('Ausgeliehen').'</td>';
	}
	else {
		echo '<td>'.$sql3['spiele'].' (';
		if ($live_scoring_spieltyp_laeuft == '') {
			echo $sql3['tore'];
		}
		else {
			echo '?';
		}
		echo ')</td><td>'.number_format($sql3['marktwert']/1000000, 3, ',', '.').'</td>';
	}
	echo '</tr>';
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
echo '<tr><td colspan="8">'.__('Team-Alter: %s Jahre', number_format($dAlter, 1, ",", ".")).'</td></tr>';
echo '<tr class="odd"><td colspan="8">'.__('Team-Marktwert: %s €', number_format($gMarktwert, 0, ",", ".")).'</td></tr>';
?>
</tbody>
</table>
<p><select name="farbe" size="1" style="width:200px">
	<option value="Keine"><?php echo _('Keine'); ?></option>
	<option value="Aqua"><?php echo _('Aqua'); ?></option>
	<option value="Blau"><?php echo _('Blau'); ?></option>
	<option value="Gelb"><?php echo _('Gelb'); ?></option>
	<option value="Lila"><?php echo _('Lila'); ?></option>
	<option value="Oliv"><?php echo _('Oliv'); ?></option>
	<option value="Pink"><?php echo _('Pink'); ?></option>
	<option value="Rot"><?php echo _('Rot'); ?></option>
	<option value="Silber"><?php echo _('Silber'); ?></option>
	<option value="Gruen"><?php echo _('Grün'); ?></option>
</select> <input type="submit" value="<?php echo _('Ausgewählte Spieler markieren'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<p><strong><?php echo _('Überschriften:').'</strong> '._('MT: Mannschaftsteil, TS: Transferstatus, AL: Alter, PS: Pflichtspiele (Tore), MW: Marktwert in Millionen Euro'); ?></p>
<p><strong><?php echo _('Mannschaftsteile:').'</strong> '._('T: Torwart, A: Abwehr, M: Mittelfeld, S: Sturm'); ?></p>
<p><strong><?php echo _('Durchgestrichen:').'</strong> '._('verletzte oder gesperrte Spieler'); ?></p>
<h1 id="besetzung"><?php echo _('Besetzung des Kaders'); ?></h1>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Position'); ?></th>
<th scope="col"><?php echo _('Besetzung'); ?></th>
<th scope="col"><?php echo _('Spieler'); ?></th>
</tr>
</thead>
<tbody>
<?php
// BESETZUNGEN BERECHNEN ANFANG
$startSql = mt_rand(0, 234)*8;
$posSel1 = "SELECT team, position, COUNT(*) AS anzahl FROM ".$prefix."spieler WHERE (team = '".$cookie_team."' AND leiher = 'keiner') OR leiher = '".$cookie_team."' GROUP BY position";
$posSel2 = mysql_query($posSel1);
$posArr = array();
while ($posSel3 = mysql_fetch_assoc($posSel2)) {
	switch ($posSel3['position']) {
		case 'T': $posArr['T'] = $posSel3['anzahl']/1.5; break;
		case 'A': $posArr['A'] = $posSel3['anzahl']/6; break;
		case 'M': $posArr['M'] = $posSel3['anzahl']/6; break;
		case 'S': $posArr['S'] = $posSel3['anzahl']/3; break;
	}
}
// BESETZUNGEN BERECHNEN ENDE
// BEWERTUNG AUSGEBEN ANFANG
function besetzungToWort($besetzung) {
	if ($besetzung < 0.5) { return _('sehr schlecht'); }
	elseif ($besetzung < 0.8) { return _('schlecht'); }
	elseif ($besetzung < 1.1) { return _('solide'); }
	elseif ($besetzung < 1.4) { return _('gut'); }
	elseif ($besetzung < 1.7) { return _('sehr gut'); }
	elseif ($besetzung < 2) { return _('hervorragend'); }
	elseif ($besetzung < 2.3) { return _('weltklasse'); }
	else { return 'überbesetzt'; }
}
krsort($posArr);
foreach ($posArr as $position=>$besetzung) {
	switch ($position) {
		case 'T': $posToSearch = 'Torwart'; $anzahlSpieler = $besetzung*1.5; break;
		case 'A': $posToSearch = 'Abwehr'; $anzahlSpieler = $besetzung*6; break;
		case 'M': $posToSearch = 'Mittelfeld'; $anzahlSpieler = $besetzung*6; break;
		case 'S': $posToSearch = 'Sturm'; $anzahlSpieler = $besetzung*3; break;
		default: $posToSearch = '?'; $anzahlSpieler = 0; break;
	}
	echo '<tr><td>'.$posToSearch.'</td><td>'.besetzungToWort($besetzung).'</td><td>'.$anzahlSpieler.'</td></tr>';
}
// BEWERTUNG AUSGEBEN ENDE
?>
</tbody>
</table>
<?php
// FESTLEGEN WAS GESUCHT WERDEN SOLL ANFANG
$sql1 = "SELECT posToSearch FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) == 0) {
	$currentlySearching = '0';
}
else {
	$sql3 = mysql_fetch_assoc($sql2);
	$currentlySearching = $sql3['posToSearch'];
}
echo '<form action="/kader.php" method="post" accept-charset="utf-8">';
echo '<p><select name="positionToSearch" size="1" style="width:200px">';
	echo '<option value="T"'; if ($currentlySearching == 'T') { echo ' selected="selected"'; } echo '>'._('Torwart suchen').'</option>';
	echo '<option value="A"'; if ($currentlySearching == 'A') { echo ' selected="selected"'; } echo '>'._('Abwehr suchen').'</option>';
	echo '<option value="M"'; if ($currentlySearching == 'M') { echo ' selected="selected"'; } echo '>'._('Mittelfeld suchen').'</option>';
	echo '<option value="S"'; if ($currentlySearching == 'S') { echo ' selected="selected"'; } echo '>'._('Sturm suchen').'</option>';
echo '</select> <input type="submit" value="'._('Festlegen').'"'.noDemoClick($cookie_id).' /></p>';
echo '</form>';
// FESTLEGEN WAS GESUCHT WERDEN SOLL ENDE
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
