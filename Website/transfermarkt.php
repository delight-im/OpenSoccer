<?php include 'zz1.php'; ?>
<title><?php echo _('Transfermarkt | Kaufen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Markt durchsuchen'); ?></h1>
<?php
define('MIN_GEBOT', 0); // in 10 Mio
define('MAX_GEBOT', 40); // in 10 Mio
// FILTER ANFANG
$zusatzbedingungen = "";
$value_for_wiealt_start = 17;
$value_for_wiealt_end = 35;
$value_for_position = 'all';
$value_for_staerke_start = 0;
$value_for_staerke_end = 9;
$value_for_maxGebot_start = MIN_GEBOT;
$value_for_maxGebot_end = MAX_GEBOT;
if (isset($_GET['wiealt_start']) && isset($_GET['wiealt_end']) && isset($_GET['position']) && isset($_GET['staerke_start']) && isset($_GET['staerke_end']) && isset($_GET['maxGebot_start']) && isset($_GET['maxGebot_end'])) {
	if ($_GET['wiealt_start'] >= 17 && $_GET['wiealt_start'] <= 35 && $_GET['wiealt_end'] >= 17 && $_GET['wiealt_end'] <= 35) {
		if ($_GET['position'] == 'T' OR $_GET['position'] == 'A' OR $_GET['position'] == 'M' OR $_GET['position'] == 'S' OR $_GET['position'] == 'all') {
			if ($_GET['staerke_start'] >= 0 && $_GET['staerke_start'] <= 9 && $_GET['staerke_end'] >= 0 && $_GET['staerke_end'] <= 9) {
				if ($_GET['maxGebot_start'] >= 0 && $_GET['maxGebot_start'] <= ($value_for_maxGebot_end*10000000) && $_GET['maxGebot_end'] >= 0 && $_GET['maxGebot_end'] <= ($value_for_maxGebot_end*10000000)) {
					$value_for_wiealt_start = intval($_GET['wiealt_start']);
					$value_for_wiealt_end = intval($_GET['wiealt_end']);
					$zusatzbedingungen .= " AND b.wiealt > ".intval($value_for_wiealt_start*365)." AND b.wiealt < ".intval($value_for_wiealt_end*365+365);
					$value_for_position = mysql_real_escape_string(trim(strip_tags($_GET['position'])));
					if ($value_for_position != 'all') {
						$zusatzbedingungen .= " AND b.position = '".$value_for_position."'";
					}
					$value_for_staerke_start = intval($_GET['staerke_start']);
					$value_for_staerke_end = intval($_GET['staerke_end']);
					$zusatzbedingungen .= " AND b.staerke > ".floatval($value_for_staerke_start-0.1)." AND b.staerke < ".floatval($value_for_staerke_end+1.0);
					$value_for_maxGebot_start = intval($_GET['maxGebot_start']);
					$value_for_maxGebot_end = intval($_GET['maxGebot_end']);
					$zusatzbedingungen .= " AND a.betrag_highest > ".intval($value_for_maxGebot_start*10000000-1)." AND a.betrag_highest < ".intval($value_for_maxGebot_end*10000000+1);
				}
			}
		}
	}
}
// FILTER ENDE
?>
<form action="/transfermarkt.php" method="get" accept-charset="utf-8">
<p><select name="wiealt_start" size="1" style="width:95px">
	<?php
	for ($i = 17; $i <= 35; $i++) {
		echo '<option value="'.$i.'"';
		if ($i == $value_for_wiealt_start) { echo ' selected="selected"'; }
		echo '>'.__('von %d', $i).'</option>';
	}
	?>
</select> 
<select name="wiealt_end" size="1" style="width:160px">
	<?php
	for ($i = 17; $i <= 35; $i++) {
		echo '<option value="'.$i.'"';
		if ($i == $value_for_wiealt_end) { echo ' selected="selected"'; }
		echo '>'.__('bis %d Jahre', $i).'</option>';
	}
	?>
