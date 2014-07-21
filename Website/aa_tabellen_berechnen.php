<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$sql1 = "SELECT ids FROM ".$prefix."ligen";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
    $sql4 = "SELECT ids, name, punkte, tore, gegentore FROM ".$prefix."teams WHERE liga = '".$sql3['ids']."' ORDER BY punkte DESC, (tore-gegentore) DESC, tore DESC";
    $sql5 = mysql_query($sql4);
    $counter = 1;
    while ($sql6 = mysql_fetch_assoc($sql5)) {
		$sql7 = "UPDATE ".$prefix."teams SET rank = ".$counter." WHERE ids = '".$sql6['ids']."'";
		$sql8 = mysql_query($sql7);
		if (date('H', time()) == 16 OR date('H', time()) == 17) {
			$team = $sql6['name'];
			$punkte = $sql6['punkte'];
			$tore = $sql6['tore'];
			$gegentore = $sql6['gegentore'];
			$in1 = "INSERT INTO ".$prefix."geschichte_tabellen (saison, spieltag, liga, team, platz, punkte, tore, gegentore) ";
			$in1 .= "VALUES (".GameTime::getSeason().", ".GameTime::getMatchDay().", '".$sql3['ids']."', '".$team."', ".$counter.", ".$punkte.", ".$tore.", ".$gegentore.")";
			$in2 = mysql_query($in1);
		}
        $counter++;
    }
}
?>
