<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$datum_heute = date('Y-m-d', time());
$u3 = "SELECT team, zahlen FROM ".$prefix."lotto_tipps WHERE datum != '".$datum_heute."'";
$u4 = mysql_query($u3);
if (mysql_num_rows($u4) != 0) {
    $get_jackpot1 = "SELECT jackpot FROM ".$prefix."lotto LIMIT 0, 1";
    $get_jackpot2 = mysql_query($get_jackpot1);
    $get_jackpot3 = mysql_fetch_assoc($get_jackpot2);
	// ZAHLEN ZIEHEN ANFANG
	$gewinnzahlen = array();
	while (count($gewinnzahlen) < 4) {
		$temp = mt_rand(0, 14)+1; // um Kalkulieren mit PHP-Zeit schwerer zu machen plus 1
		if (in_array($temp, $gewinnzahlen)) { continue; }
		$gewinnzahlen[] = $temp;
	}
	$zahlen_gestern = implode('-', $gewinnzahlen);
	$gewinner_teams = array(2=>array(), 3=>array(), 4=>array());
	// ZAHLEN ZIEHEN ENDE
    while ($u5 = mysql_fetch_assoc($u4)) {
    	$zahlen_user = explode('-', $u5['zahlen'], 4);
		$richtige = count(array_intersect($zahlen_user, $gewinnzahlen));
		switch ($richtige) {
			case 2: $gewinner_teams[2][] = $u5['team']; break;
			case 3: $gewinner_teams[3][] = $u5['team']; break;
			case 4: $gewinner_teams[4][] = $u5['team']; break;
		}
    }
    // AUSZAHLUNGEN ANFANG
    $auszahlungen = array();
    for ($i = 2; $i <= 4; $i++) {
    	$gewinner_mit_i_richtigen = $gewinner_teams[$i];
    	$gewinner_mit_i_richtigen_cnt = count($gewinner_mit_i_richtigen);
    	if ($gewinner_mit_i_richtigen_cnt == 0) { continue; }
    	switch ($i) {
    		case 2: $prozentsatz_vom_jackpot = 0.05; break;
    		case 3: $prozentsatz_vom_jackpot = 0.15; break;
    		case 4: $prozentsatz_vom_jackpot = 0.8; break;
    		default: continue; break;
    	}
    	$anteil_fuer_diese_gruppe = $get_jackpot3['jackpot']*$prozentsatz_vom_jackpot;
    	$anteil_fuer_jeden_aus_gruppe = floor($anteil_fuer_diese_gruppe/$gewinner_mit_i_richtigen_cnt);
    	foreach ($gewinner_mit_i_richtigen as $temp) {
    		$auszahlungen[] = $anteil_fuer_jeden_aus_gruppe;
    		$az1 = "UPDATE ".$prefix."teams SET konto = konto+".$anteil_fuer_jeden_aus_gruppe." WHERE ids = '".$temp."'";
    		$az2 = mysql_query($az1);
            $az3 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$temp."', 'Lottogewinn', ".$anteil_fuer_jeden_aus_gruppe.", '".time()."')";
            $az4 = mysql_query($az3);
            // PROTOKOLL ANFANG
            $formulierung = 'Du hast im Lotto mit '.$i.' Richtigen '.number_format($anteil_fuer_jeden_aus_gruppe, 0, ',', '.').' € gewonnen.';
            $az5 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$temp."', '".$formulierung."', 'Finanzen', ".time().")";
            $az6 = mysql_query($az5);
            // PROTOKOLL ENDE
            $az7 = "INSERT INTO ".$prefix."lotto_gewinner (team, zeit, summe, richtige) VALUES ('".$temp."', ".intval(getTimestamp('-1 hour')).", ".$anteil_fuer_jeden_aus_gruppe.", ".$i.")";
            $az8 = mysql_query($az7);
    	}
    }
    $ausgegebenes_geld = array_sum($auszahlungen);
    $lower_jackpot1 = "UPDATE ".$prefix."lotto SET jackpot = jackpot-".$ausgegebenes_geld.", zahlen_gestern = '".$zahlen_gestern."'";
    $lower_jackpot2 = mysql_query($lower_jackpot1);
    $delete_old_tipps1 = "DELETE FROM ".$prefix."lotto_tipps WHERE datum != '".$datum_heute."'";
    $delete_old_tipps2 = mysql_query($delete_old_tipps1);
    // AUSZAHLUNGEN ENDE
}
?>
