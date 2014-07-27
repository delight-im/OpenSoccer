<?php include 'zz1.php'; ?>
<title><?php echo _('Namensänderung'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php if ($cookie_team != '__'.$cookie_id) { ?>
<?php
$showTeam = mysql_real_escape_string(trim(strip_tags($cookie_team)));
$showTeamName = mysql_real_escape_string(trim(strip_tags($cookie_teamname)));
$showTeamLiga = mysql_real_escape_string(trim(strip_tags($cookie_liga)));
if ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin') {
	if (isset($_GET['team'])) {
		$showTeam = mysql_real_escape_string(trim(strip_tags($_GET['team'])));
		$showTeamName1 = "SELECT name, liga FROM ".$prefix."teams WHERE ids = '".$showTeam."'";
		$showTeamName2 = mysql_query($showTeamName1);
		if (mysql_num_rows($showTeamName2) == 0) { exit; }
		$showTeamName3 = mysql_fetch_assoc($showTeamName2);
		$showTeamName = mysql_real_escape_string(trim(strip_tags($showTeamName3['name'])));
		$showTeamLiga = mysql_real_escape_string(trim(strip_tags($showTeamName3['liga'])));
	}
}

if ($cookie_teamname == $showTeamName) { // if the name of one's own club is to be changed
	echo '<h1>'._('Namensänderung').'</h1>';
}
else { // if the name of another user's club is to be changed by the support staff
    echo '<h1>'.__('Namensänderung für %s', htmlspecialchars($showTeamName)).'</h1>';
}

// CHECK IF USER IS ALLOWED TO CHANGE TEAM NAME AGAIN BEGIN
if ($_SESSION['status'] != 'Helfer' && $_SESSION['status'] != 'Admin') {
	$changeLockDuration = 2592000;
	$changeLockUnit = 86400;
	$changeLockUnitStr = _('Tagen');
}
else {
	$changeLockDuration = 1800;
	$changeLockUnit = 60;
	$changeLockUnitStr = _('Minuten');
}
$letzteAenderung1 = "SELECT MAX(zeit) FROM ".$prefix."vNameChanges WHERE team = '".$showTeam."' AND sperre = 1";
$letzteAenderung2 = mysql_query($letzteAenderung1);
if (mysql_num_rows($letzteAenderung2) == 0) {
	$letzteAenderungVor = time();
}
else {
	$letzteAenderung3 = mysql_fetch_assoc($letzteAenderung2);
	$letzteAenderungVor = time()-$letzteAenderung3['MAX(zeit)'];
}
if ($letzteAenderungVor < $changeLockDuration) { // check time limit for team name changing lock
	$lastTeamNameChange = round($letzteAenderungVor / $changeLockUnit);
	addInfoBox(__('Der Name Deines Vereins wurde zuletzt vor %1$d %2$d geändert. Du kannst ihn in %3$d %4$d das nächste Mal ändern.', $lastTeamNameChange, $changeLockUnitStr, intval(30-$lastTeamNameChange), $changeLockUnitStr));
	include 'zz3.php';
	exit;
}
// CHECK IF USER IS ALLOWED TO CHANGE TEAM NAME AGAIN END

?>
<p><?php echo _('Auf dieser Seite kannst Du den Namen Deines Vereins ändern. Du kannst den Namen einer Stadt und einen beliebigen Zusatz aus der Liste wählen, der vor oder nach dem Städtenamen stehen kann.'); ?></p>
<p><?php echo __('Hier fehlt ein Städtename, den Du gerne dabei hättest? Dann %s, vielleicht kommt er dann dazu!', '<a href="/wio.php#teamList">'._('sag uns Bescheid').'</a>'); ?></p>
<p><strong><?php echo _('Hinweis:').'</strong> '._('Du kannst den Namen Deines Klubs nur alle 30 Tage ändern.'); ?></p>
<?php
if (isset($_POST['kuerzel1']) && isset($_POST['kuerzel2']) && isset($_POST['stadt']) && $cookie_id != CONFIG_DEMO_USER) {
	if ($live_scoring_spieltyp_laeuft == '') {
		$kuerzel1 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel1'])));
		$kuerzel2 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel2'])));
		$stadt = mysql_real_escape_string(trim(strip_tags($_POST['stadt'])));
		if (in_array($kuerzel1, $kuerzelListe) OR in_array($kuerzel2, $kuerzelListe)) {
			if ($kuerzel1 != '' && $kuerzel2 != '') {
				addInfoBox(_('Du darfst nur <i>einen</i> Zusatz für den Namen wählen. Bitte entscheide Dich für einen und versuche es noch einmal.'));
			}
			else {
				if ($kuerzel2 == '') {
					$neuerName = $kuerzel1.' '.$stadt;
				}
				else {
					$neuerName = $stadt.' '.$kuerzel2;
				}
				$sperrListe1 = "SELECT zusatz FROM ".$prefix."vNameOriginals WHERE stadt = '".$stadt."'";
				$sperrListe2 = mysql_query($sperrListe1);
				$sperrListe = array();
				while ($sperrListe3 = mysql_fetch_assoc($sperrListe2)) {
					$sperrListe[] = $sperrListe3['zusatz'];
				}
				if (in_array($kuerzel1, $sperrListe) OR in_array($kuerzel2, $sperrListe)) {
					addInfoBox(_('Dieser Name steht auf der Sperrliste, weil er einem realen Vereins-Namen zu sehr ähnelt. Bitte versuche es noch einmal mit einem anderen Namen.'));
				}
				else {
					$sql1 = "UPDATE ".$prefix."teams SET name = '".$neuerName."' WHERE name = '".$showTeamName."'";
					$sql2 = mysql_query($sql1);
					if ($sql2 == FALSE) {
						addInfoBox(_('Dieser Name ist leider schon vergeben. Bitte versuche es noch einmal mit einem anderen Namen.'));
					}
					else {
						$sql3 = "INSERT INTO ".$prefix."vNameChanges (team, zeit, vonName, zuName) VALUES ('".$showTeam."', ".time().", '".$showTeamName."', '".$neuerName."')";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."pokalsieger SET sieger = '".$neuerName."' WHERE sieger = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."pokalsieger SET finalgegner = '".$neuerName."' WHERE finalgegner = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."cupsieger SET sieger = '".$neuerName."' WHERE sieger = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."cupsieger SET finalgegner = '".$neuerName."' WHERE finalgegner = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."geschichte_tabellen SET team = '".$neuerName."' WHERE team = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."spiele SET team1 = '".$neuerName."' WHERE team1 = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."spiele SET team2 = '".$neuerName."' WHERE team2 = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."testspiel_anfragen SET team1_name = '".$neuerName."' WHERE team1_name = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."transfermarkt_leihe SET bieter = '".$neuerName."' WHERE bieter = '".$showTeamName."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						if ($cookie_teamname == $showTeamName) { // if the name of one's own club was changed
							$_SESSION['teamname'] = $neuerName; // change the name in the session as well
							$cookie_teamname = $neuerName; // and for the rest of the current page
							$showTeamName = $neuerName; // just to be consistent
							addInfoBox(_('Dein Verein heißt jetzt:').' '.$neuerName);
						}
						else { // if the name of another user's club was changed by the support staff
							addInfoBox($showTeamName.' heißt jetzt: '.$neuerName);
							$showTeamName = $neuerName; // just to be consistent
						}
					}
				}
			}
		}
		else {
			addInfoBox(_('Du musst <i>einen</i> Zusatz wählen. Bitte versuche es noch einmal.'));
		}
	}
	else {
		addInfoBox(__('Du kannst den Namen Deines Vereins nur außerhalb der Spielzeiten ändern. Zurzeit laufen %s-Spiele.', $live_scoring_spieltyp_laeuft));
	}
}
?>
<form action="namensaenderung.php<?php if ($cookie_teamname != $showTeamName) { echo '?team='.urlencode($showTeam); } ?>" method="post" accept-charset="utf-8">
<p><select name="kuerzel1" size="1" style="width:100px"><option value="">&nbsp;-&nbsp;</option>
<?php
foreach ($kuerzelListe as $kuerzel) {
	echo '<option>'.$kuerzel.'</option>';
}
?>
</select> <select name="stadt" size="1" style="width:200px">
<?php
$sql1 = "SELECT name FROM ".$prefix."ligen WHERE ids = '".$showTeamLiga."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
$land = mysql_real_escape_string(trim(strip_tags(substr($sql3['name'], 0, -2))));
$sql4 = "SELECT name FROM ".$prefix."vNamePool WHERE land = '".$land."' ORDER BY name ASC";
$sql5 = mysql_query($sql4);
while ($sql6 = mysql_fetch_assoc($sql5)) {
	echo '<option>'.$sql6['name'].'</option>';
}
?>
</select> <select name="kuerzel2" size="1" style="width:100px"><option value="">&nbsp;-&nbsp;</option>
<?php
foreach ($kuerzelListe as $kuerzel) {
	echo '<option>'.$kuerzel.'</option>';
}
?>
</select></p>
<p><input type="submit" value="<?php echo _('Namen ändern'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<?php } ?>
<?php } else { ?>
<h1><?php echo _('Namensänderung'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
