<?php include 'zzserver.php'; ?>
<?php
$aktuelle_stunde = date('H');
$sql1 = "SELECT id, datei FROM ".$prefix."cronjobs WHERE (zuletzt+intervall) < ".time()." AND stunde_min <= ".$aktuelle_stunde." AND stunde_max >= ".$aktuelle_stunde." ORDER BY zuletzt ASC LIMIT 0, 1";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
$id = $sql3['id'];
$dateiname = $sql3['datei'];
include $dateiname;
$sql4 = "UPDATE ".$prefix."cronjobs SET zuletzt = ".time()." WHERE id = ".$id;
$sql5 = mysql_query($sql4);
echo 'Cronjob '.$id.' erfolgreich!';
?>
