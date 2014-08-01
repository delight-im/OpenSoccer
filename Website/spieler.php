<?php include 'zz1.php'; ?>
<?php
if (!isset($_GET['id'])) { exit; }
$sql1 = "SELECT id, ids, vorname, nachname, vertrag, position, wiealt, moral, staerke, talent, frische, marktwert, verhandlungsbasis, gehalt, transfermarkt, team, leiher, spiele_verein, spiele, tore, verletzung, jugendTeam, pokalNurFuer FROM ".$prefix."spieler WHERE ".$prefix."spieler.ids = '".mysql_real_escape_string($_GET['id'])."'";
$sql2 = mysql_query($sql1);
$sql2a = mysql_num_rows($sql2);
if ($sql2a == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);

$tm1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$sql3['team']."'";
$tm2 = mysql_query($tm1);
$tm3 = mysql_fetch_assoc($tm2);
if ($loggedin == 1 && $cookie_team != '__'.$cookie_id) {
	$getkonto1 = "SELECT konto FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
	$getkonto2 = mysql_query($getkonto1);
	$getkonto3 = mysql_fetch_assoc($getkonto2);
	$getkonto4 = $getkonto3['konto'];
}
else {
	$getkonto4 = 0;
}
// TRANSFERSTATUS KORRIGIEREN ANFANG
$tsk1 = "SELECT COUNT(*) FROM ".$prefix."transfermarkt WHERE spieler = '".$sql3['ids']."'";
$tsk2 = mysql_query($tsk1);
$tsk3 = mysql_result($tsk2, 0);
if ($tsk3 == 0) { $tskwert = 0; }
else { $tskwert = 1; }
$tsk4 = "UPDATE ".$prefix."spieler SET transfermarkt = ".$tskwert." WHERE ids = '".$sql3['ids']."' AND transfermarkt < 999998";
$tsk5 = mysql_query($tsk4);
if ($sql3['transfermarkt'] < 999998) { // nur beim Verkauf
	$sql3['transfermarkt'] = $tskwert;
}
// TRANSFERSTATUS KORRIGIEREN ENDE
if ($loggedin == 1) {
    $watch1 = "SELECT COUNT(*) FROM ".$prefix."transfermarkt_watch WHERE team = '".$cookie_team."' AND spieler_id = '".$sql3['ids']."'";
    $watch2 = mysql_query($watch1);
    $watch3 = mysql_result($watch2, 0);
}
else {
    $watch3 = 0;
}
?>
<title><?php echo _('Spieler:'); ?> <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.selectVisible {
	visibility: visible;
}
.selectHidden {
	visibility: hidden;
	width: 0;
	height: 0;
}
-->
</style>
<?php include 'zz2.php'; ?>
<h1>Spieler: <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?></h1>
<?php
if (isset($_GET['action'])) {
	if ($_GET['action'] == 'setWatching') {
		setTaskDone('watch_player');
	}
}
if (isset($_GET['sellSuccess'])) {
	$sellPrice = number_format($_GET['sellSuccess'], 0, ',', '.');
	addInfoBox(__('Du hast den Spieler erfolgreich für %s € verkauft.', $sellPrice));
}
echo '<p style="text-align:right">';
if ($loggedin == 1) {
    echo '<a href="/transfermarkt_watch.php?id='.$sql3['ids'].'" class="pagenava" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')">';
    if ($watch3 == 0) {
        echo _('Spieler beobachten');
    }
    else {
        echo _('Beobachtung beenden');
    }
    echo '</a> ';
}
echo '<a href="/spieler_historie.php?id='.$sql3['ids'].'" class="pagenava">'._('Zur Historie').'</a></p>';
?>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Bereich'); ?></th>
<th scope="col"><?php echo _('Wert'); ?></th>
</tr>
</thead>
<tbody>
<?php
$schaetzungVomScout = schaetzungVomScout($cookie_team, $cookie_scout, $_GET['id'], $sql3['talent'], $sql3['staerke'], $sql3['team']);
echo '<tr class="odd"><td>'._('Position').'</td><td>';
    if ($sql3['position'] == 'T') { echo _('Torwart'); }
    elseif ($sql3['position'] == 'A') { echo _('Abwehr'); }
    elseif ($sql3['position'] == 'M') { echo _('Mittelfeld'); }
    elseif ($sql3['position'] == 'S') { echo _('Sturm'); }
