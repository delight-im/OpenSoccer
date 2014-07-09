<?php if (!isset($_GET['mode'])) { include 'zzserver.php'; } ?>
<?php
function getRandomStrength($min, $max) {
	$ln_low = log($min, M_E);
	$logarithmicity = 1.15; // je höher desto weniger linear (am besten 0.5 < x < 1.5)
	$ln_high = log($max, M_E);
	$scale = $ln_high-$ln_low;
	$rand = pow(mt_rand()/mt_getrandmax(), $logarithmicity)*$scale+$ln_low;
	return round(pow(M_E, $rand), 1);
}
function choosePosition() {
	if (Chance_Percent(12)) { return 'T'; }
	elseif (Chance_Percent(24)) { return 'S'; }
	elseif (Chance_Percent(50)) { return 'M'; }
	else { return 'A'; }
}
// KONFIGURATION ANFANG
$in_33_tagen = endOfDay(getTimestamp('+33 days'));
$spieltage_mit_aktion = array(3, 6, 9, 12, 15, 18, 21);
if (!in_array($cookie_spieltag, $spieltage_mit_aktion)) { exit; }
$datum_stamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
$datum_stamp_alt = getTimestamp('-36 hours', $datum_stamp); // vor 1,5 Tagen
$vor1 = "SELECT name FROM ".$prefix."namen_pool WHERE typ = 1";
$vor2 = mysql_query($vor1);
$vor2a = mysql_num_rows($vor2)-1;
$vornamen = array();
while ($vor3 = mysql_fetch_assoc($vor2)) { $vornamen[] = $vor3['name']; }
$nach1 = "SELECT name FROM ".$prefix."namen_pool WHERE typ = 2";
$nach2 = mysql_query($nach1);
$nach2a = mysql_num_rows($nach2)-1;
$nachnamen = array();
while ($nach3 = mysql_fetch_assoc($nach2)) { $nachnamen[] = $nach3['name']; }
// KONFIGURATION ENDE
$sql1 = "SELECT ids, liga, jugendarbeit, posToSearch FROM ".$prefix."teams WHERE letzte_jugend < ".$datum_stamp_alt." LIMIT 0, 100";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$new_liga = $sql3['liga'];
	$new_team = $sql3['ids'];
	$vor_zahl = mt_rand(0, $vor2a); // Vorname - Position im Array
	$nach_zahl = mt_rand(0, $nach2a); // Nachname - Position im Array
	// MAXIMALES TALENT UND GEHALT ANFANG
	switch ($sql3['jugendarbeit']) {
		case 1: $talent_max = 5.9; $talent_min = 2.1; $new_gehalt = 300000; break;
		case 2: $talent_max = 6.9; $talent_min = 2.8; $new_gehalt = 500000; break;
		case 3: $talent_max = 7.9; $talent_min = 3.5; $new_gehalt = 700000; break;
		case 4: $talent_max = 8.9; $talent_min = 4.2; $new_gehalt = 900000; break;
		case 5: $talent_max = 9.9; $talent_min = 4.9; $new_gehalt = 1200000; break;
		default: $talent_max = 5.9; $talent_min = 2.1; $new_gehalt = 700000; break;
	}
	// MAXIMALES TALENT UND GEHALT ENDE
	$temp = $sql3['jugendarbeit']-1;
	// ERMITTELN VON STAERKE UND TALENT ANFANG
	$talent = getRandomStrength($talent_min, $talent_max);
	$anfangsstaerke = getRandomStrength(0.5, 0.9);
	$staerke = round(($talent*$anfangsstaerke), 1);
	// ERMITTELN VON STAERKE UND TALENT ENDE
	$zahl9 = mt_rand(6205, 7665); // Alter in Tagen
	$rein1 = "INSERT INTO ".$prefix."spieler (vorname, nachname, staerke, talent, position, wiealt, liga, team, gehalt, vertrag, spiele_verein, jugendTeam) VALUES ('".$vornamen[$vor_zahl]."', '".$nachnamen[$nach_zahl]."', ".$staerke.", ".$talent.", '".$sql3['posToSearch']."', ".$zahl9.", '".$new_liga."', '".$new_team."', ".$new_gehalt.", ".$in_33_tagen.", 0, '".$new_team."')";
	$rein2 = mysql_query($rein1);
	$rein3 = mysql_insert_id();
	$rein3a = md5($rein3);
	$rein4 = "UPDATE ".$prefix."spieler SET ids = '".$rein3a."' WHERE id = '".$rein3."'";
	$rein5 = mysql_query($rein4);
	// PROTOKOLL ANFANG
	$getmanager4 = $vornamen[$vor_zahl].' '.$nachnamen[$nach_zahl];
	$formulierung = 'Der Jugendspieler <a href="/spieler.php?id='.$rein3a.'">'.$getmanager4.'</a> scheint großes Potenzial zu haben. Er hat deshalb einen Profivertrag für Deine erste Mannschaft bekommen.';
	$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$new_team."', '".$formulierung."', 'Spieler', '".time()."')";
	$sql8 = mysql_query($sql7);
	// PROTOKOLL ENDE
	$ld1 = "UPDATE ".$prefix."teams SET letzte_jugend = ".$datum_stamp." WHERE ids = '".$new_team."'";
	$ld2 = mysql_query($ld1);
}
?>