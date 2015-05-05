<?php include 'zz1.php'; ?>
<title><?php echo _('Aufstellung'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
.os-player-row-injured td, .os-player-row-injured td a { color: #ff0000; }
</style>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Aufstellung'); ?></h1>
<?php
function moralToGraphic($intValue) {
	$outputStr = '<span';
	if ($intValue > 75) {
		$outputStr .= ' style="color:green"';
	}
	elseif ($intValue > 60) {
		$outputStr .= '';
	}
	else {
		$outputStr .= ' style="color:red"';
	}
	$outputStr .= '>'.round($intValue).'%</span>';
	return $outputStr;
}
// SPIELTYP ANFANG
$spieltypAufstellung = 'Liga';
$spieltypenMoeglich = array('Liga', 'Pokal', 'Cup', 'Test');
if (isset($_GET['spieltypAufstellung'])) {
	if (in_array($_GET['spieltypAufstellung'], $spieltypenMoeglich)) {
		$spieltypAufstellung = $_GET['spieltypAufstellung'];
	}
}
echo '<p style="text-align:right">';
foreach ($spieltypenMoeglich as $spieltypSingle) {
	echo '<a href="/aufstellung.php?spieltypAufstellung='.$spieltypSingle.'" class="pagenava'; if ($spieltypAufstellung == $spieltypSingle) { echo ' aktiv'; } echo '">'.$spieltypSingle.'</a>&nbsp;';
}
echo '</p>';
// SPIELTYP ENDE
?>
<?php if ($loggedin == 1) { ?>
<?php
$pokalNurFuerSQL = "";
if ($spieltypAufstellung == 'Pokal') {
	$pokalNurFuerSQL = " AND (pokalNurFuer = '' OR pokalNurFuer = team)";
}
$eigene_spiele1 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE typ = '".$live_scoring_spieltyp_laeuft."' AND (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."') AND ABS(datum-".time().") < 7200";
$eigene_spiele2 = mysql_query($eigene_spiele1);
$eigene_spiele3 = mysql_result($eigene_spiele2, 0);
if ($live_scoring_spieltyp_laeuft == $spieltypAufstellung && $eigene_spiele3 != 0) {
	echo '<p>'.__('Zurzeit läuft ein %s-Spiel. Deshalb kannst Du diese Aufstellung gerade nicht ändern.', $live_scoring_spieltyp_laeuft).'</p>';
}
else { ?>
<?php
if (isset($_POST['farbeAufstellen'])) {
	$farbeAufstellen = trim(strip_tags($_POST['farbeAufstellen']));
	if ($farbeAufstellen == 'Blau' OR $farbeAufstellen == 'Gelb' OR $farbeAufstellen == 'Rot' OR $farbeAufstellen == 'Gruen' OR $farbeAufstellen == 'Pink' OR $farbeAufstellen == 'Aqua' OR $farbeAufstellen == 'Silber' OR $farbeAufstellen == 'Lila' OR $farbeAufstellen == 'Oliv') {
		$aufstellungLog1 = "INSERT INTO ".$prefix."aufstellungLog (team, zeit, typ) VALUES ('".$cookie_team."', ".time().", '".$spieltypAufstellung."')";
		$aufstellungLog2 = mysql_query($aufstellungLog1);
		$scs1 = "UPDATE ".$prefix."spieler SET startelf_".$spieltypAufstellung." = 0 WHERE team = '".$cookie_team."'";
		$scs2 = mysql_query($scs1);
		$setColor1 = "SELECT a.ids, a.position FROM ".$prefix."spieler AS a JOIN ".$prefix."spieler_mark AS b ON a.ids = b.spieler WHERE a.team = '".$cookie_team."' AND b.team = '".$cookie_team."' AND b.farbe = '".$farbeAufstellen."' AND a.verletzung = 0";
		$setColor2 = mysql_query($setColor1);
		$setColorSpielerCount = array('T'=>1, 'A'=>4, 'M'=>4, 'S'=>2);
		$positionToStartelfnummer = array('T'=>array(11), 'A'=>array(10, 9, 8, 7), 'M'=>array(6, 5, 4, 3), 'S'=>array(2, 1));
		while ($setColor3 = mysql_fetch_assoc($setColor2)) {
			if ($setColorSpielerCount[$setColor3['position']] > 0) {
				$setColorSpielerCount[$setColor3['position']]--;
				if ($startelfnummer = each($positionToStartelfnummer[$setColor3['position']])) {
					$scs1 = "UPDATE ".$prefix."spieler SET startelf_".$spieltypAufstellung." = ".$startelfnummer['value']." WHERE team = '".$cookie_team."' AND ids = '".$setColor3['ids']."' AND verletzung = 0 AND frische > 4".$pokalNurFuerSQL;
					$scs2 = mysql_query($scs1);
				}
			}
		}
		if ($spieltypAufstellung == 'Cup') {
			setTaskDone('lineup_cup');
		}
	}
}

if (count($_POST) > 0) {
    if (count($_POST) == 11) {
		$aufstellungLog1 = "INSERT INTO ".$prefix."aufstellungLog (team, zeit, typ) VALUES ('".$cookie_team."', ".time().", '".$spieltypAufstellung."')";
		$aufstellungLog2 = mysql_query($aufstellungLog1);
		$aufstellung_aktivieren0 = "UPDATE ".$prefix."spieler SET startelf_".$spieltypAufstellung." = 0 WHERE team = '".$cookie_team."'";
		$aufstellung_aktivieren0 = mysql_query($aufstellung_aktivieren0);
		for ($i = 1; $i <= 11; $i++) {
			$aufstellung_aktivieren1 = "UPDATE ".$prefix."spieler SET startelf_".$spieltypAufstellung." = ".$i." WHERE team = '".$cookie_team."' AND ids = '".mysql_real_escape_string($_POST[$i])."' AND verletzung = 0 AND frische > 4".$pokalNurFuerSQL;
			$aufstellung_aktivieren1 = mysql_query($aufstellung_aktivieren1);
		}
		if ($spieltypAufstellung == 'Cup') {
			setTaskDone('lineup_cup');
		}
    }
	elseif (isset($_POST['aufstellungUebernehmen'])) {
		$aufstellungUebernehmen = mysql_real_escape_string(trim(strip_tags($_POST['aufstellungUebernehmen'])));
		if ($aufstellungUebernehmen == 'Liga' OR $aufstellungUebernehmen == 'Pokal' OR $aufstellungUebernehmen == 'Cup' OR $aufstellungUebernehmen == 'Test') {
			$aufstellungLog1 = "INSERT INTO ".$prefix."aufstellungLog (team, zeit, typ) VALUES ('".$cookie_team."', ".time().", '".$spieltypAufstellung."')";
			$aufstellungLog2 = mysql_query($aufstellungLog1);
			$aufstellungUebernehmen1 = "UPDATE ".$prefix."spieler SET startelf_".$spieltypAufstellung." = startelf_".$aufstellungUebernehmen." WHERE team = '".$cookie_team."'".$pokalNurFuerSQL;
			$aufstellungUebernehmen2 = mysql_query($aufstellungUebernehmen1);
		}
		if ($spieltypAufstellung == 'Cup') {
			setTaskDone('lineup_cup');
		}
	}
}
$aufstellung_holen1 = "SELECT COUNT(*), SUM(staerke) FROM ".$prefix."spieler WHERE startelf_".$spieltypAufstellung." != 0 AND team = '".$cookie_team."' AND verletzung = 0";
$aufstellung_holen2 = mysql_query($aufstellung_holen1);
$aufstellung_holen3 = mysql_fetch_assoc($aufstellung_holen2);
if ($spieltypAufstellung == 'Liga') {
	$aufstellung_aktivieren13 = "UPDATE ".$prefix."teams SET aufstellung = ".$aufstellung_holen3['SUM(staerke)']." WHERE ids = '".$cookie_team."'";
	$aufstellung_aktivieren13 = mysql_query($aufstellung_aktivieren13);
}
addInfoBox(__('Aufgestellte Spieler: %1$d/11, Stärke: %2$s', $aufstellung_holen3['COUNT(*)'], number_format($aufstellung_holen3['SUM(staerke)'], 1, ',', '.')));
?>
<?php
// SPIELER IN ARRAY LESEN ANFANG
$player1 = "SELECT ids, vorname, nachname, staerke, position, pokalNurFuer, startelf_".$spieltypAufstellung." AS startelfWert FROM ".$prefix."spieler WHERE team = '".$cookie_team."' AND verletzung = 0 AND frische > 4 ORDER BY startelf DESC, staerke DESC";
$player2 = mysql_query($player1);
$player_t = array(array('kein_spieler', '- Kein Spieler -', 0));
$player_a = array(array('kein_spieler', '- Kein Spieler -', 0));
$player_m = array(array('kein_spieler', '- Kein Spieler -', 0));
$player_s = array(array('kein_spieler', '- Kein Spieler -', 0));
while ($player3 = mysql_fetch_assoc($player2)) {
	if ($spieltypAufstellung == 'Pokal' && $player3['pokalNurFuer'] != '' && $player3['pokalNurFuer'] != $cookie_team) { continue; }
    $player_name = mb_substr($player3['vorname'], 0, 1, 'utf-8').'. '.$player3['nachname'].' ('.number_format($player3['staerke'], 1, ',', '.').')';
	switch ($player3['position']) {
		case 'T': $player_t[] = array($player3['ids'], $player_name, $player3['startelfWert']); break;
		case 'A': $player_a[] = array($player3['ids'], $player_name, $player3['startelfWert']); break;
		case 'M': $player_m[] = array($player3['ids'], $player_name, $player3['startelfWert']); break;
		case 'S': $player_s[] = array($player3['ids'], $player_name, $player3['startelfWert']); break;
	}
}
// SPIELER IN ARRAY LESEN ENDE
// SPIELER-MARKIERUNGEN ANFANG
$gf1 = "SELECT spieler, farbe FROM ".$prefix."spieler_mark WHERE team = '".$cookie_team."'";
$gf2 = mysql_query($gf1);
$markierungen = array();
while ($gf3 = mysql_fetch_assoc($gf2)) {
	$markierungen[$gf3['spieler']] = $gf3['farbe'];
}
// SPIELER-MARKIERUNGEN ENDE
?>
<form action="/aufstellung.php?spieltypAufstellung=<?php echo $spieltypAufstellung; ?>" method="post" accept-charset="utf-8">
<p><input type="submit" value="<?php echo _('Aufstellung speichern'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
<div class="os-lineup-field">
    <div class="visible-mobile"><p><strong><?php echo _('Sturm:'); ?></strong></p></div>
    <select name="2" size="1" class="os-lineup-player os-lineup-player-forward1">
        <?php
        foreach ($player_s as $player_ss) {
            echo '<option value="'.$player_ss[0].'"'; if ($player_ss[2] == 2) { echo ' selected="selected"'; } echo '>'.$player_ss[1].'</option>';
        }
        ?>
    </select>
    <select name="1" size="1" class="os-lineup-player os-lineup-player-forward2">
        <?php
        foreach ($player_s as $player_ss) {
            echo '<option value="'.$player_ss[0].'"'; if ($player_ss[2] == 1) { echo ' selected="selected"'; } echo '>'.$player_ss[1].'</option>';
        }
        ?>
    </select>
    <div class="visible-mobile"><p><strong><?php echo _('Mittelfeld:'); ?></strong></p></div>
    <select name="6" size="1" class="os-lineup-player os-lineup-player-midfield1">
        <?php
        foreach ($player_m as $player_ms) {
            echo '<option value="'.$player_ms[0].'"'; if ($player_ms[2] == 6) { echo ' selected="selected"'; } echo '>'.$player_ms[1].'</option>';
        }
        ?>
    </select>
    <select name="5" size="1" class="os-lineup-player os-lineup-player-midfield2">
        <?php
        foreach ($player_m as $player_ms) {
            echo '<option value="'.$player_ms[0].'"'; if ($player_ms[2] == 5) { echo ' selected="selected"'; } echo '>'.$player_ms[1].'</option>';
        }
        ?>
    </select>
    <select name="4" size="1" class="os-lineup-player os-lineup-player-midfield3">
        <?php
        foreach ($player_m as $player_ms) {
            echo '<option value="'.$player_ms[0].'"'; if ($player_ms[2] == 4) { echo ' selected="selected"'; } echo '>'.$player_ms[1].'</option>';
        }
        ?>
    </select>
    <select name="3" size="1" class="os-lineup-player os-lineup-player-midfield4">
        <?php
        foreach ($player_m as $player_ms) {
            echo '<option value="'.$player_ms[0].'"'; if ($player_ms[2] == 3) { echo ' selected="selected"'; } echo '>'.$player_ms[1].'</option>';
        }
        ?>
    </select>
    <div class="visible-mobile"><p><strong><?php echo _('Abwehr:'); ?></strong></p></div>
    <select name="10" size="1" class="os-lineup-player os-lineup-player-defender1">
        <?php
        foreach ($player_a as $player_as) {
            echo '<option value="'.$player_as[0].'"'; if ($player_as[2] == 10) { echo ' selected="selected"'; } echo '>'.$player_as[1].'</option>';
        }
        ?>
    </select>
    <select name="9" size="1" class="os-lineup-player os-lineup-player-defender2">
        <?php
        foreach ($player_a as $player_as) {
            echo '<option value="'.$player_as[0].'"'; if ($player_as[2] == 9) { echo ' selected="selected"'; } echo '>'.$player_as[1].'</option>';
        }
        ?>
    </select>
    <select name="8" size="1" class="os-lineup-player os-lineup-player-defender3">
        <?php
        foreach ($player_a as $player_as) {
            echo '<option value="'.$player_as[0].'"'; if ($player_as[2] == 8) { echo ' selected="selected"'; } echo '>'.$player_as[1].'</option>';
        }
        ?>
    </select>
    <select name="7" size="1" class="os-lineup-player os-lineup-player-defender4">
        <?php
        foreach ($player_a as $player_as) {
            echo '<option value="'.$player_as[0].'"'; if ($player_as[2] == 7) { echo ' selected="selected"'; } echo '>'.$player_as[1].'</option>';
        }
        ?>
    </select>
    <div class="visible-mobile"><p><strong><?php echo _('Torwart:'); ?></strong></p></div>
    <select name="11" size="1" class="os-lineup-player os-lineup-player-goalkeeper">
        <?php
        foreach ($player_t as $player_ts) {
            echo '<option value="'.$player_ts[0].'"'; if ($player_ts[2] == 11) { echo ' selected="selected"'; } echo '>'.$player_ts[1].'</option>';
        }
        ?>
    </select>
</div>
</form>
<form action="/aufstellung.php?spieltypAufstellung=<?php echo $spieltypAufstellung; ?>" method="post" accept-charset="utf-8">
<p><select name="farbeAufstellen" size="1" style="width:200px">
	<option value="Aqua"><?php echo _('Aqua'); ?></option>
	<option value="Blau"><?php echo _('Blau'); ?></option>
	<option value="Gelb"><?php echo _('Gelb'); ?></option>
	<option value="Lila"><?php echo _('Lila'); ?></option>
	<option value="Oliv"><?php echo _('Oliv'); ?></option>
	<option value="Pink"><?php echo _('Pink'); ?></option>
	<option value="Rot"><?php echo _('Rot'); ?></option>
	<option value="Silber"><?php echo _('Silber'); ?></option>
	<option value="Gruen"><?php echo _('Grün'); ?></option>
</select> <input type="submit" value="<?php echo _('Farbe aufstellen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<form action="/aufstellung.php?spieltypAufstellung=<?php echo $spieltypAufstellung; ?>" method="post" accept-charset="utf-8">
<p><select name="aufstellungUebernehmen" size="1" style="width:200px">
	<?php
	$spieltypenMoeglichTemp  = array($spieltypAufstellung);
	$andereSpieltypen = array_diff($spieltypenMoeglich, $spieltypenMoeglichTemp);
	foreach ($andereSpieltypen as $andererSpieltyp) {
		echo '<option>'.$andererSpieltyp.'</option>';
	}
	?>
</select> <input type="submit" value="<?php echo _('Aufstellung übernehmen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<h1><?php echo _('Meine Spieler'); ?></h1>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('MT'); ?></th>
<th scope="col"><?php echo _('Name'); ?></th>
<th scope="col"><?php echo _('AL'); ?></th>
<th scope="col"><?php echo _('Stärke'); ?></th>
<th scope="col"><?php echo _('FR'); ?></th>
<th scope="col"><?php echo _('MO'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT ids, position, vorname, nachname, wiealt, moral, staerke, talent, frische, startelf_".$spieltypAufstellung." AS startelfWert, verletzung FROM ".$prefix."spieler WHERE team = '".$cookie_team."' ORDER BY position DESC, staerke DESC";
$sql2 = mysql_query($sql1);
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
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
	if ($sql3['startelfWert'] != 0) { $mark1 = '<strong>'; $mark2 = '</strong>'; } else { $mark1 = ''; $mark2 = ''; }
	if ($counter % 2 == 0) {
		if ($sql3['verletzung'] != 0) { echo '<tr class="os-player-row-injured">'; } else { echo '<tr>'; }
	}
	else {
		if ($sql3['verletzung'] != 0) { echo '<tr class="odd os-player-row-injured">'; } else { echo '<tr class="odd">'; }
	}
	$schaetzungVomScout = schaetzungVomScout($cookie_team, $cookie_scout, $sql3['ids'], $sql3['talent'], $sql3['staerke'], $cookie_team);
	echo '<td'.$farbcode.'>&nbsp;</td><td>'.$mark1.$sql3['position'].$mark2.'</td><td class="link">'.$mark1.'<a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a>'.$mark2.'</td><td>'.$mark1.floor($sql3['wiealt']/365).$mark2.'</td><td>'.$mark1.number_format($sql3['staerke'], 1, ',', '.').' <span style="color:#999">('.number_format($schaetzungVomScout, 1, ',', '.').')</span>'.$mark2.'</td><td>'.$mark1;
	if ($sql3['verletzung'] != 0) {
		echo $sql3['verletzung'].' Tage';
	}
	else {
		echo $sql3['frische'].'%';
	}
	echo $mark2.'</td><td>'.moralToGraphic($sql3['moral']).'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<p><strong><?php echo _('Überschriften:').'</strong> '._('SE: Startelf, MT: Mannschaftsteil, AL: Alter, FR: Frische, MO: Moral'); ?></p>
<p><strong><?php echo _('Mannschaftsteile:').'</strong> '._('T: Torwart, A: Abwehr, M: Mittelfeld, S: Sturm'); ?></p>
<p><strong><?php echo _('Fettdruck:').'</strong> '._('Aufgestellte Spieler'); ?></p>
<p><strong><?php echo _('Rote Schrift:').'</strong> '._('verletzte oder gesperrte Spieler'); ?></p>
<?php } ?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
