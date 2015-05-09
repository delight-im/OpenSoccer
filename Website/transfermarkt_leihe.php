<?php include 'zz1.php'; ?>
<title><?php echo _('Transfermarkt | Ausleihen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Markt durchsuchen'); ?></h1>
<?php
// NUR 2 TRANSFERS ZWISCHEN 2 TEAMS ANFANG
$transfers_mit_team = array();
if ($loggedin == 1) {
    $n3t1 = "SELECT bieter, besitzer FROM ".$prefix."transfers WHERE bieter = '".$cookie_team."' OR besitzer = '".$cookie_team."'";
    $n3t2 = mysql_query($n3t1);
    while ($n3t3 = mysql_fetch_assoc($n3t2)) {
        $transfers_mit_team[] = $n3t3['besitzer'];
        $transfers_mit_team[] = $n3t3['bieter'];
    }
}
$transfers_mit_team = array_count_values($transfers_mit_team);
// NUR 2 TRANSFERS ZWISCHEN 2 TEAMS ENDE
?>
<form action="/transfermarkt_leihe.php" method="get" accept-charset="utf-8">
<p><select name="wiealt" size="1" style="width:200px">
	<option value="no"><?php echo _('Jedes Alter'); ?></option>
	<option value="17-20"><?php echo __('%1$d bis %2$d Jahre', 17, 20); ?></option>
	<option value="17-23"><?php echo __('%1$d bis %2$d Jahre', 17, 23); ?></option>
	<option value="20-23"><?php echo __('%1$d bis %2$d Jahre', 20, 23); ?></option>
	<option value="20-26"><?php echo __('%1$d bis %2$d Jahre', 20, 26); ?></option>
	<option value="23-26"><?php echo __('%1$d bis %2$d Jahre', 23, 26); ?></option>
	<option value="23-29"><?php echo __('%1$d bis %2$d Jahre', 23, 29); ?></option>
	<option value="26-29"><?php echo __('%1$d bis %2$d Jahre', 26, 29); ?></option>
	<option value="26-32"><?php echo __('%1$d bis %2$d Jahre', 26, 32); ?></option>
	<option value="29-32"><?php echo __('%1$d bis %2$d Jahre', 29, 32); ?></option>
	<option value="29-35"><?php echo __('%1$d bis %2$d Jahre', 29, 35); ?></option>
	<option value="32-35"><?php echo __('%1$d bis %2$d Jahre', 32, 35); ?></option>
</select></p>
<p><select name="position" size="1" style="width:200px">
	<option value="no"><?php echo _('Jede Position'); ?></option>
	<option value="T"><?php echo _('Torwart'); ?></option>
	<option value="A"><?php echo _('Abwehr'); ?></option>
	<option value="M"><?php echo _('Mittelfeld'); ?></option>
	<option value="S"><?php echo _('Sturm'); ?></option>
</select></p>
<p><select name="staerke" size="1" style="width:200px">
	<option value="no"><?php echo _('Jede Stärke'); ?></option>
	<option value="0-3"><?php echo __('Stärke %1$d bis %2$d', 0, 3); ?></option>
	<option value="3-6"><?php echo __('Stärke %1$d bis %2$d', 3, 6); ?></option>
	<option value="6-9"><?php echo __('Stärke %1$d bis %2$d', 6, 9); ?></option>
	<option value="0-0"><?php echo __('Stärke %d', 0); ?></option>
	<option value="1-1"><?php echo __('Stärke %d', 1); ?></option>
	<option value="2-2"><?php echo __('Stärke %d', 2); ?></option>
	<option value="3-3"><?php echo __('Stärke %d', 3); ?></option>
	<option value="4-4"><?php echo __('Stärke %d', 4); ?></option>
	<option value="5-5"><?php echo __('Stärke %d', 5); ?></option>
	<option value="6-6"><?php echo __('Stärke %d', 6); ?></option>
	<option value="7-7"><?php echo __('Stärke %d', 7); ?></option>
	<option value="8-8"><?php echo __('Stärke %d', 8); ?></option>
	<option value="9-9"><?php echo __('Stärke %d', 9); ?></option>
