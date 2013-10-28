<?php include 'zz1.php'; ?>
<title>Namensänderung | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Namensänderung</h1>
<?php if ($loggedin == 1) { ?>
<?php if ($cookie_team != '__'.$cookie_id) { ?>
<?php
$letzteAenderung1 = "SELECT MAX(zeit) FROM ".$prefix."vNameChanges WHERE team = '".$cookie_team."' AND sperre = 1";
$letzteAenderung2 = mysql_query($letzteAenderung1);
if (mysql_num_rows($letzteAenderung2) == 0) {
	$letzteAenderungVor = time();
}
else {
	$letzteAenderung3 = mysql_fetch_assoc($letzteAenderung2);
	$letzteAenderungVor = time()-$letzteAenderung3['MAX(zeit)'];
}
if ($letzteAenderungVor < 2592000) { // vor 30 Tagen
	$lastTeamNameChange = round($letzteAenderungVor/86400);
	echo addInfoBox('Der Name Deines Vereins wurde zuletzt vor '.$lastTeamNameChange.' Tagen geändert. Du musst also noch '.intval(30-$lastTeamNameChange).' Tage warten, bis Du ihn wieder ändern kannst.');
	include 'zz3.php';
	exit;
}
?>
<p>Auf dieser Seite kannst Du den Namen Deines Vereins ändern. Du kannst den Namen einer Stadt und einen beliebigen Zusatz aus der Liste wählen, der vor oder nach dem Städtenamen stehen kann.</p>
<p>Hier fehlt ein Städtename, den Du gerne dabei hättest? Dann <a href="/post_schreiben.php?id=c4ca4238a0b923820dcc509a6f75849b">sag uns Bescheid</a>, vielleicht kommt er dann dazu!</p>
<p><strong>Hinweis:</strong> Du kannst den Namen Deines Klubs nur alle 30 Tage ändern.</p>
<?php
if (isset($_POST['kuerzel1']) && isset($_POST['kuerzel2']) && isset($_POST['stadt']) && $cookie_id != DEMO_USER_ID) {
	if ($live_scoring_spieltyp_laeuft == '') {
		$kuerzel1 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel1'])));
		$kuerzel2 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel2'])));
		$stadt = mysql_real_escape_string(trim(strip_tags($_POST['stadt'])));
		if (in_array($kuerzel1, $kuerzelListe) OR in_array($kuerzel2, $kuerzelListe)) {
			if ($kuerzel1 != '' && $kuerzel2 != '') {
				echo addInfoBox('Du darfst nur <i>einen</i> Zusatz für den Namen wählen. Bitte entscheide Dich für einen und versuche es noch einmal.');
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
					echo addInfoBox('Dieser Name steht auf der Sperrliste, weil er einem realen Vereins-Namen zu sehr ähnelt. Bitte versuche es noch einmal mit einem anderen Namen.');
				}
				else {
					$sql1 = "UPDATE man_teams SET name = '".$neuerName."' WHERE name = '".$cookie_teamname."'";
					$sql2 = mysql_query($sql1);
					if ($sql2 == FALSE) {
						echo addInfoBox('Dieser Name ist leider schon vergeben. Bitte versuche es noch einmal mit einem anderen Namen.');
					}
					else {
						$sql3 = "INSERT INTO ".$prefix."vNameChanges (team, zeit, vonName, zuName) VALUES ('".$cookie_team."', ".time().", '".$cookie_teamname."', '".$neuerName."')";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."pokalsieger SET sieger = '".$neuerName."' WHERE sieger = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."pokalsieger SET finalgegner = '".$neuerName."' WHERE finalgegner = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."cupsieger SET sieger = '".$neuerName."' WHERE sieger = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."cupsieger SET finalgegner = '".$neuerName."' WHERE finalgegner = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."geschichte_tabellen SET team = '".$neuerName."' WHERE team = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."spiele SET team1 = '".$neuerName."' WHERE team1 = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."spiele SET team2 = '".$neuerName."' WHERE team2 = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."testspiel_anfragen SET team1_name = '".$neuerName."' WHERE team1_name = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$sql3 = "UPDATE ".$prefix."transfermarkt_leihe SET bieter = '".$neuerName."' WHERE bieter = '".$cookie_teamname."'";
						$sql4 = mysql_query($sql3) or reportError(mysql_error(), $sql3);
						$_SESSION['teamname'] = $neuerName;
						$cookie_teamname = $neuerName;
						echo addInfoBox('Dein Verein heißt jetzt: '.$neuerName);
					}
				}
			}
		}
		else {
			echo addInfoBox('Du musst <i>einen</i> Zusatz wählen. Bitte versuche es noch einmal.');
		}
	}
	else {
		echo addInfoBox('Du kannst den Namen Deines Vereins nur außerhalb der Spielzeiten ändern. Zurzeit laufen '.$live_scoring_spieltyp_laeuft.'spiele.');
	}
}
?>
<form action="namensaenderung.php" method="post" accept-charset="utf-8">
<p><select name="kuerzel1" size="1" style="width:100px"><option value="">&nbsp;-&nbsp;</option>
<?php
foreach ($kuerzelListe as $kuerzel) {
	echo '<option>'.$kuerzel.'</option>';
}
?>
</select> <select name="stadt" size="1" style="width:200px">
<?php
$sql1 = "SELECT name FROM ".$prefix."ligen WHERE ids = '".$cookie_liga."'";
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
<p><input type="submit" value="Namen ändern"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<?php } ?>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>