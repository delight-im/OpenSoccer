<?php include 'zz1.php'; ?>
<title><?php echo _('Testspiele'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
require_once('./classes/Friendlies.php');
if (isset($_GET['recall']) && $cookie_id != CONFIG_DEMO_USER) {
	$recall_team2 = mysql_real_escape_string(trim(strip_tags($_GET['recall'])));
	$anfa = "DELETE FROM ".$prefix."testspiel_anfragen WHERE team1 = '".$cookie_team."' AND team2 = '".$recall_team2."'";
	$anfb = mysql_query($anfa);
	addInfoBox(_('Deine Anfrage wurde zurückgezogen.'));
}
?>
<?php
// KONTOSTAND PRUEFEN ANFANG
if ($cookie_team != '__'.$cookie_id) {
	$getkonto1 = "SELECT konto FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
	$getkonto2 = mysql_query($getkonto1);
	$getkonto3 = mysql_fetch_assoc($getkonto2);
	$getkonto4 = $getkonto3['konto']-einsatz_in_auktionen($cookie_team);
}
else {
	$getkonto4 = 0;
}
// KONTOSTAND PRUEFEN ENDE
// FESTLEGEN WAS GESUCHT WERDEN SOLL ANFANG
if (isset($_POST['wantTests']) && $cookie_id != CONFIG_DEMO_USER) {
	$wantTests = intval($_POST['wantTests']);
	if ($wantTests == 0 OR $wantTests == 1) {
		$up1 = "UPDATE ".$prefix."teams SET wantTests = '".$wantTests."' WHERE ids = '".$cookie_team."'";
		$up2 = mysql_query($up1);
		if ($wantTests == 1) {
			addInfoBox(_('Andere Teams können Dir nun Testspiel-Anfragen senden, die Du hier annehmen oder ablehnen kannst.'));
		}
		else {
			addInfoBox(_('Ab sofort bekommst Du keine Testspiel-Anfragen mehr.'));
		}
	}
}
$sql1 = "SELECT wantTests FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) == 0) {
	$wantTests = '0';
}
else {
	$sql3 = mysql_fetch_assoc($sql2);
	$wantTests = $sql3['wantTests'];
}
echo '<h1>'._('Interesse an Testspielen?').'</h1>';
echo '<form action="/testspiele.php" method="post" accept-charset="utf-8">';
echo '<p><select name="wantTests" size="1" style="width:200px">';
	echo '<option value="1"'; if ($wantTests == 1) { echo ' selected="selected"'; } echo '>'._('Ja, bin interessiert').'</option>';
	echo '<option value="0"'; if ($wantTests == 0) { echo ' selected="selected"'; } echo '>'._('Nein, kein Interesse').'</option>';
