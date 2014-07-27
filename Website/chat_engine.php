<?php include 'zzserver.php'; ?>
<?php include 'zzcookie.php'; ?>

<?php if ($loggedin == 1) { ?>

<?php
function extract_kommentar_ergebnis($kommentar) {
    	$ergebnis_str = '';
    	$found_ergebnis = preg_match('/ \[([0-9]+:[0-9]+)\]/i', $kommentar, $ergebnis_array);
    	if (isset($ergebnis_array[1])) {
    		$ergebnis_str = ' '.$ergebnis_array[1];
    	}
    	$kommentar_str = preg_replace('/ \[[0-9]:[0-9]\]/i', '', $kommentar);
    	return array($kommentar_str, $ergebnis_str);
}
function letzte_nachrichten() {
	global $prefix, $cookie_id, $cookie_liga, $cookie_username, $live_scoring_min_gespielt;
	$ausgabe = '';
	if (isset($_SESSION['ignoList'])) {
		$ignoList = unserialize($_SESSION['ignoList']);
	}
	else {
		$ignoList = array();
	}
	if (mt_rand(1, 8) == 5) {
		$up1 = "UPDATE ".$prefix."users SET last_chat = ".time()." WHERE ids = '".$cookie_id."'";
		mysql_query($up1);
	}
    $sql1 = "SELECT ".$prefix."chatroom.id, ".$prefix."chatroom.user, ".$prefix."chatroom.zeit, ".$prefix."chatroom.nachricht, ".$prefix."users.username FROM ".$prefix."chatroom JOIN ".$prefix."users ON ".$prefix."chatroom.user = ".$prefix."users.ids ORDER BY ".$prefix."chatroom.id DESC LIMIT 0, 20";
    $sql2 = mysql_query($sql1);
    while ($sql3 = mysql_fetch_assoc($sql2)) {
		if (in_array($sql3['user'], $ignoList)) { continue; }
        $ausgabe .= '<p';
		if ($sql3['user'] == CONFIG_OFFICIAL_USER OR stripos($sql3['nachricht'], $cookie_username) !== FALSE) {
			$ausgabe .= ' style="background-color:#ddd"';
		}
		$ausgabe .= '>';
        $ausgabe .= '<strong><a href="#" onclick="talkTo(\''.addslashes($sql3['username']).'\'); return false">'.$sql3['username'].'</a> ('.date('H:i', $sql3['zeit']).'):</strong> ';
        $ausgabe .= autoLink($sql3['nachricht']);
        $ausgabe .= '</p>';
    }
    echo $ausgabe;
}
function nachricht_erzeugen($user, $nachricht) {
	global $prefix;
    $user = mysql_real_escape_string(trim(strip_tags($user)));
	$sperrRequests = 0;
	$sanktion1 = "SELECT MAX(chatSperre) AS sperrEnde FROM ".$prefix."helferLog WHERE managerBestrafen = '".$user."'";
	$sanktion2 = mysql_query($sanktion1);
	if (mysql_num_rows($sanktion2) > 0) {
		$sanktion3 = mysql_fetch_assoc($sanktion2);
		if ($sanktion3['sperrEnde'] > 0 && $sanktion3['sperrEnde'] > time()) {
			$sperrRequests += 3; // zählt genauso wie 3 Chat-Reports von Usern => Sperre
		}
	}
	$reportDate = date('Y-m-d');
	$sql1 = "SELECT COUNT(*) FROM ".$prefix."chatroomReportedUsers WHERE user = '".$user."' AND datum = '".$reportDate."' AND sperrRelevant = 1";
	$sql2 = mysql_query($sql1);
	$sperrRequests += mysql_result($sql2, 0);
	if ($sperrRequests < 3) {
		$nachricht = trim($nachricht);
		if (substr($nachricht, 0, 7) == 'REPORT ') {
			$userToReport = mysql_real_escape_string(substr($nachricht, 7));
			$getReportedUserID1 = "SELECT ids FROM ".$prefix."users WHERE username = '".$userToReport."'";
			$getReportedUserID2 = mysql_query($getReportedUserID1);
			if (mysql_num_rows($getReportedUserID2) == 1) {
				$getReportedUserID3 = mysql_fetch_assoc($getReportedUserID2);
				$getReportedUserID4 = mysql_real_escape_string(trim(strip_tags($getReportedUserID3['ids'])));
				if ($getReportedUserID4 != CONFIG_OFFICIAL_USER && $user != CONFIG_DEMO_USER) {
					// CHAT-PROTOKOLL ANFERTIGEN ANFANG
					$sql1 = "SELECT a.user, a.zeit, a.nachricht, b.username, b.last_ip FROM ".$prefix."chatroom AS a JOIN ".$prefix."users AS b ON a.user = b.ids ORDER BY a.id DESC LIMIT 0, 100";
					$sql2 = mysql_query($sql1);
					$reportText = '';
					while ($sql3 = mysql_fetch_assoc($sql2)) {
						$reportText .= '<p>'.displayUsername($sql3['username'], $sql3['user']).' ('.date('d.m.Y H:i', $sql3['zeit']).' / '.$sql3['last_ip'].'): '.$sql3['nachricht'].'</p>';
					}
					// CHAT-PROTOKOLL ANFERTIGEN ENDE
					if ($_SESSION['pMaxGebot'] > 1) {
						$sperrRelevant = 1;
					}
					else {
						$sperrRelevant = 0;
					}
					$sql1 = "INSERT INTO ".$prefix."chatroomReportedUsers (user, reporter, datum, protokoll, sperrRelevant) VALUES ('".$getReportedUserID4."', '".$user."', '".$reportDate."', '".mysql_real_escape_string($reportText)."', ".$sperrRelevant.")";
					mysql_query($sql1);
					if (mysql_affected_rows() > 0) { // groesser 0 damit -1 als Fehler ignoriert wird
						$sql1 = "SELECT COUNT(*) FROM ".$prefix."chatroomReportedUsers WHERE user = '".$getReportedUserID4."' AND datum = '".$reportDate."' AND sperrRelevant = 1";
						$sql2 = mysql_query($sql1);
						$sql3 = mysql_result($sql2, 0);
						if ($sql3 < 3) {
							$sql1 = "INSERT INTO ".$prefix."chatroom (user, zeit, nachricht) VALUES ('".CONFIG_OFFICIAL_USER."', '".time()."', '".$userToReport." wurde gemeldet.')";
							mysql_query($sql1);
						}
						else {
							$sql1 = "INSERT INTO ".$prefix."chatroom (user, zeit, nachricht) VALUES ('".CONFIG_OFFICIAL_USER."', '".time()."', '".$userToReport." wurde für den Chat gesperrt.')";
							mysql_query($sql1);
						}
					}
				}
			}
		}
		elseif ($user != CONFIG_DEMO_USER) {
			$nachricht = mysql_real_escape_string(strip_tags($nachricht));
			if (strlen($nachricht) == 0) { exit; }
			// EMOTICONS ANFANG
			$emoticons1 = array(':)', ':D', '=D', ':O', ':o', ':P', ':p', ':(', ';)');
			$emoticons2 = array('emoticon_smile', 'emoticon_grin', 'emoticon_happy', 'emoticon_surprised', 'emoticon_surprised',
								'emoticon_tongue', 'emoticon_tongue', 'emoticon_unhappy', 'emoticon_wink');
			foreach ($emoticons1 as $key=>$value) {
				$ersetzung = '<img src="/images/'.$emoticons2[$key].'.png" alt="'.$value.'" title="'.$value.'" />';
				$nachricht = str_replace($value, $ersetzung, $nachricht);
			}
			// EMOTICONS ENDE
			$sql1 = "INSERT INTO ".$prefix."chatroom (user, zeit, nachricht) VALUES ('".$user."', '".time()."', '".$nachricht."')";
			mysql_query($sql1);
		}
	}
}
?>
<?php
if (isset($_GET['aktion'])) {
    if ($_GET['aktion'] == 'nachricht_erzeugen') {
		if (isset($_GET['nachricht'])) {
			nachricht_erzeugen($cookie_id, $_GET['nachricht']);
		}
    }
    elseif ($_GET['aktion'] == 'letzte_nachrichten') {
        letzte_nachrichten();
    }
}
?>

<?php } ?>
