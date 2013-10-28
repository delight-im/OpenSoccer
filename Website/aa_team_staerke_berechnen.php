<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$sql1 = "UPDATE ".$prefix."teams SET staerke = (SELECT AVG(staerke) FROM ".$prefix."spieler WHERE team = ".$prefix."teams.ids) ORDER BY RAND() LIMIT 100";
$sql2 = mysql_query($sql1);
?>