echo '</select> <input type="submit" value="Festlegen"'.noDemoClick($cookie_id).' /></p>';
echo '</form>';
// FESTLEGEN WAS GESUCHT WERDEN SOLL ENDE
?>
<h1><?php echo _('Entschädigung (Verband)'); ?></h1>
<p><?php echo __('Damit ein Testspiel genehmigt wird, musst Du %s € an den Verband zahlen.', number_format(Friendlies::getPrice($cookie_liga, $prefix), 0, ',', '.')); ?></p>
<h1><?php echo _('Erhaltene Anfragen'); ?></h1>
<?php
// MEHRERE TESTSPIELE PRO TAG VERHINDERN ANFANG
$an1 = "SELECT DISTINCT(datum) FROM ".$prefix."spiele WHERE typ = 'Test' AND (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."')";
$an2 = mysql_query($an1);
$testspiel_tage = array();
while ($an3 = mysql_fetch_assoc($an2)) {
	$testspiel_tage[] = $an3['datum'];
}
// MEHRERE TESTSPIELE PRO TAG VERHINDERN ENDE
if ($getkonto4 < Friendlies::getPrice($cookie_liga, $prefix)) {
	echo '<p>'._('Zurzeit hast Du leider nicht genug Geld, um Testspiele vereinbaren zu können.').'</p>';
}
else {
	$timeout = getTimestamp('+1 day');
	$an1 = "DELETE FROM ".$prefix."testspiel_anfragen WHERE datum < ".$timeout;
	$an2 = mysql_query($an1);
	$an1 = "SELECT team1, team1_name, datum FROM ".$prefix."testspiel_anfragen WHERE team2 = '".$cookie_team."' ORDER BY zeit ASC";
	$an2 = mysql_query($an1);
	$an2a = mysql_num_rows($an2);
	if ($an2a > 0) {
		echo '<p><strong>'._('Wichtig:').'</strong> '._('Das Testspiel findet immer im Stadion des Anfragenden statt. Beide Teams müssen für ein Testspiel eine Entschädigung an den Verband zahlen, damit das Spiel genehmigt wird.').'</p>';
		echo '<p><table><thead><tr class="odd"><th scope="col">'._('Anfragender').'</th><th scope="col">'._('Datum').'</th><th scope="col">'._('Aktion').'</th></tr></thead><tbody>';
		while ($an3 = mysql_fetch_assoc($an2)) {
			if (in_array($an3['datum'], $testspiel_tage) && $an3['datum'] > time()) { // wenn an dem Tag schon ein Testspiel ist
				$dl1 = "DELETE FROM ".$prefix."testspiel_anfragen WHERE team2 = '".$cookie_team."' AND datum = '".$an3['datum']."'";
				$dl2 = mysql_query($dl1);
				// PROTOKOLL ANFANG
				$formulierung = _('Dein Co-Trainer hat eine Anfrage für ein Testspiel abgelehnt.');
				$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Termine', ".time().")";
				$sql8 = mysql_query($sql7);
				$formulierung = __('Der Co-Trainer von %s hat Dein Angebot für ein Testspiel abgelehnt.', '<a href="/team.php?id='.$cookie_team.'">'.$cookie_teamname.'</a>');
				$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$an3['team1']."', '".$formulierung."', 'Termine', ".time().")";
				$sql8 = mysql_query($sql7);
				// PROTOKOLL ENDE
				if (isset($_SESSION['last_testspiele_anzahl'])) {
					$_SESSION['last_testspiele_anzahl']--;
				}
				echo '<tr><td colspan="3">'._('Dein Co-Trainer hat diese Anfrage abgelehnt, weil Du an dem Tag schon ein Testspiel hast.').'</td></tr>';
			}
			else {
				echo '<tr><td class="link"><a href="/team.php?id='.$an3['team1'].'">'.$an3['team1_name'].'</a></td><td>'.date('d.m.Y', $an3['datum']).'</td>';
				echo '<td><form action="/testspiel_antworten.php" method="get" accept-charset="utf-8"><input type="hidden" name="id" value="'.$an3['team1'].'" /> <button type="submit" name="typ" value="Annehmen"'.noDemoClick($cookie_id).'>'._('Annehmen').'</button> <button type="submit" name="typ" value="Ablehnen"'.noDemoClick($cookie_id).'>'._('Ablehnen').'</button></form></td>';
				echo '</tr>';
			}
		}
		echo '</tbody></table></p>';
	}
	else {
		echo '<p>'._('Zurzeit keine Anfragen! Du kannst selbst bei anderen Vereinen anfragen, ob sie Interesse haben. Das Formular dazu findest Du im Profil jedes Managers. Anfragen können nur bis spätestens 24h vor dem Spieltag angenommen werden.').'</p>';
	}
}
?>
<h1><?php echo _('Gesendete Angebote (bisher unbeantwortet)'); ?></h1>
<?php
$an1 = "SELECT ".$prefix."testspiel_anfragen.team2, ".$prefix."testspiel_anfragen.datum, ".$prefix."teams.name FROM ".$prefix."testspiel_anfragen JOIN ".$prefix."teams ON ".$prefix."testspiel_anfragen.team2 = ".$prefix."teams.ids WHERE team1 = '".$cookie_team."' ORDER BY ".$prefix."testspiel_anfragen.zeit ASC";
$an2 = mysql_query($an1);
$an2a = mysql_num_rows($an2);
if ($an2a > 0) {
	echo '<p><strong>'._('Wichtig:').'</strong> '._('Das Testspiel findet immer im Stadion des Anfragenden statt. Beide Teams müssen für ein Testspiel eine Entschädigung an den Verband zahlen, damit das Spiel genehmigt wird.').'</p>';
	echo '<p><table><thead><tr class="odd"><th scope="col">'._('Anfrage an').'</th><th scope="col">'._('Spiel-Datum').'</th><th scope="col">'._('Aktion').'</th></tr></thead><tbody>';
    while ($an3 = mysql_fetch_assoc($an2)) {
        echo '<tr><td class="link"><a href="/team.php?id='.$an3['team2'].'">'.$an3['name'].'</a></td><td>'.date('d.m.Y', $an3['datum']).'</td><td class="link"><a href="/testspiele.php?recall='.$an3['team2'].'" onclick="return confirm(\'Bist Du sicher?\')">Zurückziehen</a></td>';
    }
    echo '</tbody></table></p>';
}
else {
	echo '<p>'._('Zurzeit keine ausstehenden Angebote! Du kannst andere Vereine um ein Testspiel bitten. Das Formular dazu findest Du im Profil jedes Managers.').'</p>';
}
?>
<h1><?php echo _('Vereinbarte Testspiele'); ?></h1>
<?php
$an1 = "SELECT id, team1, team2, datum, ergebnis, typ FROM ".$prefix."spiele WHERE typ = 'Test' AND (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."') ORDER BY datum ASC";
$an2 = mysql_query($an1);
$an2a = mysql_num_rows($an2);
if ($an2a > 0) { echo '<p><table><thead><tr class="odd"><th scope="col">'._('Gegner').'</th><th scope="col">'._('Datum').'</th><th scope="col">'._('Ergebnis').'</th></tr></thead><tbody>';
    while ($an3 = mysql_fetch_assoc($an2)) {
        if ($an3['team1'] == $cookie_teamname) {
            $an3_gegner = $an3['team2'];
            $an3_ergebnis = $an3['ergebnis'];
        }
        else {
            $an3_gegner = $an3['team1'];
            $an3_ergebnis = ergebnis_drehen($an3['ergebnis']);
        }
        // LIVE ODER ERGEBNIS ANFANG
        if ($an3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $an3['datum'])) {
            $ergebnis_live = _('LIVE');
        }
        else {
            $ergebnis_live = $an3_ergebnis;
        }
        // LIVE ODER ERGEBNIS ENDE
        echo '<tr>';
        echo '<td>'.$an3_gegner.'</td>';
        echo '<td>'.date('d.m.Y', $an3['datum']).'</td>';
		echo '<td class="link"><a href="/spielbericht.php?id='.$an3['id'].'">'.$ergebnis_live.'</a></td>';
		echo '</tr>';
    }
    echo '</tbody></table></p>';
}
else {
	echo '<p>'._('Du hattest bisher keine Testspiele!').'</p>';
}
?>
<?php } else { ?>
<h1><?php echo _('Testspiele'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
