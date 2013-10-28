<?php if (!isset($_GET['mode'])) { include 'zzserver.php'; } ?>
<?php include 'zzfunctions.php'; ?>
<?php
$sql1 = "SELECT gespielt FROM ".$prefix."ligen LIMIT 0, 1";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
if ($sql3['gespielt'] != 22) { exit; }
$sql1 = "SELECT COUNT(*) FROM ".$prefix."teams WHERE punkte > 0";
$sql2 = mysql_query($sql1);
$sql3 = mysql_result($sql2, 0);
if ($sql3 == 0) { exit; }
$bwl5 = "UPDATE ".$prefix."teams SET gewinnGeld = (konto-vorjahr_konto), vorjahr_konto = konto, vorjahr_elo = elo, vorjahr_pokalrunde = pokalrunde, vorjahr_cuprunde = cuprunde, vorjahr_liga = liga, vorjahr_platz = rank, rank = 1, pokalrunde = 0, cuprunde = 0, punkte = 0, tore = 0, gegentore = 0, sunS = 0, sunU = 0, sunN = 0, sponsor = 0, sponsor_a = 500000, sponsor_s = 500000, stadion_aus = 0, tv_ein = 0";
$bwl6 = mysql_query($bwl5);
// AUF- UND ABSTIEG ANFANG
$abstieg1 = "SELECT ids, runter, hoch, name FROM ".$prefix."ligen WHERE runter != 'KEINE'";
$abstieg2 = mysql_query($abstieg1);
while ($abstieg3 = mysql_fetch_assoc($abstieg2)) {
	$abstieg4 = "UPDATE ".$prefix."teams SET liga = '".$abstieg3['runter']."' WHERE vorjahr_liga = '".$abstieg3['ids']."' AND vorjahr_platz > 9";
	$abstieg5 = mysql_query($abstieg4);
	if ($abstieg3['hoch'] == 'KEINE') {
		$meister1 = "UPDATE ".$prefix."teams SET meisterschaften = meisterschaften+1 WHERE vorjahr_liga = '".$abstieg3['ids']."' AND vorjahr_platz = 1";
		$meister2 = mysql_query($meister1);
	}
}
$aufstieg1 = "SELECT ids, hoch, name FROM ".$prefix."ligen WHERE hoch != 'KEINE'";
$aufstieg2 = mysql_query($aufstieg1);
while ($aufstieg3 = mysql_fetch_assoc($aufstieg2)) {
	$aufstieg4 = "UPDATE ".$prefix."teams SET liga = '".$aufstieg3['hoch']."' WHERE vorjahr_liga = '".$aufstieg3['ids']."' AND vorjahr_platz < 4";
	$aufstieg5 = mysql_query($aufstieg4);
}
// AUF- UND ABSTIEG ENDE
$aelter1 = "UPDATE ".$prefix."spieler SET wiealt = wiealt+365";
$aelter2 = mysql_query($aelter1);
$bwl1 = "UPDATE ".$prefix."zeitrechnung SET saison = saison+1";
$bwl2 = mysql_query($bwl1);
$bwl3 = "UPDATE ".$prefix."spieler SET spiele = 0, tore = 0, frische = 100, pokalNurFuer = '', verletzung = 0, karten = 0, letzte_verbesserung = 0, liga = (SELECT liga FROM ".$prefix."teams WHERE ids = ".$prefix."spieler.team)";
$bwl4 = mysql_query($bwl3);
$lei1 = "UPDATE ".$prefix."spieler SET team = leiher, leiher = 'keiner', praemienAbrechnung = 0, praemieProEinsatz = 0, startelf_Liga = 0, startelf_Pokal = 0, startelf_Cup = 0, startelf_Test = 0 WHERE leiher != 'keiner'";
$lei2 = mysql_query($lei1);
$bwl7 = "UPDATE ".$prefix."ligen SET gespielt = 0";
$bwl8 = mysql_query($bwl7);
$to1 = "INSERT INTO ".$prefix."transfers_old SELECT * FROM ".$prefix."transfers WHERE gebot > 1";
$to2 = mysql_query($to1);
$bwl11 = "TRUNCATE TABLE ".$prefix."transfers";
$bwl12 = mysql_query($bwl11);
$bwl15 = "TRUNCATE TABLE ".$prefix."spiele";
$bwl16 = mysql_query($bwl15);
$ber1 = "TRUNCATE TABLE ".$prefix."spiele_kommentare";
$ber2 = mysql_query($ber1);
$bwl17 = "UPDATE ".$prefix."users SET liga = (SELECT liga FROM ".$prefix."teams WHERE ids = ".$prefix."users.team)";
$bwl18 = mysql_query($bwl17);
?>