echo '</td></tr>';
$alter_in_jahren = floor($sql3['wiealt']/365);
echo '<tr><td>'._('Alter').'</td><td>'.__('%d Jahre', $alter_in_jahren).'</td></tr>';
echo '<tr class="odd"><td>'._('Stärke').'</td><td>'.number_format($sql3['staerke'], 1, ',', '.').'</td></tr>';
if ($sql3['team'] == 'frei') {
	$sql3['frische'] = getRegularFreshness(GameTime::getMatchDay());
}
echo '<tr><td>'._('Frische').'</td><td><img src="/images/balken/'.round($sql3['frische']).'.png" alt="'.round($sql3['frische']).'%" title="'.round($sql3['frische']).'%" width="104" /></td></tr>';
echo '<tr class="odd"><td>'._('Moral').'</td><td><img src="/images/balken/'.round($sql3['moral']).'.png" alt="'.round($sql3['moral']).'%" title="'.round($sql3['moral']).'%" width="104" /></td></tr>';
echo '<tr><td>'._('Marktwert').'</td><td>'.number_format($sql3['marktwert'], 0, ',', '.').' €</td></tr>';
if ($loggedin == 1 && $sql3['team'] == $cookie_team) {
	echo '<tr class="odd"><td>'._('Gehalt / Saison').'</td><td>'.number_format($sql3['gehalt'], 0, ',', '.').' €</td></tr>';
}
elseif ($sql3['team'] != 'frei') {
	$sosi = 0;
	if ($sql3['gehalt'] > 99999999) { $sosi = 8; }
	elseif ($sql3['gehalt'] > 9999999) { $sosi = 7; }
	elseif ($sql3['gehalt'] > 999999) { $sosi = 6; }
	elseif ($sql3['gehalt'] > 99999) { $sosi = 5; }
	elseif ($sql3['gehalt'] > 9999) { $sosi = 4; }
	elseif ($sql3['gehalt'] > 999) { $sosi = 3; }
	elseif ($sql3['gehalt'] > 99) { $sosi = 2; }
	$sidfhisudhfuo = round($sql3['gehalt']/pow(10, $sosi))*pow(10, $sosi);
	echo '<tr class="odd"><td>'._('Gehalt / Saison').'</td><td>ca. '.number_format($sidfhisudhfuo, 0, ',', '.').' €</td></tr>';
}
if ($sql3['team'] == 'frei') {
	echo '<tr><td>'._('Team').'</td><td>'._('außerhalb Europas').'</td></tr>';
}
else {
	echo '<tr><td>'._('Team').'</td><td class="link"><a href="/team.php?id='.$sql3['team'].'">'.$tm3['name'].'</a></td></tr>';
}
echo '<tr class="odd"><td>'._('Vertrag bis').'</td><td>';
	if ($sql3['team'] != 'frei') { echo date('d.m.Y H:i', $sql3['vertrag']); } else { echo _('unbekannt'); }
	echo '</td></tr>';
