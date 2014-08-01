<?php include 'zz1.php'; ?>
<title><?php echo _('Transfers'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.gebotDrueber {
	color: #008000;
}
.gebotDrunter {
	color: #f00;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php
// IN EIGENE LIGA ODER IN ALLE LIGEN ANFANG
$addSql = "";
$ligaGetValue = 'all';
$teamGetValue = '';
$ligaNav = '<p style="text-align:right"><a href="/lig_transfers.php?liga=own" class="pagenava">'._('In eigene Liga').'</a></p>';
$pageTitle = _('Transfers');
if (isset($_GET['team'])) {
	$teamGetValue = mysql_real_escape_string(trim(strip_tags($_GET['team'])));
	if ($teamGetValue != '') {
		$getTeamName1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$teamGetValue."'";
		$getTeamName2 = mysql_query($getTeamName1);
		if (mysql_num_rows($getTeamName2) > 0) {
			$getTeamName3 = mysql_fetch_assoc($getTeamName2);
			$pageTitle = 'Transfers von '.$getTeamName3['name'];
			$addSql = " WHERE a.bieter = '".$teamGetValue."' OR a.besitzer = '".$teamGetValue."'";
			$ligaNav = '<p style="text-align:right"><a href="/team.php?id='.$teamGetValue.'" class="pagenava">'._('Zum Teamprofil').'</a></p>';
		}
	}
}
if (isset($_GET['liga'])) {
	if ($_GET['liga'] == 'own') {
		$ligaGetValue = 'own';
		$addSql = " JOIN ".$prefix."teams AS c ON a.bieter = c.ids WHERE c.liga = '".$cookie_liga."'";
		$ligaNav = '<p style="text-align:right"><a href="/lig_transfers.php" class="pagenava">'._('In alle Ligen').'</a></p>';
	}
}
echo '<h1>'.$pageTitle.'</h1>';
if ($loggedin == 1) {
    echo $ligaNav;
}
// IN EIGENE LIGA ODER IN ALLE LIGEN ENDE
?>
<table>
<thead>
<tr class="odd">
<th scope="col" title="<?php echo _('Name des Spielers').'">'._('Spieler'); ?></th>
<th scope="col" title="<?php echo _('Geschätzte Ablösesumme').'">'._('Ablöse ca.'); ?></th>
<th scope="col" title="<?php echo _('Ablösesumme im Verhältnis zum Marktwert'); ?>">&nbsp;</th>
<th scope="col" title="<?php echo _('Altes Team des Spielers').'">'._('Von'); ?></th>
<th scope="col" title="<?php echo _('Neues Team des Spielers').'">'._('Zu'); ?></th>
</tr>
</thead>
<tbody>
<?php
// IDS TO TEAMNAME ANFANG
$sql1 = "SELECT ids, name FROM ".$prefix."teams";
$sql2 = mysql_query($sql1);
$ids_to_teamname = array();
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$ids_to_teamname[$sql3['ids']] = $sql3['name'];
}
function ids_to_teamname($ids) {
	global $ids_to_teamname;
	if (isset($ids_to_teamname[$ids])) {
		return $ids_to_teamname[$ids];
	}
	else {
		return 'Außerhalb Europas';
	}
}
// IDS TO TEAMNAME ENDE
$sql1 = "SELECT a.spieler, a.gebot, a.damaligerWert, a.besitzer, a.bieter, b.vorname, b.nachname, a.leihgebuehr, a.datum FROM ".$prefix."transfers AS a JOIN ".$prefix."spieler AS b ON a.spieler = b.ids".$addSql." ORDER BY a.datum DESC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td class="link"><a href="/spieler.php?id='.$sql3['spieler'].'" title="'.date('d.m.Y H:i', $sql3['datum']).' Uhr">'.mb_substr($sql3['vorname'], 0, 1, 'UTF-8').'. '.$sql3['nachname'].'</a></td>';
	echo '<td>';
	if ($sql3['gebot'] == 1) {
		if ($sql3['leihgebuehr'] == 0) { $leihgebuehr = 'ohne Prämie'; } else { $leihgebuehr = number_format($sql3['leihgebuehr'], 0, ',', '.').' € p.E.'; }
		echo '<span title="'.$leihgebuehr.'">Leihgabe</span>';
	}
	else {
		echo abloeseSchaetzen($cookie_team, $sql3['gebot'], $sql3['bieter'], $sql3['besitzer'], FALSE);
	}
	echo '</td>';
	// MARKTWERT ANTEIL ANFANG
	if ($sql3['gebot'] == 1) {
		echo '<td>&nbsp;</td>';
	}
	else {
        $anteilMarktwert = round($sql3['gebot']/$sql3['damaligerWert']*100)-100;
        if ($anteilMarktwert >= 0) {
            $anteilMarktwert = '+'.$anteilMarktwert;
            $gebotKlasse = ' class="gebotDrueber"';
        }
        else {
        	$gebotKlasse = ' class="gebotDrunter"';
        }
        echo '<td'.$gebotKlasse.'>'.$anteilMarktwert.'%</td>';
	}
	// MARKTWERT ANTEIL ENDE
	if (strlen($sql3['besitzer']) == 32) {
		echo '<td class="link"><a href="/team.php?id='.$sql3['besitzer'].'">'.ids_to_teamname($sql3['besitzer']).'</a></td>';
	}
	else {
		echo '<td>'.ids_to_teamname($sql3['besitzer']).'</td>';	
	}
	if (strlen($sql3['bieter']) == 32) {
		echo '<td class="link"><a href="/team.php?id='.$sql3['bieter'].'">'.ids_to_teamname($sql3['bieter']).'</a></td>';
	}
	else {
		echo '<td>'.ids_to_teamname($sql3['bieter']).'</td>';	
	}
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<p><strong><?php echo _('Hinweis:').'</strong> '._('Die Transfers sind nach Datum geordnet, d.h. der letzte Transfer steht ganz oben. In der Spalte hinter der Ablöse steht, ob die Ablöse über oder unter dem Marktwert lag.') ?></p>
<p><strong><?php echo _('Leihprämien:').'</strong> '._('Bei Leihgaben kannst Du die Höhe der Prämie sehen, indem Du mit der Maus über das Wort &quot;Leihgabe&quot; fährst.') ?></p>
<p><strong><?php echo _('Tipp:').'</strong> '._('Fahre mit der Maus über den Spielernamen, um das Datum mit genauer Uhrzeit zu sehen.') ?></p>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?liga='.$ligaGetValue.'&amp;team='.$teamGetValue.'&amp;seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
echo '</div>';
?>
<?php include 'zz3.php'; ?>
