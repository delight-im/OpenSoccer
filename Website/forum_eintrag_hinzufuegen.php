<?php exit; ?>
<?php include 'zz1.php'; ?>
<?php if (isset($_GET['cat'])) { $tempcat = trim($_GET['cat']); } else { $tempcat = ''; } ?>
<title>Forum: Eintrag hinzufügen | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Forum: Eintrag hinzufügen</h1>
<?php if ($loggedin == 1) { ?>
<?php
// CHAT-SPERREN ANFANG
$sql1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) > 0) {
	$sql3 = mysql_fetch_assoc($sql2);
	$chatSperreBis = $sql3['MAX(chatSperre)'];
	if ($chatSperreBis > 0 && $chatSperreBis > time()) {
		addInfoBox('Du bist noch bis zum '.date('d.m.Y H:i', $chatSperreBis).' Uhr für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das <a class="inText" href="/wio.php">Ballmanager-Team.</a>');
		include 'zz3.php';
		exit;
	}
}
// CHAT-SPERREN ENDE
?>
<p><strong>Hinweis:</strong> Wenn Du eine Frage stellen möchtest, schaue bitte zuerst bei den <a href="/tipps_des_tages.php">Tipps des Tages</a> nach, ob sie da schon beantwortet wird. Wenn Du einen Spieler verkaufen möchtest und Käufer suchst, erstelle dafür bitte kein eigenes Thema im Forum.</p>
<form action="/forum_eintrag_hinzugefuegt.php" method="post" accept-charset="utf-8">
<?php
if (!isset($_GET['id'])) { // neues Thema
	echo '<p><select name="kategorie" size="1" style="width:200px">';
	echo '<option>- Kategorie wählen -</option>';
	foreach ($kategorienListe as $kategorie) {
		echo '<option>'.$kategorie.'</option>';
	}
	echo '</select></p>';
	echo '<p><input type="text" name="titel" value="Titel eingeben ..." style="width:200px" onfocus="if(this.value == \'Titel eingeben ...\') this.value = \'\'" /></p>';
}
else { // Beitrag zu vorhandenem Thema
	$themaOldID = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
	echo '<p><input type="hidden" name="thema" value="'.$themaOldID.'" /></p>';
	if (isset($_GET['beitrag'])) { // Beitrag aendern
		$beitrag = bigintval($_GET['beitrag']);
		echo '<input type="hidden" name="beitragAltID" value="'.$beitrag.'" />';
		$addSql = " AND manager = '".$cookie_id."'";
		if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { $addSql = ""; }
		$sql1 = "SELECT inhalt FROM ".$prefix."forum_beitraege WHERE id = ".$beitrag.$addSql;
		$sql2 = mysql_query($sql1);
		if (mysql_num_rows($sql2) == 0) { exit; }
		$sql3 = mysql_fetch_assoc($sql2);
		$beitragAlt = strip_tags(trim($sql3['inhalt']));
		// ERSTER BEITRAG ODER NICHT ANFANG
		$fp1 = "SELECT MIN(id) FROM man_forum_beitraege WHERE thema = '".$themaOldID."'";
		$fp2 = mysql_query($fp1);
		$fp3 = mysql_fetch_assoc($fp2);
		$fp4 = $fp3['MIN(id)'];
		if ($fp4 == $beitrag) {
			$getOldKategorie1 = "SELECT kategorie FROM ".$prefix."forum_themen WHERE ids = '".$themaOldID."'";
			$getOldKategorie2 = mysql_query($getOldKategorie1);
			$getOldKategorie3 = mysql_fetch_assoc($getOldKategorie2);
			$getOldKategorie4 = $getOldKategorie3['kategorie'];
			echo '<p><select name="kategorie" size="1" style="width:200px">';
			echo '<option>- Kategorie wählen -</option>';
			foreach ($kategorienListe as $kategorie) {
				echo '<option';
				if ($kategorie == $getOldKategorie4) { echo ' selected="selected"'; }
				echo '>'.$kategorie.'</option>';
			}
			echo '</select></p>';
		}
		// ERSTER BEITRAG ODER NICHT ENDE
	}
	else { // neue Antwort
		if (isset($_GET['quote'])) {
			echo '<p><select name="quote" size="1" style="width:200px">';
			echo '<option value="'.bigintval($_GET['quote']).'">Zitat anhängen</option>';
			echo '<option value="0">Kein Zitat</option>';
			echo '</select></p>';
		}
	}
}
echo '<p><textarea rows="10" cols="70" name="inhalt" onfocus="if(this.value == \'Beitrag eingeben ...\') this.value = \'\'">';
if (isset($beitragAlt)) { echo $beitragAlt; } else { echo 'Beitrag eingeben ...'; } // neuer Beitrag oder Beitrag editieren
echo '</textarea></p>';
if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
	if (!isset($_GET['id'])) {
		echo '<p><select name="sichtbar_fuer" size="1" style="width:80%">';
		echo '	<option value="Alle">Sichtbar für Alle</option>';
		echo '	<option value="Helfer">Sichtbar fürs Support-Team</option>';
		echo '</select></p>';
	}
}
?>
<p><input type="submit" value="Speichern" /></p>
</form>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>