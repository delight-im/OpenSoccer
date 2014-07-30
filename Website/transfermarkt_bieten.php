<?php include 'zz1.php'; ?>
<?php
$debugStr = '0';
if (isset($_POST['spieler'])) {
	$spieler_id = mysql_real_escape_string(trim(strip_tags($_POST['spieler'])));
	$hadresse = 'Location: /transfermarkt_auktion.php?id='.$spieler_id;
}
else {
	exit;
}
if ($loggedin == 1) {
	if ($_SESSION['transferGesperrt'] == FALSE && isset($_POST['wt2']) && $cookie_id != CONFIG_DEMO_USER) {
		$teamOwnerID = mysql_real_escape_string(trim(strip_tags($_POST['wt2'])));
		// KONTOSTAND ERMITTELN ANFANG
		if ($cookie_team != '__'.$cookie_id) {
			$getkonto1 = "SELECT konto FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
			$getkonto2 = mysql_query($getkonto1);
			$getkonto3 = mysql_fetch_assoc($getkonto2);
			$kontostand = $getkonto3['konto']-einsatz_in_auktionen($cookie_team);
		}
		else {
			$kontostand = 0;
		}
		// KONTOSTAND ERMITTELN ENDE
		// NUR 2 TRANSFERS ZWISCHEN 2 TEAMS ANFANG
		$n3t1_a = "SELECT COUNT(*) FROM ".$prefix."transfers WHERE (besitzer = '".$teamOwnerID."' AND bieter = '".$cookie_team."') OR (bieter = '".$teamOwnerID."' AND besitzer = '".$cookie_team."')";
		$n3t2_a = mysql_query($n3t1_a);
		$n3t3_a = mysql_result($n3t2_a, 0);
		$n3t1_b = "SELECT COUNT(*) FROM ".$prefix."transfermarkt WHERE (besitzer = '".$teamOwnerID."' AND bieter_highest = '".$cookie_team."') OR (bieter_highest = '".$teamOwnerID."' AND besitzer = '".$cookie_team."')";
		$n3t2_b = mysql_query($n3t1_b);
		$n3t3_b = mysql_result($n3t2_b, 0);
		$n3t3 = intval($n3t3_a+$n3t3_b);
		// NUR 2 TRANSFERS ZWISCHEN 2 TEAMS ENDE
		if (isset($_POST['gebot']) && isset($_POST['spieler']) && isset($_POST['wt1']) && isset($_POST['wt3']) && isset($_POST['wt4']) && isset($_POST['wt5']) && !isset($_POST['incGebotButton']) && !isset($_POST['decGebotButton'])) {
			$debugStr = '1';
			$gebot = str_replace('.', '', $_POST['gebot']);
			$gebot = str_replace(',', '.', $gebot);
			$gebot = intval($gebot);
			$spieler_id = mysql_real_escape_string(trim(strip_tags($_POST['spieler'])));
			$mindestgebot = betrag_dekodieren(trim(strip_tags($_POST['wt1'])));
			$maximalgebot = betrag_dekodieren(trim(strip_tags($_POST['wt4'])));
			$altes_hoechstgebot = betrag_dekodieren(trim(strip_tags($_POST['wt3'])));
			$sollHash = mysql_real_escape_string(trim(strip_tags($_POST['wt5'])));
			$istHash = md5('29'.betrag_enkodieren($mindestgebot).$teamOwnerID.betrag_enkodieren($altes_hoechstgebot).betrag_enkodieren($maximalgebot).'1992');
			if ($istHash == $sollHash) { // Hacking verhindern
				$debugStr = '1.1';
				if ($kontostand > $gebot) {
					$debugStr = '2';
					if (($gebot-$altes_hoechstgebot) >= $mindestgebot) {
						$debugStr = '3';
						if ($gebot <= $maximalgebot) {
							$debugStr = '4';
							if ($n3t3 < 2) { // nur 2 Transfers zwischen zwei Teams
								$debugStr = '5';
                                require_once('./classes/TransferMarket.php');
								$in1 = "UPDATE ".$prefix."transfermarkt SET betrag_highest = ".$gebot.", bieter_highest = '".$cookie_team."', gebote = gebote+1, ende = ende+".intval(TransferMarket::AUCTION_TIME_EXTENSION_ON_BID * 60)." WHERE spieler = '".$spieler_id."' AND betrag_highest < ".$gebot." AND ende > ".time();
								$in2 = mysql_query($in1);
								$transferLog1 = "INSERT INTO ".$prefix."transfers_gebote (spieler, datum, bieter, bieterIP, betrag) VALUES ('".$spieler_id."', ".time().", '".$cookie_team."', '".getUserIP()."', ".bigintval($gebot).")";
								$transferLog2 = mysql_query($transferLog1);
								// SPIELER AUCH AUF BEOBACHTUNGSLISTE NACH GEBOT ANFANG
								$getName1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$spieler_id."'";
								$getName2 = mysql_query($getName1);
								$getName3 = mysql_fetch_assoc($getName2);
								$spieler_name = $getName3['vorname'].' '.$getName3['nachname'];
								$watch1 = "INSERT INTO ".$prefix."transfermarkt_watch (team, spieler_id, spieler_name) VALUES ('".$cookie_team."', '".$spieler_id."', '".$spieler_name."')";
								$watch2 = mysql_query($watch1);
								// SPIELER AUCH AUF BEOBACHTUNGSLISTE NACH GEBOT ENDE
							}
						}
					}
				}
			}
		}
	}
}
$hadresse .= '&debug='.$debugStr;
header($hadresse);
?>
