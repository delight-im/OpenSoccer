<?php include 'zz1.php'; ?>
<title><?php echo ((isset($_SESSION['status']) && ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin')) ? 'Kontrollzentrum' : 'Sanktionen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
// ANTRAEGE ANFANG
function dayAndMonth($timestamp = 0) {
	if ($timestamp == 0) {
		$timestamp = time();
	}
	$out = date('j', $timestamp).'. ';
	$month = date('n', $timestamp);
	switch ($month) {
		case '1': return $out.'Januar';
		case '2': return $out.'Februar';
		case '3': return $out.'März';
		case '4': return $out.'April';
		case '5': return $out.'Mai';
		case '6': return $out.'Juni';
		case '7': return $out.'Juli';
		case '8': return $out.'August';
		case '9': return $out.'September';
		case '10': return $out.'Oktober';
		case '11': return $out.'November';
		case '12': return $out.'Dezember';
		default: return date('d.m.Y', $timestamp);
	}
}
if ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin') {
	$profileIDSQL = "WHERE last_login > ".getTimestamp('-14 days');
	if (isset($_POST['email_text'])) {
		$emailText = trim(htmlspecialchars(strip_tags($_POST['email_text'])));
		$throttling1 = "UPDATE ".$prefix."users SET lastBackendEmail = '".date('Y-m-d')."' WHERE ids = '".$cookie_id."' AND (status = 'Admin' OR status = 'Helfer')";
		mysql_query($throttling1);
		if (mysql_affected_rows() == 1) {
			$queueEmails1 = "INSERT INTO ".$prefix."backendEmails_pending (zeit, user, text, voters) VALUES (".time().", '".$cookie_id."', '".mysql_real_escape_string($emailText)."', '-".$cookie_id."-')";
			mysql_query($queueEmails1);
			addInfoBox('Die E-Mails werden versendet, sobald es die Bestätigung von 2 Team-Mitgliedern gibt.');
		}
		else {
			addInfoBox('Es ist nur ein einziger E-Mail-Versand pro Tag möglich!');
		}
	}
    else {
        $emailText = '';
    }
	if (isset($_GET['approveMail']) && isset($_GET['approveToken'])) {
		$approveMailID = intval(trim($_GET['approveMail']));
		$approveTokenIst = trim($_GET['approveToken']);
		$approveTokenSoll = md5('ABC'.$approveMailID.'567');
		$approveMode = intval(trim($_GET['approveMode']));
		if ($approveTokenIst == $approveTokenSoll) {
			if ($approveMode == 1) { // bestätigen
				$approveMail1 = "UPDATE ".$prefix."backendEmails_pending SET votes = votes+1, voters = CONCAT(voters, '-".$cookie_id."-') WHERE id = ".$approveMailID." AND votes < 3 AND voters NOT LIKE '%".$cookie_id."%'";
				$approveMail2 = mysql_query($approveMail1);
				if (mysql_affected_rows() > 0) {
					$approveMail3 = "SELECT text FROM ".$prefix."backendEmails_pending WHERE id = ".$approveMailID." AND votes >= 3 AND voters LIKE '%-".$cookie_id."-'";
					$approveMail4 = mysql_query($approveMail3);
					if (mysql_num_rows($approveMail4) == 1) {
						$approveMail5 = mysql_fetch_assoc($approveMail4);
						function email_senden($email, $bodyText, $bcc = array()) {
							$empfaenger = $email;
							$betreff = CONFIG_SITE_NAME.': News vom '.dayAndMonth();
							$nachricht = "Lieber Manager,\n\n".$bodyText."\n\nWir wünschen Dir noch viel Spaß beim Managen!\n\nSportliche Grüße\n".CONFIG_SITE_NAME."\n".CONFIG_SITE_DOMAIN."\n\n------------------------------\n\nDu erhältst diese E-Mail, weil Du Dich auf ".CONFIG_SITE_DOMAIN." mit dieser Adresse registriert hast. Du kannst Deinen Account jederzeit löschen, nachdem Du Dich eingeloggt hast, sodass Du anschließend keine E-Mails mehr von uns bekommst. Bei Missbrauch Deiner E-Mail-Adresse meldest Du Dich bitte per E-Mail unter ".CONFIG_SITE_EMAIL;
							if (CONFIG_EMAIL_PHP_MAILER) {
								require './phpmailer/PHPMailerAutoload.php';
								$mail = new PHPMailer(); // create a new object
								$mail->CharSet= CONFIG_EMAIL_CHARSET;
								$mail->IsSMTP();
								$mail->SMTPAuth = CONFIG_EMAIL_AUTH;
								$mail->SMTPSecure = CONFIG_EMAIL_SECURE;
								$mail->Host = CONFIG_EMAIL_HOST;
								$mail->Port = CONFIG_EMAIL_PORT;
								$mail->Username = CONFIG_EMAIL_USER;
								$mail->Password = CONFIG_EMAIL_PASS;
								$mail->SetFrom(CONFIG_EMAIL_FROM);
								$mail->Subject = $betreff;
								$mail->Body = $nachricht;
								$mail->AddAddress($empfaenger);
                                if (!empty($bcc)){
                                    foreach($bcc as $bccAddress){
                                        $mail->AddBCC($bccAddress);
                                    }
                                }
								$mail->Send();
							}
							else{
								$header = "From: ".CONFIG_SITE_NAME." <".CONFIG_SITE_EMAIL.">\r\nContent-type: text/plain; charset=utf-8";
								if(!empty($bcc)){
									$header.="\r\nBCC: ";
									foreach ($bcc as $index => $adresse){
										$header.=$adresse;
										if($index!=0) $header.=', ';
									}
								}
								mail($empfaenger, $betreff, $nachricht, $header);
							}
						}
						$emailUsers1 = "SELECT email, team FROM ".$prefix."users WHERE last_login > ".(time()-3600*24*42);
						$emailUsers2 = mysql_query($emailUsers1);
						$emailUsersCount = 0;
						$bccList = array();
						while ($emailUsers3 = mysql_fetch_assoc($emailUsers2)) {
							if (mb_substr($emailUsers3['email'], 0, 9) != 'GELOESCHT' && mb_strlen($emailUsers3['team']) == 32) {
								if ($emailUsers3['email'] == CONFIG_SITE_EMAIL) {
									$bccList[]=trim($emailUsers3['email']);
									$emailUsersCount++;
								}
							}
						}
						if(!empty($bccList)){
							email_senden(CONFIG_SITE_EMAIL, $approveMail5['text'], $bccList);
							$logBackend1 = "INSERT INTO ".$prefix."backendEmails (zeit, user, text) VALUES (".time().", '".$cookie_id."', '".mysql_real_escape_string($emailText)."')";
                            mysql_query($logBackend1);
							addInfoBox('Insgesamt '.$emailUsersCount.' E-Mails werden nun versendet ...');
						}
						else {
							addInfoBox('Die E-Mail konnten nicht gesendet werden!');
						}
					}
					else {
						addInfoBox('Die E-Mail an alle User wurde bestätigt!');
					}
				}
				else {
					addInfoBox('Die E-Mail an alle User konnte nicht bestätigt werden!');
				}
			}
			else { // ablehnen
				$approveMail1 = "UPDATE ".$prefix."backendEmails_pending SET voters = CONCAT(voters, '-".$cookie_id."-') WHERE id = ".$approveMailID." AND votes < 3 AND voters NOT LIKE '%".$cookie_id."%'";
				$approveMail2 = mysql_query($approveMail1);
				addInfoBox('Die E-Mail an alle User wurde abgelehnt!');
			}
		}
		else {
			addInfoBox('Die E-Mail an alle User konnte nicht bestätigt werden!');
		}
	}
	if (isset($_GET['profileID'])) {
		if ($_GET['profileID'] != '') {
			$profileIDSQL = "WHERE ids = '".mysql_real_escape_string(trim(strip_tags($_GET['profileID'])))."'";
		}
	}
	if (isset($_POST['managerBestrafen']) && isset($_POST['strafe'])) {
		$managerBestrafen = mysql_real_escape_string(trim(strip_tags($_POST['managerBestrafen'])));
		$getTeam1 = "SELECT team, anzSanktionen FROM ".$prefix."users WHERE ids = '".$managerBestrafen."'";
		$getTeam2 = mysql_query($getTeam1);
		if (mysql_num_rows($getTeam2) == 0) {
			addInfoBox('Der angegebene User konnte nicht gefunden werden.');
		}
		else {
			$getTeam3 = mysql_fetch_assoc($getTeam2);
			$sanktionsFaktor = intval($getTeam3['anzSanktionen']+1);
			$strafe = mysql_real_escape_string(trim(strip_tags($_POST['strafe'])));
			switch ($strafe) {
				case 'CO': $transferSperre = 0; $chatSperre = getTimestamp('+'.intval(4*$sanktionsFaktor).' days'); $geldStrafe = 0; break;
				case 'TR': $transferSperre = getTimestamp('+'.intval(22*$sanktionsFaktor).' days'); $chatSperre = 0; $geldStrafe = 0; break;
				case 'GE': $transferSperre = 0; $chatSperre = 0; $geldStrafe = -30000000*$sanktionsFaktor; break;
				case 'TG': $transferSperre = getTimestamp('+'.intval(22*$sanktionsFaktor).' days'); $chatSperre = 0; $geldStrafe = -10000000*$sanktionsFaktor; break;
				default: $transferSperre = 0; $chatSperre = 0; $geldStrafe = 0; break;
			}
			$verstoss = 0;
			if (isset($_POST['verstoss'])) {
				$verstoss = intval($_POST['verstoss']);
			}
			if ($transferSperre != 0 OR $geldStrafe != 0) {
				$team = $getTeam3['team'];
				if ($geldStrafe != 0) {
					$geldAbziehen1 = "UPDATE ".$prefix."teams SET konto = konto+".$geldStrafe." WHERE ids = '".$team."'";
					mysql_query($geldAbziehen1);
					$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$team."', 'Sanktion', ".$geldStrafe.", ".time().")";
					mysql_query($buch1);
				}
				else {
					$vomTMrunter1 = "DELETE FROM ".$prefix."transfermarkt WHERE besitzer = '".$team."'";
					mysql_query($vomTMrunter1);
					$vomTMrunter3 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE team = '".$team."'";
					mysql_query($vomTMrunter3);
				}
				$close7 = "INSERT INTO ".$prefix."helferLog (helfer, managerBestrafen, zeit, chatSperre, transferSperre, geldStrafe, verstoss) VALUES ('".$cookie_id."', '".$managerBestrafen."', ".time().", ".$chatSperre.", ".$transferSperre.", ".$geldStrafe.", ".$verstoss.")";
				$close8 = mysql_query($close7);
			}
			else {
				$close7 = "INSERT INTO ".$prefix."helferLog (helfer, managerBestrafen, zeit, chatSperre, transferSperre, geldStrafe, verstoss) VALUES ('".$cookie_id."', '".$managerBestrafen."', ".time().", ".$chatSperre.", ".$transferSperre.", ".$geldStrafe.", ".$verstoss.")";
				$close8 = mysql_query($close7);
			}
			$anzSanktionen1 = "UPDATE ".$prefix."users SET anzSanktionen = anzSanktionen+1 WHERE ids = '".$managerBestrafen."'";
			mysql_query($anzSanktionen1);
			addInfoBox('Die Strafe wurde ausgeführt.');
		}
	}
	if (isset($_GET['un1']) && isset($_GET['un2'])) {
		$un1 = mysql_real_escape_string(trim(strip_tags($_GET['un1'])));
		$un2 = bigintval($_GET['un2']);
		$unData1 = "SELECT geldStrafe FROM ".$prefix."helferLog WHERE managerBestrafen = '".$un1."' AND zeit = ".$un2;
		$unData2 = mysql_query($unData1);
		if (mysql_num_rows($unData2) == 1) {
			$unData3 = mysql_fetch_assoc($unData2);
			$geldStrafeBack = intval($unData3['geldStrafe']);
			if ($geldStrafeBack != 0) {
				$unData1 = "SELECT team FROM ".$prefix."users WHERE ids = '".$un1."'";
				$unData2 = mysql_query($unData1);
				if (mysql_num_rows($unData2) == 1) {
					$unData3 = mysql_fetch_assoc($unData2);
					$unGeld1 = "UPDATE ".$prefix."teams SET konto = konto-".$geldStrafeBack." WHERE ids = '".$unData3['team']."'";
					mysql_query($unGeld1);
				}
			}
			$un3 = "UPDATE ".$prefix."helferLog SET chatSperre = zeit, transferSperre = zeit, geldStrafe = -1 WHERE managerBestrafen = '".$un1."' AND zeit = ".$un2;
			mysql_query($un3);
			addInfoBox('Die Sperre wurde aufgehoben, bleibt jedoch in der Liste stehen.');
		}
	}
	if (isset($_POST['IDofUserToChange']) && isset($_POST['neuerName'])) {
		$IDofUserToChange = mysql_real_escape_string(trim(strip_tags($_POST['IDofUserToChange'])));
		$temp = explode('_', $IDofUserToChange, 2);
		$IDofUserToChange_id = $temp[0];
		$IDofUserToChange_name = $temp[1];
		$neuerName = mysql_real_escape_string(trim(strip_tags($_POST['neuerName'])));
		if (!validUsername($neuerName)) {
			echo '<p>Ein Managername darf nur die folgenden Zeichen enthalten (Länge: 3-30).</p>';
			echo '<p><strong>Buchstaben:</strong> A-Z und Umlaute (groß und klein)<br /><strong>Zahlen:</strong> 0-9<br /><strong>Sonderzeichen:</strong> Bindestrich</p>';
			echo '<p>Nicht erlaubt sind also Leerzeichen, Punkt, Komma, Sternchen usw.</p>';
			echo '<p>Bitte versuche es noch einmal.</p>';
		}
		else {
			$nnChange1 = "UPDATE ".$prefix."users SET username = '".$neuerName."' WHERE ids = '".$IDofUserToChange_id."'";
			$nnChange2 = mysql_query($nnChange1);
			if ($nnChange2 == FALSE) {
				addInfoBox('Dieser Username existiert schon.');
			}
			else {
				$nameChanges1 = "INSERT INTO ".$prefix."nameChanges (helfer, zeit, vonID, vonName, zuName) VALUES ('".$cookie_id."', ".time().", '".$IDofUserToChange_id."', '".$IDofUserToChange_name."', '".$neuerName."')";
				mysql_query($nameChanges1);
				addInfoBox('Der Username wurde erfolgreich geändert.');
			}
		}
	}
	echo '<h1>Usernamen ändern</h1>';
	echo '<p>Wenn ein Manager sich eine Namensänderung wünscht oder der aktuelle Name gegen die <a href="/regeln.php">Regeln</a> verstößt, kannst Du den Namen des Managers hier ändern.</p>';
	echo '<form action="/sanktionen.php" method="post" accept-charset="utf-8">';
	echo '<p><select name="IDofUserToChange" size="1" style="width:200px">';
	$nm1 = "SELECT ids, username FROM ".$prefix."users ".$profileIDSQL." ORDER BY username ASC";
	$nm2 = mysql_query($nm1);
	$userChooseable = array();
	while ($nm3 = mysql_fetch_assoc($nm2)) {
		if (substr($nm3['username'], 0, 9) == 'GELOESCHT') { continue; }
		if (in_array($nm3['ids'], unserialize(CONFIG_PROTECTED_USERS))) { continue; }
		$userChooseable[] = array($nm3['ids'], $nm3['username']);
	}
	foreach ($userChooseable as $userChooseableEntry) {
		echo '<option value="'.$userChooseableEntry[0].'_'.$userChooseableEntry[1].'">'.$userChooseableEntry[1].'</option>';
	}
	echo '</select></p>';
	echo '<p><input type="text" name="neuerName" value="Neuer Username" /></p>';
	echo '<p><input type="submit" value="Ändern" onclick="return confirm(\'Bist Du sicher?\')" /></p>';
	echo '</form>';
	echo '<h1>Sanktion durchführen</h1><p>Wähle bitte den betroffenen Manager und die gewünschte Strafe aus, um eine Sanktion durchzuführen.</p>';
	echo '<form action="/sanktionen.php" method="post" accept-charset="utf-8">';
	echo '<p><select name="managerBestrafen" size="1" style="width:200px">';
	foreach ($userChooseable as $userChooseableEntry) {
		echo '<option value="'.$userChooseableEntry[0].'">'.$userChooseableEntry[1].'</option>';
	}
	echo '</select></p>';
	echo '<p><select name="strafe" size="1" style="width:200px">';
	echo '<option value="00">-&nbsp;Strafe wählen&nbsp;-</option>';
	echo '<option value="CO">Community-Sperre</option>';
	echo '<option value="TR">Transfer-Sperre</option>';
	echo '<option value="GE">Geldstrafe</option>';
	echo '<option value="TG">Transfer + Geld</option>';
	echo '</select></p>';
	echo '<p><select name="verstoss" size="1" style="width:200px">';
	echo '<option value="0">Ohne Begründung</option>';
	echo '<option value="1">Verstoß gegen Regel I.</option>';
	echo '<option value="2">Verstoß gegen Regel II.</option>';
	echo '<option value="3">Verstoß gegen Regel III.</option>';
	echo '<option value="4">Verstoß gegen Regel IV.</option>';
	echo '<option value="5">Verstoß gegen Regel V.</option>';
	echo '</select></p>';
	echo '<p><input type="submit" value="Ausführen" onclick="return confirm(\'Bist Du sicher?\')" /></p>';
	echo '</form>';
}
// ANTRAEGE ENDE
?>
<h1>Letzte Sanktionen</h1>
<p>Bei unpassendem Verhalten und unfairen Aktionen können Sanktionen beschlossen werden. Für das betroffene Team kann es Transfer- oder Community-Sperren sowie Geldstrafen geben.</p>
<p>Die Höhe der Strafen wird automatisch festgelegt und kann nicht variiert werden.</p>
<p>Community-Sperren beinhalten Sperren für den Chat und das Versenden von privaten Nachrichten. Auch der Zugriff auf den Support-Bereich wird blockiert.</p>
<?php
$timeout = getTimestamp('-28 days');
$sql1 = "SELECT a.managerBestrafen, a.zeit, a.chatSperre, a.transferSperre, a.geldStrafe, a.helfer, a.verstoss, b.username FROM ".$prefix."helferLog AS a JOIN ".$prefix."users AS b ON a.managerBestrafen = b.ids WHERE zeit > ".$timeout." OR chatSperre > ".time()." OR transferSperre > ".time()." ORDER BY zeit DESC";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) > 0) {
?>
<table>
<thead>
<tr class="odd">
<th scope="col">Manager</th>
<th scope="col">Datum</th>
<th scope="col">Community</th>
<th scope="col">Transfer</th>
<th scope="col">Geld</th>
<th scope="col">&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$userName = displayUsername($sql3['username'], $sql3['managerBestrafen']);
	if ($userName == 'Gelöschter User') { continue; }
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	switch ($sql3['verstoss']) {
		case '1': $verstossName = 'Verstoß gegen Regel I.'; break;
		case '2': $verstossName = 'Verstoß gegen Regel II.'; break;
		case '3': $verstossName = 'Verstoß gegen Regel III.'; break;
		case '4': $verstossName = 'Verstoß gegen Regel IV.'; break;
		case '5': $verstossName = 'Verstoß gegen Regel V.'; break;
		default: $verstossName = 'Kein Grund angegeben'; break;
	}
	echo '<td><span title="'.$verstossName.'">'.$userName.'</span></td>';
	echo '<td>'.date('d.m.', $sql3['zeit']).'</td>';
	if ($sql3['transferSperre'] == $sql3['zeit'] OR $sql3['chatSperre'] == $sql3['zeit'] OR $sql3['geldStrafe'] == -1) { // aufgehoben
		echo '<td colspan="3"><i>Aufgehoben</i></td>';
	}
	else {
		if ($sql3['chatSperre'] != 0) {
			echo '<td>'.date('d.m.', $sql3['chatSperre']).'</td>';
		}
		else {
			echo '<td>&nbsp;-&nbsp;</td>';
		}
		if ($sql3['transferSperre'] != 0) {
			echo '<td>'.date('d.m.', $sql3['transferSperre']).'</td>';
		}
		else {
			echo '<td>&nbsp;-&nbsp;</td>';
		}
		if ($sql3['geldStrafe'] != 0) {
			echo '<td>'.number_format($sql3['geldStrafe'], 0, ',', '.').'</td>';
		}
		else {
			echo '<td>&nbsp;-&nbsp;</td>';
		}
	}
	if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin' && ($sql3['transferSperre'] != $sql3['zeit'] && $sql3['chatSperre'] != $sql3['zeit'] && $sql3['geldStrafe'] != -1)) { // nicht aufgehoben
		echo '<td class="link"><a title="Sperre aufheben" href="/sanktionen.php?un1='.$sql3['managerBestrafen'].'&amp;un2='.$sql3['zeit'].'" onclick="return confirm(\'Soll diese Sperre wirklich aufgehoben werden?\')"><img src="/images/fehler.png" alt="" /></a></td>';
	}
	else {
		echo '<td>&nbsp;</td>';
	}
	echo '</tr>';
}
?>
</tbody>
</table>
<p><strong>Tooltips:</strong> Wenn Du mit der Maus über den Usernamen fährst, siehst Du den Grund für die Sanktion. Wenn Du über das Datum fährst, erscheint der TeamCode des Verantwortlichen für die Strafe.</p>
<?php if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { ?><p><strong>Hinweis:</strong> Du kannst eine Community- oder Transfer-Sperre aufheben, indem Du auf das rote Schild mit dem &quot;X&quot; klickst.</p><?php } ?>
<?php } ?>
<?php
function zahleEntschaedigung($teamID, $betrag, $reason = '') {
	global $prefix, $cookie_id;
	$sql1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$teamID."', 'Entschädigung', ".$betrag.", ".time().")";
	mysql_query($sql1);
	$sql1 = "UPDATE ".$prefix."teams SET konto = konto+".$betrag." WHERE ids = '".$teamID."'";
	mysql_query($sql1);
	$sql1 = "INSERT INTO ".$prefix."compensations (helferID, zeit, teamID, reason, betrag) VALUES ('".mysql_real_escape_string(trim($cookie_id))."', ".time().", '".$teamID."', '".$reason."', ".$betrag.")";
	mysql_query($sql1);
}
if ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin') {
	$getBackendMails1 = "SELECT a.id, a.user, b.username, a.zeit, a.text, a.votes, a.voters FROM ".$prefix."backendEmails_pending AS a JOIN ".$prefix."users AS b ON a.user = b.ids WHERE a.votes < 3 ORDER BY a.zeit ASC";
	$getBackendMails2 = mysql_query($getBackendMails1);
	if (mysql_num_rows($getBackendMails2) > 0) {
		echo '<h1>E-Mails bestätigen</h1>';
		echo '<p style="display:block; margin:2px 4px; padding:2px 4px; background-color:#f00; color:#fff; font-weight:bold;">Die folgenden E-Mails warten noch auf Bestätigung. Bitte prüfe sie und klicke auf &quot;Bestätigen&quot;, wenn die E-Mail gesendet werden soll.</p>';
		echo '<table>';
		echo '<thead>';
		echo '<tr class="odd">';
		echo '<th scope="col">Autor</th>';
		echo '<th scope="col">Datum</th>';
		echo '<th scope="col">Text</th>';
		echo '<th scope="col">Aktionen</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		while ($getBackendMails3 = mysql_fetch_assoc($getBackendMails2)) {
			echo '<tr>';
			echo '<td>'.displayUsername($getBackendMails3['username'], $getBackendMails3['user']).'</td>';
			echo '<td>'.date('d.m.Y H:i', $getBackendMails3['zeit']).'</td>';
			echo '<td>Lieber Manager,<br /><br />'.nl2br($getBackendMails3['text']).'<br /><br />Wir wünschen Dir noch viel Spaß beim Managen!<br /><br />Sportliche Grüße<br />'.CONFIG_SITE_NAME.'<br />'.CONFIG_SITE_DOMAIN.'<br /><br /><strong>Bestätigungen: '.$getBackendMails3['votes'].' von 3</strong></td>';
			if (stripos($getBackendMails3['voters'], $cookie_id) === FALSE) {
				echo '<td><a href="/sanktionen.php?approveMail='.$getBackendMails3['id'].'&approveToken='.md5('ABC'.$getBackendMails3['id'].'567').'&approveMode=1" onclick="return confirm(\'Bist Du sicher?\');">Bestätigen</a> / <a href="/sanktionen.php?approveMail='.$getBackendMails3['id'].'&approveToken='.md5('ABC'.$getBackendMails3['id'].'567').'&approveMode=0" onclick="return confirm(\'Bist Du sicher?\');">Ablehnen</a></td>';
			}
			else {
				echo '<td>&nbsp;</td>';
			}
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}
	echo '<h1>E-Mail an alle User</h1>';
	echo '<form action="/sanktionen.php" method="post" accept-charset="utf-8" id="form_email_all_users" style="display:none;">';
	echo '<p style="font-size:120%; font-weight:bold;">'.CONFIG_SITE_NAME.': News vom '.dayAndMonth().'</p>';
	echo '<p style="font-size:120%;">Lieber Manager,</p>';
	echo '<p><textarea name="email_text" style="width:90%; height:300px;"></textarea>';
	echo '<p style="font-size:120%;">Wir wünschen Dir noch viel Spaß beim Managen!</p>';
	echo '<p style="font-size:120%;">Sportliche Grüße<br />'.CONFIG_SITE_NAME.'<br />'.CONFIG_SITE_DOMAIN.'</p>';
	echo '<p><input type="submit" value="Zum Versand vorschlagen" onclick="return confirm(\'Bist Du wirklich sicher, dass diese E-Mail genau so an alle aktiven User des Spiels gesendet werden soll?\');" /></p>';
	echo '</form>';
	echo '<div id="warning_email_all_users"><p>Über diese Funktion kann eine E-Mail an alle aktiven Manager des Spiels gesendet werden. Der obere und untere Bereich der E-Mail sind schon vorgefertigt und müssen nicht mehr geschrieben werden. Der Text dazwischen kann frei eingegeben werden.</p><p>Diese Funktion steht nur dem Support-Team zur Verfügung und darf nicht missbraucht werden. Die Nutzer wollen nicht mit häufigen E-Mails belästigt werden. Aus Sicherheitsgründen ist nur <em>ein einziger</em> E-Mail-Versand pro Tag möglich. Trotzdem sollte diese Funktion nur so selten wie möglich genutzt werden, am besten gar nicht.</p><p style="text-align:center;"><a href="#" onclick="document.getElementById(\'warning_email_all_users\').style.display = \'none\'; document.getElementById(\'form_email_all_users\').style.display = \'block\'; return false;">Ich habe diese Erklärung sorgfältig gelesen und verstanden!</a></p></div>';

	if (isset($_POST['compensationTeam']) && isset($_POST['compensationReason']) && isset($_POST['compensationValue'])) {
		$compensationTeam = mysql_real_escape_string(trim(strip_tags($_POST['compensationTeam'])));
		$compensationValue = intval(trim($_POST['compensationValue']));
		$compensationReason = mysql_real_escape_string(trim(strip_tags($_POST['compensationReason'])));
		if ($compensationTeam != '' && $compensationValue >= 5000000 && $compensationValue <= 100000000) {
			zahleEntschaedigung($compensationTeam, $compensationValue, $compensationReason);
			addInfoBox('Die Entschädigung in Höhe von '.number_format($compensationValue, 0, ',', '.').'€ wurde gezahlt.');
		}
	}
	echo '<h1>Entschädigung zahlen</h1>';
	echo '<form action="/sanktionen.php" method="post" accept-charset="utf-8">';
	echo '<p><select name="compensationTeam" size="1" style="width:200px">';
	echo '<option value="">-- Team (Empfänger) --</option>';
	$getTeam1 = "SELECT ids, name FROM ".$prefix."teams ORDER BY name ASC LIMIT 0, 624";
	$getTeam2 = mysql_query($getTeam1);
	while ($getTeam3 = mysql_fetch_assoc($getTeam2)) {
		echo '<option value="'.$getTeam3['ids'].'">'.$getTeam3['name'].'</option>';
	}
	echo '</select></p>';
	echo '<p><select name="compensationValue" size="1" style="width:200px">';
	echo '<option value="0">-- Höhe der Entschädigung --</option>';
	for ($c = 1; $c <= 20; $c++) {
		echo '<option value="'.($c*5000000).'">'.number_format(($c*5000000), 0, ',', '.').'€</option>';
	}
	echo '</select></p>';
	echo '<p><strong>Begründung angeben:</strong><br /><textarea name="compensationReason" style="width:200px; height:100px;"></textarea></p>';
	echo '<p><input type="submit" value="Zahlung durchführen" onclick="return confirm(\'Bist Du sicher?\');" /></p>';
	echo '</form>';
	
	echo '<h1>Entschädigungen in den letzten 14 Tagen</h1>';
	echo '<ul>';
	$sql1 = "SELECT a.helferID, a.zeit, a.teamID, a.reason, a.betrag, b.username, c.name FROM ".$prefix."compensations AS a JOIN ".$prefix."users AS b ON a.helferID = b.ids JOIN ".$prefix."teams AS c ON a.teamID = c.ids WHERE a.zeit > ".(time()-3600*24*14)." ORDER BY a.id DESC";
	$sql2 = mysql_query($sql1) or die(mysql_error());
	if (mysql_num_rows($sql2) > 0) {
		while ($sql3 = mysql_fetch_assoc($sql2)) {
			echo '<li>'.displayUsername($sql3['username'], $sql3['helferID']).' hat '.number_format($sql3['betrag'], 0, ',', '.').'€ an <a href="/team.php?id='.$sql3['teamID'].'">'.$sql3['name'].'</a> gezahlt.<ul>';
			echo '<li>'.date('d.m.Y H:i', $sql3['zeit']).' Uhr</li>';
			echo '<li>'.(trim($sql3['reason']) == '' ? '<em>ohne Begründung</em>' : htmlspecialchars($sql3['reason'])).'</li>';
			echo '</ul></li>';
		}
	}
	else {
		echo '<li><em>keine</em></li>';
	}
	echo '</ul>';
    
    $invalidMatchesTimeout = time()-3600*6; // Spiel muss mindestens 6 Stunden her sein und trotzdem nicht simuliert
    $invalidMatchesWhere = "datum < ".$invalidMatchesTimeout." AND ergebnis = '-:-'";
    if (isset($_POST['repeat_tomorrow1']) || isset($_POST['repeat_tomorrow2'])) {
        $repeatTomorrow1 = strtotime('tomorrow 15:00:00');
        $repeatTomorrow2 = $repeatTomorrow1+3600*24;
        $repeatTime = 0;
        if (isset($_POST['repeat_tomorrow1'])) {
            $repeatTime = $repeatTomorrow1;
        }
        elseif (isset($_POST['repeat_tomorrow2'])) {
            $repeatTime = $repeatTomorrow2;
        }
        if ($repeatTime > 0) {
            $doRepeatPostponeBy = "ROUND((".$repeatTime."-datum)/3600/24)"; // Ausdruck gibt die Tage an, um die das jeweilige Spiel verschoben werden muss
            $doRepeat1 = "UPDATE ".$prefix."spiele SET datum = datum+(3600*24*".$doRepeatPostponeBy."), simuliert = 0 WHERE ".$invalidMatchesWhere." LIMIT 312";
            mysql_query($doRepeat1) or die(mysql_error());
            addInfoBox('Es wurden insgesamt '.mysql_affected_rows().' Spiele verschoben!');
        }
    }
    $invalidMatches1 = "SELECT id, team1, team2, typ, datum FROM ".$prefix."spiele WHERE ".$invalidMatchesWhere." LIMIT 0, 312";
    $invalidMatches2 = mysql_query($invalidMatches1);
    echo '<h1>Fehlerhafte Spiele</h1>';
    if (mysql_num_rows($invalidMatches2) > 0) {
        echo '<form action="/sanktionen.php" method="post" accept-charset="utf-8">';
        echo '<p><input type="submit" name="repeat_tomorrow1" value="Alle Spiele morgen nachholen" onclick="return confirm(\'Bist Du sicher?\');" /> oder <input type="submit" name="repeat_tomorrow2" value="Alle Spiele übermorgen nachholen" onclick="return confirm(\'Bist Du sicher?\');" /></p>';
        echo '</form>';
        echo '<ul>';
        while ($invalidMatches3 = mysql_fetch_assoc($invalidMatches2)) {
            echo '<li><a href="/spielbericht.php?id='.$invalidMatches3['id'].'">'.$invalidMatches3['team1'].' - '.$invalidMatches3['team2'].'</a><ul>';
            echo '<li>Ursprünglicher Spieltermin: '.date('d.m.Y H:i', ($invalidMatches3['datum']-3600)).'</li>';
            echo '<li>Wettbewerb: '.$invalidMatches3['typ'].'</li>';
            echo '</ul></li>';
        }
        echo '</ul>';
    }
    else {
        echo '<p>Es gab keine fehlerhaften Spiele in den letzten Tagen.</p>';
        echo '<p>Neue fehlerhafte Spiele tauchen hier erst 5-6 Stunden nach geplantem Abpfiff auf.</p>';
    }

}
?>
<?php } else { ?>
<h1>Sanktionen</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>