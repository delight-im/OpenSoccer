<?php
include 'zzserver.php';
include 'zzcookie.php';
$hadresse = 'Location: /forum.php';
if (!isset($_GET['id'])) { exit; }
$ids = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
if (in_array($cookie_id, $forum_moderatoren)) {
	$sql1 = "UPDATE ".$prefix."forum_themen SET datum = 0 WHERE ids = '".$ids."'";
	$sql2 = mysql_query($sql1);
}
header($hadresse);
?>
