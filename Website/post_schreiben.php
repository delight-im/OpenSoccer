<?php include 'zz1.php'; ?>
<title><?php echo _('Post schreiben'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>

<?php
if (isset($_POST['titel']) && isset($_POST['inhalt']) && isset($_POST['an']) && isset($_POST['in_reply_to']) && isset($_POST['secHash']) && $cookie_id != CONFIG_DEMO_USER) {
	echo '<h1>'._('Post geschrieben').'</h1><p>';
	if ($loggedin == 0) {
		echo '<p>'._('Du musst angemeldet sein, um diese Seite aufrufen zu können!').'</p>';
		if (strlen($_POST['inhalt']) > 0) {
			echo '<p><strong>'._('Dies ist der Text, den Du schreiben wolltest:').'</strong></p>';
			echo '<p>'.trim(nl2br(strip_tags($_POST['inhalt']))).'</p>';
		}
	}
	else if (trim($_POST['an']) == CONFIG_OFFICIAL_USER) { // message to official user
		echo '<p>'._('Du kannst diesem Manager nicht direkt eine Nachricht schicken!').'</p>';
		if (strlen($_POST['inhalt']) > 0) {
			echo '<p><strong>'._('Dies ist der Text, den Du schreiben wolltest:').'</strong></p>';
			echo '<p>'.trim(nl2br(strip_tags($_POST['inhalt']))).'</p>';
		}
	}
	else {
		$secHash = md5('29'.$cookie_id.$_POST['an'].'1992');
		if (strlen($_POST['titel']) > 0 && strlen($_POST['inhalt']) > 0 && $secHash == $_POST['secHash']) {
			$in_reply_to = mysql_real_escape_string(trim(strip_tags($_POST['in_reply_to'])));
			$titel = mysql_real_escape_string(trim(strip_tags($_POST['titel'])));
			$inhalt = mysql_real_escape_string(trim(nl2br(strip_tags($_POST['inhalt']))));
			$von = mysql_real_escape_string(trim(strip_tags($cookie_id)));
			$an = mysql_real_escape_string(trim(strip_tags($_POST['an'])));
			if (strlen($titel) < 3) { $titel = '... '.$titel; }
			$sql1 = "INSERT INTO ".$prefix."pn (von, an, titel, inhalt, zeit, in_reply_to) VALUES ('".$von."', '".$an."', '".$titel."', '".$inhalt."', '".time()."', '".$in_reply_to."')";
			$sql2 = mysql_query($sql1);
			$uup1 = "UPDATE ".$prefix."pn SET ids = MD5(id) WHERE ids = ''";
			$uup2 = mysql_query($uup1);
			echo _('Die Post wurde erfolgreich versendet. Eine Kopie wurde in Deinem Postausgang gespeichert.');
			if ($loggedin == 1) {
				if (isset($_SESSION['status'])) {
					if ($_SESSION['status'] == 'Admin' || $_SESSION['status'] == 'Helfer') {
						$welcomedByStaff1 = "UPDATE ".$prefix."users SET welcomedByStaff = 1 WHERE ids = '".$an."'";
						$welcomedByStaff2 = mysql_query($welcomedByStaff1);
					}
					elseif ($_SESSION['status'] != 'Bigpoint') {
						echo '</p><div style="width:250px; height:250px; margin:25px auto; padding:0;">';
						?>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-5616874035428509";
/* Ballmanager - IM */
google_ad_slot = "9687398140";
google_ad_width = 250;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
						<?php
						echo '</div><p>&nbsp;';
					}
				}
			}
		}
		else {
			echo _('Deine Post konnte leider nicht versendet werden. Du musst alle Felder ausfüllen. Bitte versuche es noch einmal.');
		}
	}
	echo '</p>';
	include 'zz3.php';
	exit;
}
elseif (isset($_GET['id'])) {
	echo '<h1>'._('Post schreiben').'</h1>';
	$an = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
	if ($loggedin == 1 AND $an != $cookie_id) {
		$em1 = "SELECT username, status FROM ".$prefix."users WHERE ids = '".$an."'";
		$em2 = mysql_query($em1);
		$em3 = mysql_fetch_assoc($em2);
		// CHAT-SPERREN ANFANG
		if ($em3['status'] == 'Helfer' || $em3['status'] == 'Admin') { // Gesperrte User duerfen dem Team trotzdem schreiben
			$sql1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
			$sql2 = mysql_query($sql1);
			if (mysql_num_rows($sql2) > 0) {
				$sql3 = mysql_fetch_assoc($sql2);
				$chatSperreBis = $sql3['MAX(chatSperre)'];
				if ($chatSperreBis > 0 && $chatSperreBis > time()) {
					addInfoBox('Du bist noch bis zum '.date('d.m.Y H:i', $chatSperreBis).' Uhr für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das <a class="inText" href="/wio.php">Support-Team.</a>');
					include 'zz3.php';
					exit;
				}
			}
		}
		// CHAT-SPERREN ENDE
		if ($an == CONFIG_OFFICIAL_USER) {
			echo '<p style="text-align:right"><a href="/manager.php?id='.$an.'" class="pagenava">'._('Zurück zum Profil').'</a></p>';
			echo '<p>'._('Du kannst diesem Manager nicht direkt eine Nachricht schicken! Bitte wähle ein Mitglied des Support-Teams aus, dem du schreiben möchtest.').'</p>';
		}
		else {
			echo '<p style="text-align:right"><a href="/manager.php?id='.$an.'" class="pagenava">'._('Zurück zum Profil').'</a></p>';
			if (isset($_GET['betreff'])) {
				$betreff = trim(htmlentities(utf8_decode($_GET['betreff'])));
				$betreff_sperre = ' readonly="readonly"';
			}
			else {
				$betreff = '';
				$betreff_sperre = '';
			}
			if (isset($_GET['in_reply_to'])) {
				$in_reply_to = mysql_real_escape_string(trim(strip_tags($_GET['in_reply_to'])));
			}
			else {
				$in_reply_to = '';
			}
			$absenderID = $cookie_id;
			$in_reply_toStr = '';
			if ($in_reply_to != '') {
				$ur1 = "SELECT inhalt FROM ".$prefix."pn WHERE ids = '".$in_reply_to."'";
				$ur2 = mysql_query($ur1);
				if (mysql_num_rows($ur2) != 0) {
					$ur3 = mysql_fetch_assoc($ur2);
					$in_reply_toStr .= '<h1>'._('Ursprüngliche Nachricht').'</h1>';
					$in_reply_toStr .= '<p>'.$ur3['inhalt'].'</p>';
				}
			}
			echo '<form method="post" action="/post_schreiben.php" accept-charset="utf-8">';
			if (strpos($betreff, 'RE: [Teampost]') === 0 && ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin')) {
				echo '<p>'._('Absender:').'<br /><input type="text" size="50" name="absender_r" value="'.CONFIG_SITE_NAME.'" readonly="readonly" /></p>';
			}
			echo '<p>'._('Empfänger:').'<br /><input type="text" size="50" name="empfaenger_r" value="'.$em3['username'].'" readonly="readonly" /></p>';
			echo '<p>'._('Betreff:').'<br /><input type="text" size="50" name="titel" value="'.$betreff.'"'.$betreff_sperre.' /></p>';
			echo '<p>'._('Inhalt:').'<br /><textarea rows="10" cols="50" name="inhalt"></textarea></p>';
			echo '<p><input type="hidden" name="secHash" value="'.md5('29'.$cookie_id.$an.'1992').'" /><input type="hidden" name="an" value="'.$an.'" /><input type="hidden" name="in_reply_to" value="'.$in_reply_to.'" /><input type="submit" value="'._('Senden').'"'.noDemoClick($cookie_id).' /></p>';
			echo '</form>';
			echo $in_reply_toStr;
		}
	}
	else {
		echo '<p>'._('Du musst angemeldet sein, um diese Seite aufrufen zu können!').'</p>';
	}
}
?>

<?php include 'zz3.php'; ?>