</select></p>
<p><input type="submit" value="<?php echo _('Suchen'); ?>" /></p>
</form>
<h1><?php echo _('Transfermarkt | Ausleihen'); ?></h1>
<?php
// AM ANFANG NOCH KEINE TRANSFERS ANFANG
if ($loggedin == 1 && $_SESSION['pMaxGebot'] == 0) {
	echo '<p>'._('Bist Du wirklich sicher, dass Du schon eine Verstärkung für Dein Team brauchst?').'</p>';
	echo '<p>'._('Der Vorstand empfiehlt Dir, als neuer Trainer in den ersten zwei Stunden auf Transfers zu verzichten.').'</p>';
	echo '<p>'.__('Du solltest Dir zuerst einmal %1$s ansehen und versuchen, eine erste %2$s daraus zu formen.', '<a href="/kader.php">'._('Deinen Kader').'</a>', '<a href="/aufstellung.php">'._('Mannschaft').'</a>').'</p>';
	include 'zz3.php';
	exit;
}
// AM ANFANG NOCH KEINE TRANSFERS ENDE
if ($loggedin == 1) {
    $multiListe = explode('-', $_SESSION['multiAccountList']);
}
else {
    $multiListe = array();
}
$weg1 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE transfermarkt > 999998 AND team NOT IN (SELECT team FROM ".$prefix."users)"; // Computer-Spieler da PC nicht antworten kann
$weg2 = mysql_query($weg1);
if (isset($_GET['id']) && $loggedin == 1 && $cookie_id != CONFIG_DEMO_USER) {
	if ($cookie_team != '__'.$cookie_id) {
		$ids = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
		$anfa = "SELECT team FROM ".$prefix."spieler WHERE ids = '".$ids."' AND transfermarkt > 999998";
		$anfb = mysql_query($anfa);
		$anfc = mysql_fetch_assoc($anfb);
		$anf_besitzer = $anfc['team'];
		if (!isset($transfers_mit_team[$anf_besitzer])) {
			$transfers_mit_team[$anf_besitzer] = 0;
		}
		$anf01 = "SELECT COUNT(*) FROM ".$prefix."transfermarkt_leihe WHERE spieler = '".$ids."' AND bieter = '".$cookie_teamname."' AND akzeptiert = 0";
		$anf02 = mysql_query($anf01);
		$anf03 = mysql_result($anf02, 0);
		if ($_SESSION['transferGesperrt'] == FALSE) {
			if (isset($_GET['praemie']) && $anf03 == 0) {
				$praemie = bigintval($_GET['praemie']);
				if ($praemie > 350000) { $praemie = 0; }
				if ($transfers_mit_team[$anf_besitzer] >= 2) { // nur 2 Transfers
					addInfoBox(_('Du kannst den Spieler nicht ausleihen: 2-Transfers-Sperre'));
				}
				else {
					$anf1 = "INSERT INTO ".$prefix."transfermarkt_leihe (besitzer, spieler, bieter, praemie, zeit) VALUES ('".$anf_besitzer."', '".$ids."', '".$cookie_teamname."', ".$praemie.", ".time().")";
					$anf2 = mysql_query($anf1);
					$transferLog1 = "INSERT INTO ".$prefix."transfers_gebote (spieler, datum, bieter, bieterIP, betrag) VALUES ('".$ids."', ".time().", '".$cookie_team."', '".getUserIP()."', 1)";
					$transferLog2 = mysql_query($transferLog1);
					addInfoBox(_('Deine Anfrage für diesen Spieler wurde gesendet.'));
					setTaskDone('market_borrow');
				}
			}
			elseif ($anf03 != 0) {
				$anf1 = "DELETE FROM ".$prefix."transfermarkt_leihe WHERE spieler = '".$ids."' AND bieter = '".$cookie_teamname."' AND akzeptiert = 0";
				$anf2 = mysql_query($anf1);
				addInfoBox(_('Deine Anfrage wurde zurückgezogen.'));
			}
		}
		else {
            addInfoBox(__('Du bist noch für den Transfermarkt %1$s. Wenn Dir unklar ist, warum, frage bitte ein %2$s.', '<a class="inText" href="/sanktionen.php">'._('gesperrt').'</a>', '<a class="inText" href="/post_schreiben.php?id=18a393b5e23e2b9b4da106b06d8235f3">'._('Team-Mitglied').'</a>'));
		}
	}
}
?>
<table>
<thead><tr class="odd"><th scope="col"><?php echo _('MT').'</th><th scope="col">'._('Spieler').'</th><th scope="col">'._('AL').'</th><th scope="col">'._('ST').'</th><th scope="col">'._('Prämie p.P.').'</th><th scope="col">'._('Aktion').'</th></tr></thead>'; ?>
<tbody>
<?php
$entryToMark = '';
if (isset($_GET['mark'])) {
	$entryToMark = trim(strip_tags($_GET['mark']));
}

$laufende_verhandlungen = array();
if ($loggedin == 1) {
    $lauf1 = "SELECT spieler FROM ".$prefix."transfermarkt_leihe WHERE bieter = '".$cookie_teamname."' AND akzeptiert = 0";
    $lauf2 = mysql_query($lauf1);
    while ($lauf3 = mysql_fetch_assoc($lauf2)) {
        $laufende_verhandlungen[] = $lauf3['spieler'];
    }
}

