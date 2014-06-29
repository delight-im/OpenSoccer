<?php include 'zz1.php'; ?>
<title>Forum | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1>Suchen im Forum</h1>
<form action="/forum.php" method="get" accept-charset="utf-8">
<p><input type="text" name="q" style="width:200px" /> <input type="submit" value="Suchen" /></p>
</form>
<?php
$addSql = " AND sichtbar_fuer = 'Alle'";
if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { $addSql = ""; }
$isSearchPage = FALSE;
$q = '';
if (isset($_GET['q'])) {
	$q = mysql_real_escape_string(trim(strip_tags($_GET['q'])));
	if (strlen($q) > 3) {
		$isSearchPage = TRUE;
	}
}
$cat = '';
if (isset($_GET['cat'])) {
	$cat = mysql_real_escape_string(trim(strip_tags($_GET['cat'])));
	if (strlen($cat) > 3) {
		$addSql = " AND kategorie = '".$cat."'";
	}
}
if ($isSearchPage == TRUE) { echo '<h1>Deine Suche nach &quot;'.$q.'&quot;</h1>'; } else { echo '<h1>Forum</h1>'; }
if (isset($_GET['archive'])) {
	$themaToArchive = mysql_real_escape_string($_GET['archive']);
	if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
		$sql1 = "UPDATE ".$prefix."forum_themen SET datum = 0 WHERE ids = '".$themaToArchive."'";
		$sql2 = mysql_query($sql1);
		addInfoBox('Das Thema wurde archiviert.');
	}
}
if (isset($_GET['mark'])) {
	if ($_GET['mark'] == 'read') {
		$markRead1 = "INSERT IGNORE INTO ".$prefix."forum_gelesen (thema, user) SELECT ids, '".$cookie_id."' AS user FROM ".$prefix."forum_themen WHERE datum > 0";
		$markRead2 = mysql_query($markRead1);
		if ($markRead2 == FALSE) {
			addInfoBox('Es konnten nicht alle Themen als gelesen markiert werden (E073).');
		}
		else {
			$_SESSION['last_forumneu_anzahl'] = 0;
			addInfoBox('Alle Themen wurden als gelesen markiert.');
		}
	}
}
if (isset($_GET['msg'])) {
	if ($_GET['msg'] == 'replied') {
		addInfoBox('Danke, Deine Antwort wurde gespeichert.');
	}
	elseif ($_GET['msg'] == 'edited') {
		addInfoBox('Danke, Dein Beitrag wurde geändert.');
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
$gelesene_themen = array();
$sql1 = "SELECT thema FROM ".$prefix."forum_gelesen WHERE user = '".$cookie_id."'";
$sql2 = mysql_query($sql1);
if ($sql2 == FALSE) { exit; }
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$gelesene_themen[] = $sql3['thema'];
}
?>
<p>In diesem Forum werden Fragen beantwortet, Diskussionen zu neuen Funktionen und Verbesserungen geführt usw. Es wäre schön, wenn Du
ab und zu antworten würdest und Deine eigene Meinung zum jeweiligen Thema schreiben würdest. So hilfst Du uns, das Spiel weiter zu
verbessern. Danke!</p>
<p style="text-align:right"><a href="/forum.php?mark=read" class="pagenava" onclick="return confirm('Bist Du sicher?')">Alle als gelesen markieren</a> <a href="#" class="pagenava">Neues Thema</a><!-- /forum_eintrag_hinzufuegen.php --></p>
<table>
<thead>
<tr class="odd">
<th scope="col">Thema</th>
<th scope="col">Kategorie</th>
<th scope="col">Letzter Beitrag</th>
</tr>
</thead>
<tbody>
<?php
if ($isSearchPage) {
	// NUR TITEL DURCHSUCHEN: $sql1 = "SELECT ids, titel, manager, lastposter, datum, datum, sticky, sichtbar_fuer, kategorie, postCount FROM ".$prefix."forum_themen WHERE sichtbar = 'J'".$addSql." AND MATCH (titel) AGAINST ('".$q."') ORDER BY sticky DESC, datum DESC LIMIT ".$start.", ".$eintraege_pro_seite;
	$sql1 = "SELECT a.inhalt, b.ids, b.titel, b.manager, b.lastposter, b.datum, b.datum, b.sticky, b.sichtbar_fuer, b.kategorie, b.postCount FROM ".$prefix."forum_beitraege AS a JOIN ".$prefix."forum_themen AS b ON a.thema = b.ids WHERE b.sichtbar = 'J'".$addSql." AND MATCH (a.inhalt) AGAINST ('".$q."') GROUP BY b.ids ORDER BY COUNT(*) DESC, b.datum DESC LIMIT ".$start.", ".$eintraege_pro_seite;
}
else {
	$sql1 = "SELECT ids, titel, manager, lastposter, datum, datum, sticky, sichtbar_fuer, kategorie, postCount FROM ".$prefix."forum_themen WHERE sichtbar = 'J'".$addSql." ORDER BY sticky DESC, datum DESC LIMIT ".$start.", ".$eintraege_pro_seite;
}
$sql2 = mysql_query($sql1);
if ($sql2 == FALSE) { exit; }
if (mysql_num_rows($sql2) == 0) { exit; }
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
$themen_tabelle = array();
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($_SESSION['status'] != 'Helfer' && $_SESSION['status'] != 'Admin') {
		if ($sql3['sichtbar_fuer'] != 'Alle') {
			continue;
		}
	}
	if ($sql3['sticky'] == 1) {
		$sticky = '<img src="/images/daumen_hoch.png" alt="Top-Thema" title="Top-Thema" /> ';
	}
	else {
		$sticky = '';
	}
	$mark1 = '';
	$mark2 = '';
	if (!in_array($sql3['ids'], $gelesene_themen) && $sql3['datum'] != 0) {
		if ($sql3['sticky'] == 0 OR $_SESSION['readSticky'] == 1) {
            $mark1 = '<strong>';
            $mark2 = '</strong>';
		}
	}
	if ($sql3['datum'] == 0) {
		$faerbung = ' style="font-style:italic"';
		$aktualitaet = 'im Archiv';
	}
	else {
		$faerbung = '';
		$aktualitaet = time_rel_s($sql3['datum']);
	}
	$sichtbar_fuer_zusatz = '';
	if ($sql3['sichtbar_fuer'] == 'Helfer') {
		$sichtbar_fuer_zusatz = ' style="color:red"';
	}
	// IMMER LINK AUF LETZTE SEITE ANFANG
	$letzte_seite = ceil($sql3['postCount']/$eintraege_pro_seite);
	$pageLink = '/forum_thema.php?id='.$sql3['ids'].'&amp;seite='.$letzte_seite;
	// IMMER LINK AUF LETZTE SEITE ENDE
	echo '<tr'.$faerbung.'><td>'.$sticky.$mark1.'<a'.$sichtbar_fuer_zusatz.' href="'.$pageLink.'">'.$sql3['titel'].'</a>'.$mark2.'</td><td class="link">'.$mark1.'<a href="/forum.php?cat='.urlencode($sql3['kategorie']).'">'.$sql3['kategorie'].'</a>'.$mark2.'</td><td>'.$mark1.$aktualitaet.' von '.$sql3['lastposter'].$mark2.'</td></tr>';
}
?>
</tbody>
</table>
<p style="text-align:right"><a href="/forum.php?mark=read" class="pagenava" onclick="return confirm('Bist Du sicher?')">Alle als gelesen markieren</a> <a href="#" class="pagenava">Neues Thema</a><!-- /forum_eintrag_hinzufuegen.php --></p>
<?php
$q = urlencode($q);
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite=1">Erste</a> '; } else { echo '<span class="this-page">Erste</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$vorherige.'">Vorherige</a> '; } else { echo '<span class="this-page">Vorherige</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.$naechste.'">Nächste</a> '; } else { echo '<span class="this-page">Nächste</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?q='.$q.'&amp;cat='.$cat.'&amp;seite='.ceil($wieviel_seiten).'">Letzte</a>'; } else { echo '<span clss="this-page">Letzte</span>'; }
echo '</div>';
?>
<?php } else { ?>
<h1>Forum</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>