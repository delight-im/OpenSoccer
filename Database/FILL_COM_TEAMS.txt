<?php include 'zzserver.php'; ?>
<?php
// ToDo: Spieler darf gerade nicht versteigert werden
$sql1 = "SELECT ids, name FROM ".$prefix."teams WHERE ids NOT IN (SELECT team FROM ".$prefix."users)";
$sql2 = mysql_query($sql1);
$teams = array();
$spieler = array();
$vertrag = $zeit+3600*24*7;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$teams[$sql3['ids']] = $sql3['name'];
	$spieler[$sql3['ids']] = array();
	$spieler[$sql3['ids']]['T'] = 0;
	$spieler[$sql3['ids']]['A'] = 0;
	$spieler[$sql3['ids']]['M'] = 0;
	$spieler[$sql3['ids']]['S'] = 0;
}
$sql1 = "SELECT position, team FROM ".$prefix."spieler WHERE team != 'frei'";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if (!isset($spieler[$sql3['team']])) { continue; }
	$spieler[$sql3['team']][$sql3['position']]++;
}
for ($i = 0; $i < count($spieler); $i++) {
	if ($spiel = each($spieler)) {
        if ($spiel['value']['T'] < 1) {
        	$up1 = "UPDATE ".$prefix."spieler SET team = '".$spiel['key']."', vertrag = ".$vertrag." WHERE team = 'frei' AND position = 'T' ORDER BY RAND() LIMIT 1";
        	$up2 = mysql_query($up1) or die(mysql_error());
        }
        if ($spiel['value']['A'] < 4) {
        	$up1 = "UPDATE ".$prefix."spieler SET team = '".$spiel['key']."', vertrag = ".$vertrag." WHERE team = 'frei' AND position = 'A' ORDER BY RAND() LIMIT 1";
        	$up2 = mysql_query($up1) or die(mysql_error());
        }
        if ($spiel['value']['M'] < 4) {
        	$up1 = "UPDATE ".$prefix."spieler SET team = '".$spiel['key']."', vertrag = ".$vertrag." WHERE team = 'frei' AND position = 'M' ORDER BY RAND() LIMIT 1";
        	$up2 = mysql_query($up1) or die(mysql_error());
		}
        if ($spiel['value']['S'] < 2) {
        	$up1 = "UPDATE ".$prefix."spieler SET team = '".$spiel['key']."', vertrag = ".$vertrag." WHERE team = 'frei' AND position = 'S' ORDER BY RAND() LIMIT 1";
        	$up2 = mysql_query($up1) or die(mysql_error());
        }
	}
}
?>