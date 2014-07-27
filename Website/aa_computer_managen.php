<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
define('COMPUTER_MINIMUM_KONTOSTAND', 20000000);

$in_14_tagen = endOfDay(getTimestamp('+14 days'));
$ligaToKlasse = array();
$lig1a = "SELECT ids, name FROM ".$prefix."ligen";
$lig1b = mysql_query($lig1a);
while ($lig1c = mysql_fetch_assoc($lig1b)) {
	$ligaToKlasse[$lig1c['ids']] = substr($lig1c['name'], -1);
}

$demoTeamID1 = "SELECT team FROM ".$prefix."users WHERE ids = '".CONFIG_DEMO_USER."'";
$demoTeamID2 = mysql_query($demoTeamID1);
$sqlIsDemoTeam = "";
if (mysql_num_rows($demoTeamID2) == 1) {
	$demoTeamID3 = mysql_result($demoTeamID2, 0);
	$sqlIsDemoTeam = " OR ids = '".mysql_real_escape_string($demoTeamID3)."'";
}

$urlaub1 = "SELECT user, team FROM ".$prefix."urlaub WHERE ende > ".time();
$urlaub2 = mysql_query($urlaub1);
$urlaub_string = "('LEER', ";
$beurlaubte_teams = array();
while ($urlaub3 = mysql_fetch_assoc($urlaub2)) {
	if ($urlaub3['user'] != CONFIG_DEMO_USER) {
		$urlaub_string .= "'".$urlaub3['user']."', ";
		$beurlaubte_teams[] = $urlaub3['team'];
	}
}
$urlaub_string = substr($urlaub_string, 0, -2);
$urlaub_string .= ")";
$sql1 = "SELECT name, ids, liga FROM ".$prefix."teams WHERE ids NOT IN (SELECT team FROM ".$prefix."users WHERE ids NOT IN ".$urlaub_string.")".$sqlIsDemoTeam." ORDER BY last_managed ASC LIMIT 0, 8";
$sql2 = mysql_query($sql1);

