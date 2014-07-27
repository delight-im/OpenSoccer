<?php include 'zz1.php'; ?>
<?php
if (isset($_POST['spieler'])) {
	$spieler_id = mysql_real_escape_string(trim(strip_tags($_POST['spieler'])));
	$hadresse = 'Location: /spieler.php?id='.$spieler_id;
}
else {
	exit;
}
if ($loggedin == 1) {
    if (isset($_POST['typ']) && $cookie_id != CONFIG_DEMO_USER) { // && isset($_POST['laenge']) && isset($_POST['startgebot']) && isset($_POST['autorestart'])
        $sql1 = "SELECT vorname, nachname, marktwert, spiele_verein, staerke FROM ".$prefix."spieler WHERE ids = '".$spieler_id."' AND team = '".$cookie_team."' AND leiher = 'keiner'";
        $sql2 = mysql_query($sql1);
        $sql2a = mysql_num_rows($sql2);
        if ($sql2a > 0) {
            $sql3 = mysql_fetch_assoc($sql2);
            $sperre1 = "SELECT COUNT(*) FROM ".$prefix."spieler WHERE team = '".$cookie_team."' AND transfermarkt = 0";
            $sperre2 = mysql_query($sperre1);
            $sperre3 = mysql_result($sperre2, 0);
            if ($sperre3 > 13) {
                if ($_POST['typ'] == 'Kauf') { // Verkaufen
					$spielerName = mysql_real_escape_string($sql3['vorname'].' '.$sql3['nachname']);
					$sql1 = "UPDATE ".$prefix."spieler SET transfermarkt = 0, liga = 'frei', team = 'frei', gehalt = 0, vertrag = 0 WHERE ids = '".$spieler_id."'";
					$sql2 = mysql_query($sql1);
					$dl1 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$spieler_id."'";
					$dl2 = mysql_query($dl1);
					$dl1 = "DELETE FROM ".$prefix."transfermarkt_leihe WHERE spieler = '".$spieler_id."'";
					$dl2 = mysql_query($dl1);
					// TRANSFERSTEUER ANFANG
					$transfersteuer = round($sql3['marktwert']*0.05);
					$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Transfersteuer', -".$transfersteuer.", ".time().")";
					$buch2 = mysql_query($buch1);
					// TRANSFERSTEUER ENDE
					$upKon1 = "UPDATE ".$prefix."teams SET konto = konto+".round($sql3['marktwert']-$transfersteuer)." WHERE ids = '".$cookie_team."'";
					$upKon2 = mysql_query($upKon1);
					$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Ablöse', ".$sql3['marktwert'].", ".time().")";
					$buch2 = mysql_query($buch1);
					$formulierung = __('Du hast den Spieler %1$s für %2$s € an einen Verein außerhalb Europas verkauft.', '<a href="/spieler.php?id='.$spieler_id.'">'.$spielerName.'</a>', number_format($sql3['marktwert'], 0, ',', '.'));
					$free1 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Transfers', '".time()."')";
					$free2 = mysql_query($free1);
					$move1 = "INSERT INTO ".$prefix."transfers (spieler, besitzer, bieter, datum, gebot, damaligerWert, spiele_verein, damaligeStaerke) VALUES ('".$spieler_id."', '".$cookie_team."', 'AUSSERHALB_EU', ".time().", ".$sql3['marktwert'].", ".$sql3['marktwert'].", ".$sql3['spiele_verein'].", ".$sql3['staerke'].")";
					$move2 = mysql_query($move1);
					/*$startgebot = ceil($sql3['marktwert']*intval($_POST['startgebot'])/100);
                    $laenge = intval($_POST['laenge']);
                    if ($_POST['autorestart'] == 'Ja') { $autorestart = $laenge; } else { $autorestart = 0; }
					$laengen = array(24, 36, 48, 60, 72);
					if (in_array($laenge, $laengen)) {
						if (!isset($cookie_team) OR $cookie_team == '') {
							echo 'Bitte melde den Fehler E5 im Forum oder unter <'.CONFIG_SITE_EMAIL.'>';
							$phpf1 = "INSERT INTO ".$prefix."php_fehler (datei) VALUES ('transfermarkt_auktion KEIN Team')";
							$phpf2 = mysql_query($phpf1);
							exit;
						}
                        $sql8 = "INSERT INTO ".$prefix."transfermarkt (spieler, besitzer, gehalt, ende, betrag_highest, autorestart) VALUES ('".$spieler_id."', '".$cookie_team."', ".ceil($sql3['marktwert']/11).", ".getTimestamp('+'.$laenge.' hours').", ".$startgebot.", ".$autorestart.")";
                        $sql9 = mysql_query($sql8);
                        if (mysql_affected_rows() != 0) {
                            $sql4 = "UPDATE ".$prefix."spieler SET transfermarkt = 1, moral = moral-15 WHERE ids = '".$spieler_id."'";
                            $sql5 = mysql_query($sql4);
                        }
                    }*/
                }
                elseif ($_POST['typ'] > 999998) { // Leihgabe
                    $sql4 = "UPDATE ".$prefix."spieler SET transfermarkt = ".bigintval($_POST['typ']).", moral = moral-10 WHERE ids = '".$spieler_id."'";
                    $sql5 = mysql_query($sql4);
                }
            }
        }
    }
    elseif (isset($_POST['abbrechen']) && $cookie_id != CONFIG_DEMO_USER) {
    	if ($_POST['abbrechen'] == 'Ja') {
            $sql6 = "DELETE FROM ".$prefix."transfermarkt WHERE spieler = '".$spieler_id."' AND ende > ".getTimestamp('+30 minutes')." AND gebote = 0";
            $sql7 = mysql_query($sql6);
            $sql6 = "DELETE FROM ".$prefix."transfermarkt_leihe WHERE spieler = '".$spieler_id."' AND akzeptiert = 0";
            $sql7 = mysql_query($sql6);
            $sql4 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE ids = '".$spieler_id."'";
            $sql5 = mysql_query($sql4);
        }
    }
}
header($hadresse);
exit;
?>
