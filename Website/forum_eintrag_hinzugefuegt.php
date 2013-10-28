<?php exit; ?>
<?php include 'zzserver.php'; ?>
<?php include 'zzcookie.php'; ?>
<?php
$adresse = 'Location: /forum.php';
if (isset($_POST['inhalt'])) {
	$inhalt = mysql_real_escape_string(nl2br($_POST['inhalt']));
	if (strlen($inhalt) > 1) {
        if (isset($_POST['titel'])) { // neues Thema
            $titel = mysql_real_escape_string(trim(strip_tags($_POST['titel'])));
            if (strlen($titel) > 1) {
                $sichtbar_fuer = 'Alle';
            	if (isset($_POST['sichtbar_fuer']) && isset($_SESSION['status'])) {
            		if ($_POST['sichtbar_fuer'] == 'Helfer' && ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin')) {
            			$sichtbar_fuer = 'Helfer';
            		}
            	}
				$kategorieEntry = 'Sonstiges';
				if (isset($_POST['kategorie'])) {
					$kategorie = mysql_real_escape_string(trim(strip_tags($_POST['kategorie'])));
					if (in_array($kategorie, $kategorienListe)) {
						$kategorieEntry = $kategorie;
					}
				}
                $sql1 = "INSERT INTO ".$prefix."forum_themen (datum, manager, titel, lastposter, sichtbar_fuer, kategorie) VALUES ('".time()."', '".$cookie_id."', '".$titel."', '".mysql_real_escape_string($cookie_username)."', '".$sichtbar_fuer."', '".$kategorieEntry."')";
                $sql2 = mysql_query($sql1);
                $sql2a = mysql_insert_id();
                $sql2b = md5($sql2a);
                $get1 = "UPDATE ".$prefix."forum_themen SET ids = MD5(id) WHERE ids = ''";
                $get2 = mysql_query($get1);
                $sql3 = "INSERT INTO ".$prefix."forum_beitraege (thema, manager, postIP, datum, inhalt) VALUES ('".$sql2b."', '".$cookie_id."', '".getUserIP()."', ".time().", '".$inhalt."')";
                $sql4 = mysql_query($sql3);
                $adresse = 'Location: /forum_thema.php?id='.$sql2b;
            }
        }
        elseif (isset($_POST['thema'])) { // Beitrag zu vorhandenem Thema
            $thema = mysql_real_escape_string(trim(strip_tags($_POST['thema'])));
			if (isset($_POST['beitragAltID'])) { // Beitrag aendern
				if (isset($_POST['kategorie'])) {
					$kategorie = mysql_real_escape_string(trim(strip_tags($_POST['kategorie'])));
					if (in_array($kategorie, $kategorienListe)) {
						$sql3 = "UPDATE ".$prefix."forum_themen SET kategorie = '".$kategorie."' WHERE ids = '".$thema."'";
						$sql4 = mysql_query($sql3);
					}
				}
				$beitragAltID = bigintval($_POST['beitragAltID']);
				$addSql = " AND manager = '".$cookie_id."'";
				$inhaltSignature = '<br /><i>Dieser Beitrag wurde von '.mysql_real_escape_string($cookie_username).' geändert am '.date('d.m.Y H:i').' Uhr.</i>';
				$inhalt .= $inhaltSignature;
				$sql1 = "UPDATE ".$prefix."forum_beitraege SET inhalt = '".$inhalt."' WHERE id = ".$beitragAltID.$addSql;
				$sql2 = mysql_query($sql1);
				$adresse = 'Location: /forum.php?msg=edited';
			}
			else { // neuer Beitrag
				$quoteText = '';
				if (isset($_POST['quote'])) {
					$quoteID = bigintval($_POST['quote']);
					$quote1 = "SELECT manager, datum, inhalt FROM ".$prefix."forum_beitraege WHERE id = ".$quoteID;
					$quote2 = mysql_query($quote1);
					if (mysql_num_rows($quote2) == 1) {
						$quote3 = mysql_fetch_assoc($quote2);
						$quotedUser1 = "SELECT username FROM ".$prefix."users WHERE ids = '".$quote3['manager']."'";
						$quotedUser2 = mysql_query($quotedUser1);
						if (mysql_num_rows($quotedUser2) == 0) {
							$quotedUserName = 'Unbekannt';
						}
						else {
							$quotedUser3 = mysql_fetch_assoc($quotedUser2);
							$quotedUserName = $quotedUser3['username'];
						}
						$quoteText = '<p><strong>'.displayUsername($quotedUserName, $quote3['manager']).' ('.date('d.m.Y H:i', $quote3['datum']).')</strong><br />'.$quote3['inhalt'].'</p>';
						$quoteText = mysql_real_escape_string($quoteText);
					}
				}
				$postAutor = $cookie_id;
				$postAutorName = mysql_real_escape_string($cookie_username);
				$sql1 = "INSERT INTO ".$prefix."forum_beitraege (thema, manager, postIP, datum, inhalt, quote) VALUES ('".$thema."', '".$postAutor."', '".getUserIP()."', ".time().", '".$inhalt."', '".$quoteText."')";
				$sql2 = mysql_query($sql1);
				$sql3 = "UPDATE ".$prefix."forum_themen SET datum = ".time().", lastposter = '".$postAutorName."', postCount = postCount+1 WHERE ids = '".$thema."'";
				$sql4 = mysql_query($sql3);
				$sql5 = "DELETE FROM ".$prefix."forum_gelesen WHERE thema = '".$thema."' AND user != '".$cookie_id."'";
				$sql6 = mysql_query($sql5);
				$adresse = 'Location: /forum.php?msg=replied';
			}
        }
	}
}
header($adresse);
?>