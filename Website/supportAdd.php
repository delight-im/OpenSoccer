<?php include 'zz1.php'; ?>
<title>Support: Neue Anfrage | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Support: Neue Anfrage</h1>
<?php if ($loggedin == 1) { ?>
<p style="text-align:right"><a href="/support.php" class="pagenava">Zurück zur Hauptseite</a></p>
<?php
// CHAT-SPERREN ANFANG
$blockCom1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
$blockCom2 = mysql_query($blockCom1);
if (mysql_num_rows($blockCom2) > 0) {
	$blockCom3 = mysql_fetch_assoc($blockCom2);
	$chatSperreBis = $blockCom3['MAX(chatSperre)'];
	if ($chatSperreBis > 0 && $chatSperreBis > time()) {
		echo addInfoBox('Du bist noch bis zum '.date('d.m.Y H:i', $chatSperreBis).' Uhr für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das <a class="inText" href="/wio.php">Ballmanager-Team.</a>');
		include 'zz3.php';
		exit;
	}
}
// CHAT-SPERREN ENDE
$timeout = getTimestamp('-1 day');
$sql1 = "SELECT COUNT(*) FROM ".$prefix."supportRequests WHERE timeAdded > ".$timeout." AND author = '".$cookie_id."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_result($sql2, 0);
if ($sql3 <= 5) {
?>
	<form action="/support.php" method="post" accept-charset="utf-8">
	<?php if ($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer') { ?>
		<p><label for="newVisibility">Wer soll die Anfrage sehen können?</label><select name="visibility" id="newVisibility" size="1" style="width:200px">
			<option value="0">Jeder User</option>
			<option value="1">Nur Support-Team</option>
		</select></p>
	<?php } ?>
	<p><label for="newCategory">Was für eine Anfrage möchtest Du erstellen?</label><select name="category" id="newCategory" size="1" style="width:200px">
		<option>Frage</option>
		<option>Fehlerbericht</option>
		<option>Vorschlag</option>
	</select></p>
	<p><label for="newTitle">Deine Anfrage (max. 150 Zeichen):</label><input type="text" name="title" id="newTitle" style="width:200px" /></p>
	<p><label for="newDescription">Möchtest Du Deine Anfrage noch genauer beschreiben ... ?</label><textarea name="description" id="newDescription" cols="10" rows="10" style="width:350px; height:200px"></textarea></p>
	<p><input type="submit" value="Anfrage erstellen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" /></p>
	</form>
<?php }
else {
	echo '<p>Du hast heute schon '.$sql3.' neue Anfragen erstellt. Da nur 5 pro Tag erlaubt sind, bitten wir Dich, etwas zu warten. Später kannst Du gerne wieder eine neue Anfrage erstellen.</p>';
}
?>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>