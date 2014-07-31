<?php include 'zz1.php'; ?>
<title><?php echo _('Internationaler Pokal'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php
// LAENDER HOLEN ANFANG
$ld1 = "SELECT ids, name FROM ".$prefix."ligen";
$ld2 = mysql_query($ld1);
$laender = array();
$ligIDToLand = array();
$punkte_der_ligen = array(); // 5-Jahres-Wertung
$anzahlTeilnehmer = array(); // Divisor
while ($ld3 = mysql_fetch_assoc($ld2)) {
	$land = substr($ld3['name'], 0, -2);
	switch ($land) {
		case 'Österreich': $ld_kurz = 'at'; break;
		case 'Belgien': $ld_kurz = 'be'; break;
		case 'Deutschland': $ld_kurz = 'de'; break;
		case 'England': $ld_kurz = 'en'; break;
		case 'Frankreich': $ld_kurz = 'fr'; break;
		case 'Italien': $ld_kurz = 'it'; break;
		case 'Niederlande': $ld_kurz = 'nl'; break;
		case 'Polen': $ld_kurz = 'pl'; break;
		case 'Portugal': $ld_kurz = 'pt'; break;
		case 'Schweden': $ld_kurz = 'se'; break;
		case 'Schweiz': $ld_kurz = 'ch'; break;
		case 'Spanien': $ld_kurz = 'es'; break;
		case 'Türkei': $ld_kurz = 'tr'; break;
		default: $ld_kurz = 'de'; break;
	}
	$punkte_der_ligen[$ld3['ids']] = 0; // 5-Jahres-Wertung
	$anzahlTeilnehmer[$ld3['ids']] = 0; // Divisor
	$laender[$ld3['ids']] = $ld_kurz;
	$ligIDToLand[$ld3['ids']] = $land;
}
// LAENDER HOLEN ENDE
if (isset($laender[$cookie_liga])) {
	echo '<style type="text/css">';
	echo '<!--';
	echo '.land_'.$laender[$cookie_liga].' { background-color: #eee; }';
	echo '-->';
	echo '</style>';
}
?>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
setTaskDone('pokal_standings');
if (isset($_POST['nachricht']) && $cookie_id != CONFIG_DEMO_USER) {
	// CHAT-SPERREN ANFANG
	$sql1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) > 0) {
		$sql3 = mysql_fetch_assoc($sql2);
		$chatSperreBis = $sql3['MAX(chatSperre)'];
		if ($chatSperreBis > 0 && $chatSperreBis > time()) {
			addInfoBox(__('Du bist noch bis zum %1$d für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das %2$s', date('d.m.Y H:i', $chatSperreBis), '<a class="inText" href="/wio.php">'._('Support-Team.').'</a>'));
			include 'zz3.php';
			exit;
		}
	}
	// CHAT-SPERREN ENDE
	$nachricht = mysql_real_escape_string(trim(strip_tags($_POST['nachricht'])));
	$sql1 = "INSERT INTO ".$prefix."chats_pokal (user, zeit, nachricht) VALUES ('".$cookie_id."', '".time()."', '".$nachricht."')";
	$sql2 = mysql_query($sql1);
}
?>
<h1><?php echo _('Internationaler Pokal'); ?></h1>
<p><?php echo _('Für diesen Wettbewerb qualifizieren sich die 32 besten Teams des Spiels. Das sind alle Erstplatzierten, alle Zweitplatzierten und dazu die sechs besten Drittplatzierten.'); ?></p>
<p><?php echo _('Es gibt fünf Runden, die jeweils ausgelost werden. In einem Hin- und einem Rückspiel mit Auswärtstorregel ermitteln die Teams den Sieger, der dann die nächste Runde erreicht.'); ?></p>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/pokal_ergebnisse.html';
if (file_exists($tmp_dateiname)) {
	if (filemtime($tmp_dateiname) > (time()-7200)) {
		$tmp_liga_cache = file_get_contents($tmp_dateiname);
		if (strlen($tmp_liga_cache) > 0) {
			$ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
			echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
            $temp = TRUE;
			?>
<h1><?php echo _('Deine Nachricht'); ?></h1>
<form action="/pokal.php" method="post" accept-charset="utf-8">
<p><input type="text" name="nachricht" style="width:60%" /> <input type="submit" value="<?php echo _('Eintragen'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<h1><?php echo _('Manager-Talk'); ?></h1>
<?php
if (isset($_GET['delEntry']) && $cookie_id != CONFIG_DEMO_USER) {
	$delEntry = mysql_real_escape_string(trim(strip_tags($_GET['delEntry'])));
	$addSql = " AND user = '".$cookie_id."'";
	if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { $addSql = ""; }
	$gb_in1 = "DELETE FROM ".$prefix."chats_pokal WHERE id = ".$delEntry.$addSql;
	$gb_in2 = mysql_query($gb_in1);
}
$sql1 = "SELECT a.id, a.user, a.zeit, a.nachricht, b.username FROM ".$prefix."chats_pokal AS a JOIN ".$prefix."users AS b ON a.user = b.ids ORDER BY a.zeit DESC LIMIT 0, 20";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	echo '<p><b>'.__('%1$s schrieb am %2$s:', displayUsername($sql3['username'], $sql3['user']), date('d.m.Y, H:i', $sql3['zeit']));
	if ($sql3['user'] == $cookie_id OR $_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
		echo ' <a href="/pokal.php?delEntry='.$sql3['id'].'">[Löschen]</a>';
	}
	echo '</b><br />'.$sql3['nachricht'].'</p>';
}
?>			
			<?php
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
$getteam1 = "SELECT ids, name, liga FROM ".$prefix."teams WHERE pokalrunde > 0";
$getteam2 = mysql_query($getteam1);
$team_ids = array();
while ($getteam3 = mysql_fetch_assoc($getteam2)) {
	$team_ids[$getteam3['name']] = array($getteam3['ids'], $getteam3['liga']);
	$anzahlTeilnehmer[$getteam3['liga']]++;
}
// TEAM-IDS HOLEN ENDE
$spiele = array();
$spiele['Vorrunde'] = array();
$spiele['Achtelfinale'] = array();
$spiele['Viertelfinale'] = array();
$spiele['Halbfinale'] = array();
$spiele['Finale'] = array();
$wrij1 = "SELECT id, team1, team2, ergebnis, liga, kennung, typ, datum FROM ".$prefix."spiele WHERE typ = 'Pokal' ORDER BY datum ASC";
$wrij2 = mysql_query($wrij1);
while ($wrij3 = mysql_fetch_assoc($wrij2)) {
	switch ($wrij3['liga']) {
		case 'Pokal_Runde_1': $rundenarray = _('Vorrunde'); break;
		case 'Pokal_Runde_2': $rundenarray = _('Achtelfinale'); break;
		case 'Pokal_Runde_3': $rundenarray = _('Viertelfinale'); break;
		case 'Pokal_Runde_4': $rundenarray = _('Halbfinale'); break;
		case 'Pokal_Runde_5': $rundenarray = _('Finale'); break;
		default: $rundenarray = ''; break;
	}
	if ($rundenarray != '') {
        if (!isset($spiele[$rundenarray][$wrij3['kennung']])) {
            // LIVE ODER ERGEBNIS ANFANG
            if ($wrij3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $wrij3['datum'])) {
                $ergebnis_live = 'LIVE';
            }
            else {
                $ergebnis_live = $wrij3['ergebnis'];
            }
            // LIVE ODER ERGEBNIS ENDE
            $ergebnis_string = '<a href="/spielbericht.php?id='.$wrij3['id'].'">'.$ergebnis_live.'</a>';
            $spiele[$rundenarray][$wrij3['kennung']] = array('team1'=>$wrij3['team1'], 'team2'=>$wrij3['team2'], 'ergebnis1'=>$ergebnis_string, 'ergebnis2'=>'');
        }
        else {
            // LIVE ODER ERGEBNIS ANFANG
            if ($wrij3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $wrij3['datum'])) {
                $ergebnis_live = 'LIVE';
            }
            else {
                $ergebnis_live = ergebnis_drehen($wrij3['ergebnis']);
            }
            // LIVE ODER ERGEBNIS ENDE
            $ergebnis_string = '<a href="/spielbericht.php?id='.$wrij3['id'].'">'.$ergebnis_live.'</a>';
            $spiele[$rundenarray][$wrij3['kennung']]['ergebnis2'] = $ergebnis_string;
        }
	}
	// 5-JAHRES-WERTUNG ANFANG
	$tore_ha = explode(':', $wrij3['ergebnis'], 2);
	if ($tore_ha[0] != '-') { // wenn das Ergebnis nicht -:- ist
        if ($tore_ha[0] > $tore_ha[1]) {
        	$punkte_der_ligen[$team_ids[$wrij3['team1']][1]] += 2;
        }
        elseif ($tore_ha[1] > $tore_ha[0]) {
        	$punkte_der_ligen[$team_ids[$wrij3['team2']][1]] += 2;
        }
        else {
        	$punkte_der_ligen[$team_ids[$wrij3['team1']][1]] += 1;
        	$punkte_der_ligen[$team_ids[$wrij3['team2']][1]] += 1;
        }
	}
	// 5-JAHRES-WERTUNG ENDE
}
?>
<?php
$spiele = array_reverse($spiele); // aktuellste Runde ganz oben
for ($i = 1; $i <= 6; $i++) {
	$spiels = each($spiele);
	$spielliste = $spiels['value'];
	if (count($spielliste) == 0) { continue; }
	$tmp_liga_cache .= '<h1>'.$spiels['key'].'</h1>';
	$tmp_liga_cache .= '<p><table><thead><tr class="odd"><th scope="col">'._('Team 1').'</th><th scope="col">&nbsp;</th><th scope="col">'._('Team 2').'</th><th scope="col">&nbsp;</th><th scope="col">'._('H').'</th><th scope="col">'._('R').'</th></tr></thead><tbody>';
	foreach ($spielliste as $spiel) {
		$tmp_liga_cache .= '<tr class="land_'.$laender[$team_ids[$spiel['team1']][1]].' land_'.$laender[$team_ids[$spiel['team2']][1]].'"><td class="link"><a href="/team.php?id='.$team_ids[$spiel['team1']][0].'">'.$spiel['team1'].'</a></td><td><img src="/images/flaggen/'.$laender[$team_ids[$spiel['team1']][1]].'.png" alt="" /></td><td class="link"><a href="/team.php?id='.$team_ids[$spiel['team2']][0].'">'.$spiel['team2'].'</a></td><td><img src="/images/flaggen/'.$laender[$team_ids[$spiel['team2']][1]].'.png" alt="" /></td><td class="link">'.$spiel['ergebnis1'].'</td><td class="link">'.$spiel['ergebnis2'].'</td></tr>';
	}
	$tmp_liga_cache .= '</tbody></table></p>';
	$tmp_liga_cache .= '<p><strong>'._('Hinweis:').'</strong> '._('Spiele mit Beteiligung aus dem eigenen Land werden grau hinterlegt.').'</p>';
}
?>
<?php
// 5-SAISON-WERTUNG ANFANG
foreach ($punkte_der_ligen as $key=>$val) {
	if ($val > 0) {
		$wertungsZahl = round($val/$anzahlTeilnehmer[$key], 1);
        $saiwer1 = "UPDATE ".$prefix."ligen SET pkt_saison1 = ".$wertungsZahl." WHERE land = '".$ligIDToLand[$key]."'";
        $saiwer2 = mysql_query($saiwer1);
	}
}
$saiwer3 = "UPDATE ".$prefix."ligen SET pkt_gesamt = (pkt_saison1+pkt_saison2+pkt_saison3+pkt_saison4+pkt_saison5)";
$saiwer4 = mysql_query($saiwer3);
// 5-SAISON-WERTUNG ENDE
$datei = fopen($tmp_dateiname, 'w+');
fwrite($datei, $tmp_liga_cache);
fclose($datei);
$ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
}
?>
<h1><?php echo _('Deine Nachricht'); ?></h1>
<form action="/pokal.php" method="post" accept-charset="utf-8">
<p><input type="text" name="nachricht" style="width:60%" /> <input type="submit" value="<?php echo _('Eintragen'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<h1><?php echo _('Manager-Talk'); ?></h1>
<?php
if (isset($_GET['delEntry'])) {
	$delEntry = mysql_real_escape_string(trim(strip_tags($_GET['delEntry'])));
	$addSql = " AND user = '".$cookie_id."'";
	if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { $addSql = ""; }
	$gb_in1 = "DELETE FROM ".$prefix."chats_pokal WHERE id = ".$delEntry.$addSql;
	$gb_in2 = mysql_query($gb_in1);
}
$sql1 = "SELECT a.id, a.user, a.zeit, a.nachricht, b.username FROM ".$prefix."chats_pokal AS a JOIN ".$prefix."users AS b ON a.user = b.ids ORDER BY a.zeit DESC LIMIT 0, 20";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	echo '<p><b>'.displayUsername($sql3['username'], $sql3['user']).' schrieb am '.date('d.m.Y, H:i', $sql3['zeit']).':';
	if ($sql3['user'] == $cookie_id OR $_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
		echo ' <a href="/pokal.php?delEntry='.$sql3['id'].'">'._('[Löschen]').'</a>';
	}
	echo '</b><br />'.$sql3['nachricht'].'</p>';
}
?>
<?php } else { ?>
<h1><?php echo _('Internationaler Pokal'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
