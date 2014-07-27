<?php
if (!isset($_GET['id'])) { exit; }
include 'zzserver.php';
include 'zzcookie.php';
$spieler = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
if ($cookie_id != CONFIG_DEMO_USER) {
	$sql1 = "DELETE FROM ".$prefix."transfers WHERE bieter = '".$cookie_teamname."' AND spieler = '".$spieler."'";
	$sql2 = mysql_query($sql1);
}
header('Location: /spieler.php?id='.$spieler);
?>