</select></p>
<p><select name="position" size="1" style="width:260px">
	<?php
	echo '<option value="all"';
	if ($value_for_position == 'all') { echo ' selected="selected"'; }
	echo '>'._('Jede Position').'</option>';
	echo '<option value="T"';
	if ($value_for_position == 'T') { echo ' selected="selected"'; }
	echo '>'._('Torwart').'</option>';
	echo '<option value="A"';
	if ($value_for_position == 'A') { echo ' selected="selected"'; }
	echo '>'._('Abwehr').'</option>';
	echo '<option value="M"';
	if ($value_for_position == 'M') { echo ' selected="selected"'; }
	echo '>'._('Mittelfeld').'</option>';
	echo '<option value="S"';
	if ($value_for_position == 'S') { echo ' selected="selected"'; }
	echo '>'._('Sturm').'</option>';
	?>
</select></p>
<p><select name="staerke_start" size="1" style="width:95px">
	<?php
	for ($i = 0; $i <= 9; $i++) {
		echo '<option value="'.$i.'"';
		if ($i == $value_for_staerke_start) { echo ' selected="selected"'; }
		echo '>'.__('von %d,0', $i).'</option>';
	}
	?>
</select> 
<select name="staerke_end" size="1" style="width:160px">
	<?php
	for ($i = 0; $i <= 9; $i++) {
		echo '<option value="'.$i.'"';
		if ($i == $value_for_staerke_end) { echo ' selected="selected"'; }
		echo '>'.__('bis %d,9 Stärke', $i).'</option>';
	}
	?>
</select></p>
<p><select name="maxGebot_start" size="1" style="width:95px">
	<?php
	for ($i = 0; $i <= MAX_GEBOT; $i++) {
		echo '<option value="'.$i.'"';
		if ($i == $value_for_maxGebot_start) { echo ' selected="selected"'; }
		echo '>'.__('von %d', intval($i*10)).'</option>';
	}
	?>
</select> 
<select name="maxGebot_end" size="1" style="width:160px">
	<?php
	for ($i = 0; $i <= MAX_GEBOT; $i++) {
		echo '<option value="'.$i.'"';
		if ($i == $value_for_maxGebot_end) { echo ' selected="selected"'; }
		echo '>'.__('bis %d Mio. €', intval($i*10)).'</option>';
	}
	?>
