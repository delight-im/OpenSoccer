<?php
if (!isset($_GET['mode'])) { include 'zzserver.php'; }
$was_wird_getan = mt_rand(1, 2);
if ($was_wird_getan == 1) {
	$sql1 = "DELETE FROM ".$prefix."users_multis WHERE user1 NOT IN (SELECT ids FROM ".$prefix."users)";
	$sql2 = mysql_query($sql1);
}
else {
	// MULTIS FINDEN ANFANG
	$sql1 = "SELECT ids, last_ip, last_uniqueHash, team FROM ".$prefix."users WHERE last_login > ".getTimestamp('-24 hours');
	$sql2 = mysql_query($sql1);
	$multis = array();
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		if (strlen($sql3['team']) != 32) { continue; } // aktuell kein Team
		// IP-LOGGING ANFANG
		if ($sql3['last_ip'] != 'd41d8cd98f00b204e9800998ecf8427e' && strlen($sql3['last_ip']) > 6) {
			if (isset($multis[$sql3['last_ip']])) {
				$multis[$sql3['last_ip']][] = $sql3['ids'];
			}
			else {
				$multis[$sql3['last_ip']] = array($sql3['ids']);
			}
		}
		// IP-LOGGING ENDE
		// UNIQUE-HASH-LOGGING ANFANG
		if (strlen($sql3['last_uniqueHash']) > 6) {
			$uniqueHashIndex = 'UNIQUE_'.$sql3['last_uniqueHash'];
			if (isset($multis[$uniqueHashIndex])) {
				$multis[$uniqueHashIndex][] = $sql3['ids'];
			}
			else {
				$multis[$uniqueHashIndex] = array($sql3['ids']);
			}
		}
		// UNIQUE-HASH-LOGGING ENDE
	}
	// MULTIS FINDEN ENDE
	// MULTIS EINTRAGEN ANFANG
	foreach ($multis as $found_ip=>$multi) {
		if (count($multi) == 1) { continue; }
		for ($i = 0; $i < count($multi); $i++) {
			for ($k = $i+1; $k < count($multi); $k++) {
				if (in_array($multi[$i], unserialize(CONFIG_PROTECTED_USERS)) || in_array($multi[$k], unserialize(CONFIG_PROTECTED_USERS))) { continue; } // Admin nicht als Multi identifizieren
				$in1 = "INSERT IGNORE INTO ".$prefix."users_multis (user1, user2, found_ip, found_time) VALUES ('".$multi[$i]."', '".$multi[$k]."', '".$found_ip."', ".time()."),('".$multi[$k]."', '".$multi[$i]."', '".$found_ip."', ".time().")";
				$in2 = mysql_query($in1);
			}
		}
	}
	// MULTIS EINTRAGEN ENDE
}
?>