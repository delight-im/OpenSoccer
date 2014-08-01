<?php if (!isset($_GET['id'])) { exit; } ?>
<?php include 'zz1.php'; ?>
<?php
function extract_kommentar_ergebnis($kommentar) {
    	$ergebnis_str = '';
		$ergebnisPattern = '/ \[([0-9]+:[0-9]+)\]/i';
    	$found_ergebnis = preg_match($ergebnisPattern, $kommentar, $ergebnis_array);
    	if (isset($ergebnis_array[1])) {
    		$ergebnis_str = ' '.$ergebnis_array[1];
    	}
    	$kommentar_str = preg_replace($ergebnisPattern, '', $kommentar);
    	return array($kommentar_str, $ergebnis_str);
}
$sql1 = "SELECT id, team1, team2, ergebnis, typ, tore1, tore2, datum, zuschauer, ballbesitz1, ballbesitz2, fouls1, fouls2, abseits1, abseits2, schuesse1, schuesse2, karte_gelb1, karte_gelb2, karte_rot1, karte_rot2 FROM ".$prefix."spiele WHERE id = '".mysql_real_escape_string($_GET['id'])."'";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
$tid1 = "SELECT ids, elo FROM ".$prefix."teams WHERE name = '".$sql3['team1']."'";
$tid1 = mysql_query($tid1);
$tid1 = mysql_fetch_assoc($tid1);
$eloTeam1 = $tid1['elo'];
$tid1 = $tid1['ids'];
$tid2 = "SELECT ids, elo FROM ".$prefix."teams WHERE name = '".$sql3['team2']."'";
$tid2 = mysql_query($tid2);
$tid2 = mysql_fetch_assoc($tid2);
$eloTeam2 = $tid2['elo'];
$tid2 = $tid2['ids'];
// LIVE-SCORING ANFANG
if ($sql3['typ'] == $live_scoring_spieltyp_laeuft && date('d.m.Y', time()) == date('d.m.Y', $sql3['datum'])) {
	$live_scoring_meldung = '<span style="color:red">'._('Spiel läuft').'</span>';
}
else {
	$live_scoring_meldung = '';
}
// LIVE-SCORING ENDE
?>
<title><?php echo $sql3['team1']; ?> - <?php echo $sql3['team2']; ?> <?php if ($live_scoring_meldung != '') { echo _('LIVE'); } else { echo $sql3['ergebnis']; } ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
div.matchReport {
	position: relative;
	width: 100%;
	height: 36px;
	margin-top: 1em;
	background: transparent url(/images/match_score_back.png) scroll repeat-x left center;
}
div.matchReport div.scores {
	width: 108px;
	height: 36px;
	margin: 0 auto;
}
div.matchReport div.scores span.score {
	display: block;
	float: left;
	width: 46px;
	line-height: 36px;
	margin: 0 4px;
	background: transparent url(/images/match_score_highlight.png) scroll repeat-x left center;
	text-align: center;
	vertical-align: middle;
	font-size: 2.0em;
	font-weight: bold;
	color: #fff;
}
div.matchReport span.team {
	display: block;
	width: 40%;
	line-height: 32px;
	margin: 2px 8px;
	vertical-align: middle;
	font-size: 1.3em;
	font-weight: bold;
	color: #fff;
}
div.matchReport span.teamLeft {
	position: absolute;
	left: 0;
	top: 0;
	text-align: left;
}
div.matchReport span.teamRight {
	position: absolute;
	right: 0;
	top: 0;
	text-align: right;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php
if ($live_scoring_meldung != '') {
    $scoreStr = _('LIVE');
    // cut string in two halves (LI...VE)
    $scoreHalf = ceil(mb_strlen($scoreStr) / 2);
	$score1 = mb_substr($scoreStr, 0, $scoreHalf);
	$score2 = mb_substr($scoreStr, $scoreHalf);
}
else {
	$scores = explode(':', $sql3['ergebnis']);
	$score1 = $scores[0];
	$score2 = $scores[1];
}
echo '<div class="matchReport">';
	echo '<span class="team teamLeft">'.$sql3['team1'].'</span>';
	echo '<div class="scores">';
		echo '<span class="score">'.$score1.'</span>';
		echo '<span class="score">'.$score2.'</span>';
		echo '<div style="clear:both;"></div>';
	echo '</div>';
	echo '<span class="team teamRight">'.$sql3['team2'].'</span>';
echo '</div>';
?>
<?php
if ($live_scoring_meldung != '') {
	echo '<p style="text-align:right">';
	echo '<a href="/spielbericht.php?id='.$sql3['id'].'" onclick="location.reload(); return false" class="pagenava">'._('Aktualisieren').'</a>';
	echo '<a href="/liveZentrale.php" class="pagenava">'._('Zur LIVE-Zentrale').'</a>';
	echo '</p>';
}
// TORSCHUTZEN ANFANG
if ($sql3['tore1'] == '') {
	$torschuetzen1 = '-';
}
else {
    $tore1 = explode('-', $sql3['tore1']);
    $torschuetzen1 = '';
    foreach ($tore1 as $tor1) {
        $temp1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$tor1."'";
        $temp2 = mysql_query($temp1);
        $temp3 = mysql_fetch_assoc($temp2);
        $torschuetzen1 = $torschuetzen1.'<a href="/spieler.php?id='.$tor1.'">'.$temp3['vorname'].' '.$temp3['nachname'].'</a>, ';
    }
    $torschuetzen1 = substr($torschuetzen1, 0, -2);
}
if ($sql3['tore2'] == '') {
	$torschuetzen2 = '-';
}
else {
    $tore2 = explode('-', $sql3['tore2']);
    $torschuetzen2 = '';
    foreach ($tore2 as $tor2) {
        $temp1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$tor2."'";
        $temp2 = mysql_query($temp1);
        $temp3 = mysql_fetch_assoc($temp2);
        $torschuetzen2 = $torschuetzen2.'<a href="/spieler.php?id='.$tor2.'">'.$temp3['vorname'].' '.$temp3['nachname'].'</a>, ';
    }
    $torschuetzen2 = substr($torschuetzen2, 0, -2);
}
// TORSCHUETZEN ENDE
// GELB ANFANG
if ($sql3['karte_gelb1'] == '') {
	$gelb1 = '-';
}
else {
    $karte_gelb1 = explode('-', $sql3['karte_gelb1']);
    $gelb1 = '';
    foreach ($karte_gelb1 as $karte_gelb_einzeln2) {
        $temp1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$karte_gelb_einzeln2."'";
        $temp2 = mysql_query($temp1);
        $temp3 = mysql_fetch_assoc($temp2);
        $gelb1 = $gelb1.'<a href="/spieler.php?id='.$karte_gelb_einzeln2.'">'.$temp3['vorname'].' '.$temp3['nachname'].'</a>, ';
    }
    $gelb1 = substr($gelb1, 0, -2);
}
if ($sql3['karte_gelb2'] == '') {
	$gelb2 = '-';
}
else {
    $karte_gelb2 = explode('-', $sql3['karte_gelb2']);
    $gelb2 = '';
    foreach ($karte_gelb2 as $karte_gelb_einzeln2) {
        $temp1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$karte_gelb_einzeln2."'";
        $temp2 = mysql_query($temp1);
        $temp3 = mysql_fetch_assoc($temp2);
        $gelb2 = $gelb2.'<a href="/spieler.php?id='.$karte_gelb_einzeln2.'">'.$temp3['vorname'].' '.$temp3['nachname'].'</a>, ';
    }
    $gelb2 = substr($gelb2, 0, -2);
}
// GELB ENDE
// ROT ANFANG
if ($sql3['karte_rot1'] == '') {
	$rot1 = '-';
}
else {
    $karte_rot1 = explode('-', $sql3['karte_rot1']);
    $rot1 = '';
    foreach ($karte_rot1 as $karte_rot_einzeln2) {
        $temp1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$karte_rot_einzeln2."'";
        $temp2 = mysql_query($temp1);
        $temp3 = mysql_fetch_assoc($temp2);
        $rot1 = $rot1.'<a href="/spieler.php?id='.$karte_rot_einzeln2.'">'.$temp3['vorname'].' '.$temp3['nachname'].'</a>, ';
    }
    $rot1 = substr($rot1, 0, -2);
}
if ($sql3['karte_rot2'] == '') {
	$rot2 = '-';
}
else {
    $karte_rot2 = explode('-', $sql3['karte_rot2']);
    $rot2 = '';
    foreach ($karte_rot2 as $karte_rot_einzeln2) {
        $temp1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$karte_rot_einzeln2."'";
        $temp2 = mysql_query($temp1);
        $temp3 = mysql_fetch_assoc($temp2);
        $rot2 = $rot2.'<a href="/spieler.php?id='.$karte_rot_einzeln2.'">'.$temp3['vorname'].' '.$temp3['nachname'].'</a>, ';
    }
    $rot2 = substr($rot2, 0, -2);
}
// ROT ENDE
if ($live_scoring_meldung != '') {
	$minute_sql = " AND minute < ".$live_scoring_min_gespielt;
}
else {
	$minute_sql = "";
}
$ber1 = "SELECT minute, kommentar FROM ".$prefix."spiele_kommentare WHERE spiel = ".$sql3['id'].$minute_sql." ORDER BY minute ASC, id ASC";
$ber2 = mysql_query($ber1);
$ber2a = mysql_num_rows($ber2);
$kommentar_liste = '';
if ($ber2a > 0) {
    $kommentar_liste = '';
    $spielstand = '';
	$counter = 0;
    while ($ber3 = mysql_fetch_assoc($ber2)) {
		$counter++;
        if ($ber3['minute'] == 0) {
            $minute_str = '';
        }
        else {
            $minute_str = $ber3['minute'].'\': ';
        }
        $kommentar_ergebnis = extract_kommentar_ergebnis($ber3['kommentar']);
		$kommentar_ergebnisStr = trim($kommentar_ergebnis[0]);
		if (mb_substr($kommentar_ergebnisStr, 0, 9, 'UTF-8') == 'Noten für' OR mb_substr($kommentar_ergebnisStr, 0, 23, 'UTF-8') == '<strong>Die Zeitschrift') {
			if ($live_scoring_meldung != '') {
				continue; // Noten in LIVE-Spielberichten nicht anzeigen
			}
		}
		else {
			$kommentar_ergebnisStr = str_replace('</strong>', '</strong> <img src="/images/ball_14.png" alt="Tor!" width="14" />', $kommentar_ergebnisStr);
		}
		$firstVorkommen1 = strpos($kommentar_ergebnisStr, $sql3['team1']);
		if ($firstVorkommen1 === FALSE) { $firstVorkommen1 = 999; }
		$firstVorkommen2 = strpos($kommentar_ergebnisStr, $sql3['team2']);
		if ($firstVorkommen2 === FALSE) { $firstVorkommen2 = 999; }
		if ($firstVorkommen1 < $firstVorkommen2 && $minute_str != '') { $lineColor = '#eee'; } else { $lineColor = '#fff'; }
        $kommentar_liste .= '<p';
		if ($counter == $ber2a) {
			$kommentar_liste .= ' id="lastAction"';
		}
		$kommentar_liste .= ' style="background-color:'.$lineColor.'">'.$minute_str.$kommentar_ergebnisStr.'</p>';
        if ($kommentar_ergebnis[1] != '') {
            $spielstand = $kommentar_ergebnis[1];
        }
    }
}
else {
	$spielstand = '-:-';
	if ($live_scoring_meldung != '') {
		$kommentar_liste = '<p>'._('Herzlich willkommen im Stadion. Heute findet das Spiel zwischen '.$sql3['team1'].' und '.$sql3['team2'].' statt. Viele Gästefans haben den Weg ins Stadion gefunden. Die Fans singen für ihr Team und die Spieler stehen schon in den Katakomben.').'</p>';
	}
}
// HIN-RUCKRUNDEN-ERGEBNIS ANFANG
$hinrueckStr = '';
if ($sql3['typ'] == 'Liga') {
	$hinrueck1 = "SELECT id, datum, ergebnis FROM ".$prefix."spiele WHERE team1 = '".$sql3['team2']."' AND team2 = '".$sql3['team1']."' AND typ = 'Liga'";
	$hinrueck2 = mysql_query($hinrueck1);
	if (mysql_num_rows($hinrueck2) == 1) {
		$hinrueck3 = mysql_fetch_assoc($hinrueck2);
		if ($live_scoring_spieltyp_laeuft == 'Liga' && date('d.m.Y', time()) == date('d.m.Y', $hinrueck3['datum'])) {
			$hinrueckStr = 'LIVE';
		}
		else {
			if ($hinrueck3['datum'] > $sql3['datum']) { $hinRueckCase = 'Rückrunde'; } else { $hinRueckCase = 'Hinrunde'; }
			$hinrueckStr = ' (<a href="/spielbericht.php?id='.$hinrueck3['id'].'">'.$hinRueckCase.': '.ergebnis_drehen($hinrueck3['ergebnis']).'</a>)';
		}
	}
}
// HIN-RUCKRUNDEN-ERGEBNIS ENDE
?>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Bereich'); ?></th>
<th scope="col"><?php echo _('Wert'); ?></th>
</tr>
</thead>
<tbody>
<tr><td><?php echo _('Begegnung'); ?></td><td><?php echo '<a href="/team.php?id='.$tid1.'">'.$sql3['team1'].'</a>'; ?> - <?php echo '<a href="/team.php?id='.$tid2.'">'.$sql3['team2'].'</a>'; ?></td></tr>
<tr class="odd"><td><?php echo _('Ergebnis'); ?></td><td><?php if ($live_scoring_meldung != '') { echo $spielstand.' ('.$live_scoring_meldung.')'; } else { echo $sql3['ergebnis'].$hinrueckStr; } ?></td></tr>
<tr><td><?php echo _('Datum'); ?></td><td><?php echo date('d.m.Y H:i', $sql3['datum']-3600); ?></td></tr>
<tr class="odd"><td><?php echo _('Wettbewerb'); ?></td><td><?php echo $sql3['typ']; ?></td></tr>
<tr><td><?php echo _('Zuschauer'); ?></td><td><?php echo number_format($sql3['zuschauer'], 0, ',', '.'); ?></td></tr>
<?php if ($live_scoring_meldung == '') { ?><tr class="odd"><td><?php echo _('Tore (Heim)'); ?></td><td><?php echo $torschuetzen1; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr><td><?php echo _('Tore (Auswärts)'); ?></td><td><?php echo $torschuetzen2; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr class="odd"><td><?php echo _('Ballbesitz'); ?></td><td><?php echo $sql3['ballbesitz1'].'% - '.$sql3['ballbesitz2'].'%'; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr><td><?php echo _('Fouls'); ?></td><td><?php echo $sql3['fouls1'].' - '.$sql3['fouls2']; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr class="odd"><td><?php echo _('Abseits'); ?></td><td><?php echo $sql3['abseits1'].' - '.$sql3['abseits2']; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr><td><?php echo _('Schüsse'); ?></td><td><?php echo $sql3['schuesse1'].' - '.$sql3['schuesse2']; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr class="odd"><td><?php echo _('Gelb (Heim)'); ?></td><td><?php echo $gelb1; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr><td><?php echo _('Gelb (Auswärts)'); ?></td><td><?php echo $gelb2; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr class="odd"><td><?php echo _('Rot (Heim)'); ?></td><td><?php echo $rot1; ?></td></tr><?php } ?>
<?php if ($live_scoring_meldung == '') { ?><tr><td><?php echo _('Rot (Auswärts)'); ?></td><td><?php echo $rot2; ?></td></tr><?php } ?>
</tbody>
</table>
<?php
$abstand_spiel_heute = time()-$sql3['datum'];
if ($abstand_spiel_heute > -3600) { // wenn das Spiel ueberhaupt schon war dann Kommentare zeigen
    echo '<h1>'._('Kommentar des Reporters');
    if ($live_scoring_meldung != '') { echo ' ('.$live_scoring_min_gespielt.'. Minute)'; }
    echo '</h1>';
    echo $kommentar_liste;
}
elseif ($live_scoring_spieltyp_laeuft == '') {
	echo '<h1>'._('Mögliche RKP-Veränderungen').'</h1>';
	$ergebnisStrings = array('0:0', '1:0', '2:0', '3:0', '0:3', '0:2', '0:1');
	echo '<table><thead><tr class="odd"><th scope="col">'._('Ergebnis').'</th><th scope="col">'.$sql3['team1'].' ('.round($eloTeam1).')</th><th scope="col">'.$sql3['team2'].' ('.round($eloTeam2).')</th></tr></thead><tbody>';
	foreach ($ergebnisStrings as $ergebnisStr) {
		$eloChange1 = round(eloChange($ergebnisStr, $eloTeam1, $eloTeam2, $sql3['typ']));
		if ($eloChange1 > 0) { $eloChange1 = '+'.$eloChange1; }
		$eloChange2 = round(-eloChange($ergebnisStr, $eloTeam1, $eloTeam2, $sql3['typ']));
		if ($eloChange2 > 0) { $eloChange2 = '+'.$eloChange2; }
		echo '<tr><td>'.$ergebnisStr.'</td><td>'.$eloChange1.'</td><td>'.$eloChange2.'</td>';
	}
	echo '</tbody></table>';
}
?>
<?php include 'zz3.php'; ?>
