<?php
exit;
include 'zzserver.php';
session_start();
if (!isset($_GET['authHash'])) { exit; } // error handling
$authHash = trim(strip_tags($_GET['authHash']));
$secretKey = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'; // DEV/LIVE
$projectID = '111'; // DEV/LIVE
$partnerID = '222';
if (!isset($_POST)) { exit; } // error handling
$xml = file_get_contents('php://input');
$sollHash = strtolower(md5($xml.$secretKey));
if ($authHash == $sollHash) { // wenn uebergebener Hash korrekt ist
	if ($antwort = xmlrpc_decode($xml)) {
		$temp = simplexml_load_string($xml);
		$_SESSION['bigpoint'] = TRUE;
		$muss_wieder_registriert_werden = FALSE;
        if ($temp->methodName == 'game.getUserStats') {
        	$sql1 = "SELECT b.elo FROM ".$prefix."users AS a JOIN ".$prefix."teams AS b ON a.team = b.ids WHERE a.ids = '".$antwort[0]['userID']."'";
        	$sql2 = mysql_query($sql1);
        	if (mysql_num_rows($sql2) == 0) {
        		$u_elo = 0;
        	}
        	else {
        		$sql3 = mysql_fetch_assoc($sql2);
        		$u_elo = $sql3['elo'];
        	}
            $sql4 = "SELECT COUNT(*) FROM ".$prefix."teams WHERE elo > ".$u_elo;
            $sql5 = mysql_query($sql4);
            $sql6 = mysql_result($sql5, 0);
            $u_rank = $sql6+1;
            $responseParams = array (
                'result' => 'OK',
                'userRank' => $u_rank,
                'virtualCurrency' => 0,
                'realCurrency' => 0
            );
            echo xmlrpc_encode_request(NULL, $responseParams);
        }
        elseif ($temp->methodName == 'game.login') {
            $getCountA1 = "SELECT COUNT(*) FROM ".$prefix."users WHERE id = ".$antwort[0]['userID'];
            $getCountA2 = mysql_query($getCountA1);
            $getCountA3 = mysql_result($getCountA2, 0);
            if ($getCountA3 == 0) { // kein Account
				$muss_wieder_registriert_werden = TRUE; // ruft nachher registerAndLogin auf
            }
            else {
				$u_email = 'BP_'.$antwort[0]['bpUserID'];
                $loginURL = 'http://www.ballmanager.de/login.php?bp='.$antwort[0]['userID'].'&auth='.md5('29'.$u_email.'1992');
                $_SESSION['bp_id'] = $antwort[0]['bpUserID'];
                $responseParams = array (
                    'result' => 'OK',
                    'redirectURL' => $loginURL
                );
				echo xmlrpc_encode_request(NULL, $responseParams);
            }
        }
        if ($temp->methodName == 'game.registerAndLogin' OR $muss_wieder_registriert_werden == TRUE) {
        	$u_email = 'BP_'.$antwort[0]['bpUserID'];
        	$schon_drin1 = "SELECT COUNT(*) FROM ".$prefix."users WHERE email = '".$u_email."'";
        	$schon_drin2 = mysql_query($schon_drin1);
        	$schon_drin3 = mysql_result($schon_drin2, 0);
            $u_ids = md5($u_email.time());
        	if ($schon_drin3 == 0) { // nur wenn noch nicht in man_users drin
                $u_password = mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
                $u_password_db = md5('1'.$u_password.'29');
                $sql1 = "INSERT INTO ".$prefix."users (email, username, password, regdate, last_login, last_ip, ids, status, liga, team) VALUES ('".$u_email."', '".$u_email."', '".$u_password_db."', '".$antwort[0]['authTimestamp']."', '".getTimestamp('-14 days', $antwort[0]['authTimestamp'])."', '".$antwort[0]['userIP']."', '".$u_ids."', 'Bigpoint', '', '__".$u_ids."')";
                $sql1 = mysql_query($sql1);
				$automatisch_generierte_id = mysql_insert_id();
				$loginURL = 'http://www.ballmanager.de/login.php?bp='.$automatisch_generierte_id.'&auth='.md5('29'.$u_email.'1992');
				$responseParams = array (
					'result' => 'OK',
					'userID' => $automatisch_generierte_id,
					'redirectURL' => $loginURL
				);
				echo xmlrpc_encode_request(NULL, $responseParams);
            }
			else {
                $responseParams = array (
                    'faultCode' => -1,
                    'faultString' => 'REG_ERROR'
                );
				echo xmlrpc_encode_request(NULL, $responseParams);
			}
        }
    }
    else {
		$phpfehler1 = "INSERT INTO ".$prefix."php_fehler (datei) VALUES ('API Error - Stelle 1')";
		$phpfehler2 = mysql_query($phpfehler1);
    	exit; // error handling (xml parsing problem)
    }
}
else {
	$phpfehler1 = "INSERT INTO ".$prefix."php_fehler (datei) VALUES ('API Error - Stelle 2')";
	$phpfehler2 = mysql_query($phpfehler1);
    exit; // error handling (wrong authHash)
}
?>