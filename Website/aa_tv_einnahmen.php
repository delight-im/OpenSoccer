<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$hundert_prozent = 60000000; // 100% entsprechen 60.000.000 Euro
$ex1 = "SELECT COUNT(*) FROM ".$prefix."teams WHERE tv_ein = 0";
$ex2 = mysql_query($ex1);
$ex3 = mysql_result($ex2, 0);
if ($ex3 == 0) { exit; }
$lig1 = "SELECT ids, name FROM ".$prefix."ligen";
$lig2 = mysql_query($lig1);
$ligen = array();
while ($lig3 = mysql_fetch_assoc($lig2)) {
	$ligen[$lig3['ids']] = substr($lig3['name'], -1, 1);
}
$sql1 = "SELECT ids, vorjahr_liga, vorjahr_platz FROM ".$prefix."teams WHERE tv_ein = 0";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if (!isset($ligen[$sql3['vorjahr_liga']])) { continue; }
	$liga = $ligen[$sql3['vorjahr_liga']];
	if ($liga == 1) {
		$einnahmen = 100-(33/11)*($sql3['vorjahr_platz']-1);
	}
	elseif ($liga == 2) {
		$einnahmen = 67-(33/11)*($sql3['vorjahr_platz']-1);
	}
	else {
		$einnahmen = 34-(33/11)*($sql3['vorjahr_platz']-1);
	}
	$einnahmen = $hundert_prozent*$einnahmen/100;
	$einnahmen = $einnahmen*0.6; // Anpassungen wegen Inflation/Deflation
	$einnahmen = $einnahmen+15000000; // Anpassungen wegen Inflation/Deflation
	$up1 = "UPDATE ".$prefix."teams SET konto = konto+".$einnahmen.", tv_ein = ".$einnahmen." WHERE ids = '".$sql3['ids']."' AND tv_ein = 0";
	$up2 = mysql_query($up1);
    $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['ids']."', 'TV-Gelder', ".$einnahmen.", '".time()."')";
    $buch2 = mysql_query($buch1);
}
?>
