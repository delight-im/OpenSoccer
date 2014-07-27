<?php
if (!isset($_GET['id']) OR !isset($_GET['typ'])) { exit; }
include 'zzserver.php';
include 'zzcookie.php';
require_once('./classes/Friendlies.php');
function isFriendlyDateValid($matchTime) {
    return $matchTime > time() && $matchTime < (time() + 3600 * 24 * (22 - GameTime::getMatchDay()));
}
$team = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
$typ = mysql_real_escape_string(trim(strip_tags($_GET['typ'])));
if ($cookie_id != CONFIG_DEMO_USER) {
	if ($typ == 'Annehmen') {
		$testspiel_preis_ich = Friendlies::getPrice($cookie_liga, $prefix);
		$gt1 = "SELECT a.team1_name, a.datum, b.liga, b.ids FROM ".$prefix."testspiel_anfragen AS a JOIN ".$prefix."teams AS b ON a.team1 = b.ids WHERE a.team1 = '".$team."' AND a.team2 = '".$cookie_team."'";
		$gt2 = mysql_query($gt1);
		if (mysql_num_rows($gt2) == 0) { exit; }
		$gt3 = mysql_fetch_assoc($gt2);
		$team1_name = $gt3['team1_name'];
		$team1_liga = $gt3['liga'];
		$team1_ids = $gt3['ids'];
		$testspiel_preis_der_andere = Friendlies::getPrice($team1_liga, $prefix);
		$datum_spiel = $gt3['datum'];

        $gt1 = "DELETE FROM ".$prefix."testspiel_anfragen WHERE team1 = '".$cookie_team."' AND datum = ".$datum_spiel;
        $gt2 = mysql_query($gt1);
        $gt1 = "DELETE FROM ".$prefix."testspiel_anfragen WHERE team1 = '".$team."' AND datum = ".$datum_spiel;
        $gt2 = mysql_query($gt1);

        if (isFriendlyDateValid($datum_spiel)) {
            // HAT EINER DER BEIDEN SCHON EIN SPIEL AN DEM TAG ANFANG
            $yetBelegt1 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."') AND datum = ".$datum_spiel." AND typ = 'Test'";
            $yetBelegt2 = mysql_query($yetBelegt1);
            $yetBelegt3 = mysql_result($yetBelegt2, 0);
            $yetBelegt4 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE (team1 = '".$team1_name."' OR team2 = '".$team1_name."') AND datum = ".$datum_spiel." AND typ = 'Test'";
            $yetBelegt5 = mysql_query($yetBelegt4);
            $yetBelegt6 = mysql_result($yetBelegt5, 0);
            // HAT EINER DER BEIDEN SCHON EIN SPIEL AN DEM TAG ENDE
            if ($yetBelegt3 == 0 && $yetBelegt6 == 0) {
                $gt1 = "INSERT INTO ".$prefix."spiele (liga, datum, team1, team2, typ) VALUES ('Testspiel', ".$datum_spiel.", '".$team1_name."', '".$cookie_teamname."', 'Test')";
                $gt2 = mysql_query($gt1);
                // GEBUEHR ABBUCHEN ANFANG
                $abb1 = "UPDATE ".$prefix."teams SET konto = konto-".$testspiel_preis_ich." WHERE ids = '".$cookie_team."'";
                $abb2 = mysql_query($abb1);
                $abb3 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Testspiel', -".$testspiel_preis_ich.", '".time()."')";
                $abb4 = mysql_query($abb3);
                $abb1 = "UPDATE ".$prefix."teams SET konto = konto-".$testspiel_preis_der_andere." WHERE ids = '".$team."'";
                $abb2 = mysql_query($abb1);
                $abb3 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$team."', 'Testspiel', -".$testspiel_preis_der_andere.", '".time()."')";
                $abb4 = mysql_query($abb3);
                $antworttext = 'angenommen';
                // GEBUEHR ABBUCHEN ENDE
            }
            else {
                $antworttext = 'ablehnen lassen, weil der Termin schon belegt war';
            }
        }
        else {
            $antworttext = 'ablehnen lassen, weil der Termin nicht passt';
        }
		if (isset($_SESSION['last_testspiele_anzahl'])) {
			$_SESSION['last_testspiele_anzahl']--;
		}
	}
	elseif ($typ == 'Ablehnen') {
		$gt1 = "DELETE FROM ".$prefix."testspiel_anfragen WHERE team1 = '".$team."' AND team2 = '".$cookie_team."'";
		$gt2 = mysql_query($gt1);
		if (mysql_affected_rows() == 0) { exit; }
		$antworttext = 'abgelehnt';
		$_SESSION['last_testspiele_anzahl']--;
	}
	else {
		exit;
	}
	// PROTOKOLL ANFANG
	$formulierung = 'Du hast eine Anfrage für ein Testspiel '.$antworttext;
	$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Termine', ".time().")";
	$sql8 = mysql_query($sql7);
	$formulierung = '<a href="/team.php?id='.$cookie_team.'">'.$cookie_teamname.'</a> hat Dein Angebot für ein Testspiel '.$antworttext;
	$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$team."', '".$formulierung."', 'Termine', ".time().")";
	$sql8 = mysql_query($sql7);
	// PROTOKOLL ENDE
}
header('Location: /testspiele.php');
?>