</select></p>
<p><input type="submit" value="<?php echo _('Suchen'); ?>" /></p>
</form>
<h1><?php echo _('Transfermarkt | Kaufen'); ?></h1>
<?php
// SPIELER MIT FALSCHEM BESITZER RUNTER ANFANG
$a1 = "SELECT b.ids FROM ".$prefix."transfermarkt AS a JOIN ".$prefix."spieler AS b ON a.spieler = b.ids WHERE b.team != 'frei' AND a.besitzer != b.team";
$a2 = mysql_query($a1);
while ($a3 = mysql_fetch_assoc($a2)) {
	$a4 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$a3['ids']."'";
	$a5 = mysql_query($a4);
}
// SPIELER MIT FALSCHEM BESITZER RUNTER ENDE
// ALTE AUKTIONEN BEENDEN UND VERTRAEGE ABSCHLIESSEN ANFANG
$sql1 = "SELECT spieler, besitzer, bieter_highest, betrag_highest, autorestart FROM ".$prefix."transfermarkt WHERE ende < ".time();
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
    if ($sql3['bieter_highest'] != 'keiner') {
        $sql6 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$sql3['spieler']."'";
        $sql7 = mysql_query($sql6);
    	// WEITERE SPIELERDATEN HOLEN ANFANG
        $getmanager1 = "SELECT vorname, nachname, marktwert, spiele_verein, staerke FROM ".$prefix."spieler WHERE ids = '".$sql3['spieler']."'";
        $getmanager2 = mysql_query($getmanager1);
        $getmanager3 = mysql_fetch_assoc($getmanager2);
    	// WEITERE SPIELERDATEN HOLEN ENDE
        $vertragsende = endOfDay(getTimestamp('+29 days')); // 29 Tage
		$neuesGehalt = round(pow(($sql3['betrag_highest']/1000), (1.385+0.006*3)));
        $sql4 = "UPDATE ".$prefix."spieler SET transfermarkt = 0, leiher = 'keiner', vertrag = ".$vertragsende.", gehalt = ".$neuesGehalt.", startelf_Liga = 0, startelf_Pokal = 0, startelf_Cup = 0, startelf_Test = 0, spiele_verein = 0, moral = 100, frische = ".getRegularFreshness(GameTime::getMatchDay()).", team = '".$sql3['bieter_highest']."' WHERE ids = '".$sql3['spieler']."'";
        $sql5 = mysql_query($sql4);
        $move1 = "INSERT INTO ".$prefix."transfers (spieler, besitzer, bieter, datum, gebot, damaligerWert, spiele_verein, damaligeStaerke) VALUES ('".$sql3['spieler']."', '".$sql3['besitzer']."', '".$sql3['bieter_highest']."', ".time().", ".$sql3['betrag_highest'].", ".$getmanager3['marktwert'].", ".$getmanager3['spiele_verein'].", ".$getmanager3['staerke'].")";
        $move2 = mysql_query($move1);
        if ($move2 !== FALSE) {
            // TRANSFERSTEUER ANFANG
			$transfersteuer = round($sql3['betrag_highest']*0.05);
			$kontoabzug = round($sql3['betrag_highest']*1.05);
            $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['bieter_highest']."', 'Transfersteuer', -".$transfersteuer.", ".time().")";
            $buch2 = mysql_query($buch1);
            // TRANSFERSTEUER ENDE
            $sql4 = "UPDATE ".$prefix."teams SET konto = konto-".$kontoabzug." WHERE ids = '".$sql3['bieter_highest']."'";
            $sql5 = mysql_query($sql4);
            $sql4 = "UPDATE ".$prefix."teams SET konto = konto+".$sql3['betrag_highest']." WHERE ids = '".$sql3['besitzer']."'";
            $sql5 = mysql_query($sql4);
            $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['bieter_highest']."', 'Ablöse', -".$sql3['betrag_highest'].", ".time().")";
            $buch2 = mysql_query($buch1);
            $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['besitzer']."', 'Ablöse', ".$sql3['betrag_highest'].", ".time().")";
            $buch2 = mysql_query($buch1);
            $getmanager4 = $getmanager3['vorname'].' '.$getmanager3['nachname'];
            $formulierung = __('Du hast den Spieler %1$s fur %2$s € gekauft.', '<a href="/spieler.php?id='.$sql3['spieler'].'">'.$getmanager4.'</a>', number_format($sql3['betrag_highest'], 0, ',', '.'));
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$sql3['bieter_highest']."', '".$formulierung."', 'Transfers', ".time().")";
            $sql8 = mysql_query($sql7);
            $formulierung = __('Du hast den Spieler %1$s fur %2$s € verkauft.', '<a href="/spieler.php?id='.$sql3['spieler'].'">'.$getmanager4.'</a>', number_format($sql3['betrag_highest'], 0, ',', '.'));
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$sql3['besitzer']."', '".$formulierung."', 'Transfers', ".time().")";
            $sql8 = mysql_query($sql7);
            // SPIELER VON BEOBACHTUNGSLISTE RUNTERNEHMEN BEIM GEWINNER ANFANG
            $watch1 = "DELETE FROM ".$prefix."transfermarkt_watch WHERE team = '".$sql3['bieter_highest']."' AND spieler_id = '".$sql3['spieler']."'";
            $watch2 = mysql_query($watch1);
            // SPIELER VON BEOBACHTUNGSLISTE RUNTERNEHMEN BEIM GEWINNER ENDE
        }
    }
    else {
        if ($sql3['autorestart'] != 0) {
            $sql6 = "UPDATE ".$prefix."transfermarkt SET ende = ".getTimestamp('+'.$sql3['autorestart'].' hours').", bieter_highest = 'keiner' WHERE spieler = '".$sql3['spieler']."'";
            $sql7 = mysql_query($sql6);
        }
        else {
            $sql6 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$sql3['spieler']."'";
            $sql7 = mysql_query($sql6);
            $sql6 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE ids = '".$sql3['spieler']."'";
            $sql7 = mysql_query($sql6);
        }
    }
}
// ALTE AUKTIONEN BEENDEN UND VERTRAEGE ABSCHLIESSEN ENDE
?>
<table>
<thead><tr class="odd"><th scope="col"><?php echo _('MT').'</th><th scope="col">'._('Spieler').'</th><th scope="col">'._('AL').'</th><th scope="col">'._('ST').'</th><th scope="col">'._('Gebot').'</th><th scope="col">'._('Noch'); ?></th></tr></thead>
<tbody>
<?php
$sql1 = "SELECT a.spieler, a.gebote, a.betrag_highest, a.ende, a.besitzer, b.vorname, b.nachname, b.position, b.staerke, b.talent, b.wiealt FROM ".$prefix."transfermarkt AS a";
$sql1 .= " JOIN ".$prefix."spieler AS b ON a.spieler = b.ids WHERE a.ende > ".getTimestamp('+20 seconds').$zusatzbedingungen." ORDER BY a.ende ASC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1); // Zeitlimit + 20 Sekunden zur Sicherheit
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
if (mysql_num_rows($sql2) == 0) {
	echo '<tr><td colspan="5">'._('Zurzeit keine Auktionen!').'</td></tr>';
}
else {
    while ($sql3 = mysql_fetch_assoc($sql2)) {
		echo '<tr><td>'.$sql3['position'].'</td><td class="link"><a href="/transfermarkt_auktion.php?id='.$sql3['spieler'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a></td><td>'.floor($sql3['wiealt']/365).'</td><td>'.number_format($sql3['staerke'], 1, ',', '.');
		$schaetzungVomScout = schaetzungVomScout($cookie_team, $cookie_scout, $sql3['spieler'], $sql3['talent'], $sql3['staerke'], $sql3['besitzer']);
		echo ' <span style="color:#999">('.number_format($schaetzungVomScout, 1, ',', '.').')</span>';
		echo '</td><td>['.$sql3['gebote'].'] '.number_format($sql3['betrag_highest'], 0, ',', '.').' €</td><td>';
		$noch_zeit = intval(($sql3['ende']-time())/60);
		if ($noch_zeit < 61) {
			echo '<span style="color:red" title="'.date('d.m.Y H:i', $sql3['ende']).'">'.$noch_zeit.' min</span>';
		}
		else {
			if ($noch_zeit < 121) {
				echo '<span title="'.date('d.m.Y H:i', $sql3['ende']).'">'.$noch_zeit.' min</span>';
			}
			else {
				echo '<span title="'.date('d.m.Y H:i', $sql3['ende']).'">'.intval($noch_zeit/60).' h</span>';
			}
		}
		echo '</td></tr>';
    }
}
?>
</tbody>
</table>
<p><strong><?php echo _('Hinweis:').'</strong> <!-- Der Besitzer eines Spielers kann die Auktion bis 30 Minuten vor Ende noch abbrechen. -->'._('Vor dem Höchstgebot steht in eckigen Klammern die Anzahl der abgegebenen Gebote.'); ?></p>
<p><strong><?php echo _('Überschriften:').'</strong> '._('MT: Mannschaftsteil, AL: Alter, ST: Stärke'); ?></p>
<p><strong><?php echo _('Mannschaftsteile:').'</strong> '._('T: Torwart, A: Abwehr, M: Mittelfeld, S: Sturm'); ?></p>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?wiealt_start='.$value_for_wiealt_start.'&amp;wiealt_end='.$value_for_wiealt_end.'&amp;position='.$value_for_position.'&amp;staerke_start='.$value_for_staerke_start.'&amp;staerke_end='.$value_for_staerke_end.'&amp;maxGebot_start='.$value_for_maxGebot_start.'&amp;maxGebot_end='.$value_for_maxGebot_end.'&amp;seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
echo '</div>';
?>
<?php include 'zz3.php'; ?>
