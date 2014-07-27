<?php include 'zz1.php'; ?>
<?php
if (isset($_GET['delQuote']) && isset($_GET['delQuoteHash'])) {
	$delQuote = bigintval($_GET['delQuote']);
	$delQuoteHash = $_GET['delQuoteHash'];
	$delQuoteHashNew = md5('29'.$delQuote.'1992');
	if ($delQuoteHash == $delQuoteHashNew) {
		$delQuote1 = "UPDATE ".$prefix."forum_beitraege SET quote = '' WHERE id = ".$delQuote;
		$delQuote2 = mysql_query($delQuote1);
	}
}
if (!isset($_GET['id'])) { exit; }
$addSql = " AND sichtbar_fuer = 'Alle'";
$is_moderator = FALSE;
if ($loggedin == 1) {
    if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
        $addSql = "";
        $is_moderator = TRUE;
    }
}
$themaID = mysql_real_escape_string($_GET['id']);
$sql1 = "SELECT id, manager, datum, inhalt, quote FROM ".$prefix."forum_beitraege WHERE thema = '".$themaID."' AND sichtbar = 'J' ORDER BY datum ASC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
if ($sql2 == FALSE) { exit; }
$sql2a = mysql_num_rows($sql2);
if ($sql2a == 0) { exit; }
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
if ($loggedin == 1) {
    $gel1 = "INSERT IGNORE INTO ".$prefix."forum_gelesen (thema, user) VALUES ('".mysql_real_escape_string($_GET['id'])."', '".$cookie_id."')";
    $gel2 = mysql_query($gel1);
    if (mysql_affected_rows() == 1) { $_SESSION['last_forumneu_anzahl']--; }
}
$sql4 = "SELECT titel, sichtbar_fuer, datum FROM ".$prefix."forum_themen WHERE ids = '".$themaID."'".$addSql."";
$sql5 = mysql_query($sql4);
$sql5a = mysql_num_rows($sql5);
if ($sql5a == 0) { exit; }
$sql6 = mysql_fetch_assoc($sql5);
?>
<title><?php echo $sql6['titel']; ?> | Forum - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo $sql6['titel']; ?> | Forum</h1>
<?php
if ($loggedin == 1) {
// BUTTONS ANZEIGEN ANFANG
echo '<p style="text-align:right"><a href="#" class="pagenava" onclick="return confirm(\'Möchtest Du wirklich ein neues Thema eröffnen?\')">Neues Thema</a><!-- /forum_eintrag_hinzufuegen.php -->';
if ($sql6['datum'] != 0) { // im Archiv nicht anzeigen
	/*echo '<a href="/forum_eintrag_hinzufuegen.php?id='.$themaID;
	if ($sql6['sichtbar_fuer'] == 'Helfer') {
		echo '&amp;postAsTyp=privatePost';
	}
	echo '" class="pagenava">Antworten</a>';*/
	if ($_SESSION['status'] == 'Admin') {
		echo '<a href="/forum.php?archive='.$themaID.'" class="pagenava" onclick="return confirm(\'Bist Du sicher?\')">Archivieren</a>';
	}
}
echo '</p>';
// BUTTONS ANZEIGEN ENDE
if ($sql6['sichtbar_fuer'] == 'Helfer') { echo '<p style="color:red">Dieses Thema ist nur für Team-Mitglieder sichtbar.</p>'; }
?>
<?php
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$user_holen1 = "SELECT username FROM ".$prefix."users WHERE ids = '".$sql3['manager']."'";
	$user_holen2 = mysql_query($user_holen1);
	if (mysql_num_rows($user_holen2) == 0) {
		$username_temp = 'Unbekannt';
	}
	else {
		$user_holen3 = mysql_fetch_assoc($user_holen2);
		$username_temp = displayUsername($user_holen3['username'], $sql3['manager']);
	}
	echo '<p><b>'.$username_temp.' ('.date('d.m.Y H:i', $sql3['datum']).')</b>';
	if ($sql3['quote'] != '') { // Zitat enthalten
		echo '</p><blockquote>'.autoLink(hideTeamCode($sql3['quote'], $_SESSION['status'])).'</blockquote><p>'.autoLink(hideTeamCode($sql3['inhalt'], $_SESSION['status']));
	}
	else { // kein Zitat enthalten
		echo '<br />'.autoLink(hideTeamCode($sql3['inhalt'], $_SESSION['status']));
	}
	/*if ($username_temp != 'Unbekannt') {
		if ($sql3['manager'] == $cookie_id OR $is_moderator == TRUE) {
			echo '<br /><a href="/forum_eintrag_hinzufuegen.php?id='.$themaID.'&amp;beitrag='.$sql3['id'].'">[Ändern]</a> ';
		}
	}*/
	/*echo '<a href="/forum_eintrag_hinzufuegen.php?id='.$themaID.'&amp;quote='.$sql3['id'];
	if ($sql6['sichtbar_fuer'] == 'Helfer') {
		echo '&amp;postAsTyp=privatePost';
	}
	echo '">[Zitieren]</a>';*/
	if ($sql3['quote'] != '') { // Zitat enthalten
		if ($sql3['manager'] == $cookie_id OR $is_moderator == TRUE) {
			$delQuoteHash = md5('29'.$sql3['id'].'1992');
			echo ' <a href="'.$_SERVER['REQUEST_URI'].'&amp;delQuote='.$sql3['id'].'&amp;delQuoteHash='.$delQuoteHash.'">[Zitat löschen]</a>';
		}
	}
	echo '</p>';
}
// BUTTONS ANZEIGEN ANFANG
echo '<p style="text-align:right"><a href="#" class="pagenava" onclick="return confirm(\'Möchtest Du wirklich ein neues Thema eröffnen?\')">Neues Thema</a><!-- /forum_eintrag_hinzufuegen.php -->';
if ($sql6['datum'] != 0) { // im Archiv nicht anzeigen
	/*echo '<a href="/forum_eintrag_hinzufuegen.php?id='.$themaID;
	if ($sql6['sichtbar_fuer'] == 'Helfer') {
		echo '&amp;postAsTyp=privatePost';
	}
	echo '" class="pagenava">Antworten</a>';*/
	if ($_SESSION['status'] == 'Admin') {
		echo '<a href="/forum.php?archive='.$themaID.'" class="pagenava" onclick="return confirm(\'Bist Du sicher?\')">Archivieren</a>';
	}
}
echo '</p>';
// BUTTONS ANZEIGEN ENDE
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite=1">Erste</a> '; } else { echo '<span class="this-page">Erste</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$vorherige.'">Vorherige</a> '; } else { echo '<span class="this-page">Vorherige</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.$naechste.'">Nächste</a> '; } else { echo '<span class="this-page">Nächste</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?id='.$_GET['id'].'&amp;seite='.ceil($wieviel_seiten).'">Letzte</a>'; } else { echo '<span clss="this-page">Letzte</span>'; }
echo '</div>';
?>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>