<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
set_time_limit(300);
$datum = date('Y-m-d', time());
$timeout1 = getTimestamp('-6 days');
$timeout2 = getTimestamp('-9 days');
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ANFANG
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ANFANG
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ANFANG
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ANFANG
$timeout_urlaub1 = getTimestamp('-16 days');
$timeout_urlaub2 = getTimestamp('-36 days');
$sql1 = "SELECT ids, username, email, regdate, team, last_login, status, last_urlaub_kurz, last_urlaub_lang, verwarnt FROM ".$prefix."users WHERE last_login < ".$timeout1." AND last_login != 1 AND LENGTH(team) = 32";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
if ($sql3['last_urlaub_kurz'] > $timeout_urlaub1) { continue; }
if ($sql3['last_urlaub_lang'] > $timeout_urlaub2) { continue; }
if ($sql3['status'] == 'Helfer' OR $sql3['status'] == 'Admin') { continue; } // Team-Mitglieder vor Löschung schützen
if ($sql3['verwarnt'] != 0) { continue; } // wenn schon verwarnt, dann nicht noch mal verwarnen, sondern löschen
if (strlen($sql3['team']) != 32) { continue; } // wenn der User überhaupt ein Team hat, sonst eh passiver User
if ($sql3['ids'] == DEMO_USER_ID) { continue; }
$monateDabei = round(abs(time()-$sql3['regdate'])/3600/24/30);
$tageNichtOnline = round(abs(time()-$sql3['last_login'])/3600/24)-9;
if ($monateDabei > $tageNichtOnline) { continue; } // nach jedem Monat darf man einen Tag länger fehlen
$vw1 = "UPDATE ".$prefix."users SET verwarnt = ".time()." WHERE ids = '".$sql3['ids']."'";
$vw2 = mysql_query($vw1);
if ($sql3['status'] == 'Bigpoint') {
$bpUserID = str_replace('BP_', '', $sql3['email']);
$nachricht = '
Hallo,

Du hast Dich nun schon eine Weile nicht mehr beim Ballmanager blicken lassen. Am '.date('d.m.Y', $sql3['last_login']).' wurdest Du zuletzt auf dem Trainingsgelaende gesehen. Deine Spieler fuehlen sich schon etwas vernachlaessigt.

Wenn Du Deinen Posten als Manager behalten möchtest, logge Dich bitte innerhalb der naechsten 2 Tage wieder ein und kuemmere Dich um Dein Team.

Falls Du keine Lust mehr hast, logge Dich bitte nicht mehr ein. Denn in 3 Tagen wird Dein Account dann automatisch geloescht.

Manager, die Ihre Mannschaften seit 9 Tagen nicht mehr betreut haben, werden beim Ballmanager regelmaessig geloescht. So machen wir Platz fuer ehrgeizigere Manager, die wirklich beim Ballmanager spielen wollen.

Wenn Du doch noch Lust hast weiterzuspielen, bist Du natuerlich jederzeit willkommen!

Sportliche Gruesse
Ballmanager
www.ballmanager.de

---------------

Du erhaeltst diese E-Mail, weil Du Dich ueber Bigpoint beim Ballmanager registriert bist. Du kannst Deinen Account jederzeit loeschen, sodass Du keine E-Mails mehr von uns erhaeltst.';
$bp_mails1 = "INSERT INTO ".$prefix."bp_mails (bpUserId, userID, mailSubject, mailText, zeit) VALUES ('".$bpUserID."', '".$sql3['ids']."', 'Ballmanager: Keine Lust mehr?', '".$nachricht."', ".time().")";
$bp_mails2 = mysql_query($bp_mails1);
}
else {
	// E-MAIL VERSENDEN ANFANG
	$empfaenger = $sql3['email'];
	$betreff = 'Ballmanager: Keine Lust mehr?';
	$nachricht = "Hallo ".$sql3['username'].",\n\nDu hast Dich nun schon eine Weile nicht mehr beim Ballmanager (www.ballmanager.de) blicken lassen. Am ".date('d.m.Y', $sql3['last_login'])." wurdest Du zuletzt auf dem Trainingsgelände gesehen. Deine Spieler fühlen sich schon etwas vernachlässigt.\n\nAnsonsten wird sich der Vorstand nach einem Nachfolger umsehen und in 3 Tagen einen neuen Manager für Deinen Klub präsentieren.\n\nWenn Du noch Lust hast weiterzuspielen, bist Du natürlich jederzeit herzlich willkommen!\n\nSportliche Grüße\nDas Ballmanager Support-Team\nwww.ballmanager.de\n\n------------------------------\n\nDu erhältst diese E-Mail, weil Du Dich auf www.ballmanager.de mit dieser Adresse registriert hast. Du kannst Deinen Account jederzeit löschen, nachdem Du Dich eingeloggt hast, sodass Du anschließend keine E-Mails mehr von uns bekommst. Bei Missbrauch Deiner E-Mail-Adresse meldest Du Dich bitte per E-Mail unter info@ballmanager.de";
	if($config['PHP_MAILER']){
		require './phpmailer/PHPMailerAutoload.php';
		$mail = new PHPMailer(); // create a new object
		$mail->CharSet= $config['SMTP_CHARSET'];
		$mail->IsSMTP();
		$mail->SMTPAuth = $config['SMTP_AUTH'];
		$mail->SMTPSecure = $config['SMTP_SECURE'];
		$mail->Host = $config['SMTP_HOST'];
		$mail->Port = $config['SMTP_PORT'];
		$mail->Username = $config['SMTP_USER'];
		$mail->Password = $config['SMTP_PASS'];
		$mail->SetFrom($config['SMTP_FROM']);
		$mail->Subject = $betreff;
		$mail->Body = $nachricht;
		$mail->AddAddress($empfaenger);
		$mail->Send();
	}
	else{
		$header = 'From: Ballmanager <info@ballmanager.de>\r\nContent-type: text/plain; charset=utf-8';
		mail($empfaenger, $betreff, $nachricht, $header);
	}
	// E-MAIL VERSENDEN ENDE
}
}
//} // if mysql_affected_rows
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ENDE
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ENDE
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ENDE
// WARN-EMAIL WARN-EMAIL WARN-EMAIL ENDE
// LOESCHEN LOESCHEN LOESCHEN ANFANG
// LOESCHEN LOESCHEN LOESCHEN ANFANG
// LOESCHEN LOESCHEN LOESCHEN ANFANG
// LOESCHEN LOESCHEN LOESCHEN ANFANG
$delete_manager = array();
$delete_teams = array();
$timeoutv = getTimestamp('-2 days'); // Verwarnung muss mindestens 2 Tage her sein
$timeout_urlaub1 = getTimestamp('-19 days');
$timeout_urlaub2 = getTimestamp('-39 days');
$sd8faa1 = "SELECT ids, username, liga, team, last_urlaub_kurz, last_urlaub_lang, verwarnt, status, last_login FROM ".$prefix."users WHERE (last_login < ".$timeout2." OR last_login = 1) AND LENGTH(team) = 32";
$sd8faa2 = mysql_query($sd8faa1);
while ($sd8faa3 = mysql_fetch_assoc($sd8faa2)) {
	if ($sd8faa3['last_urlaub_kurz'] > $timeout_urlaub1) { continue; }
	if ($sd8faa3['last_urlaub_lang'] > $timeout_urlaub2) { continue; }
	if ($sd8faa3['status'] == 'Helfer' OR $sd8faa3['status'] == 'Admin') { continue; }
	if ($sd8faa3['ids'] == DEMO_USER_ID) { continue; }
	if ($sd8faa3['verwarnt'] == 0 && $sd8faa3['last_login'] > 1) { continue; } // bei last_login = 1 keine Verwarnung noetig
	if ($sd8faa3['verwarnt'] > $timeoutv) { continue; }
	if (strlen($sd8faa3['team']) != 32) { continue; }
	$gtn1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$sd8faa3['team']."'";
	$gtn2 = mysql_query($gtn1);
	$gtn3 = mysql_fetch_assoc($gtn2);
	$dtran1 = "UPDATE ".$prefix."transfers SET bieter = '???' WHERE bieter = '".$gtn3['name']."'";
	$dtran2 = mysql_query($dtran1);
	$dtran1 = "UPDATE ".$prefix."transfers SET besitzer = '???' WHERE besitzer = '".$gtn3['name']."'";
	$dtran2 = mysql_query($dtran1);
	$prot1 = "DELETE FROM ".$prefix."protokoll WHERE team = '".$sd8faa3['team']."'";
	$prot2 = mysql_query($prot1);
	$prot1 = "DELETE FROM ".$prefix."buchungen WHERE team = '".$sd8faa3['team']."'";
	$prot2 = mysql_query($prot1);
	$in_14_tagen = endOfDay(getTimestamp('+14 days'));
	$sql9 = "UPDATE ".$prefix."spieler SET transfermarkt = 0, vertrag = ".$in_14_tagen." WHERE team = '".$sd8faa3['team']."' AND leiher = 'keiner'";
	$sql10 = mysql_query($sql9);
	// SPIELER VOM TRANSFERMARKT HOLEN ANFANG
	$tm1 = "DELETE FROM ".$prefix."transfermarkt_leihe WHERE besitzer = '".$sd8faa3['team']."'";
	$tm2 = mysql_query($tm1);
	$tm1 = "DELETE FROM ".$prefix."transfermarkt WHERE besitzer = '".$sd8faa3['team']."'";
	$tm2 = mysql_query($tm1);
	// SPIELER VOM TRANSFERMARKT HOLEN ENDE
	$sql9 = "UPDATE ".$prefix."forum_themen SET lastposter = '[Unbekannt]' WHERE lastposter = '".$sd8faa3['username']."'";
	$sql10 = mysql_query($sql9);
    $straf3 = "UPDATE ".$prefix."teams SET konto = 25000000, meisterschaften = 0, pokalsiege = 0, cupsiege = 0, friendlies = 0, friendlies_ges = 0, jugendarbeit = 5, fanbetreuer = 1, scout = 1, vorjahr_konto = 25000000, taktik = 'N', einsatz = 100, stadion_aus = 0, last_cookie_user = '' WHERE ids = '".$sd8faa3['team']."'";
    $straf4 = mysql_query($straf3);
    $straf5 = "UPDATE ".$prefix."stadien SET plaetze = 15000, preis = 40, parkplatz = 0, ubahn = 0, restaurant = 0, bierzelt = 0, pizzeria = 0, imbissstand = 0, vereinsmuseum = 0, fanshop = 0 WHERE team = '".$sd8faa3['team']."'";
    $straf6 = mysql_query($straf5);
    $straf7 = "UPDATE ".$prefix."spieler SET team = leiher, leiher = 'keiner', startelf_Liga = 0, startelf_Pokal = 0, startelf_Cup = 0, startelf_Test = 0 WHERE leiher != 'keiner' AND team = '".$sd8faa3['team']."'";
    $straf8 = mysql_query($straf7);
	// ACCOUNT LOESCHEN ANFANG
	$sql4 = "UPDATE ".$prefix."users SET team = '__".$sd8faa3['ids']."', last_login = 0 WHERE ids = '".$sd8faa3['ids']."'";
	$sql5 = mysql_query($sql4);
	// ACCOUNT LOESCHEN ENDE
}
$clearTimeout = getTimestamp('-26 weeks');
$clear1 = "DELETE FROM ".$prefix."users WHERE last_login < ".$clearTimeout." AND LENGTH(team) = 34";
$clear2 = mysql_query($clear1);
?>