if ($sql3['team'] == 'frei') {
	echo '<tr><td>'._('Transferstatus').'</td><td>'._('unbekannt').'</td></tr>';
}
else {
	echo '<tr><td>'._('Transferstatus').'</td><td>';
	if ($sql3['transfermarkt'] == 0) {
		if ($sql3['leiher'] != 'keiner') {
			echo _('Ausgeliehen');
		}
		else {
			echo _('Unverkäuflich');
		}
	}
	elseif ($sql3['transfermarkt'] == 1) {
		echo '<a href="/transfermarkt_auktion.php?id='.$sql3['ids'].'">'._('Zur Auktion').'</a>';
	}
	elseif ($sql3['transfermarkt'] > 999998) {
		$getLeihPos1 = "SELECT COUNT(*) FROM ".$prefix."spieler WHERE transfermarkt > 999998 AND ((staerke > ".$sql3['staerke'].") OR (staerke = ".$sql3['staerke']." AND id < ".$sql3['id']."))";
		$getLeihPos2 = mysql_query($getLeihPos1);
		$getLeihPos3 = mysql_result($getLeihPos2, 0);
		$getLeihPage = floor($getLeihPos3/$eintraege_pro_seite)+1;
		echo '<a href="/transfermarkt_leihe.php?seite='.$getLeihPage.'&amp;mark='.$sql3['ids'].'">'._('Zur Leihgabe').'</a>';
	}
	echo '</td></tr>';
}
echo '<tr class="odd"><td>'._('Spiele für Verein').'</td><td>'.$sql3['spiele_verein'].'</td></tr>';
echo '<tr><td>'._('Pflichtspiele (Tore)').'</td><td>'.$sql3['spiele'].' (';
if ($live_scoring_spieltyp_laeuft == '') { echo $sql3['tore']; } else { echo '?'; }
echo ')</td></tr>';
echo '<tr class="odd"><td>'._('Gesundheit').'</td><td>';
if ($sql3['verletzung'] == 0) { echo _('Gesund'); } else { echo '<span style="color:red">Verletzt ('.$sql3['verletzung'].' Tag'; if ($sql3['verletzung'] > 1) { echo 'e'; } echo ')</span>'; }
echo '</td></tr>';
echo '<tr><td>'._('Pokal-Sperre').'</td><td>';
if ($sql3['pokalNurFuer'] == '') {
	echo _('Nein');
}
else {
	if ($sql3['pokalNurFuer'] == $sql3['team']) {
		echo _('nach Transfer'); // noch nicht
	}
	else {
		echo _('Ja'); // jetzt schon
	}
}
echo '</td></tr>';
echo '<tr class="odd"><td>'._('Talent').'</td><td>';
$talentStars = round($schaetzungVomScout/9.9*5);
for ($stars_full = 1; $stars_full <= $talentStars; $stars_full++) {
	echo '<img src="/images/stern.png" alt="+" width="16" />';
}
for ($stars_empty = ($talentStars+1); $stars_empty <= 5; $stars_empty++) {
	echo '<img src="/images/stern_leer.png" alt="O" width="16" />';
}
echo '</td></tr>';
if ($schaetzungVomScout <= $sql3['staerke']) {
	echo '<tr><td colspan="2">'._('Dein Scout glaubt, dass dieser Spieler seinen Höhepunkt bereits erreicht hat.').'</td></tr>';
}
else {
	echo '<tr><td colspan="2">'.__('Dein Scout glaubt, dass dieser Spieler eine Stärke von %s erreichen kann.', number_format($schaetzungVomScout, 1, ',', '.')).'</td></tr>';
}
if ($loggedin == 1 && $sql3['team'] == $cookie_team && $sql3['leiher'] == 'keiner') {
	if ($sql3['marktwert'] > 0) {
		echo '<tr class="odd"><td colspan="2" class="link"><a href="/vertrag_verlaengern.php?id='.$sql3['ids'].'">'._('Vertrag verlängern').'</a></td></tr>';
	}
	else {
		echo '<tr class="odd"><td colspan="2">'._('Der Spieler bietet Dir noch keine Vertragsverlängerung an.').'</td></tr>';
	}
	if ($sql3['jugendTeam'] == $sql3['team'] && $sql3['gehalt'] % 100000 == 0) {
		$entlassungskosten = 0; // Jugendspieler umsonst entlassen
	}
	else {
		$entlassungskosten = $sql3['gehalt']*ceil(($sql3['vertrag']-time())/86400/22)/2;
	}
	if ($sql3['vertrag'] < getTimestamp('+48 hours')) {
		echo '<tr><td colspan="2">'._('Der Vertrag des Spielers läuft aus, Du kannst ihn deshalb nicht mehr entlassen').'</td></tr>';
	}
	else {
		echo '<tr><td colspan="2" class="link"><a href="/spieler_entlassen.php?id='.$sql3['ids'].'" onclick="return confirm(\''._('Bist Du sicher?').'\')">'.__('Für %s € entlassen', number_format($entlassungskosten, 0, ',', '.')).'</a></td></tr>';
	}
}
?>
</tbody>
</table>
<?php
if ($loggedin == 1 && $sql3['team'] == $cookie_team && $sql3['leiher'] == 'keiner' && $sql3['marktwert'] > 0) {
	if (($sql3['spiele_verein'] > 5 && $alter_in_jahren < 34) OR ($sql3['spiele'] <= 6 && $alter_in_jahren < 34)) {
		if ($_SESSION['transferGesperrt'] == FALSE) {
			echo '<h1>'._('Transfermarkt').'</h1>';
			if ($sql3['transfermarkt'] == 0) {
				echo '<p>'._('Du kannst diesen Spieler auf dem Transfermarkt verkaufen oder ihn zur Leihgabe anbieten. Wenn Du ihn zum Verkauf anbietest, wird er direkt gegen eine Ablöse an ein anderes Team verkauft. Wenn Du den Spieler zur Leihgabe anbietest, kannst Du später noch die Angebote prüfen und entscheiden, ob Du eins davon annimmst.').'</p>';
				if ($sql3['spiele_verein'] > 5 && $alter_in_jahren < 34) {
					echo '<form action="/transfermarkt_aktion.php" method="post" accept-charset="utf-8">';
					echo '<p><select id="aukTyp" name="typ" size="1">';
					echo '<option value="Kauf">'.__('Verkauf für %s €', number_format($sql3['marktwert'], 0, ',', '.')).'</option>';
					echo '</select> <input type="hidden" name="spieler" value="'.$sql3['ids'].'" /><input type="submit" value="'._('Jetzt verkaufen').'" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /></p>';
					echo '</form>';
				}
				if ($sql3['spiele'] <= 6 && $alter_in_jahren < 34) { // 6er-Leihgaben-Sperre
					echo '<form action="/transfermarkt_aktion.php" method="post" accept-charset="utf-8">';
					echo '<p><select id="aukTyp" name="typ" size="1">';
					echo '<option value="999999">'._('zur Leihgabe (ohne Prämie)').'</option>';
					echo '<option value="5000000">'.__('zur Leihgabe (%s Prämie p.P.)', '50.000').'</option>';
					echo '<option value="10000000">'.__('zur Leihgabe (%s Prämie p.P.)', '100.000').'</option>';
					echo '<option value="15000000">'.__('zur Leihgabe (%s Prämie p.P.)', '150.000').'</option>';
					echo '<option value="20000000">'.__('zur Leihgabe (%s Prämie p.P.)', '200.000').'</option>';
					echo '<option value="25000000">'.__('zur Leihgabe (%s Prämie p.P.)', '250.000').'</option>';
					echo '<option value="30000000">'.__('zur Leihgabe (%s Prämie p.P.)', '300.000').'</option>';
					echo '<option value="35000000">'.__('zur Leihgabe (%s Prämie p.P.)', '350.000').'</option>';
					echo '</select> <input type="hidden" name="spieler" value="'.$sql3['ids'].'" /><input type="submit" value="'._('Anbieten zur Leihgabe').'" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /></p>';
					echo '</form>';
				}
			}
			else {
				$gcnt1 = "SELECT gebote FROM ".$prefix."transfermarkt WHERE spieler = '2fea04bd67b80c08086b7411bea92f04' LIMIT 0, 1";
				$gcnt2 = mysql_query($gcnt1);
				$gcnt3 = mysql_fetch_assoc($gcnt2);
				if ($gcnt3['gebote'] == 0) {
					echo '<p>'._('Du bietest diesen Spieler bereits auf dem Transfermarkt an. Du kannst dieses Angebot aber abbrechen: Möchtest Du das Angebot auf dem Transfermarkt abbrechen?').'</p>';
					echo '<form action="/transfermarkt_aktion.php" method="post" accept-charset="utf-8">';
					echo '<p><select name="abbrechen" size="1" style="width:400px">';
					echo '<option value="Nein">'._('Nein, ich möchte nichts tun.').'</option>';
					echo '<option value="Ja">'._('Ja, ich möchte den Spieler nicht weiter anbieten.').'</option>';
					echo '</select></p>';
					echo '<p><input type="hidden" name="spieler" value="'.$sql3['ids'].'" /><input type="submit" value="'._('Absenden').'" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /></p>';
					echo '</form>';
				}
			}
		}
	}
	else {
		echo '<h1>'._('Spieler anbieten').'</h1>';
		echo '<p>'._('Dein Spieler muss für Deinen Verein mindestens 6 Spiele absolviert haben, bevor Du ihn verkaufen kannst. Damit Du ihn verleihen kannst, darf er höchstens 6 Einsätze in der aktuellen Saison haben.').'</p>';
		echo '<p>'._('Spieler, die über 33 Jahre alt sind, können grundsätzlich nicht mehr verkauft oder verliehen werden.').'</p>';
	}
}
?>
<?php include 'zz3.php'; ?>
