<?php include 'zzserver.php'; ?>
<?php include 'zzcookie.php'; ?>
<?php
$adresse = 'Location: /freunde.php';
if (isset($_GET['id']) && isset($_GET['aktion']) && $loggedin == 1 && $cookie_id != CONFIG_DEMO_USER) {
	$id = mysql_real_escape_string($_GET['id']);
	if ($id != CONFIG_DEMO_USER) {
		$aktion = mysql_real_escape_string($_GET['aktion']);
		if ($aktion == 'einladen' && $id != $cookie_id) {
			$adresse = 'Location: /manager.php?id='.$id;
			$freund1 = "SELECT COUNT(*) FROM ".$prefix."freunde_anfragen WHERE (von = '".$cookie_id."' AND an = '".$id."') OR (von = '".$id."' AND an = '".$cookie_id."')";
			$freund2 = mysql_query($freund1);
			$freund3 = mysql_result($freund2, 0);
			if ($freund3 == 0) {
				$freund4 = "INSERT INTO ".$prefix."freunde_anfragen (von, an) VALUES ('".$cookie_id."', '".$id."')";
				$freund5 = mysql_query($freund4);
			}
		}
		elseif ($aktion == 'Annehmen' && $id != $cookie_id) {
			$adresse = 'Location: /freunde.php';
			$freund1 = "INSERT INTO ".$prefix."freunde (f1, f2, typ) VALUES ('".$cookie_id."', '".$id."', 'F') ON DUPLICATE KEY UPDATE typ = 'F'";
			$freund2 = mysql_query($freund1);
			$freund3 = "INSERT INTO ".$prefix."freunde (f1, f2, typ) VALUES ('".$id."', '".$cookie_id."', 'F') ON DUPLICATE KEY UPDATE typ = 'F'";
			$freund4 = mysql_query($freund3);
			$freund5 = "DELETE FROM ".$prefix."freunde_anfragen WHERE von = '".$id."' AND an = '".$cookie_id."'";
			$freund6 = mysql_query($freund5);
			$_SESSION['last_freunde_anzahl']--;
		}
		elseif ($aktion == 'beenden' && $id != $cookie_id) {
			$adresse = 'Location: /freunde.php';
			$freund1 = "DELETE FROM ".$prefix."freunde WHERE ((f1 = '".$cookie_id."' AND f2 = '".$id."') OR (f1 = '".$id."' AND f2 = '".$cookie_id."')) AND typ = 'F'";
			$freund2 = mysql_query($freund1);
		}
		elseif ($aktion == 'Ablehnen' && $id != $cookie_id) {
			$adresse = 'Location: /freunde.php';
			$freund1 = "DELETE FROM ".$prefix."freunde_anfragen WHERE von = '".$id."' AND an = '".$cookie_id."'";
			$freund2 = mysql_query($freund1);
			$_SESSION['last_freunde_anzahl']--;
		}
		elseif ($aktion == 'Ignorieren' && $id != $cookie_id) {
			$adresse = 'Location: /freunde.php';
			$freund1 = "INSERT INTO ".$prefix."freunde (f1, f2, typ) VALUES ('".$cookie_id."', '".$id."', 'B') ON DUPLICATE KEY UPDATE typ = 'B'";
			$freund2 = mysql_query($freund1);
			$freund1 = "DELETE FROM ".$prefix."freunde WHERE f1 = '".$id."' AND f2 = '".$cookie_id."'";
			$freund2 = mysql_query($freund1);
		}
		elseif ($aktion == 'StopBlock' && $id != $cookie_id) {
			$adresse = 'Location: /freunde.php';
			$freund1 = "DELETE FROM ".$prefix."freunde WHERE f1 = '".$cookie_id."' AND f2 = '".$id."' AND typ = 'B'";
			$freund2 = mysql_query($freund1);
		}
	}
}
header($adresse);
?>