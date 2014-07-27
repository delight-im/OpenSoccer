<?php
include 'zzserver.php';
session_start();
// BIGPOINT ANFANG
$valid_bigpoint_user = '';
if (isset($_GET['bp']) && isset($_GET['auth'])) {
	$bigpoint_id = intval($_GET['bp']);
	$bigpoint_auth = htmlentities(trim($_GET['auth']));
	$sql1 = "SELECT ids, email FROM ".$prefix."users WHERE id = ".$bigpoint_id;
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) != 0) {
		$sql3 = mysql_fetch_assoc($sql2);
		$hashVergleich = md5('29'.$sql3['email'].'1992');
		if ($bigpoint_auth == $hashVergleich) {
			$valid_bigpoint_user = $sql3['ids'];
		}
	}
}
unset($_SESSION['bp_id']);
// BIGPOINT ENDE
$hadresse = 'Location: /index.php';
if ((isset($_POST['lusername']) && isset($_POST['lpassword'])) OR $valid_bigpoint_user != '') {
    if ($valid_bigpoint_user == '') {
        // WENN DAS LOGIN-FORMULAR AUSGEFÜLLT WURDE
        $loemail = mysql_real_escape_string($_POST['lusername']);
        $lopassword = trim($_POST['lpassword']);
        $lopassword_salted = md5('1'.$lopassword.'29');
        $lologin1 = "SELECT id, ids, email, username, status, liga, team, regdate, last_login, readSticky, multiSperre, acceptedRules, hasLicense FROM ".$prefix."users WHERE (email = '".$loemail."' OR username = '".$loemail."') AND password = '".$lopassword_salted."'";
    }
    else {
        $lologin1 = "SELECT id, ids, email, username, status, liga, team, regdate, last_login, readSticky, multiSperre, acceptedRules, hasLicense FROM ".$prefix."users WHERE ids = '".$valid_bigpoint_user."'";
    }
    $lologin2 = mysql_query($lologin1);
    $lologin3 = mysql_num_rows($lologin2);
    if ($lologin3 == 1) {
        $lologin4 = mysql_fetch_assoc($lologin2);
		if (substr($lologin4['username'], 0, 9) == 'GELOESCHT') {
			$_SESSION['loggedin'] = 0;
			$hadresse = 'Location: /geloeschterAccount.php';
		}
		else {
			if ($lologin4['team'] == '__'.$lologin4['ids']) {
				$_SESSION['multiAccountList'] = '';
				$_SESSION['liga'] = '';
				$_SESSION['teamname'] = '';
				$_SESSION['scout'] = 1;
				$_SESSION['multiSperre'] = 0;
			}
			else {
				$teamname1 = "SELECT name, scout FROM ".$prefix."teams WHERE ids = '".$lologin4['team']."'";
				$teamname2 = mysql_query($teamname1);
				$teamname3 = mysql_fetch_assoc($teamname2);
				// WERT last_managed AUF 0 SETZEN ANFANG
				$tu1 = "UPDATE ".$prefix."teams SET last_managed = 0 WHERE ids = '".$lologin4['team']."'";
				$tu2 = mysql_query($tu1);
				// WERT last_managed AUF 0 SETZEN ENDE
				// MULTIS TEAMBEZOGEN LADEN ANFANG
				$ma1 = "SELECT a.user2, b.team FROM ".$prefix."users_multis AS a JOIN ".$prefix."users AS b ON a.user2 = b.ids WHERE a.user1 = '".$lologin4['ids']."'";
				$ma2 = mysql_query($ma1);
				$aktiveMultiAccounts = 0;
				$multiAccountList = array();
				while ($ma3 = mysql_fetch_assoc($ma2)) {
					if (strlen($ma3['team']) == 32) { $aktiveMultiAccounts++; }
					$multiAccountList[] = $ma3['team'];
				}
				$_SESSION['multiSperre'] = 0; // entfernen wenn Multi-Sperre wieder aktiviert werden soll
				/*if ($lologin4['multiSperre'] == 1) {
					$_SESSION['multiSperre'] = 1;
				}
				else {
					$_SESSION['multiSperre'] = 0;
					if ($aktiveMultiAccounts >= 5) {
						$multiSperre1 = "UPDATE ".$prefix."users SET multiSperre = 1, last_urlaub_kurz = ".time().", last_urlaub_lang = ".time()." WHERE ids = '".$lologin4['ids']."'";
						$multiSperre2 = mysql_query($multiSperre1);
						$_SESSION['multiSperre'] = 1;
					}
				}*/
				$multiAccountList = implode('-', $multiAccountList);
				// MULTIS TEAMBEZOGEN LADEN ENDE
				$_SESSION['multiAccountList'] = $multiAccountList;
				$_SESSION['liga'] = $lologin4['liga'];
				$_SESSION['teamname'] = $teamname3['name'];
				$_SESSION['scout'] = $teamname3['scout'];
			}
			$_SESSION['loggedin'] = 1;
			$_SESSION['userid'] = $lologin4['ids'];
			$_SESSION['username'] = $lologin4['username'];
			$_SESSION['status'] = $lologin4['status'];
			$_SESSION['team'] = $lologin4['team'];
			$_SESSION['readSticky'] = $lologin4['readSticky'];
			$_SESSION['acceptedRules'] = $lologin4['acceptedRules'];
			$_SESSION['hasLicense'] = $lologin4['hasLicense'];
			$_SESSION['transferGesperrt'] = FALSE;
			$_SESSION['last_forumneu_anzahl'] = 0;
			// LOGIN-LOG ANFANG
			if (isset($_SERVER['HTTP_USER_AGENT'])) { $loginLog_userAgent = mysql_real_escape_string(trim(strip_tags($_SERVER['HTTP_USER_AGENT']))); } else { $loginLog_userAgent = ''; }
			$loginLog_ip = getUserIP();
			if (isset($_COOKIE['uniqueHash'])) { $loginLog_uniqueHash = mysql_real_escape_string(trim(strip_tags($_COOKIE['uniqueHash']))); } else { $loginLog_uniqueHash = $loginLog_ip; setcookie('uniqueHash', $loginLog_ip, getTimestamp('+30 days'), '/', str_replace('www.', '.', CONFIG_SITE_DOMAIN), FALSE, TRUE); }
			if (!in_array($lologin4['ids'], unserialize(CONFIG_PROTECTED_USERS))) {
				$loginLog1 = "INSERT INTO ".$prefix."loginLog (user, zeit, ip, userAgent, uniqueHash) VALUES ('".$lologin4['ids']."', ".time().", '".$loginLog_ip."', '".$loginLog_userAgent."', '".$loginLog_uniqueHash."')";
				$loginLog2 = mysql_query($loginLog1);
			}
			// LOGIN-LOG ENDE
			// MAXIMALGEBOT ANFANG
			$tageHier = (time()-$lologin4['regdate'])/86400;
			if ($tageHier < 0.08) {
				$_SESSION['pMaxGebot'] = 0;
			}
			elseif ($tageHier < 7) {
				$_SESSION['pMaxGebot'] = 1.25;
			}
			elseif ($tageHier < 21) {
				$_SESSION['pMaxGebot'] = 2.5;
			}
			elseif ($tageHier < 42) {
				$_SESSION['pMaxGebot'] = 4;
			}
			elseif ($tageHier < 84) {
				$_SESSION['pMaxGebot'] = 8;
			}
			else {
				$_SESSION['pMaxGebot'] = 16;
			}
			// MAXIMALGEBOT ENDE
			$hadresse = 'Location: /index.php';
			// BIGPOINT NUTZERNAMEN ANFANG
			if (substr($lologin4['username'], 0, 3) == 'BP_') {
				$hadresse = 'Location: /bp_username_waehlen.php';
				$_SESSION['bp_username'] = $lologin4['email'];
			}
			// BIGPOINT NUTZERNAMEN ENDE
			// MANAGER DER SAISON ANFANG
			$_SESSION['mds_abgestimmt'] = TRUE; // vielleicht nicht moeglich
			if (GameTime::getMatchDay() <= 3) {
				$timeout = getTimestamp('-22 days');
				if ($lologin4['regdate'] < $timeout) {
					// WENN WAHLBERECHTIGT ANFANG
					$mds4 = "SELECT COUNT(*) FROM ".$prefix."users_mds WHERE voter = '".$lologin4['ids']."'";
					$mds5 = mysql_query($mds4);
					$mds6 = mysql_result($mds5, 0);
					if ($mds6 == 0) { $_SESSION['mds_abgestimmt'] = FALSE; } // wenn wahlberechtigt und trotzdem keine Stimme gefunden
					// WENN WAHLBERECHTIGT ENDE
				}
			}
			// MANAGER DER SAISON ENDE
			// TRANSFER-SPERREN ANFANG
			$_SESSION['transferGesperrt'] = FALSE;
			if ($lologin4['team'] != '__'.$lologin4['ids']) {
				$sperrQL1 = "SELECT MAX(transferSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$lologin4['ids']."'";
				$sperrQL2 = mysql_query($sperrQL1);
				if (mysql_num_rows($sperrQL2) > 0) {
					$sperrQL3 = mysql_fetch_assoc($sperrQL2);
					$transferSperreBis = $sperrQL3['MAX(transferSperre)'];
					if ($transferSperreBis > time()) {
						$_SESSION['transferGesperrt'] = TRUE;
					}
				}
			}
			// TRANSFER-SPERREN ENDE
			if (isset($_POST['returnURL'])) {
				if (strpos($_POST['returnURL'], 'registrier') === FALSE) {
					$hadresse = 'Location: '.trim(strip_tags($_POST['returnURL']));
				}
			}
		}
    }
}
header($hadresse);
exit;
?>