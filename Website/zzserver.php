<?php

// declare the content type and encoding
header('Content-Type: text/html; charset=utf-8');
// prevent clickjacking
header('X-Frame-Options: sameorigin');
// prevent content sniffing (MIME sniffing)
header('X-Content-Type-Options: nosniff');
// remove unnecessary HTTP headers
header_remove('X-Powered-By');

date_default_timezone_set('Europe/Berlin');
ignore_user_abort(true);
include 'config.php';

// connect to the database
mysql_connect(CONFIG_DATABASE_HOST, CONFIG_DATABASE_USERNAME, CONFIG_DATABASE_PASSWORD) or die ('Falsche MySQL-Daten!');
mysql_select_db(CONFIG_DATABASE_NAME) or die ('Datenbank existiert nicht!');

// ERROR REPORTING BEGIN
error_reporting(E_ALL);
ini_set('display_errors', 'stdout');
function reportError($text, $statement = '') {
    global $prefix;
	$err1 = "INSERT INTO ".$prefix."php_fehler (datei, zeile, beschreibung, zeit) VALUES ('".$_SERVER['REQUEST_URI']."_".mt_rand(0, 1000000)."', '0', '".mysql_real_escape_string(trim($text).'/'.$statement)."', ".time().")";
	mysql_query($err1);
	return FALSE;
}
// ERROR REPORTING END

$prefix = 'man_';
$kuerzelListe = array('AC', 'AJ', 'AS', 'ASC', 'ASV', 'Athletic', 'Atletico', 'Austria', 'AZ', 'BC', 'BSV', 'BV', 'Calcio', 'CD', 'CF', 'City', 'Club', 'Deportivo', 'Espanyol', 'FC', 'FF', 'FK', 'FSC', 'FSG', 'FV', 'IF', 'KV', 'Olympique', 'OSC', 'PSV', 'Racing', 'Rapid', 'Rapids', 'RC', 'RCD', 'Real', 'Rovers', 'RS', 'SG', 'SK', 'Spartans', 'Sporting', 'SSC', 'Sturm', 'SV', 'TSV', 'TV', 'UD', 'Union', 'United', 'Wanderers');
$kategorienListe = array('Finanzen', 'Spiele & Taktik', 'Verein & Spieler', 'Accounts', 'Transfers', 'Grundregeln', 'Sonstiges');

function isMobile() {
	return stripos($_SERVER['SERVER_NAME'], 'm.') === 0;
}

/**
 * Returns the base URL for this website in the form of <http://www.example.com>, i.e. without a trailing slash
 *
 * @param boolean|NULL $forceMobile whether to return the URL for the mobile site (true) or the desktop site (false) or auto-detect the type (NULL)
 * @return string the base URL
 */
function getBaseURL($forceMobile = NULL) {
    if (!isset($forceMobile)) {
        $forceMobile = isMobile();
    }

    if ($forceMobile) {
        $hostname = str_replace('www.', 'm.', CONFIG_SITE_DOMAIN);
    }
    else {
        $hostname = CONFIG_SITE_DOMAIN;
    }

    return (CONFIG_USE_HTTPS ? 'https' : 'http').'://'.$hostname;
}

function noDemoClick($userID, $inExisting = FALSE) {
	if ($userID == CONFIG_DEMO_USER) {
		if ($inExisting) {
			return ' noDemoPopup();';
		}
		else {
			return ' onclick="noDemoPopup(); return false;"';
		}
	}
	else {
		return '';
	}
}
function getRegularFreshness($spieltag) {
	return 100-(($spieltag >= 22 ? 1 : $spieltag)*2);
}
function betrag_enkodieren($kette) {
    $ketteen = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, '.', ',');
    $buchstaben = array('a', 'c', 'e', 'g', 'i', 'k', 'm', 'o', 'q', 's', 'u', 'w');
	$kette = str_replace($ketteen, $buchstaben, $kette);
	$kette = strrev($kette);
	return $kette;
}
function betrag_dekodieren($kette) {
    $ketteen = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, '.', ',');
    $buchstaben = array('a', 'c', 'e', 'g', 'i', 'k', 'm', 'o', 'q', 's', 'u', 'w');
	$kette = str_replace($buchstaben, $ketteen, $kette);
	$kette = strrev($kette);
	return $kette;
}