$zusatzbedingungen = "";
$value_for_wiealt = 'no';
$value_for_position = 'no';
$value_for_staerke = 'no';
if (isset($_GET['wiealt']) && isset($_GET['position']) && isset($_GET['staerke'])) {
	$wiealt_werte = array('no', '17-20', '17-23', '20-23', '20-26', '23-26', '23-29', '26-29', '26-32', '29-32', '29-35', '32-35');
	$position_werte = array('no', 'T', 'A', 'M', 'S');
	$staerke_werte = array('no', '0-3', '3-6', '6-9', '0-0', '1-1', '2-2', '3-3', '4-4', '5-5', '6-6', '7-7', '8-8', '9-9');
	if (in_array($_GET['wiealt'], $wiealt_werte)) {
		if (in_array($_GET['position'], $position_werte)) {
			if (in_array($_GET['staerke'], $staerke_werte)) {
				if ($_GET['wiealt'] != 'no') {
					$value_for_wiealt = $_GET['wiealt'];
					$temp1 = intval(substr($_GET['wiealt'], 0, 2))*365;
					$temp2 = intval(substr($_GET['wiealt'], 3, 2))*365+365;
					$zusatzbedingungen .= " AND a.wiealt > ".$temp1." AND a.wiealt < ".$temp2;
				}
				if ($_GET['position'] != 'no') {
					$value_for_position = $_GET['position'];
					$zusatzbedingungen .= " AND a.position = '".mysql_real_escape_string($_GET['position'])."'";
				}
				if ($_GET['staerke'] != 'no') {
					$value_for_staerke = $_GET['staerke'];
					$temp1 = intval(substr($_GET['staerke'], 0, 1))-0.1;
					$temp2 = intval(substr($_GET['staerke'], 2, 1))+1;
					$zusatzbedingungen .= " AND a.staerke > ".$temp1." AND a.staerke < ".$temp2;
				}
			}
		}
	}
}
// ZEITLIMIT + 20sec ZUR SICHERHEIT
$sql1 = "SELECT a.ids, a.vorname, a.nachname, a.position, a.staerke, a.wiealt, a.team, a.transfermarkt FROM ".$prefix."spieler AS a WHERE a.transfermarkt > 999998".$zusatzbedingungen." ORDER BY a.staerke DESC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
if (mysql_num_rows($sql2) == 0) {
	echo '<tr><td colspan="6">'._('Zurzeit keine Angebote!').'</td></tr>';
}
else {
    while ($sql3 = mysql_fetch_assoc($sql2)) {
        if (!isset($transfers_mit_team[$sql3['team']])) {
            $transfers_mit_team[$sql3['team']] = 0;
        }
		echo '<tr';
		if ($sql3['ids'] == $entryToMark) { echo ' style="background-color:#eee"'; }
		echo '><td>'.$sql3['position'].'</td><td class="link"><a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a></td>';
		echo '<td>'.floor($sql3['wiealt']/365).'</td><td>'.number_format($sql3['staerke'], 1, ',', '.').'</td>';
		// PRÄMIE ANFANG
		switch ($sql3['transfermarkt']) {
			case 5000000: $leihprämie = '50.000 €'; $prämieInt = 50000; break;
			case 10000000: $leihprämie = '100.000 €'; $prämieInt = 100000; break;
			case 15000000: $leihprämie = '150.000 €'; $prämieInt = 150000; break;
			case 20000000: $leihprämie = '200.000 €'; $prämieInt = 200000; break;
			case 25000000: $leihprämie = '250.000 €'; $prämieInt = 250000; break;
			case 30000000: $leihprämie = '300.000 €'; $prämieInt = 300000; break;
			case 35000000: $leihprämie = '350.000 €'; $prämieInt = 350000; break;
			default: $leihprämie = 'keine'; $prämieInt = 0; break;
		}
		echo '<td>'.$leihprämie.'</td>';
		// PRÄMIE ENDE
		echo '<td class="link">';
        if ($loggedin != 1) {
            echo '&nbsp;'; // not signed in
        }
		elseif ($sql3['team'] == $cookie_team) {
			echo '&nbsp;'; // eigener Spieler
		}
		elseif (in_array($sql3['ids'], $laufende_verhandlungen)) {
			echo '<a href="/transfermarkt_leihe.php?id='.$sql3['ids'].'" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Zurückziehen').'</a>';
		}
		elseif (in_array($sql3['team'], $multiListe)) {
			echo '&nbsp;'; // Multi-Account
		}
		else {
			if ($transfers_mit_team[$sql3['team']] >= 2) {
				echo '&nbsp;'; // 2-Transfers-Sperre
			}
			else {
				echo '<a href="/transfermarkt_leihe.php?id='.$sql3['ids'].'&praemie='.$prämieInt.'" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')">'._('Anfragen').'</a>';
			}
		}
		echo '</td></tr>';
    }
}
?>
</tbody>
</table>
<p><strong><?php echo _('Hinweis:').'</strong> '._('Indem Du auf &quot;Anfragen&quot; klickst, schickst Du dem Besitzer des Spielers ein Angebot, den Spieler bis Saisonende zu übernehmen. Der Besitzer des Spielers hat dann die Möglichkeit, das Angebot entweder abzulehnen oder anzunehmen. Wenn er Dein Angebot annimmt, gehört der Spieler sofort Dir (bis Saisonende).'); ?></p>
<p><strong><?php echo _('Überschriften:').'</strong> '._('MT: Mannschaftsteil, AL: Alter, ST: Stärke'); ?></p>
<p><strong><?php echo _('Mannschaftsteile:').'</strong> '._('T: Torwart, A: Abwehr, M: Mittelfeld, S: Sturm'); ?></p>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt='.$value_for_wiealt.'&amp;position='.$value_for_position.'&amp;staerke='.$value_for_staerke.'&amp;seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
echo '</div>';
?>
<?php include 'zz3.php'; ?>