while ($sql3 = mysql_fetch_assoc($sql2)) {
	$limit_staerke = " AND staerke < 8.0";
	if (isset($ligaToKlasse[$sql3['liga']])) {
		switch ($ligaToKlasse[$sql3['liga']]) {
			case 1:
				$limit_staerke = " AND staerke < 7.5";
				$kick_limit_staerke = "staerke > 7.5";
				break;
			case 2:
				$limit_staerke = " AND staerke < 6.3";
				$kick_limit_staerke = "staerke > 6.3";
				break;
			case 3:
				$limit_staerke = " AND staerke < 5.1";
				$kick_limit_staerke = "staerke > 5.1";
				break;
			case 4:
				$limit_staerke = " AND staerke < 3.9";
				$kick_limit_staerke = "staerke > 3.9";
				break;
            default:
                throw new Exception('Unknown league: '.$ligaToKlasse[$sql3['liga']]);
		}
	}
	$moralSQL = "";
	$contractMaxAge = 13140;
	if (!in_array($sql3['ids'], $beurlaubte_teams)) { // nur bei Computer-Teams
		$moralSQL = ", moral = 60";
		$contractMaxAge = 10585;
		// SPIELER KUENDIGEN ODER KADER AUFFUELLEN ANFANG
		$mannschaftsteile = array('T'=>3, 'A'=>7, 'M'=>7, 'S'=>4);
		for ($u = 0; $u < count($mannschaftsteile); $u++) {
			if ($mannschaftsteil_daten = each($mannschaftsteile)) {
				$ver_t1 = "SELECT COUNT(*) FROM ".$prefix."spieler WHERE team = '".$sql3['ids']."' AND position = '".$mannschaftsteil_daten['key']."'";
				$ver_t2 = mysql_query($ver_t1);
				$ver_t3 = mysql_result($ver_t2, 0);
				if ($ver_t3 > $mannschaftsteil_daten['value']) { // spieler rauswerfen
					$gvd1 = "UPDATE ".$prefix."spieler SET team = 'frei', liga = 'frei', vertrag = 0, startelf_Liga = 0, startelf_Pokal = 0, startelf_Cup = 0, startelf_Test = 0, transfermarkt = 0, leiher = 'keiner', frische = ".mt_rand(50, 100)." WHERE team = '".$sql3['ids']."' AND (position = '".$mannschaftsteil_daten['key']."' OR wiealt > 10585 OR ".$kick_limit_staerke.") ORDER BY staerke DESC LIMIT 1";
					$gvd2 = mysql_query($gvd1);
				}
				elseif ($ver_t3 < $mannschaftsteil_daten['value']) { // spieler holen
					$gvd1 = "UPDATE ".$prefix."spieler SET team = '".$sql3['ids']."', liga = '".$sql3['liga']."', vertrag = ".$in_14_tagen.", frische = ".mt_rand(50, 100).", startelf_Liga = 0, startelf_Pokal = 0, startelf_Cup = 0, startelf_Test = 0, gehalt = ROUND(marktwert/14) WHERE team = 'frei' AND wiealt < 10585 AND transfermarkt = 0 AND position = '".$mannschaftsteil_daten['key']."'".$limit_staerke." ORDER BY staerke DESC LIMIT 1";
					$gvd2 = mysql_query($gvd1);
				}
			}
		}
		// SPIELER KUENDIGEN ODER KADER AUFFUELLEN ENDE
		// ZU STARKE SPIELER SCHWAECHEN ANFANG
		$sql4 = "UPDATE ".$prefix."spieler SET staerke = staerke-1.8, talent = talent-1.8, marktwert = 0 WHERE team = '".$sql3['ids']."' AND staerke > 8.5";
		$sql4 = mysql_query($sql4);
		// ZU STARKE SPIELER SCHWAECHEN ENDE
    }
    // VERTRAEGE VERLAENGERN ANFANG
    $vt1 = "UPDATE ".$prefix."spieler SET vertrag = ".$in_14_tagen.", gehalt = ROUND(marktwert/14)".$moralSQL." WHERE team = '".$sql3['ids']."' AND vertrag < ".$in_14_tagen." AND wiealt < ".$contractMaxAge;
    $vt2 = mysql_query($vt1);
    // VERTRAEGE VERLAENGERN ENDE
	$xql4 = "UPDATE ".$prefix."spieler SET startelf_Liga = 0, startelf_Pokal = 0, startelf_Cup = 0 WHERE team = '".$sql3['ids']."'";
	$xql4 = mysql_query($xql4);
	// SPIELER AUSWAEHLEN ANFANG
	$sql4 = "UPDATE ".$prefix."spieler SET startelf_Liga = 1, startelf_Pokal = 1, startelf_Cup = 1 WHERE team = '".$sql3['ids']."' AND position = 'T' AND verletzung = 0 ORDER BY (staerke*frische) DESC LIMIT 1";
	$sql4 = mysql_query($sql4);
	$sql4 = "UPDATE ".$prefix."spieler SET startelf_Liga = 1, startelf_Pokal = 1, startelf_Cup = 1 WHERE team = '".$sql3['ids']."' AND position = 'A' AND verletzung = 0 ORDER BY (staerke*frische) DESC LIMIT 4";
	$sql4 = mysql_query($sql4);
	$sql4 = "UPDATE ".$prefix."spieler SET startelf_Liga = 1, startelf_Pokal = 1, startelf_Cup = 1 WHERE team = '".$sql3['ids']."' AND position = 'M' AND verletzung = 0 ORDER BY (staerke*frische) DESC LIMIT 4";
	$sql4 = mysql_query($sql4);
	$sql4 = "UPDATE ".$prefix."spieler SET startelf_Liga = 1, startelf_Pokal = 1, startelf_Cup = 1 WHERE team = '".$sql3['ids']."' AND position = 'S' AND verletzung = 0 ORDER BY (staerke*frische) DESC LIMIT 2";
	$sql4 = mysql_query($sql4);
	// SPIELER AUSWAEHLEN ENDE
	if (!in_array($sql3['ids'], $beurlaubte_teams)) { // nur bei Computer-Teams
		$lastm1 = "UPDATE ".$prefix."teams SET konto = ".COMPUTER_MINIMUM_KONTOSTAND." WHERE ids = '".$sql3['ids']."' AND konto < ".COMPUTER_MINIMUM_KONTOSTAND;
		$lastm2 = mysql_query($lastm1);
	}
	$lastm1 = "UPDATE ".$prefix."teams SET last_managed = ".time()." WHERE ids = '".$sql3['ids']."'";
	$lastm2 = mysql_query($lastm1);
}
// TAKTIKEN ZURUECKSETZEN ANFANG
$tr1 = "UPDATE ".$prefix."taktiken SET ausrichtung = DEFAULT, geschw_auf = DEFAULT, pass_auf = DEFAULT, risk_pass = DEFAULT, druck = 1, aggress = 1 WHERE team NOT IN (SELECT team FROM ".$prefix."users)";
$tr2 = mysql_query($tr1);
// TAKTIKEN ZURUECKSETZEN ENDE
?>