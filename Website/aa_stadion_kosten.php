<?php if (!isset($_GET['mode'])) { include 'zzserver.php'; } ?>
<?php
function sgn($number) {
	if ($number == 0) {
		return 0;
	}
	elseif ($number > 0) {
		return 1;
	}
	else {
		return -1;
	}
}
$datum = date('Y-m-d', time());
$sql3 = "UPDATE ".$prefix."zeitrechnung SET letzte_stadionkosten = '".$datum."'";
$sql4 = mysql_query($sql3);
if (mysql_affected_rows() > 0) {
	$ligen1 = array();
	$ligen1a = "SELECT ids FROM ".$prefix."ligen WHERE name LIKE '%1'";
	$ligen1b = mysql_query($ligen1a);
	while ($ligen1c = mysql_fetch_assoc($ligen1b)) {
		$ligen1[] = $ligen1c['ids'];
	}
	$sql1 = "SELECT ids, fanbetreuer, liga, elo FROM ".$prefix."teams";
	$sql2 = mysql_query($sql1);
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		$sql4 = "SELECT plaetze, preis, parkplatz, ubahn, restaurant, bierzelt, pizzeria, imbissstand, vereinsmuseum, fanshop FROM ".$prefix."stadien WHERE team = '".$sql3['ids']."'";
		$sql5 = mysql_query($sql4);
		$sql6 = mysql_fetch_assoc($sql5);
		// KOSTEN ANFANG
		$kosten = 1550000+$sql6['plaetze']*250;
		// GELAENDE ANFANG
        // kurzname, vollname, 1_pro_x_zuschauer, kosten_pro_1
        $gebaeude_a = array(
            array('parkplatz', 'Parkplatz', 7500, 30000),
            array('ubahn', 'U-Bahn', 40000, 90000),
            array('restaurant', 'Restaurant', 15000, 320000),
            array('bierzelt', 'Bierzelt', 20000, 74000),
            array('pizzeria', 'Pizzeria', 12000, 90000),
            array('imbissstand', 'Imbissstand', 10000, 45000),
            array('vereinsmuseum', 'Vereinsmuseum', 50000, 655000),
            array('fanshop', 'Fanshop', 30000, 160000),
        );
        foreach ($gebaeude_a as $tm) {
            $tempwert = round($tm[3]*$sql6[$tm[0]]); // letzter Faktor = Anzahl der Gebaeude-Einheiten
            $kosten += $tempwert;
        }
        // GELAENDE ENDE
		$kosten = $kosten/22;
		// KOSTEN ENDE
		// FANAUFKOMMEN ANFANG
		// maximales Fanaufkommen ist 100.000
		// Gegner-Platzierung wird bei Spieltag-Simulation live miteinberechnet
		// Ticketpreis wird bei Spieltag-Simulation live miteinberechnet
		$fanaufkommen1 = $sql3['fanbetreuer']*5000; // Fanbetreuer
		$fanaufkommen2 = (sgn($sql6['parkplatz'])+sgn($sql6['ubahn'])+sgn($sql6['restaurant'])+sgn($sql6['bierzelt'])+sgn($sql6['pizzeria'])+sgn($sql6['imbissstand'])+sgn($sql6['vereinsmuseum'])+sgn($sql6['fanshop']))*1750; // Stadiongelaende
		$fanaufkommen4 = mt_rand(0, 40)*75; // Zufall
		$fanaufkommen6 = round(pow($sql3['elo'], 3)/250000); // RKP
		$fanaufkommen = round($fanaufkommen1+$fanaufkommen2+$fanaufkommen4+$fanaufkommen6);
		// FANAUFKOMMEN ENDE
		$sql7 = "UPDATE ".$prefix."teams SET konto = konto-".$kosten.", fanaufkommen = ".$fanaufkommen." WHERE ids = '".$sql3['ids']."'";
		$sql8 = mysql_query($sql7);
        $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$sql3['ids']."', 'Stadion', -".$kosten.", '".time()."')";
        $buch2 = mysql_query($buch1);
	}
}
?>
