<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$getCount1 = "SELECT COUNT(*) FROM ".$prefix."spieler WHERE marktwert = 0 AND wiealt < 14600";
$getCount2 = mysql_query($getCount1);
$getCount3 = mysql_result($getCount2, 0);
if ($getCount3 == 0) {
	$sql1 = "UPDATE ".$prefix."spieler SET marktwert = ".$marktwertAusdruck." WHERE wiealt < 14600 ORDER BY RAND() LIMIT 750";
}
else {
	$sql1 = "UPDATE ".$prefix."spieler SET marktwert = ".$marktwertAusdruck." WHERE marktwert = 0 AND wiealt < 14600 LIMIT 750";
}
$sql2 = mysql_query($sql1);
?>
