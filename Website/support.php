<?php include 'zz1.php'; ?>
<title><?php echo _('Support'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.ungelesen td {
	font-weight: bold;
}
.teamRequest td {
	background-color: #ddd;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
// CHAT-SPERREN ANFANG
$blockCom1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
$blockCom2 = mysql_query($blockCom1);
if (mysql_num_rows($blockCom2) > 0) {
	$blockCom3 = mysql_fetch_assoc($blockCom2);
	$chatSperreBis = $blockCom3['MAX(chatSperre)'];
	if ($chatSperreBis > 0 && $chatSperreBis > time()) {
		addInfoBox(__('Du bist noch bis zum %1$s Uhr für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das %2$s', date('d.m.Y H:i', $chatSperreBis), '<a class="inText" href="/wio.php">'._('Support-Team.').'</a>'));
		include 'zz3.php';
		exit;
	}
}
// CHAT-SPERREN ENDE
?>
<h1><?php echo _('Unser Support-Bereich'); ?></h1>
<p style="text-align:right"><a href="/support.php?mark=read" class="pagenava" onclick="return confirm('<?php echo _('Bist Du sicher?'); ?>')"><?php echo _('Alle als gelesen markieren'); ?></a> <a href="/support.php" class="pagenava"><?php echo _('Support-Hauptseite'); ?></a> <a href="/supportAdd.php" class="pagenava"><?php echo _('Neue Anfrage'); ?></a></p>
<p><?php echo _('Du hast Vorschläge, wie wir das Spiel besser gestalten können? Du hast noch Fragen zum Spiel oder Du hast einen Fehler gefunden?'); ?></p>
<p><?php echo _('Auf dieser Seite kannst Du Ideen und Fragen eintragen und die Einträge von anderen Usern bewerten und kommentieren. Vielen Dank für Deine Hilfe!'); ?></p>
<h1><?php echo _('Anfragen durchsuchen'); ?></h1>
<form action="/support.php" method="get" accept-charset="utf-8">
<p><input type="text" name="q" style="width:200px" /> <input type="submit" value="<?php echo _('Suchen'); ?>" /></p>
</form>
<?php
// ANFRAGE LÖSCHEN ANFANG
if (isset($_GET['del']) && ($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer') && $cookie_id != CONFIG_DEMO_USER) {
	$delID = bigintval(secure2id($_GET['del']));
	$sql1 = "SELECT author, pro, contra FROM ".$prefix."supportRequests WHERE id = ".$delID;
	$sql2 = mysql_query($sql1) or die(mysql_error());
	if (mysql_num_rows($sql2) == 1) {
		$delVoteCount = mysql_fetch_assoc($sql2);
		$sql1 = "UPDATE ".$prefix."supportRequests SET visibilityLevel = 2 WHERE id = ".$delID." AND open = 1";
		$sql2 = mysql_query($sql1) or die(mysql_error());
		if (mysql_affected_rows() == 1) {
			$sql1 = "DELETE FROM ".$prefix."supportComments WHERE requestID = ".$delID;
			$sql2 = mysql_query($sql1) or die(mysql_error());
			$sql1 = "DELETE FROM ".$prefix."supportVotes WHERE request = ".$delID;
			$sql2 = mysql_query($sql1) or die(mysql_error());
			addInfoBox(_('Die ausgewählte Anfrage wurde vollständig gelöscht.'));
		}
	}
}
// ANFRAGE LÖSCHEN ENDE
// SICHTBARKEITSSTUFEN ANFANG
$addSql = " WHERE visibilityLevel = 0";
if ($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer') {
	$addSql = " WHERE visibilityLevel < 2"; // 0=öffentlich, 1=Team-only, 2=gelöscht
}
// SICHTBARKEITSSTUFEN ENDE
// NEUE ANFRAGE ERSTELLEN ANFANG
if (isset($_POST['category']) && isset($_POST['title']) && isset($_POST['description']) && $cookie_id != CONFIG_DEMO_USER) {
	$newCategory = mysql_real_escape_string(trim(strip_tags($_POST['category'])));
	$newTitle = mysql_real_escape_string(trim(strip_tags($_POST['title'])));
	if (in_array($cookie_id, unserialize(CONFIG_PROTECTED_USERS))) { // für den Administrator
		$newDescription = mysql_real_escape_string(trim(strip_tags(nl2br($_POST['description']), '<br><strong><p><a>'))); // mehrere HTML-Tags erlauben
	}
	else { // für alle anderen User
		$newDescription = mysql_real_escape_string(trim(strip_tags(nl2br($_POST['description']), '<br>'))); // nur den Zeilenumbruch erlauben
	}
	if (isset($_POST['visibility']) && ($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer')) {
		$newVisibility = intval($_POST['visibility']);
	}
	else {
		$newVisibility = 0;
	}
	$sql1 = "INSERT INTO ".$prefix."supportRequests (category, title, description, timeAdded, lastAction, author, visibilityLevel) VALUES ('".$newCategory."', '".$newTitle."', '".$newDescription."', ".time().", ".time().", '".$cookie_id."', ".$newVisibility.")";
	$sql2 = mysql_query($sql1);
	if ($sql2 == FALSE) {
		addInfoBox(_('Sorry, Du kannst nicht zwei Anfragen mit dem gleichen Titel erstellen.'));
	}
	else {
		$sql1 = "INSERT INTO ".$prefix."supportVotes (request, userID, vote) VALUES (".mysql_insert_id().", '".$cookie_id."', 1)";
		$sql2 = mysql_query($sql1);
		addInfoBox(_('Danke, Deine Anfrage wurde erstellt.'));
	}
}
// NEUE ANFRAGE ERSTELLEN ENDE
$isSearchPage = FALSE;
$q = '';
if (isset($_GET['q'])) {
	$q = mysql_real_escape_string(trim(strip_tags($_GET['q'])));
	if (strlen($q) > 3) {
		$isSearchPage = TRUE;
	}
}
if ($isSearchPage == TRUE) { echo '<h1>'.__('Aktuelle Anfragen zum Thema &quot;%s&quot;', $q).'</h1>'; } else { echo '<h1>'._('Aktuelle Anfragen').'</h1>'; }
if (isset($_GET['mark'])) {
	if ($_GET['mark'] == 'read') {
		$markRead1 = "INSERT IGNORE INTO ".$prefix."supportRead (userID, anfrageID) SELECT '".$cookie_id."' AS user, id FROM ".$prefix."supportRequests WHERE open = 1";
		$markRead2 = mysql_query($markRead1);
		if ($markRead2 == FALSE) {
			addInfoBox(_('Es konnten nicht alle Themen als gelesen markiert werden (E073).'));
		}
		else {
			//$_SESSION['last_forumneu_anzahl'] = 0;
			addInfoBox(_('Alle Themen wurden als gelesen markiert.'));
		}
	}
}
if (isset($_GET['msg'])) {
	if ($_GET['msg'] == 'replied') {
		addInfoBox(_('Danke, Dein Kommentar wurde gespeichert.'));
	}
	elseif ($_GET['msg'] == 'edited') {
		addInfoBox(_('Danke, Dein Kommentar wurde geändert.'));
	}
}
function time_rel_s($zeitstempel) {
	$ago = time()-$zeitstempel;
    if ($ago < 60) { $agos = 'vor kurzem'; }
    elseif ($ago < 3600) { $ago1 = round($ago/60, 0); if ($ago1 == 1) { $agos = 'vor 1m'; } else { $agos = 'vor '.$ago1.'m'; } }
    elseif ($ago < 86400) { $ago1 = round($ago/3600, 0);  if ($ago1 == 1) { $agos = 'vor 1h'; } else { $agos = 'vor '.$ago1.'h'; } }
    else { $ago1 = round($ago/86400, 0);  if ($ago1 == 1) { $agos = 'vor 1d'; } else { $agos = 'vor '.$ago1.'d'; } }
	return $agos;
}
?>
<?php
$geleseneRequests = array();
$sql1 = "SELECT anfrageID FROM ".$prefix."supportRead WHERE userID = '".$cookie_id."'";
$sql2 = mysql_query($sql1);
if ($sql2 == FALSE) { exit; }
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$geleseneRequests[$sql3['anfrageID']] = TRUE;
}
?>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Bewertung'); ?></th>
<th scope="col"><?php echo _('Anfrage'); ?></th>
<th scope="col"><?php echo _('Aktivität'); ?></th>
</tr>
</thead>
<tbody>
<?php
// MARKIERUNGEN (GEVOTED + GELESEN) ANFANG
$listVoted = array();
$sql1 = "SELECT request FROM ".$prefix."supportVotes WHERE userID = '".$cookie_id."'";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$listVoted[$sql3['request']] = TRUE;
}
// MARKIERUNGEN (GEVOTED + GELESEN) ENDE
$sql1 = "SELECT id, pro, contra, category, title, description, lastAction, open, visibilityLevel FROM ".$prefix."supportRequests".$addSql;
if ($isSearchPage) {
	$sql1 .= " AND MATCH (description) AGAINST ('".$q."')";
}
$sql1 .= " ORDER BY open DESC, lastAction DESC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
if ($sql2 == FALSE) { exit; }
if (mysql_num_rows($sql2) == 0) {
	echo '<tr><td colspan="3">'.__('Es gibt noch keine Anfragen. %s', '<a href="/supportAdd.php">'._('Mache den Anfang!').'</a>').'</td></tr>';
	echo '</tbody>';
	echo '</table>';
}
else {
	$blaetter3 = anzahl_datensaetze_gesamt($sql1);
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		// CSS-KLASSEN BESTIMMEN ANFANG
		$trCSS = '';
		if (!isset($geleseneRequests[$sql3['id']]) && $sql3['open'] == 1) { $trCSS .= ' ungelesen'; }
		if ($sql3['visibilityLevel'] == 1) { $trCSS .= ' teamRequest'; }
		$trCSS = trim($trCSS);
		echo '<tr';
		if ($trCSS != '') { echo ' class="'.$trCSS.'"'; }
		echo '>';
		// CSS-KLASSEN BESTIMMEN ENDE
		if ($sql3['open'] != 1) {
			echo '<td>&nbsp;</td><td colspan="2"><strong>';
			if ($sql3['open'] == 0) {
				echo _('Umgesetzt');
			}
			else {
				if ($sql3['category'] == 'Vorschlag') {
					echo _('Abgelehnt');
				}
				else {
					echo _('Geklärt');
				}
			}
			echo ':</strong> <a href="/supportRequest.php?id='.id2secure($sql3['id']).'">'.$sql3['title'].'</a></td>';
		}
		else {
			echo '<td>';
			if ($sql3['category'] != 'Vorschlag') {
				echo '<img src="/images/balken/50.png" alt="Frage/Fehlerbericht" title="'._('Frage / Fehlerbericht').'" />';
			}
			elseif (isset($listVoted[$sql3['id']])) {
				$pVotes = round($sql3['pro']/($sql3['pro']+$sql3['contra'])*100);
				echo '<img src="/images/balken/'.$pVotes.'.png" alt="'.$pVotes.'%" title="'.__('%d%% sind für diesen Vorschlag', $pVotes).'" />';
			}
			else {
				echo '<img src="/images/balken/undefined.png" alt="???" title="'._('Du hast diesen Vorschlag noch nicht bewertet').'" />';
			}
			echo '</td>';
			echo '<td class="link"><a href="/supportRequest.php?id='.id2secure($sql3['id']).'" title="'._('Zur Anfrage mit Beschreibung').'">'.$sql3['title'].'</a></td>';
			echo '<td class="link"><a href="/supportRequest.php?id='.id2secure($sql3['id']).'#lastComment" title="'._('Zum letzten Kommentar').'">'.time_rel_s($sql3['lastAction']).'</a></td>';
		}
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	// PAGE-NAVIGATION ANFANG
	$q = urlencode($q);
	echo '<div class="pagebar">';
	$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
	$vorherige = $seite-1;
	if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
	if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
	$naechste = $seite+1;
	$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
	$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
	$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
	$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
	echo '<span class="this-page">'.$seite.'</span> ';
	$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
	$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
	$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
	$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
	if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
	if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
	echo '</div>';
	// PAGE-NAVIGATION ENDE
}
?>
<h1><?php echo _('Aktive Manager in diesem Bereich'); ?></h1>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Manager'); ?></th>
<th scope="col" colspan="2"><?php echo _('Antworten'); ?></th>
<th scope="col">&quot;<?php echo _('Guter Kommentar'); ?>&quot;</th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT a.userID, a.replies, a.fastReplies, a.thanksReceived, a.votes, b.username FROM ".$prefix."supportUsers AS a JOIN ".$prefix."users AS b ON a.userID = b.ids ORDER BY a.points DESC LIMIT 0, 10";
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$displayUsername = displayUsername($sql3['username'], $sql3['userID']);
	if ($displayUsername == 'Gelöschter User') { continue; }
	echo '<tr>';
	echo '<td>'.$counter.'.</td>';
	echo '<td class="link">'.$displayUsername.'</td>';
	echo '<td>'.__('<b>%d</b> insgesamt', $sql3['replies']).'</td>';
	echo '<td>'.__('<b>%d</b> schnelle', $sql3['fastReplies']).'</td>';
	echo '<td>'.__('<b>%d</b> Stimmen erhalten', $sql3['thanksReceived']).'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<?php
$myRequests1 = "SELECT id, title FROM ".$prefix."supportRequests WHERE author = '".$cookie_id."' ORDER BY id DESC LIMIT 0, 5";
$myRequests2 = mysql_query($myRequests1);
if (mysql_num_rows($myRequests2) > 0) {
	echo '<h1>'._('Meine letzten Anfragen').'</h1><ul>';
	while ($myRequests3 = mysql_fetch_assoc($myRequests2)) {
		echo '<li><a href="/supportRequest.php?id='.id2secure($myRequests3['id']).'">'.$myRequests3['title'].'</a></li>';
	}
	echo '</ul>';
}
?>
<?php } else { ?>
<h1><?php echo _('Support'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
