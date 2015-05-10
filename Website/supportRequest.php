<?php
// AJAX BEGIN
if (isset($_GET['likeComment'])) {
	include 'zzserver.php';
	include 'zzcookie.php';
	if ($cookie_id != CONFIG_DEMO_USER) {
		$likeComment = intval(secure2id(trim($_GET['likeComment'])));
		$getAuthor1 = "SELECT userID FROM ".$prefix."supportComments WHERE id = ".$likeComment;
		$getAuthor2 = mysql_query($getAuthor1);
		if (mysql_num_rows($getAuthor2) == 1) {
			$getAuthor3 = mysql_real_escape_string(mysql_result($getAuthor2, 0));
		}
		else {
			$getAuthor3 = FALSE;
		}
		$sql1 = "INSERT INTO ".$prefix."supportLikes (userID, commentID, zeit) VALUES ('".$cookie_id."', ".$likeComment.", ".time().")";
		$sql2 = mysql_query($sql1);
		if ($sql2 !== FALSE && $getAuthor3 !== FALSE) {
			$sql1 = "UPDATE ".$prefix."supportComments SET likes = likes+1 WHERE id = ".$likeComment;
			$sql2 = mysql_query($sql1);
			$thx1 = "INSERT INTO ".$prefix."supportUsers (userID, thanksReceived) VALUES ('".$getAuthor3."', 1) ON DUPLICATE KEY UPDATE thanksReceived = thanksReceived+1, points = (replies*10+fastReplies*25+thanksReceived*5+votes*1)";
			$thx2 = mysql_query($thx1);
			echo _('Vielen Dank!');
		}
	}
	exit;
}
// AJAX END
?>
<?php if (!isset($_GET['id'])) { exit; } ?>
<?php include 'zz1.php'; ?>
<?php
$requestID = secure2id($_GET['id']);
$sql1 = "SELECT id, open, pro, contra, timeAdded, lastAction, author, category, title, description, visibilityLevel FROM ".$prefix."supportRequests WHERE id = ".$requestID;
$sql2 = mysql_query($sql1);
if ($sql2 == FALSE) { exit; }
if (mysql_num_rows($sql2) != 1) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
$entryNumber = $sql3['category'].' #'.id2secure($sql3['id']).' (';
switch ($sql3['open']) {
	case 1: $entryNumber .= _('offen'); break;
	case 0: $entryNumber .= _('umgesetzt'); break;
	case -1: if ($sql3['category'] == 'Vorschlag') { $entryNumber .= _('abgelehnt'); } else { $entryNumber .= _('geklärt'); } break;
	default: exit; break;
}
$entryNumber .= ')';
?>
<title><?php echo __('Support: %s', $entryNumber); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.commentBoxBlack, .commentBoxRed, .commentBoxOrange {
	box-shadow: 0 2px 2px rgba(0, 0, 0, 0.4);
	-moz-box-shadow: 0 2px 2px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 0 2px 2px rgba(0, 0, 0, 0.4);
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
}
.commentBoxBlack {
	margin: 10px 0 20px 0;
	background-color: #eee;
	border: 5px solid #eee;
}
.commentBoxRed {
	margin: 10px 0 20px 0;
	background-color: #eee;
	border: 5px solid #f00;
}
.commentBoxOrange {
	margin: 10px 0 20px 0;
	background-color: #eee;
	border: 5px solid #ff8c00;
}
-->
</style>
<?php include 'zz2.php'; ?>
<h1><?php echo __('Support: %s', $entryNumber); ?></h1>
<?php if ($loggedin == 1) { ?>
<?php
setTaskDone('read_support');
if (($_SESSION['status'] == 'Admin' || $_SESSION['status'] == 'Helfer') && $sql3['open'] == 1 && $cookie_id != CONFIG_DEMO_USER) {
	if (isset($_GET['setOpen'])) {
		$setOpen = intval($_GET['setOpen']);
		$up1 = "UPDATE ".$prefix."supportRequests SET open = ".$setOpen." WHERE id = ".$requestID;
		$up2 = mysql_query($up1);
		$sql3['open'] = $setOpen;
		// AUTOR DER ANFRAGE PER POST BENACHRICHTIGEN ANFANG
		if ($setOpen != 1) {
            $betreff = __('Support: Anfrage #%s', id2secure($sql3['id']));
            $notifyText = _('Hallo').',<br /><br />'._('vielen Dank für Deine Beteiligung im Support-Forum. Eine Deiner Anfragen wurde jetzt geschlossen, Du findest sie hier:').'<br />http://'.CONFIG_SITE_DOMAIN.'/supportRequest.php?id='.id2secure($sql3['id']).'<br /><br />'._('Sportliche Grüße').'<br />'.CONFIG_SITE_NAME.'<br />'.CONFIG_SITE_DOMAIN;
            $sql1 = "INSERT INTO ".$prefix."pn (von, an, titel, inhalt, zeit, in_reply_to) VALUES ('".CONFIG_OFFICIAL_USER."', '".$sql3['author']."', '".$betreff."', '".$notifyText."', ".time().", '')";
			$sql2 = mysql_query($sql1);
			$sql1 = "UPDATE ".$prefix."pn SET ids = MD5(id) WHERE ids = ''";
			$sql2 = mysql_query($sql1);
		}
		// AUTOR DER ANFRAGE PER POST BENACHRICHTIGEN ENDE
		addInfoBox(_('Der Status der Anfrage wurde geändert.'));
	}
}
if (isset($_GET['delCom']) && $cookie_id != CONFIG_DEMO_USER) {
	$delCom = secure2id($_GET['delCom']);
	$up1 = "UPDATE ".$prefix."supportComments SET deleted = 1 WHERE id = ".$delCom;
	if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Helfer') {
		$up1 .= " AND userID = '".$cookie_id."'";
	}
	$up2 = mysql_query($up1);
}
if ($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer') {
	if ($sql3['visibilityLevel'] == 1) {
		addInfoBox(_('Diese Anfrage ist geschützt und nur für das Support-Team sichtbar.'));
	}
	elseif ($sql3['visibilityLevel'] == 2) {
		addInfoBox(_('Bei dieser Anfrage handelt es sich um einen gelöschten Eintrag.'));
	}
}
else {
	if ($sql3['visibilityLevel'] > 0) {
		exit;
	}
}
// CHAT-SPERREN ANFANG
$blockCom1 = "SELECT MAX(chatSperre) FROM ".$prefix."helferLog WHERE managerBestrafen = '".$cookie_id."'";
$blockCom2 = mysql_query($blockCom1);
if (mysql_num_rows($blockCom2) > 0) {
	$blockCom3 = mysql_fetch_assoc($blockCom2);
	$chatSperreBis = $blockCom3['MAX(chatSperre)'];
	if ($chatSperreBis > 0 && $chatSperreBis > time()) {
		addInfoBox(__('Du bist noch bis zum %1$s Uhr für die Kommunikation im Spiel gesperrt. Wenn Dir unklar ist warum, frage bitte das %2$s.', date('d.m.Y H:i', $chatSperreBis), '<a class="inText" href="/wio.php">'._('Support-Team').'</a>'));
		include 'zz3.php';
		exit;
	}
}
// CHAT-SPERREN ENDE
// ANFRAGE ALS GELESEN MARKIEREN ANFANG
$read1 = "INSERT IGNORE INTO ".$prefix."supportRead (userID, anfrageID) VALUES ('".$cookie_id."', ".$requestID.")";
$read2 = mysql_query($read1);
//if (mysql_affected_rows() == 1) { $_SESSION['last_forumneu_anzahl']--; }
// ANFRAGE ALS GELESEN MARKIEREN ENDE
?>
<p style="text-align:right">
	<a href="/support.php" class="pagenava"><?php echo _('Zurück zur Hauptseite'); ?></a>
	<?php if (($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer') && $sql3['open'] == 1 && $sql3['visibilityLevel'] == 0) { echo ' <a href="/support.php?del='.id2secure($requestID).'" class="pagenava" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Anfrage löschen').'</a>'; } ?>
	<?php if (($_SESSION['status'] == 'Admin' || $_SESSION['status'] == 'Helfer') && $sql3['open'] == 1) {
			if ($sql3['category'] == 'Vorschlag') {
				echo ' <a href="/supportRequest.php?id='.id2secure($requestID).'&amp;setOpen=0" class="pagenava" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Umgesetzt').'</a>';
				echo ' <a href="/supportRequest.php?id='.id2secure($requestID).'&amp;setOpen=-1" class="pagenava" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Ablehnen').'</a>';
			}
			else {
				echo ' <a href="/supportRequest.php?id='.id2secure($requestID).'&amp;setOpen=-1" class="pagenava" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Geklärt').'</a>';
			}
		}
	?>
