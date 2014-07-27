<?php if (isset($_GET['id'])) { ?>
<?php include 'zz1.php'; ?>
<title><?php echo _('Post'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>

<?php if ($loggedin == 0) { echo '<h1>'._('Post').'</h1><p>'._('Du musst angemeldet sein, um diese Seite aufrufen zu können!').'</p>'; } else { ?>
<?php
$post = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
$sql1 = "SELECT ".$prefix."pn.ids, von, an, titel, inhalt, zeit, username, in_reply_to FROM ".$prefix."pn LEFT JOIN ".$prefix."users ON ".$prefix."pn.von = ".$prefix."users.ids WHERE ".$prefix."pn.ids = '".$post."' AND (an = '".$cookie_id."' OR von = '".$cookie_id."')";
$sql2 = mysql_query($sql1);
$sql2_err = mysql_num_rows($sql2);
if ($sql2_err == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
if ($sql3['an'] == $cookie_id) {
    $gelesen1 = "UPDATE ".$prefix."pn SET gelesen = 1 WHERE ids = '".$sql3['ids']."' AND gelesen = 0";
    mysql_query($gelesen1);
    if (mysql_affected_rows() == 1 && isset($_SESSION['last_pn_anzahl'])) { $_SESSION['last_pn_anzahl']--; }
}
$fromUser = isset($sql3['username']) ? displayUsername($sql3['username'], $sql3['von']) : 'einem gelöschten User';
echo '<h1>'.$sql3['titel'].'</h1><p><strong>Von '.$fromUser.' am ';
echo date('d.m.Y', $sql3['zeit']).' um '.date('H:i', $sql3['zeit']).' Uhr</strong><br />'.autoLink(hideTeamCode($sql3['inhalt'], $_SESSION['status'])).'<br />';
// "RE: " EINFUEGEN, WENN NICHT SCHON ANTWORT WAR ANFANG
$temp = substr($sql3['titel'], 0, 4);
if ($temp != 'RE: ') {
	$temp = 'RE: '.$sql3['titel'];
}
else {
	$temp = $sql3['titel'];
}
// "RE: " EINFUEGEN, WENN NICHT SCHON ANTWORT WAR ENDE
if ($sql3['von'] != CONFIG_OFFICIAL_USER) {
	echo '<strong><a href="/post_schreiben.php?id='.$sql3['von'].'&amp;betreff='.urlencode($temp).'&amp;in_reply_to='.$sql3['ids'].'">'._('Antworten').'</a> | <a href="/post_schreiben.php?id='.$sql3['von'].'">'._('Neue Nachricht').'</a></strong>';
}
echo '</p>';
if ($sql3['in_reply_to'] != '') {
	$ur1 = "SELECT inhalt FROM ".$prefix."pn WHERE ids = '".mysql_real_escape_string($sql3['in_reply_to'])."' AND (an = '".$cookie_id."' OR von = '".$cookie_id."')";
	$ur2 = mysql_query($ur1);
	if (mysql_num_rows($ur2) != 0) {
		$ur3 = mysql_fetch_assoc($ur2);
		echo '<h1>'._('Ursprüngliche Nachricht').'</h1>';
		echo '<p>'.autoLink(hideTeamCode($ur3['inhalt'], $_SESSION['status'])).'</p>';
	}
}
}
?>

<?php include 'zz3.php'; } ?>
