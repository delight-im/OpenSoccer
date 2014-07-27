<?php include 'zz1.php'; ?>
<title><?php echo _('Notizen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<script type="text/javascript">
function laengeStoppen(feld) {
  if (feld.value.length > 240) {
    alert('<?php echo _('Es sind nur 250 Zeichen erlaubt. Diese Grenze hast Du jetzt erreicht!'); ?>');
	return false;
  }
}
</script>
<?php if ($loggedin == 1) { ?>
<?php
if (isset($_POST['neu']) && $cookie_id != CONFIG_DEMO_USER) {
	$farben = array('fff'=>'000', '00f'=>'fff', '00008b'=>'fff', 'ff8c00'=>'000', 'ffd700'=>'000', '0f0'=>'000', '000080'=>'fff', 'f00'=>'000', 'ff0'=>'000');
	$backgroundColor = array_rand($farben);
	$textColor = $farben[$backgroundColor];
	$text = mysql_real_escape_string(nl2br(substr(trim($_POST['neu']), 0, 250)));
	$sql1 = "INSERT INTO ".$prefix."users_notizen (user, text, textColor, backgroundColor) VALUES ('".$cookie_id."', '".$text."', '".$textColor."', '".$backgroundColor."')";
	$sql2 = mysql_query($sql1);
	setTaskDone('create_note');
}
if (isset($_GET['del']) && $cookie_id != CONFIG_DEMO_USER) {
	$delID = bigintval($_GET['del']);
	$sql1 = "DELETE FROM ".$prefix."users_notizen WHERE id = ".$delID." AND user = '".$cookie_id."'";
	$sql2 = mysql_query($sql1);
}
$sql1 = "SELECT id, text, textColor, backgroundColor FROM ".$prefix."users_notizen WHERE user = '".$cookie_id."' LIMIT 0, 25";
$sql2 = mysql_query($sql1);
$sql2a = mysql_num_rows($sql2);
$notizListe = array();
if (mysql_num_rows($sql2) != 0) {
	$counter = 1;
	echo '<style type="text/css">';
	echo '<!--';
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		$text = strip_tags(trim($sql3['text']), '<br /><br>');
		$backgroundColor = $sql3['backgroundColor'];
		$textColor = $sql3['textColor'];
		echo '#notiz'.$counter.' { background-color: #'.$backgroundColor.'; width: 500px; color: #'.$textColor.'; text-decoration: none; border: 1px solid #000; margin: 10px; }';
		$notizListe[] = '<div id="notiz'.$counter.'"><p><a href="/notizen.php?del='.$sql3['id'].'">[ X ]</a> '.$text.'</p></div>';
		$counter++;
	}
	echo '-->';
	echo '</style>';
}
?>
<?php } ?>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Notizen'); ?></h1>
<?php if ($loggedin == 1) { ?>
<?php foreach ($notizListe as $notizListEntry) { echo $notizListEntry; } ?>
<p><strong><?php echo _('Hinweis:').'</strong> '._('Es werden immer nur die letzten 25 Notizen angezeigt.'); ?></p>
<h1><?php echo _('Neue Notiz erstellen'); ?></h1>
<form action="/notizen.php" method="post" accept-charset="utf-8">
<p><textarea name="neu" onkeypress="laengeStoppen(this)"></textarea></p><p><input type="submit" value="<?php echo _('Speichern'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