</p>
<?php
if ($sql3['category'] == 'Vorschlag') {
	$votes1 = "SELECT COUNT(*) FROM ".$prefix."supportVotes WHERE request = ".$requestID." AND userID = '".$cookie_id."'";
	$votes2 = mysql_query($votes1);
	$votes3 = mysql_result($votes2, 0);
}
else {
	$votes3 = 1;
}
if (isset($_POST['myComment']) && $sql3['open'] == 1 && $cookie_id != CONFIG_DEMO_USER) {
	$myComment = mysql_real_escape_string(trim(strip_tags(nl2br($_POST['myComment']), '<br>')));
	$up1 = "INSERT INTO ".$prefix."supportComments (userID, requestID, zeit, text) VALUES ('".$cookie_id."', ".$requestID.", ".time().", '".$myComment."')";
	$up2 = mysql_query($up1);
	$up1 = "UPDATE ".$prefix."supportRequests SET lastAction = ".time()." WHERE id = ".$requestID;
	$up2 = mysql_query($up1);
	// TOP-USER-LISTE AKTUALISIEREN ANFANG
	if ($sql3['visibilityLevel'] == 0) { // nur öffentliche Posts zählen
		$getOwnComments1 = "SELECT COUNT(*) FROM ".$prefix."supportComments WHERE userID = '".$cookie_id."' AND requestID = ".$requestID;
		$getOwnComments2 = mysql_query($getOwnComments1);
		$getOwnComments3 = mysql_result($getOwnComments2, 0);
		if ($getOwnComments3 == 1) { // wenn es der erste Kommentar zu dieser Anfrage war
			if ((time()-$sql3['timeAdded']) < 3600) { // fastReply
				$up1 = "INSERT INTO ".$prefix."supportUsers (userID, replies, fastReplies) VALUES ('".$cookie_id."', 1, 1) ON DUPLICATE KEY UPDATE replies = replies+1, fastReplies = fastReplies+1, points = (replies*10+fastReplies*25+thanksReceived*5+votes*1)";
				$up2 = mysql_query($up1);
			}
			else { // normalReply
				$up1 = "INSERT INTO ".$prefix."supportUsers (userID, replies) VALUES ('".$cookie_id."', 1) ON DUPLICATE KEY UPDATE replies = replies+1, points = (replies*10+fastReplies*25+thanksReceived*5+votes*1)";
				$up2 = mysql_query($up1);
			}
		}
	}
	// TOP-USER-LISTE AKTUALISIEREN ENDE
	$unRead1 = "DELETE FROM ".$prefix."supportRead WHERE userID != '".$cookie_id."' AND anfrageID = ".$requestID;
	$unRead2 = mysql_query($unRead1);
}
if (isset($_POST['ownRating']) && $votes3 == 0 && $sql3['open'] == 1 && $cookie_id != CONFIG_DEMO_USER) {
	switch ($_POST['ownRating']) {
		case 'Gut!': $sqlPro = 1; $sqlCon = 0; break;
		case 'Schlecht!': $sqlPro = 0; $sqlCon = 1; break;
		default: $sqlPro = 0; $sqlCon = 0; break;
	}
	$proConDifference = intval($sqlPro-$sqlCon);
	$up1 = "INSERT INTO ".$prefix."supportVotes (request, userID, vote) VALUES (".$requestID.", '".$cookie_id."', ".$proConDifference.")";
	$up2 = mysql_query($up1);
	if ($up2 != FALSE) {
		$up1 = "UPDATE ".$prefix."supportRequests SET pro = pro+".$sqlPro.", contra = contra+".$sqlCon." WHERE id = ".$requestID;
		$up2 = mysql_query($up1);
		// TOP-USER-LISTE AKTUALISIEREN ANFANG
		if ($sql3['visibilityLevel'] == 0) { // nur öffentliche Posts zählen
			$up1 = "INSERT INTO ".$prefix."supportUsers (userID, votes) VALUES ('".$cookie_id."', 1) ON DUPLICATE KEY UPDATE votes = votes+1, points = (replies*10+fastReplies*25+thanksReceived*5+votes*1)";
			$up2 = mysql_query($up1);
		}
		// TOP-USER-LISTE AKTUALISIEREN ENDE
	}
	// VARIABLEN AKTUALISIEREN ANFANG
	$sql3['pro'] += $sqlPro;
	$sql3['contra'] += $sqlCon;
	$votes3 = 1;
	// VARIABLEN AKTUALISIEREN ENDE
}
echo '<p style="text-align:center; font-size:14px; font-weight:bold;">'.$sql3['title'].'</p>';
// BEWERTUNG ANFANG
	if ($sql3['category'] == 'Vorschlag') {
	if ($votes3 == 0 && $sql3['open'] == 1) { // noch nicht abgestimmt
		echo '<form action="/supportRequest.php?id='.id2secure($requestID).'" method="post" accept-charset="utf-8">';
		echo '<p style="text-align:center"><input type="submit" name="ownRating" value="'._('Gut!').'" style="width:150px; font-size:14px" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /> <input type="submit" name="ownRating" value="'._('Schlecht!').'" style="width:150px; font-size:14px" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /></p>';
		echo '</form>';
	}
	else { // schon abgestimmt
		$votesGes = $sql3['pro']+$sql3['contra'];
		$pPro = round($sql3['pro']/$votesGes*100);
		$pCon = 100-$pPro;
		$pixelPro = round(400*$pPro/100);
		echo '<div style="width:400px; height:40px; margin:10px auto; background-color:#3b69b6; color:#fff">';
		echo '<div style="width:4px; height:40px; margin:0; background-color:#fff; position:relative; left:'.$pixelPro.'px; top:0"></div>';
		echo '</div>';
		echo '<p style="text-align:center; font-size:14px">'.$pPro.'% DAFÜR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$pCon.'% DAGEGEN<br />&nbsp;<br />(Stimmen: '.$votesGes.')</p>';
	}
}
// BEWERTUNG ENDE
// WEITERE INFOS ANFANG
echo '<p class="commentBoxBlack"><span style="display:block; margin-bottom:16px;">'.$sql3['description'].'</span>';
echo '<strong>'._('Anfrage von:').'</strong> <span style="display:block; margin-bottom:8px;">';
if ($votes3 == 1) { // schon abgestimmt
	$user1 = "SELECT username FROM ".$prefix."users WHERE ids = '".$sql3['author']."'";
	$user2 = mysql_query($user1);
	if (mysql_num_rows($user2) == 1) {
		$user3 = mysql_fetch_assoc($user2);
		echo displayUsername($user3['username'], $sql3['author']);
	}
}
else { // noch nicht abgestimmt
	echo _('Anonym (Bitte erst oben abstimmen!)');
}
echo '</span>';
echo '<strong>'._('Hinzugefügt am:').'</strong> <span style="display:block; margin-bottom:8px;">'.date('d.m.Y H:i', $sql3['timeAdded']).'</span>';
echo '<strong>'._('Letzte Aktion:').'</strong> <span style="display:block; margin-bottom:8px;">'.date('d.m.Y H:i', $sql3['lastAction']).'</span></p>';
// WEITERE INFOS ENDE
?>
<?php if ($sql3['open'] == 1) { ?>
<h1><?php echo _('Dein Kommentar ...'); ?></h1>
<form action="/supportRequest.php?id=<?php echo id2secure($requestID); ?>" method="post" accept-charset="utf-8">
<p><textarea name="myComment" cols="10" rows="10" style="width:350px; height:100px"></textarea></p>
<p><input type="submit" value="<?php echo _('Kommentieren'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<?php } ?>
<h1><?php echo _('Kommentare zu dieser Anfrage'); ?></h1>
<?php
$comments1 = "SELECT a.id, a.deleted, a.userID, a.text, a.zeit, a.likes, b.username, b.status FROM ".$prefix."supportComments AS a JOIN ".$prefix."users AS b ON a.userID = b.ids WHERE a.requestID = ".$requestID." ORDER BY a.zeit ASC";
$comments2 = mysql_query($comments1);
$commentsTotal = mysql_num_rows($comments2);
if ($commentsTotal == 0) {
	echo '<p>'._('Bisher hat noch niemand einen Kommentar zu dieser Anfrage abgegeben. Sei der Erste!').'</p>';
}
else {
	$commentsCounter = 1;
	while ($comments3 = mysql_fetch_assoc($comments2)) {
		echo '<p';
		if ($commentsCounter == $commentsTotal) {
			echo ' id="lastComment"';
		}
		echo ' class="';
		if (($comments3['status'] == 'Admin') && $votes3 == 1) { // wenn Admin-Kommentar und schon abgestimmt
			echo 'commentBoxRed';
		}
		elseif (($comments3['status'] == 'Helfer') && $votes3 == 1) { // wenn Team-Kommentar und schon abgestimmt
			echo 'commentBoxOrange';
		}
		else {
			echo 'commentBoxBlack';
		}
		echo '"><strong>';
		if ($votes3 == 1) {
			echo displayUsername($comments3['username'], $comments3['userID']);
		}
		else {
			echo _('Anonym');
		}
		echo ' '.__('schrieb am %s Uhr:', date('d.m.Y H:i', $comments3['zeit'])).'</strong><br />';
		if ($comments3['deleted'] == 1) {
			echo '<i>'._('Dieser Kommentar wurde gelöscht.').'</i>';
		}
		else {
			echo $comments3['text'];
		}
		if ($comments3['likes'] > 0 && $comments3['deleted'] == 0) {
			echo '<br /><strong>'.__('%d Manager finden diesen Kommentar gut', $comments3['likes']).'</strong>';
		}
		$baseURL = '/supportRequest.php?id='.id2secure($requestID).'&amp;';
		if ($_SESSION['status'] == 'Admin' OR $_SESSION['status'] == 'Helfer') {
			echo '<br />&nbsp;<br /><a class="button" href="'.$baseURL.'delCom='.id2secure($comments3['id']).'" onclick="return confirm(\''._('Bist Du sicher?').'\'">'._('Kommentar löschen').'</a>';
			if ($comments3['userID'] != $cookie_id) {
				echo ' <a class="button" href="#" onclick="voteComment(\''.id2secure($requestID).'\', \''.id2secure($comments3['id']).'\', this); return false;">'._('Guter Kommentar!').'</a>';
			}
		}
		else {
			if ($comments3['userID'] == $cookie_id) {
				echo '<br />&nbsp;<br /><a class="button" href="'.$baseURL.'delCom='.id2secure($comments3['id']).'" onclick="return confirm(\''._('Bist Du sicher?').'\'">'._('Kommentar löschen').'</a>';
			}
			else {
				echo '<br />&nbsp;<br /><a class="button" href="#" onclick="voteComment(\''.id2secure($requestID).'\', \''.id2secure($comments3['id']).'\', this); return false;">'._('Guter Kommentar!').'</a>';
			}
		}
		echo '</p>';
		$commentsCounter++;
	}
}
?>
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript">
function voteComment(requestID, commentID, linkElement) {
	if (typeof(linkElement) !== 'undefined') {
		var responseSpan = document.createElement('span');
		responseSpan.style.marginLeft = '10px';
		responseSpan.style.marginRight = '10px';
		responseSpan.innerHTML = '<img src="/images/loading_14.gif" width="14" alt="Wird gesendet ..." /> <?php echo htmlspecialchars(_('Wird gesendet ...')); ?>';
		linkElement.parentNode.appendChild(responseSpan);
		linkElement.parentNode.removeChild(linkElement);
	}
	$.ajax({
		url: '/supportRequest.php',
		data: { 'id': requestID, 'likeComment': commentID },
		cache: false,
		type: 'GET',
		dataType: 'html'
	}).done(function(response) {
		responseSpan.innerHTML = response;
	});
}
</script>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
