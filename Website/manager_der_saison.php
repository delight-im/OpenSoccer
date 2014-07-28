<?php include 'zz1.php'; ?>
<title><?php echo _('Manager der Saison'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Manager der Saison'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><?php echo _('Am 1. und 2. Spieltag jeder Saison wählen die Manager aus allen Ligen den &quot;Manager der Saison&quot;. Wer hat Deiner Meinung nach den Titel
verdient? Welcher Manager hat in der abgelaufenen Saison das Beste aus seinem Team rausgeholt?'); ?></p>
<p><?php echo _('Es können leider nur Manager teilnehmen, die schon mindestens 22 Tage lang dabei sind.'); ?></p>
<?php
setTaskDone('check_mds');
$schon_gewaehlt1 = "SELECT COUNT(*) FROM ".$prefix."users_mds_sieger WHERE saison = ".GameTime::getSeason();
$schon_gewaehlt2 = mysql_query($schon_gewaehlt1);
$schon_gewaehlt3 = mysql_result($schon_gewaehlt2, 0);
if (GameTime::getMatchDay() <= 3 && $schon_gewaehlt3 == 0) {
$timeout = getTimestamp('-22 days');
$sql1 = "SELECT regdate FROM ".$prefix."users WHERE ids = '".$cookie_id."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
$sql4 = "SELECT COUNT(*) FROM ".$prefix."users_mds WHERE voter = '".$cookie_id."'";
$sql5 = mysql_query($sql4);
$sql6 = mysql_result($sql5, 0);
if ($sql3['regdate'] > $timeout) {
	$tage_dabei = round((time()-$sql3['regdate'])/86400);
	addInfoBox(__('Du bist erst %d Tage dabei und deshalb nicht stimmberechtigt.', $tage_dabei));
}
elseif ($sql6 == 1 || $cookie_id == CONFIG_DEMO_USER) {
	addInfoBox(_('Du hast schon abgestimmt!'));
}
else {
?>
<form action="/manager_der_saison.php" method="get" accept-charset="utf-8">
<p><select name="tabellenplatz" size="1" style="width:200px">
	<option value="no"><?php echo _('Jeder Tabellenplatz'); ?></option>
	<option value="01-03"><?php echo __('%1$d. bis %2$d.', 1, 3); ?></option>
	<option value="04-06"><?php echo __('%1$d. bis %2$d.', 4, 6); ?></option>
	<option value="07-09"><?php echo __('%1$d. bis %2$d.', 7, 9); ?></option>
	<option value="10-12"><?php echo __('%1$d. bis %2$d.', 10, 12); ?></option>
</select></p>
<p><select name="pokalergebnis" size="1" style="width:200px">
	<option value="no"><?php echo _('Jedes Pokal-Ergebnis'); ?></option>
	<option value="1"><?php echo __('mind. %s', _('Vorrunde')); ?></option>
	<option value="2"><?php echo __('mind. %s', _('Achtelfinale')); ?></option>
	<option value="3"><?php echo __('mind. %s', _('Viertelfinale')); ?></option>
	<option value="4"><?php echo __('mind. %s', _('Halbfinale')); ?></option>
	<option value="5"><?php echo _('Finale'); ?></option>
</select></p>
<p><select name="cupergebnis" size="1" style="width:200px">
	<option value="no"><?php echo _('Jedes Cup-Ergebnis'); ?></option>
	<option value="1"><?php echo __('mind. %s', _('Vorrunde')); ?></option>
	<option value="2"><?php echo __('mind. %s', _('Achtelfinale')); ?></option>
	<option value="3"><?php echo __('mind. %s', _('Viertelfinale')); ?></option>
	<option value="4"><?php echo __('mind. %s', _('Halbfinale')); ?></option>
	<option value="5"><?php echo _('Finale'); ?></option>
</select></p>
<p><select name="bilanz" size="1" style="width:200px">
	<option value="no"><?php echo _('Jede Bilanz'); ?></option>
	<option value="000-010"><?php echo __('%1$d bis %2$d Mio € Gewinn', 0, 10); ?></option>
	<option value="010-030"><?php echo __('%1$d bis %2$d Mio € Gewinn', 10, 30); ?></option>
	<option value="030-060"><?php echo __('%1$d bis %2$d Mio € Gewinn', 30, 60); ?></option>
	<option value="060-100"><?php echo __('%1$d bis %2$d Mio € Gewinn', 60, 100); ?></option>
	<option value="100-150"><?php echo __('%1$d bis %2$d Mio € Gewinn', 100, 150); ?></option>
	<option value="150-210"><?php echo __('%1$d bis %2$d Mio € Gewinn', 150, 210); ?></option>
	<option value="210-280"><?php echo __('%1$d bis %2$d Mio € Gewinn', 210, 280); ?></option>
	<option value="280-360"><?php echo __('%1$d bis %2$d Mio € Gewinn', 280, 360); ?></option>
	<option value="360-450"><?php echo __('%1$d bis %2$d Mio € Gewinn', 360, 450); ?></option>
	<option value="450-550"><?php echo __('%1$d bis %2$d Mio € Gewinn', 450, 550); ?></option>
</select></p>
<p><input type="submit" value="<?php echo _('Suchen'); ?>" /></p>
</form>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Manager'); ?></th>
<th scope="col"><?php echo _('Liga'); ?></th>
<th scope="col"><?php echo _('Pokal'); ?></th>
<th scope="col"><?php echo _('Bilanz'); ?></th>
<th scope="col"><?php echo _('Wahl'); ?></th>
</tr>
</thead>
<tbody>
<?php
if (isset($_GET['wahl_id']) && isset($_GET['sec_id'])) {
	$wahl_id = mysql_real_escape_string(trim(strip_tags($_GET['wahl_id'])));
	$sec_id = mysql_real_escape_string(trim(strip_tags($_GET['sec_id'])));
	if ($sec_id == md5('29'.$cookie_id.'1992')) {
        $wahl1 = "INSERT INTO ".$prefix."users_mds (manager, voter) VALUES ('".$wahl_id."', '".$cookie_id."')";
        $wahl2 = mysql_query($wahl1);
        if ($wahl2 == FALSE) {
        	addInfoBox(_('Du hast schon abgestimmt!'));
        }
        else {
        	addInfoBox(_('Danke, Deine Stimme wurde gezählt!'));
    		$_SESSION['mds_abgestimmt'] = TRUE;
        }
	}
	else {
		addInfoBox(_('Deine Stimme war leider ungültig. Bitte versuche es noch einmal.'));
	}
}
// FILTER ANFANG
$zusatzbedingungen = "";
$value_for_tabellenplatz = 'no';
$value_for_pokalergebnis = 'no';
$value_for_cupergebnis = 'no';
$value_for_bilanz = 'no';
if (isset($_GET['tabellenplatz']) && isset($_GET['pokalergebnis']) && isset($_GET['cupergebnis']) && isset($_GET['bilanz'])) {
	$tabellenplatz_werte = array('no', '01-03', '04-06', '07-09', '10-12');
	$pokalergebnis_werte = array('no', '1', '2', '3', '4', '5');
	$cupergebnis_werte = array('no', '1', '2', '3', '4', '5');
	$bilanz_werte = array('no', '000-010', '010-030', '030-060', '060-100', '100-150', '150-210', '210-280', '280-360', '360-450', '450-550');
	if (in_array($_GET['tabellenplatz'], $tabellenplatz_werte)) {
		if (in_array($_GET['pokalergebnis'], $pokalergebnis_werte)) {
			if (in_array($_GET['cupergebnis'], $cupergebnis_werte)) {
				if (in_array($_GET['bilanz'], $bilanz_werte)) {
					if ($_GET['tabellenplatz'] != 'no') {
						$value_for_tabellenplatz = $_GET['tabellenplatz'];
						$temp1 = intval(substr($value_for_tabellenplatz, 0, 2))-1;
						$temp2 = intval(substr($value_for_tabellenplatz, 3, 2))+1;
						$zusatzbedingungen .= " AND b.vorjahr_platz > ".$temp1." AND b.vorjahr_platz < ".$temp2;
					}
					if ($_GET['pokalergebnis'] != 'no') {
						$value_for_pokalergebnis = $_GET['pokalergebnis'];
						$zusatzbedingungen .= " AND b.vorjahr_pokalrunde > '".intval($value_for_pokalergebnis-1)."'";
					}
					if ($_GET['cupergebnis'] != 'no') {
						$value_for_cupergebnis = $_GET['cupergebnis'];
						$zusatzbedingungen .= " AND b.vorjahr_cuprunde > '".intval($value_for_cupergebnis-1)."'";
					}
					if ($_GET['bilanz'] != 'no') {
						$value_for_bilanz = $_GET['bilanz'];
						$temp1 = bigintval(substr($value_for_bilanz, 0, 3))*1000000-1;
						$temp2 = bigintval(substr($value_for_bilanz, 4, 3))*1000000+1;
						$zusatzbedingungen .= " AND b.gewinnGeld > ".$temp1." AND b.gewinnGeld < ".$temp2;
					}
				}
			}
		}
	}
}
// FILTER ENDE
$sql1 = "SELECT a.ids, a.username, a.team, b.name, b.vorjahr_pokalrunde, b.gewinnGeld, b.tv_ein, b.vorjahr_liga FROM ".$prefix."users AS a JOIN ".$prefix."teams AS b ON a.team = b.ids WHERE a.team != '' AND a.regdate < ".$timeout.$zusatzbedingungen." ORDER BY a.regdate DESC";
$sql2 = mysql_query($sql1);
$counter = 0;
$multiListe = explode('-', $_SESSION['multiAccountList']);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($sql3['ids'] == $cookie_id) { continue; } // nicht fuer sich selbst stimmen
	if (in_array($sql3['team'], $multiListe)) { continue; }
	$sec_id = md5('29'.$cookie_id.'1992'); // hash damit link nur fuer diesen user gueltig ist
	// LIGA-PLATZIERUNG VOM LETZTEN JAHR HOLEN ANFANG
	$lo1 = "SELECT platz, punkte FROM ".$prefix."geschichte_tabellen WHERE liga = '".$sql3['vorjahr_liga']."' AND saison = ".intval(GameTime::getSeason()-1)." AND spieltag = 22 AND team = '".mysql_real_escape_string($sql3['name'])."'";
	$lo2 = mysql_query($lo1);
	$lo3 = mysql_fetch_assoc($lo2);
	// LIGA-PLATZIERUNG VOM LETZTEN JAHR HOLEN ENDE
	if ($counter % 2 == 1) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	$link_zur_tabelle = '/stat_geschichte.php?saison_spieltag='.intval(GameTime::getSeason()-1).'-22&liga='.$sql3['vorjahr_liga'];
	$bilanz = round($sql3['gewinnGeld']/1000000);
	if ($bilanz > 0) { $bilanz = '+'.$bilanz; }
	echo '<td>'.displayUsername($sql3['username'], $sql3['ids']).' (<a href="/team.php?id='.$sql3['team'].'">'.$sql3['name'].'</a>)</td>';
	echo '<td class="link"><a href="'.$link_zur_tabelle.'">'.$lo3['platz'].'. ('.$lo3['punkte'].' PKT)</a></td>';
	echo '<td>'.pokalrunde_wort($sql3['vorjahr_pokalrunde']).'</td>';
	echo '<td>'.__('%s Mio', $bilanz).'</td>';
	echo '<td class="link"><a href="/manager_der_saison.php?wahl_id='.$sql3['ids'].'&amp;sec_id='.$sec_id.'" onclick="return confirm('._('\'Bist Du sicher?\'').')">'._('Klicken').'</a></td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<?php
}
}
else {
	// AUSWERTUNG ANFANG
	$sql1 = "SELECT a.manager, b.username, (COUNT(*)/(SELECT COUNT(*) FROM ".$prefix."users_mds)*100) AS prozent FROM ".$prefix."users_mds AS a JOIN ".$prefix."users AS b ON a.manager = b.ids GROUP BY a.manager ORDER BY prozent DESC, b.regdate DESC LIMIT 0, 1";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 1) {
		$sql3 = mysql_fetch_assoc($sql2);
		$sql4 = "INSERT INTO ".$prefix."users_mds_sieger (saison, ids, username, stimmen) VALUES (".GameTime::getSeason().", '".$sql3['manager']."', '".mysql_real_escape_string($sql3['username'])."', ".$sql3['prozent'].")";
		$sql5 = mysql_query($sql4);
		$sql6 = "TRUNCATE TABLE ".$prefix."users_mds";
		$sql7 = mysql_query($sql6);
	}
	// AUSWERTUNG ENDE
	echo '<h1>'._('Bisherige Sieger').'</h1>';
	echo '<p><table><thead><tr class="odd"><th scope="col">'._('Saison').'</th><th scope="col">'._('Manager').'</th><th scope="col">'._('Stimmen').'</th></tr></thead><tbody>';
	$get1 = "SELECT a.saison, a.ids, b.username, a.stimmen FROM ".$prefix."users_mds_sieger AS a JOIN ".$prefix."users AS b ON a.ids = b.ids ORDER BY a.saison DESC";
	$get2 = mysql_query($get1);
	$counter = 0;
	while ($get3 = mysql_fetch_assoc($get2)) {
		if ($counter % 2 == 1) { echo '<tr>'; } else { echo '<tr class="odd">'; }
		echo '<td>'.intval($get3['saison']-1).'</td>'; // saison gibt Saison an in der gewaehlt wurde also eine Saison spaeter
		$displayUsername = displayUsername($get3['username'], $get3['ids']);
		if ($displayUsername == 'Gelöschter User') {
			echo '<td>'.$displayUsername.'</td>';		
		
		}
		else {
			echo '<td class="link">'.$displayUsername.'</td>';		
		}
		echo '<td>'.number_format($get3['stimmen'], 2, ',', '.').'%</td>';
		echo '</tr>';
		$counter++;
	}
	echo '</tbody></table></p>';
}
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
