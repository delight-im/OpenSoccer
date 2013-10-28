<?php include 'zz1.php'; ?>
<?php
if (!isset($_GET['id'])) { exit; }
function getTestspielPreis($liga, $team) {
	global $prefix;
	$bql1 = "SELECT name FROM ".$prefix."ligen WHERE ids = '".$liga."'";
	$bql2 = mysql_query($bql1);
	$bql3 = mysql_fetch_assoc($bql2);
	$ligaNr = intval(substr($bql3['name'], -1));
	if ($ligaNr == 4) {
		return 50000;
	}
	elseif ($ligaNr == 3) {
		return 100000;
	}
	elseif ($ligaNr == 2) {
		return 500000;
	}
	else {
		return 1000000;
	}
}
$clearedID = mysql_real_escape_string($_GET['id']);
$sql1 = "SELECT username, regdate, last_login, liga, team, status, infotext FROM ".$prefix."users WHERE ids = '".$clearedID."'";
$sql2 = mysql_query($sql1);
$sql2a = mysql_num_rows($sql2);
if ($sql2a == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
$urlaub1 = "SELECT COUNT(*) FROM ".$prefix."urlaub WHERE user = '".$clearedID."' AND ende > ".time();
$urlaub2 = mysql_query($urlaub1);
$urlaub3 = mysql_result($urlaub2, 0);
$liga1 = "SELECT name FROM ".$prefix."ligen WHERE ids = '".$sql3['liga']."'";
$liga2 = mysql_query($liga1);
if (mysql_num_rows($liga2) == 0) {
	$liga3 = '';
	$ligaTdContent = '<td><i>keine</i></td>';
}
else {
	$liga3 = mysql_fetch_assoc($liga2);
	$liga3 = $liga3['name'];
	$ligaTdContent = '<td class="link"><a href="/lig_tabelle.php?liga='.$sql3['liga'].'">'.$liga3.'</a></td>';
}
if ($sql3['team'] != '__'.$clearedID) {
	$team1 = "SELECT name, wantTests FROM ".$prefix."teams WHERE ids = '".$sql3['team']."'";
	$team2 = mysql_query($team1);
	$team3 = mysql_fetch_assoc($team2);
	$wantTests = $team3['wantTests'];
	$team3 = $team3['name'];
	$teamPageLink = '<a href="/team.php?id='.$sql3['team'].'">'.$team3.'</a>';
	$teamTdClass = ' class="link"';
}
else {
	$team3 = '<i>keins</i>';
	$wantTests = 0;
	$teamPageLink = $team3;
	$teamTdClass = '';
}
$temp = time()-$sql3['last_login'];
if ($temp >= 8640000) { $letzte_aktion = 'langer Zeit ;)'; }
elseif ($temp >= 86400) { $letzte_aktion = round($temp/86400); $letzte_aktion = $letzte_aktion.' Tagen'; }
elseif ($temp >= 3600) { $letzte_aktion = round($temp/3600); $letzte_aktion = $letzte_aktion.' Stunden'; }
elseif ($temp >= 60) { $letzte_aktion = round($temp/60); $letzte_aktion = $letzte_aktion.' Minuten'; }
else { $letzte_aktion = 'wenigen Sekunden'; }
?>
<title>Manager: <?php echo $sql3['username']; ?> | Ballmanager.de</title>
<script type="text/javascript">
function updateTextLength(element) {
	var counter = document.getElementById('infotext_counter');
	if (typeof(counter) != 'undefined' && counter != null && typeof(element) != 'undefined' && element != null) {
		var textLength = element.value.length;
		if (textLength > 5000) {
			element.value = element.value.substring(0, 5000);
			counter.innerHTML = '5000';
		}
		else {
			counter.innerHTML = textLength;
		}
	}
}
window.onload = function() {
	updateTextLength(document.getElementById('infotext'));
}
</script>
<?php include 'zz2.php'; ?>
<?php
$specialStatus = '';
$specialStatusCSS = 'display:inline-block; padding:1px 4px; font-size:90%; margin:0 2px; background-color:#2556A5; color:#fff;';
switch ($sql3['status']) {
	case 'Admin': $specialStatus = ' <span style="'.$specialStatusCSS.'">Administrator</span>'; break;
	case 'Helfer': $specialStatus = ' <span style="'.$specialStatusCSS.'">Support-Team</span>'; break;
	case 'Bigpoint': $specialStatus = ' <span style="'.$specialStatusCSS.'">Bigpoint-User</span>'; break;
}
?>
<h1>Manager: <?php echo $sql3['username'].$specialStatus; ?></h1>
<?php if ($loggedin == 1) { ?>
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
// MULTI-ACCOUNTS ANFANG
if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
	$mql1 = "SELECT a.user2, a.found_time, b.username, b.team FROM ".$prefix."users_multis AS a JOIN ".$prefix."users AS b ON a.user2 = b.ids WHERE a.user1 = '".mysql_real_escape_string($_GET['id'])."'";
	$mql2 = mysql_query($mql1);
	if (mysql_num_rows($mql2) != 0) {
		$geloeschteMultis = 0;
		$multi_listeStr = '<p><strong>Multi-Accounts:</strong> ';
		while ($mql3 = mysql_fetch_assoc($mql2)) {
			//if (substr($mql3['username'], 0, 9) == 'GELOESCHT') {
			if (strlen($mql3['team']) != 32) {
				$geloeschteMultis++;
			}
			else {
				$multi_listeStr .= '<a href="/manager.php?id='.$mql3['user2'].'" title="Gefunden: '.date('d.m.Y H:i', $mql3['found_time']).' Uhr">'.$mql3['username'].'</a>, ';
			}
		}
		$multi_listeStr = substr($multi_listeStr, 0, -2);
		if ($geloeschteMultis == 1) {
			$multi_listeStr .= ' und 1 Inaktiver';
		}
		elseif ($geloeschteMultis > 1) {
			$multi_listeStr .= ' und '.$geloeschteMultis.' Inaktive';
		}
		$multi_listeStr .= '</p>';
		echo $multi_listeStr;
	}
	else {
		echo '<p><strong>Multi-Accounts:</strong> keine</p>';
	}
}
// MULTI-ACCOUNTS ENDE
// TEAM-TAUSCH MIT CODE ANFANG
$ttc1 = "SELECT zeit, team2 FROM ".$prefix."teamChanges WHERE team1 = '".$sql3['team']."' AND zeit > ".$sql3['regdate']." LIMIT 0, 1";
$ttc2 = mysql_query($ttc1);
if (mysql_num_rows($ttc2) > 0) {
	$ttc3 = mysql_fetch_assoc($ttc2);
	echo '<p><strong>Letzter Team-Tausch:</strong> <a href="/team.php?id='.$ttc3['team2'].'">'.date('d.m.Y H:i', $ttc3['zeit']).' Uhr</a></p>';
}
// TEAM-TAUSCH MIT CODE ENDE
if ($urlaub3 > 0 && $clearedID != 'c4ca4238a0b923820dcc509a6f75849b') {
	echo '<p style="color:red">'.$sql3['username'].' ist zurzeit im Urlaub!</p>';
}
?>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">Bereich</th>
<th scope="col">Wert</th>
</tr>
<?php include 'manager_kontaktlink.php'; ?>
</thead>
<tbody>
<?php
echo '<tr class="odd"><td>Name</td><td>'.$sql3['username'].'</td></tr>';
echo '<tr><td>Dabei seit</td><td>'.date('d.m.Y H:i', $sql3['regdate']).'</td></tr>';
echo '<tr class="odd"><td>Liga</td>'.$ligaTdContent.'</tr>';
echo '<tr><td>Team</td><td'.$teamTdClass.'>'.$teamPageLink.'</td></tr>';
echo '<tr class="odd"><td>Letzte Aktion</td><td>';
if ($sql3['regdate'] > $sql3['last_login']) {
	echo 'noch keine';
}
else {
	echo 'vor '.$letzte_aktion;
}
echo '</td></tr>';
if ($_GET['id'] != $cookie_id) {
	echo '<tr><td colspan="2" class="link"><a href="/post_schreiben.php?id='.$_GET['id'].'"'.noDemoClick($cookie_id).'>'.$sql3['username'].' jetzt eine Nachricht schicken</a></td></tr>';
	if (isset($kontakt_link)) { echo $kontakt_link; }
}
if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
	echo '<tr class="odd"><td colspan="2" class="link"><a href="/sanktionen.php?profileID='.$clearedID.'">Sanktion für '.$sql3['username'].' festlegen</a></td></tr>';
}
?>
</tbody>
</table>
</p>
<?php
if ($sql3['team'] != '__'.$cookie_id && $clearedID != '__'.$cookie_id) {
	if ($wantTests == 1) {
		// MEHERE TESTSPIELE AM GLEICHEN TAG VERHINDERN ANFANG
		$testspiel_tage = array();
		$zm1 = "SELECT datum FROM ".$prefix."testspiel_anfragen WHERE team1 = '".$cookie_team."'";
		$zm2 = mysql_query($zm1);
		while ($zm3 = mysql_fetch_assoc($zm2)) {
			$testspiel_tage[] = $zm3['datum'];
		}
		$zm1 = "SELECT datum FROM ".$prefix."spiele WHERE typ = 'Test' AND (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."')";
		$zm2 = mysql_query($zm1);
		while ($zm3 = mysql_fetch_assoc($zm2)) {
			$testspiel_tage[] = $zm3['datum'];
		}
		$zm1 = "SELECT datum FROM ".$prefix."spiele WHERE typ = 'Test' AND (team1 = '".mysql_real_escape_string($team3)."' OR team2 = '".mysql_real_escape_string($team3)."')";
		$zm2 = mysql_query($zm1);
		$zm2n = mysql_num_rows($zm2);
		while ($zm3 = mysql_fetch_assoc($zm2)) {
			$testspiel_tage[] = $zm3['datum'];
		}
		// MEHERE TESTSPIELE AM GLEICHEN TAG VERHINDERN ENDE
		$zm1 = "SELECT COUNT(*) FROM ".$prefix."testspiel_anfragen WHERE (team1 = '".$cookie_team."' AND team2 = '".mysql_real_escape_string($sql3['team'])."') OR (team2 = '".$cookie_team."' AND team1 = '".mysql_real_escape_string($sql3['team'])."')";
		$zm2 = mysql_query($zm1);
		$zm2a = mysql_result($zm2, 0);
		if ($_GET['id'] != $cookie_id) {
			if ($zm2a == 0) {
				$gesp3_noch = 21-$cookie_spieltag;
				if ($gesp3_noch < 3) {
					echo '<h1>Testspiel vereinbaren</h1><p><strong>Zu spät:</strong> In dieser Saison können leider keine Testspiele mehr vereinbart werden.</p>';
				}
				elseif ($getkonto4 < getTestspielPreis($cookie_liga, $cookie_team)) {
					echo '<h1>Testspiel vereinbaren</h1><p><strong>Zu teuer:</strong> Im Moment hast Du leider nicht genügend Geld, um ein Testspiel vereinbaren zu können.</p>';
				}
				else {
					$optionsStr = '';
					$optionsStr .= '<h1>Testspiel vereinbaren</h1>';
					$optionsStr .= '<p><strong>Wichtig:</strong> Das Testspiel findet immer im Stadion des Anfragenden statt. Beide Teams müssen für ein Testspiel eine Entschädigung an den Verband zahlen, damit das Spiel genehmigt wird. Für Dich sind das '.number_format(getTestspielPreis($cookie_liga, $cookie_team), 0, ',', '.').' €.</p>';
					$optionsStr .= '<form action="/testspiel_anfrage.php" method="get" accept-charset="utf-8">';
					$heute_tag = date('d', time());
					$heute_monat = date('m', time());
					$heute_jahr = date('Y', time());
					$datum_spiel = mktime(23, 00, 00, $heute_monat, $heute_tag, $heute_jahr);
					$nochMoeglicheTage = 0;
					$optionsStr .= '<p><select name="datum" size="1" style="width:200px">';
					for ($d = 2; $d < $gesp3_noch; $d++) {
						$datum_spiel_temp = getTimestamp('+'.$d.' days', $datum_spiel);
						if (!in_array($datum_spiel_temp, $testspiel_tage)) {
							$optionsStr .= '<option value="'.$datum_spiel_temp.'">'.date('d.m.Y', $datum_spiel_temp).'</option>';
							$nochMoeglicheTage++;
						}
					}
					$optionsStr .= '</select>';
					$optionsStr .= ' <input type="hidden" name="id" value="'.$sql3['team'].'" /><input type="submit" value="Anfragen"'.noDemoClick($cookie_id).' /></p>';
					$optionsStr .= '</form>';
					if ($nochMoeglicheTage == 0) {
						echo '<h1>Testspiel vereinbaren</h1><p>Eure Vereine haben keine freien Termine mehr für ein Testspiel.</p>';
					}
					else {
						echo $optionsStr;
					}
				}
			}
			else {
				echo '<h1>Testspiel vereinbaren</h1><p>Zwischen Dir und '.$sql3['username'].' laufen schon Verhandlungen für ein Testspiel. Du kannst keine weitere Anfrage senden.</p>';
			}
		}
	}
	else {
		echo '<h1>Testspiel vereinbaren</h1><p>Der Verein hat kein Interesse an Testspielen.</p>';
	}
}
// GAESTEBUCH ANFANG
if (isset($_POST['gaestebuch_eintrag']) && $cookie_id != DEMO_USER_ID) {
	// CHAT-SPERREN ANFANG
	$ban1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
	$ban2 = mysql_query($ban1);
	if (mysql_num_rows($ban2) > 0) {
		$ban3 = mysql_fetch_assoc($ban2);
		$chatSperreBis = $ban3['MAX(chatSperre)'];
		if ($chatSperreBis > 0 && $chatSperreBis > time()) {
			echo addInfoBox('Du bist noch bis zum '.date('d.m.Y H:i', $chatSperreBis).' Uhr für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das <a class="inText" href="/wio.php">Ballmanager-Team.</a>');
			include 'zz3.php';
			exit;
		}
	}
	// CHAT-SPERREN ENDE
	$gb_text = mysql_real_escape_string(trim(strip_tags($_POST['gaestebuch_eintrag'])));
	$gb_in1 = "INSERT INTO ".$prefix."chats (user, zeit, nachricht, liga) VALUES ('".$cookie_id."', ".time().", '".$gb_text."', 'GB".mysql_real_escape_string($_GET['id'])."')";
	$gb_in2 = mysql_query($gb_in1);
}
if (isset($_GET['delGB']) && $cookie_id != DEMO_USER_ID) {
	$delGB = mysql_real_escape_string(trim(strip_tags($_GET['delGB'])));
	$addSql = " AND (user = '".$cookie_id."' OR liga = 'GB".mysql_real_escape_string($cookie_id)."')";
	if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { $addSql = ""; }
	$gb_in1 = "DELETE FROM ".$prefix."chats WHERE id = ".$delGB.$addSql;
	$gb_in2 = mysql_query($gb_in1);
}
echo '<h1 id="anker_gaestebuch">Gästebuch</h1>';
echo '<form action="/manager.php?id='.$_GET['id'].'" method="post" accept-charset="utf-8">';
echo '<p><input type="text" name="gaestebuch_eintrag" style="width:80%" /> <input type="submit" value="Eintragen"'.noDemoClick($cookie_id).' /></p>';
echo '</form>';
$gb1 = "SELECT a.id, a.user, a.zeit, a.nachricht, b.username FROM ".$prefix."chats AS a JOIN ".$prefix."users AS b ON a.user = b.ids WHERE a.liga = 'GB".mysql_real_escape_string($_GET['id'])."' ORDER BY a.zeit DESC LIMIT 0, 20";
$gb2 = mysql_query($gb1);
if (mysql_num_rows($gb2) == 0) {
	echo '<p>Das Gästebuch von '.$sql3['username'].' ist noch leer. Sei der Erste, der sich einträgt!</p>';
}
else {
	while ($gb3 = mysql_fetch_assoc($gb2)) {
		echo '<p><b>'.displayUsername($gb3['username'], $gb3['user']).' schrieb am '.date('d.m.Y, H:i', $gb3['zeit']).':';
		if ($_GET['id'] == $cookie_id OR $gb3['user'] == $cookie_id OR $_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
			echo ' <a href="/manager.php?id='.mysql_real_escape_string($_GET['id']).'&amp;delGB='.$gb3['id'].'">[Löschen]</a>';
		}
		echo '</b><br />'.$gb3['nachricht'].'</p>';
	}
}
// GAESTEBUCH ENDE
// INFOTEXT ANFANG
$infotext = trim($sql3['infotext']);
function br2nl($text) {
	return str_replace('<br />', '', $text);
}
$isOwnProfile = FALSE;
if ($_GET['id'] == $cookie_id && $cookie_id != DEMO_USER_ID) {
	$isOwnProfile = TRUE;
	if (isset($_POST['infotext'])) {
		$infotext = nl2br(mb_substr(strip_tags(trim($_POST['infotext'])), 0, 10000));
		$infotext1 = "UPDATE ".$prefix."users SET infotext = '".mysql_real_escape_string($infotext)."' WHERE ids = '".$cookie_id."'";
		$infotext2 = mysql_query($infotext1);
	}
}
echo '<h1 id="anker_infotext">Infotext'.($isOwnProfile ? ' (<span id="infotext_counter">0</span>/5000 Zeichen)' : '').'</h1>';
if ($isOwnProfile) { // eigenes Profil
	echo '<form action="/manager.php?id='.$_GET['id'].'" method="post" accept-charset="utf-8">';
	echo '<p><textarea rows="15" cols="12" id="infotext" name="infotext" style="width:450px; height:300px" maxlength="5000" onkeyup="updateTextLength(this);">'.br2nl($infotext).'</textarea></p>';
	echo '<p><input type="submit" value="Speichern"'.noDemoClick($cookie_id).' /></p>';
}
else { // fremdes Profil
	if (strlen($infotext) == 0) {
		echo '<p>'.$sql3['username'].' hat noch keinen Text über sich und den Verein geschrieben.</p>';
	}
	else {
		echo '<p>'.$infotext.'</p>';
	}
}
// INFOTEXT ENDE
?>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>