function getTimestamp($shift='', $startTime=-1) {
	if ($startTime == -1) {
		$dateTime = new DateTime(); // construct DateTime object with current time
	}
	else {
		$startTime = round($startTime);
		$dateTime = new DateTime('@'.$startTime); // construct DateTime object based on given timestamp
	}
	$dateTime->setTimeZone(new DateTimeZone('Europe/Berlin')); // timezone 408: Europe/Berlin
	if ($shift != '') { // if a time shift is set (e.g.: +1 month)
		$dateTime->modify($shift); // shift the time
	}
	return $dateTime->format('U'); // return the UNIX timestamp
}
$inflation_deflation_faktor = 0.8;
$inflation_deflation_summand = 12000000;
$marktwertAusdruck = "ROUND(POW(1.75,staerke)*talent*30000/POW(((FLOOR(wiealt/365))/27), 1.7)+FLOOR(100+(RAND()*1000)))";
function anzahl_datensaetze_gesamt($text) {
	$blaetter1 = preg_replace('/SELECT (.+) FROM/i', 'SELECT COUNT(*) FROM', $text);
	$blaetter1 = explode(' LIMIT', $blaetter1, 2);
	$blaetter1 = $blaetter1[0];
	$blaetter1 = explode(' ORDER BY', $blaetter1, 2);
	$blaetter1 = $blaetter1[0];
    $blaetter2 = mysql_query($blaetter1);
    if ($blaetter2 == FALSE) { return 0; }
    if (mysql_num_rows($blaetter2) == 0) { return 0; }
    return mysql_result($blaetter2, 0);
}
// LIVE-SCORING ANFANG
$aktuelle_stunde = date('H', getTimestamp());
$aktuelle_minute = date('i', getTimestamp());
switch ($aktuelle_stunde) {
	case 10: $live_scoring_spieltyp_laeuft = 'Cup'; $live_scoring_h = 0; break;
	case 11: $live_scoring_spieltyp_laeuft = 'Cup'; $live_scoring_h = 1; break;
	case 14: $live_scoring_spieltyp_laeuft = 'Liga'; $live_scoring_h = 0; break;
	case 15: $live_scoring_spieltyp_laeuft = 'Liga'; $live_scoring_h = 1; break;
	case 18: $live_scoring_spieltyp_laeuft = 'Pokal'; $live_scoring_h = 0; break;
	case 19: $live_scoring_spieltyp_laeuft = 'Pokal'; $live_scoring_h = 1; break;
	case 22: $live_scoring_spieltyp_laeuft = 'Test'; $live_scoring_h = 0; break;
	case 23: $live_scoring_spieltyp_laeuft = 'Test'; $live_scoring_h = 1; break;
	default: $live_scoring_spieltyp_laeuft = ''; $live_scoring_h = 0; break;
}
if ($live_scoring_spieltyp_laeuft == '') { $live_scoring_min_gespielt = 0; }
else { $live_scoring_min_gespielt = $live_scoring_h*60+$aktuelle_minute; }
if ($live_scoring_min_gespielt > 125) { $live_scoring_min_gespielt = 125; }
elseif ($live_scoring_min_gespielt < 0) { $live_scoring_min_gespielt = 0; }
// LIVE-SCORING ENDE
function bigintval($value) {
    $value = trim($value);
    if (ctype_digit($value)) {
    	return $value;
    }
    $value = preg_replace("/[^0-9](.*)$/", '', $value);
    if (ctype_digit($value)) {
    	return $value;
    }
    return 0;
}
function endOfDay($stempel) {
	return mktime(23, 59, 59, date('m', $stempel), date('d', $stempel), date('Y', $stempel));
}
function displayUsername($name, $ids = '') {
	if (substr($name, 0, 9) == 'GELOESCHT') {
		return 'Gelöschter User';
	}
	else {
		if ($ids == '') {
			return $name;
		}
		else {
			return '<a href="/manager.php?id='.$ids.'">'.$name.'</a>';
		}
	}
}
function einsatz_in_auktionen($team) {
	global $prefix;
	$sql1 = "SELECT SUM(betrag_highest) AS einsatz FROM ".$prefix."transfermarkt WHERE bieter_highest = '".$team."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) { return 0; }
	$sql3 = mysql_fetch_assoc($sql2);
	return intval($sql3['einsatz']);
}
function ergebnis_drehen($ergebnis) {
	$temp = explode(':', $ergebnis, 2);
	$neu = $temp[1].':'.$temp[0];
	return $neu;
}
function Chance_Percent($chance, $universe = 100) {
	$chance = abs(intval($chance));
	$universe = abs(intval($universe));
	if (mt_rand(1, $universe) <= $chance) {
		return true;
	}
	return false;
}
function showKontostand($exakterWert) {
	return number_format($exakterWert, 0, ',', '.');
}
function autoLink($text) {
	return preg_replace('/(?<!")(https?:\/\/[^\s<]+)/i', '<a href="\0" target="_blank">\0</a>', $text);
}
function schaetzungVomScout($cookie_team, $cookie_scout, $spielerID, $spielerTalent, $spielerStaerke) {
	$possibleMD5chr = array(48=>-1, 49=>1, 50=>-1, 51=>1, 52=>-1, 53=>1, 54=>-1, 55=>1, 56=>-1, 57=>1, 97=>-1, 98=>1, 99=>-1, 100=>1, 101=>-1, 102=>1);
	if ($spielerStaerke == $spielerTalent) { return $spielerTalent; }
	$scout_hash = md5($cookie_team.$cookie_scout.$spielerID);
	$scout_hash_zahl = ord($scout_hash);
	if ($possibleMD5chr[$scout_hash_zahl] == 1) {
		$abweichung_max = 0.35-$cookie_scout*0.05;
	}
	else {
		$abweichung_max = -0.35+$cookie_scout*0.05;
	}
	$scout_hash = substr($scout_hash, 16);
	$scout_hash_zahl = ord($scout_hash);
	$zufallszahl_abweichung = $scout_hash_zahl-47;
	if ($zufallszahl_abweichung > 10) {
		$zufallszahl_abweichung = $zufallszahl_abweichung-39;
	}
	// $zufallszahl_abweichung ist jetzt eine Zufallszahl zwischen 1 und 16
	$abweichung_pro_zufallszahl = $abweichung_max/16;
	$abweichung = 1+$zufallszahl_abweichung*$abweichung_pro_zufallszahl;
	$schaetzung_des_scouts = round(($spielerTalent*$abweichung), 1);
	if ($schaetzung_des_scouts > 9.9) { $schaetzung_des_scouts = 9.9; }
	if ($schaetzung_des_scouts < $spielerStaerke) { $schaetzung_des_scouts = $spielerStaerke; }
	return $schaetzung_des_scouts;
}
function abloeseSchaetzen($cookie_team='', $betrag=0, $kaeufer='', $verkaeufer='', $addCirca=TRUE) {
	if ($cookie_team == $kaeufer OR $cookie_team == $verkaeufer) { return number_format($betrag, 0, ',', '.').' €'; }
	$geld_hash = md5($cookie_team.$betrag);
	$geld_hash_zahl = ord($geld_hash);
	if ($geld_hash_zahl > 47 && $geld_hash_zahl < 56) {
		$abweichung_max = 0.2;
	}
	else {
		$abweichung_max = -0.2;
	}
	$geld_hash = substr($geld_hash, 16);
	$geld_hash_zahl = ord($geld_hash);
	$zufallszahl_abweichung = $geld_hash_zahl-47;
	if ($zufallszahl_abweichung > 10) {
		$zufallszahl_abweichung = $zufallszahl_abweichung-39;
	}
	// $zufallszahl_abweichung ist jetzt eine Zufallszahl zwischen 1 und 16
	$abweichung_pro_zufallszahl = $abweichung_max/16;
	$abweichung = 1+$zufallszahl_abweichung*$abweichung_pro_zufallszahl;
	if ($addCirca) {
		$abloeseSchaetzung = 'ca. ';
	}
	else {
		$abloeseSchaetzung = '';
	}
	$abloeseSchaetzung .= number_format(round($betrag*$abweichung), 0, ',', '.').' €';
	return $abloeseSchaetzung;
}
function pokalrunde_wort($runde) {
	switch ($runde) {
		case 1: $wort = _('Vorrunde'); break;
		case 2: $wort = _('Achtelfinale'); break;
		case 3: $wort = _('Viertelfinale'); break;
		case 4: $wort = _('Halbfinale'); break;
		case 5: $wort = _('Finale'); break;
		case 6: $wort = _('Sieger'); break;
		default: $wort = '-'; break;
	}
	return $wort;
}
function cuprunde_wort($runde) {
	switch ($runde) {
		case 1: $wort = _('Qualifikation'); break;
		case 2: $wort = _('Vorrunde'); break;
		case 3: $wort = _('Achtelfinale'); break;
		case 4: $wort = _('Viertelfinale'); break;
		case 5: $wort = _('Halbfinale'); break;
		case 6: $wort = _('Finale'); break;
		default: $wort = '-'; break;
	}
	return $wort;
}
function validUsername($username) {
	return preg_match('/^[0-9a-zöäüß-]{3,30}$/i', $username);
}
function maskIPs($text) {
	return preg_replace('/([0-9]{1,3}\.[0-9]{1,3})\.[0-9]{1,3}\.[0-9]{1,3}/i', '\1.XXX.XXX', $text);
}
function showInfoBox($meldungListe) {
	$output = '';
	if (is_array($meldungListe)) {
		$anzMeldungen = count($meldungListe);
		if ($anzMeldungen > 0) {
			$counter = 0;
			foreach ($meldungListe as $meldungEntry) {
				$ID = intval(99-$counter);
				if ($ID > 0) {
					$output .= '<div id="showInfoBox'.$ID.'" class="showInfoBox" style="z-index:'.$ID.'"><span>'.trim($meldungEntry).'</span>';
					if ($anzMeldungen > 1) {
						$output .= '<a class="closeLink" href="#" onclick="document.getElementById(\'showInfoBox'.$ID.'\').style.display=\'none\'; return false;">'.intval($counter+1).'/'.$anzMeldungen.' [X]</a>';
					}
					$output .= '</div>';
					$counter++;
				}
			}
		}
	}
	return $output;
}
function id2secure($old_number) {
	$alphabet = '23456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
	// no 0, 1, a, e, i, o, u in alphabet to avoid offensive words (which need vowels)
	$new_number = '';
	while ($old_number > 0) {
		$rest = $old_number%33;
		if ($rest >= 33) { return FALSE; }
		$new_number .= $alphabet[$rest];
		$old_number = floor($old_number/33);
	}
	$new_number = strrev($new_number);
	return $new_number;
}
function secure2id($new_number) {
	$alphabet = '23456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
	// no 0, 1, a, e, i, o, u in alphabet to avoid offensive words (which need vowels)
	$old_number = 0;
	$new_number = strrev($new_number);
	$len = strlen($new_number);
	$n = 0;
	$base = 1;
	while($n < $len) {
		$c = $new_number[$n];
		$index = strpos($alphabet, $c);
		if ($index === FALSE) { return FALSE; }
		$old_number += $base*$index;
		$base *= 33;
		$n++;
	}
	return $old_number;
}
function eloChange($ergebnisStr, $eloHeim, $eloGast, $turnierTyp) {
	switch ($turnierTyp) {
		case 'Liga': $turnierGewichtung = 1; break;
		case 'Pokal': $turnierGewichtung = 2; break;
		case 'Cup': $turnierGewichtung = 1.3; break;
		case 'Test': $turnierGewichtung = 0; break;
		default: exit; break;
	}
	$ergebnisStr = explode(':', $ergebnisStr, 2);
	$toreHeim = $ergebnisStr[0];
	$toreGast = $ergebnisStr[1];
	if ($toreHeim == $toreGast) {
		$ergebnis = 0.5;
	}
	elseif ($toreHeim > $toreGast) {
		$ergebnis = 1;
	}
	else {
		$ergebnis = 0;
	}
	if ($eloHeim > $eloGast) {
		$eloPunkteAbstand = -abs($eloHeim+100-$eloGast); // +100 Heimvorteil
	}
	else {
		$eloPunkteAbstand = abs($eloHeim+100-$eloGast); // +100 Heimvorteil
	}
	$erwartung = 1/(pow(10, ($eloPunkteAbstand/400))+1);
	$tordifferenz = abs($toreHeim-$toreGast);
	switch ($tordifferenz) {
		case 0: $torFaktor = 1; break;
		case 1: $torFaktor = 1; break;
		case 2: $torFaktor = 1.5; break;
		default: $torFaktor = (11+$tordifferenz)/8; break;
	}
	$change = 40*$turnierGewichtung*$torFaktor*($ergebnis-$erwartung);
	return $change;
}
function anonymizeText($text) {
	return mb_strtolower($text, 'UTF-8');
}
function getUserIP() {
	if (isset($_SERVER['REMOTE_ADDR'])) {
		return md5($_SERVER['REMOTE_ADDR']);
	}
	else {
		return 'd41d8cd98f00b204e9800998ecf8427e';
	}
}
function hideTeamCode($text, $userStatus='Benutzer') {
	if ($userStatus == 'Helfer' OR $userStatus == 'Admin') {
		return $text;
	}
	else {
		return preg_replace('/Nachricht vom Team \/ ([A-Z0-9]{5})/i', 'Nachricht vom Team', $text);
	}
}
function cleanCSSclass($text) {
	return preg_replace('/[^a-z0-9]/i', '_', $text);
}

class GameTime {

    const MATCH_DAYS_PER_SEASON = 22;

    private static $season;
    private static $matchDay;

    public static function init() {
        $installDate = strtotime(CONFIG_INSTALL_DATE.' 12:00:00');
        // if it's summer time (DST) right now
        if (date('I') != 1) { $installDate += 3600; }
        $daysPassed = round((getTimestamp() - $installDate) / 86400);
        self::$season = floor($daysPassed / self::MATCH_DAYS_PER_SEASON);
        self::$matchDay = $daysPassed - self::$season * self::MATCH_DAYS_PER_SEASON + 1;
    }

    public static function getSeason() {
        return self::$season;
    }

    public static function getMatchDay() {
        return self::$matchDay;
    }

}
GameTime::init();

?>
