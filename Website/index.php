<?php include 'zz1.php'; ?>
<?php
define('FACEBOOK_LIKEBOX', '<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fwww.ballmanager.de&amp;width=400&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23ffffff&amp;stream=false&amp;header=false&amp;appId=454258221256761" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:400px; height:258px;" allowTransparency="true"></iframe>');
?>
<?php if ($loggedin == 1) { ?><title><?php echo _('Büro'); ?> - <?php echo CONFIG_SITE_NAME; ?></title><?php } else { ?><title><?php echo _('Online-Fußball-Manager'); ?> - <?php echo CONFIG_SITE_NAME; ?></title><?php } ?>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if ($cookie_team != '__'.$cookie_id) {
	$myteam1 = "SELECT sponsor FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
	$myteam2 = mysql_query($myteam1);
	$myteam3 = mysql_fetch_assoc($myteam2);
	$myspieler1 = "SELECT SUM(frische), COUNT(*) FROM ".$prefix."spieler WHERE team = '".$cookie_team."'";
	$myspieler2 = mysql_query($myspieler1);
	$myspieler3 = mysql_fetch_assoc($myspieler2);
	$timeout = getTimestamp('+3 days');
	$auslauf1 = "SELECT COUNT(*) FROM ".$prefix."spieler WHERE team = '".$cookie_team."' AND vertrag < ".$timeout;
	$auslauf2 = mysql_query($auslauf1);
	$auslauf3 = mysql_result($auslauf2, 0);
	$unvollstaendig1 = "SELECT SUM(startelf_Liga) AS a1, SUM(startelf_Pokal) AS a2, SUM(startelf_Cup) AS a3, SUM(startelf_Test) AS a4 FROM ".$prefix."spieler WHERE team = '".$cookie_team."' AND verletzung = 0";
	$unvollstaendig2 = mysql_query($unvollstaendig1);
	$unvollstaendig3 = mysql_fetch_assoc($unvollstaendig2);
	$unvollstaendigStr = '';
	if ($unvollstaendig3['a1'] != 66 && $live_scoring_spieltyp_laeuft != 'Liga') { $unvollstaendigStr .= 'Liga, '; }
	if ($unvollstaendig3['a2'] != 66 && $live_scoring_spieltyp_laeuft != 'Pokal') { $unvollstaendigStr .= 'Pokal, '; }
	if ($unvollstaendig3['a3'] != 66 && $live_scoring_spieltyp_laeuft != 'Cup') { $unvollstaendigStr .= 'Cup, '; }
	if ($unvollstaendig3['a4'] != 66 && $live_scoring_spieltyp_laeuft != 'Test') { $unvollstaendigStr .= 'Test, '; }
	// KANN ETWAS LIVE UEBERTRAGEN WERDEN ANFANG
	if ($live_scoring_spieltyp_laeuft == '') {
		$laufende_spiele3 = 0;
	}
	else {
		$laufende_spiele1 = "SELECT COUNT(*) FROM ".$prefix."spiele WHERE typ = '".$live_scoring_spieltyp_laeuft."' AND ABS(datum-".time().") < 3600";
		$laufende_spiele2 = mysql_query($laufende_spiele1);
		$laufende_spiele3 = mysql_result($laufende_spiele2, 0);
	}
	// KANN ETWAS LIVE UEBERTRAGEN WERDEN ENDE
	?>
	<?php
	$sucheName = '';
	if (isset($_GET['sucheName'])) {
		$sucheName = mysql_real_escape_string(trim(strip_tags($_GET['sucheName'])));
		addInfoBox('<a class="inText" href="#sucheNameErgebnisse">'._('Klicke hier, um die Ergebnisse für Deine Suche anzeigen zu lassen.').'</a>');
	}
	if ($_SESSION['acceptedRules'] == 0) {
		addInfoBox(__('Bitte akzeptiere unsere aktuellen %s, die dem Spiel zugrunde liegen.', '<a class="inText" href="/regeln.php">'._('Regeln').'</a>'));
	}
	if ($unvollstaendigStr != '') {
		addInfoBox(__('Die folgenden %1$s sind unvollständig: %2$s', '<a class="inText" href="/aufstellung.php">'._('Aufstellungen').'</a>', substr($unvollstaendigStr, 0, -2)));
	}
	if ($auslauf3 != 0) {
		addInfoBox(__('In den nächsten drei Tagen laufen %s aus.', '<a class="inText" href="/vertraege.php">'.__('%d Verträge', $auslauf3).'</a>'));
	}
	if ($myteam3['sponsor'] == 0) {
		addInfoBox(__('Du hast für die aktuelle Saison noch keinen Vertrag mit einem Sponsor abgeschlossen. %s', '<a class="inText" href="/sponsoren.php">'._('Klicke hier, um jetzt einen Vertrag abzuschließen.').'</a>'));
	}
	if ($laufende_spiele3 != 0) {
		addInfoBox(__('LIVE: %1$s-spiele von heute! %2$s', $live_scoring_spieltyp_laeuft, '<a class="inText" href="/liveZentrale.php">&raquo; '._('Zur LIVE-Zentrale').'</a>'));
	}
	if ($_SESSION['mds_abgestimmt'] == FALSE) {
		addInfoBox(__('Die Wahl zum &quot;Manager der Saison&quot; läuft! %s', '<a class="inText" href="/manager_der_saison.php">&raquo; '._('Jetzt abstimmen').'</a>'));
	}
	if ($_SESSION['hasLicense'] == 0 && $cookie_team != '__'.$cookie_id) {
		addInfoBox(__('Du hast Deine %s noch nicht abgeschlossen: Für jede erledigte Aufgabe bekommst Du 1 Mio. auf Dein Vereinskonto!', '<a class="inText" href="/managerPruefung.php">'._('Manager-Prüfung').'</a>'));
	}
	?>
	<?php if (isMobile() && isset($nextGamesHTML)) { echo str_replace(' (<a href="/wio.php">WIO</a>)', '', $nextGamesHTML); } ?>
	<h1><?php echo __('Dein Verein: %s', $cookie_teamname); ?></h1>
    <p style="float:left; text-align:left; margin-bottom:0;">
        <a class="pagenava" href="/freundeWerben.php"><?php echo _('Freunde einladen'); ?></a>
    </p>
    <p style="text-align:left; margin-bottom:0;">
        <img style="width:16px; height:16px; vertical-align:middle;" src="/images/icon_spieler.png" width="16" alt="<?php echo _('Freunde einladen'); ?>"> <?php echo _('Lade Deine Freunde ein und erhalte 7,5 Mio. Bonus!'); ?>
    </p>
    <div style="clear:both;"></div>
	<table>
	<thead>
	<tr class="odd">
	<th scope="col"><?php echo _('Bereich'); ?></th>
	<th scope="col"><?php echo _('Vereinsdaten'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$vd1 = "SELECT aufstellung, staerke, konto, punkte, rank, elo, pokalrunde, cuprunde, posToSearch, wantTests FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
	$vd2 = mysql_query($vd1);
	$vd3 = mysql_fetch_assoc($vd2);
	$vd_aufstellungsstaerke = floor($vd3['aufstellung']/108.9*100);
	$vd_kaderstaerke = floor($vd3['staerke']/9.9*100);
	$einsatzAuk = einsatz_in_auktionen($cookie_team);
	$verfuegbaresGeld = $vd3['konto']-$einsatzAuk;
	if ($verfuegbaresGeld < 0) { $verfuegbaresGeld = 0; }
	?>
	<tr><td><?php echo _('Liga'); ?></td><td><?php echo $vd3['rank'].'. Platz'; ?></td></tr>
	<tr class="odd"><td><?php echo _('Pokal'); ?></td><td><?php if ($vd3['pokalrunde'] == 0) { echo '-'; } else { echo pokalrunde_wort($vd3['pokalrunde']); } ?></td></tr>
	<tr><td><?php echo _('Cup'); ?></td><td><?php if ($vd3['cuprunde'] == 0) { echo '-'; } else { echo cuprunde_wort($vd3['cuprunde']); } ?></td></tr>
	<tr class="odd"><td><?php echo _('RKP'); ?></td><td><?php echo __('%s Punkte', number_format($vd3['elo'], 0, ',', '.')); ?></td></tr>
	<tr><td><?php echo _('Kontostand'); ?></td><td<?php if ($vd3['konto'] < 0) { echo ' style="color:red"'; } ?>><?php echo showKontostand($vd3['konto']); ?> €</td></tr>
	<tr class="odd"><td><?php echo _('Einsatz in Auktionen'); ?></td><td><?php echo number_format($einsatzAuk, 0, ',', '.'); ?> €</td></tr>
	<tr><td><?php echo _('Verfügbares Geld'); ?></td><td><?php echo showKontostand($verfuegbaresGeld); ?> €</td></tr>
	<tr class="odd"><td><?php echo _('Aufstellungsstärke'); ?></td><td><img src="/images/balken/<?php echo $vd_aufstellungsstaerke; ?>.png" alt="" /></td></tr>
	<tr><td><?php echo _('Kaderstärke'); ?></td><td><img src="/images/balken/<?php echo $vd_kaderstaerke; ?>.png" alt="" /></td></tr>
	<tr class="odd"><td><?php echo _('Jugendabteilung'); ?></td><td class="link"><a href="/kader.php#besetzung">
		<?php
		switch ($vd3['posToSearch']) {
			case 'T': $posToSearch = _('Torwart'); break;
			case 'A': $posToSearch = _('Abwehr'); break;
			case 'M': $posToSearch = _('Mittelfeld'); break;
			case 'S': $posToSearch = _('Sturm'); break;
			default: $posToSearch = '?'; break;
		}
		echo __('%s gesucht', $posToSearch);
		?>
	</a></td></tr>
	<?php
	if (GameTime::getMatchDay() < 22) {
		$daysUntilNextYouth = intval(GameTime::getMatchDay() % 3);
		switch ($daysUntilNextYouth) {
			case 2: $nextYouth = _('morgen'); $nextYouthDay = GameTime::getMatchDay()+1; break;
			case 1: $nextYouth = _('übermorgen'); $nextYouthDay = GameTime::getMatchDay()+2; break;
			case 0: $nextYouth = _('heute'); $nextYouthDay = GameTime::getMatchDay(); break;
            default: throw new Exception('Invalid next youth index: '.$daysUntilNextYouth);
		}
	}
	else {
		$nextYouth = _('in drei Tagen');
		$nextYouthDay = 3;
	}
	echo '<tr><td>'._('Nächster Jugendspieler').'</td><td>'.$nextYouth.' ('.__('Spieltag %d', $nextYouthDay).')</td></tr>';
	?>
	<tr class="odd"><td><?php echo _('Testspiele'); ?></td><td class="link"><a href="/testspiele.php">
		<?php
		if ($vd3['wantTests'] == 1) {
			echo _('Interessiert');
		}
		else {
			echo _('Kein Interesse');
		}
		?>
	</a></td></tr>
	<tr><td colspan="2"><?php echo '<img style="vertical-align: middle;" alt="Systemzeit" src="/images/clock.gif" width="16" /> '.__('Saison %d', GameTime::getSeason()).' &middot; '.__('Spieltag %d', GameTime::getMatchDay()).' &middot; '.__('%s Uhr', date('d.m.Y, H:i')); ?></td></tr>
	</tbody>
	</table>
	<?php
	// SPECIAL OFFERS BEGIN
	if (!isset($_SESSION['last_special_offer_check']) || $_SESSION['last_special_offer_check'] < (time()-3600)) {
		$specialOffer = getSpecialOffer();
		if ($specialOffer !== FALSE) {
			addInfoBox($specialOffer);
		}
	}
	// SPECIAL OFFERS END
	$in_14_tagen = getTimestamp('+14 days');
	$auslauf1 = "SELECT ids, vorname, nachname, vertrag, gehalt, wiealt FROM ".$prefix."spieler WHERE team = '".$cookie_team."' ORDER BY vertrag ASC LIMIT 0, 10";
	$auslauf2 = mysql_query($auslauf1);
	$auslauf2a = mysql_num_rows($auslauf2);
	if ($auslauf2a > 0) {
	?>
	<h1><?php echo _('Kürzeste Verträge'); ?></h1>
    <p><?php echo _('Die Verträge der folgenden Spieler laufen als Nächstes aus. Du solltest sie rechtzeitig verlängern, damit die Spieler bei Deinem Verein bleiben:'); ?></p>
	<table>
	<thead>
	<tr class="odd">
	<th scope="col"><?php echo _('Spieler'); ?></th>
	<th scope="col"><?php echo _('Alter'); ?></th>
	<th scope="col"><?php echo _('Vertrag bis'); ?></th>
	<th scope="col"><?php echo _('Gehalt'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$counter = 0;
	while ($auslauf3 = mysql_fetch_assoc($auslauf2)) {
		echo '<tr';
		if ($counter % 2 == 1) { echo ' class="odd"'; }
		echo '><td class="link"><a href="/spieler.php?id='.$auslauf3['ids'].'">'.$auslauf3['vorname'].' '.$auslauf3['nachname'].'</a></td><td>'.floor($auslauf3['wiealt']/365).'</td><td>'.date('d.m.Y', $auslauf3['vertrag']).'</td><td>'.number_format($auslauf3['gehalt'], 0, ',', '.').' €</td></tr>';
		$counter++;
	} // while auslauf3
	echo '</tbody></table>';
	} // if auslauf2a > 0
	echo '<h1>'._('Suche nach Teams oder Managern').'</h1>';
	echo '<form action="/index.php" method="get" accept-charset="utf-8">';
	echo '<p><input type="text" name="sucheName" style="width:200px" value="'.$sucheName.'" /> <input type="submit" value="'._('Suchen').'" /></p>';
	echo '</form>';
	if ($sucheName != '') {
		echo '<h1 id="sucheNameErgebnisse">'._('Suchergebnisse (maximal 10)').'</h1>';
		$suchErgebnisCounter = 0;
		echo '<ul>';
		$sn1 = "SELECT ids, name FROM ".$prefix."teams WHERE name LIKE '%".$sucheName."%' LIMIT 0, 10";
		$sn2 = mysql_query($sn1);
		while ($sn3 = mysql_fetch_assoc($sn2)) {
			if ($suchErgebnisCounter < 10) {
				echo '<li><strong>'._('Team:').'</strong> <a href="/team.php?id='.$sn3['ids'].'">'.$sn3['name'].'</a></li>';
				$suchErgebnisCounter++;
			}
		}
		$sn1 = "SELECT ids, username FROM ".$prefix."users WHERE username LIKE '%".$sucheName."%' LIMIT 0, 10";
		$sn2 = mysql_query($sn1);
		while ($sn3 = mysql_fetch_assoc($sn2)) {
			if ($suchErgebnisCounter < 10) {
				echo '<li><strong>'._('Manager:').'</strong> '.displayUsername($sn3['username'], $sn3['ids']).'</li>';
				$suchErgebnisCounter++;
			}
		}
		echo '</ul>';
	}
	echo '<h1>'._('Fairplay und Multi-Accounts').'</h1>';
	echo '<p>'._('Wie im Sport genießt auch bei uns das Fairplay absolute Priorität.').'</p><p>'._('Pro Person ist nur ein Account erlaubt. Bekannte, Freunde oder die geliebte Familie dürfen natürlich vom selben Internet-Anschluss aus mitspielen.').'</p><p>'._('Solltest Du aber gemeinsam mit Anderen einen Rechner benutzen, unterliegt dieses Vorgehen einer Auflage, welche das Fairplay gewährleisten und schützen soll:').'<br />'.('Unter allen diesen Accounts sind jegliche Aktivitäten, die einem der Accounts einen Vorteil verschaffen können, untersagt. Ansonsten genießen diese Accounts selbstverständlich die gleichen Rechte, Pflichten und Spielfunktionen wie die Einzelspieler.').'</p>';
}
else {
	if (isset($_GET['newUser']) && isset($_GET['selectTeam']) && isset($_GET['verify'])) {
		$newUser_selectTeam = mysql_real_escape_string(trim(strip_tags($_GET['selectTeam'])));
		$newUser_verify = mysql_real_escape_string(trim(strip_tags($_GET['verify'])));
		if (intval($_GET['newUser']) == 1 && $newUser_verify == md5($newUser_selectTeam)) {
			$newUser_getData1 = "SELECT name, liga FROM ".$prefix."teams WHERE ids = '".$newUser_selectTeam."'";
			$newUser_getData2 = mysql_query($newUser_getData1);
			if (mysql_num_rows($newUser_getData2) == 1) {
				$newUser_getData3 = mysql_fetch_assoc($newUser_getData2);
				$in1 = "UPDATE ".$prefix."users SET team = '".$newUser_selectTeam."', liga = '".$newUser_getData3['liga']."' WHERE ids = '".$cookie_id."'";
				$in2 = mysql_query($in1);
				if ($in2 == FALSE) {
					addInfoBox(_('Sorry, ein anderer Manager war leider schneller: Er hat Dir das Team weggeschnappt! Bitte versuche es mit einem anderen Team ...'));
				}
				else {
					$maxFrische = floor(98.5-1.75*GameTime::getMatchDay());
					$tu1 = "UPDATE ".$prefix."teams SET konto = 5000000, vorjahr_konto = 5000000, meisterschaften = 0, pokalsiege = 0, cupsiege = 0, friendlies = 0, friendlies_ges = 0 WHERE ids = '".$newUser_selectTeam."'";
					$tu2 = mysql_query($tu1);
					// SPIELER VOM TRANSFERMARKT HOLEN ANFANG
					$tm1 = "DELETE FROM ".$prefix."transfermarkt WHERE besitzer = '".$newUser_selectTeam."'";
					$tm2 = mysql_query($tm1);
					$tm3 = "UPDATE ".$prefix."spieler SET transfermarkt = 0, verletzung = 0";
					if (GameTime::getMatchDay() < 22) {
						$tm3 .= ", frische = ".$maxFrische;
					}
					$tm3 .= " WHERE team = '".$newUser_selectTeam."'";
					$tm4 = mysql_query($tm3);
					// SPIELER VOM TRANSFERMARKT HOLEN ENDE
					// WILLKOMMENS-POST SCHICKEN ANFANG
					$willkommensText = 'Hallo '.$cookie_username.',<br /><br />herzlich willkommen bei '.CONFIG_SITE_NAME.'. Wir hoffen, du findest Dich hier schnell zurecht.<br />Damit Dir der Einstieg etwas leichter fällt, haben wir viele nützliche <a href="/tipps_des_tages.php">Tipps</a> gesammelt.<br />Wenn Du noch Fragen hast, helfen wir Dir auch gerne im <a href="/chat.php">Chat</a> oder in unserem <a href="/support.php">Support-Bereich</a> weiter.<br />Es wartet eine nette Community auf Dich :)<br /><br />Viel Spaß wünscht<br />'.CONFIG_SITE_NAME.'<br />'.CONFIG_SITE_DOMAIN;
					$sql1 = "INSERT INTO ".$prefix."pn (von, an, titel, inhalt, zeit, in_reply_to) VALUES ('".CONFIG_OFFICIAL_USER."', '".$cookie_id."', 'Willkommen bei '.CONFIG_SITE_NAME, '".$willkommensText."', '".time()."', '')";
					$sql2 = mysql_query($sql1);
					$sql1 = "UPDATE ".$prefix."pn SET ids = MD5(id) WHERE ids = ''";
					$sql2 = mysql_query($sql1);
					// WILLKOMMENS-POST SCHICKEN ENDE
					addInfoBox(_('Der Rasen ist gestutzt, der Kugelschreiber poliert: Starte Deine Karriere nach dem nächsten Login!').' <a class="inText" href="/logout.php">'._('[Ausloggen]').'</a>');
					echo '<h1>'._('Dein neuer Job!').'</h1>';
					echo '<p>'.__('Herzlichen Glückwunsch! %s freut sich sehr, Dich als neuen Manager begrüßen zu dürfen! Du erhältst Dein Team automatisch beim nächsten Login.', $newUser_getData3['name']).'</p>';
					echo '<p>'._('Wenn Du sofort spielen möchtest, loggst Du Dich am besten aus und anschließend direkt wieder ein. Du kommst dann direkt ins Büro Deines Vereins.').'</p>';
					echo '<p>'._('Viel Spaß!').'</p>';
					include 'zz3.php';
					exit;
				}
			}
		}
	}
	if ($cookie_team != 'c10992567e8f511ff789d1164fd3612e' && $cookie_team != '18a393b5e23e2b9b4da106b06d8235f3') {
		echo '<h1>'.__('Herzlich Willkommen, %s!', $cookie_username).'</h1>';
		$lastManagedTimeout = getTimestamp('-3 days');
		$getleerLimit = (isset($_GET['show_all'])) ? 624 : 65;
		$getleer1 = "SELECT a.ids, a.name, a.liga, b.name AS ligaName FROM ".$prefix."teams AS a JOIN ".$prefix."ligen AS b ON a.liga = b.ids WHERE a.ids NOT IN (SELECT team FROM ".$prefix."users) AND a.last_managed > ".$lastManagedTimeout." ORDER BY b.level ASC LIMIT 0, ".$getleerLimit;
		$getleer2 = mysql_query($getleer1);
		if (mysql_num_rows($getleer2) > 0) {
			echo '<p>'._('Du bist zwar ein Neuling, aber man sagt, du hättest großes Talent. Deshalb haben wir hier ein paar Job-Angebote für Dich. Such Dir einfach ein Team aus und deine Reise als Manager kann beginnen:').'</p>';
			echo '<table><thead><tr class="odd"><th scope="col">'._('Team').'</th><th scope="col">'._('Liga').'</th><th scope="col">'._('Aktion').'</th></tr></thead><tbody>';
			$counter = 0;
			while ($getleer3 = mysql_fetch_assoc($getleer2)) {
				echo '<tr';
				if ($counter % 2 != 0) { echo ' class="odd"'; }
				echo '>';
				echo '<td class="link"><a href="/team.php?id='.$getleer3['ids'].'">'.$getleer3['name'].'</a></td>';
				echo '<td class="link"><a href="/lig_tabelle.php?liga='.$getleer3['liga'].'">'.$getleer3['ligaName'].'</a></td>';
				echo '<td class="link"><a href="/?newUser=1&amp;selectTeam='.$getleer3['ids'].'&amp;verify='.md5($getleer3['ids']).'" onclick="return confirm(\''._('Bist Du sicher?').'\');">'._('Team wählen').'</a></td>';
				echo '</tr>';
				$counter++;
			}
			echo '</tbody></table>';
		}
		else {
			echo '<p>'._('Leider sind im Moment keine Jobs als Manager frei - aber es wird nicht lange dauern, dann wirst Du Dein erstes Job-Angebot bekommen. Versprochen! Schau einfach in ein paar Stunden wieder vorbei, dann wird ein Team für Dich frei sein. Man sagt, Du hättest das Zeug zum Top-Manager ...').'</p>';
		}
        echo '<p>'.__('Nicht genug Angebote? Du möchtest in einer anderen Liga anfangen? %s', '<a href="/?show_all=1">'._('Klicke hier, um mehr anzuzeigen!').'</a>').'</p>';
		echo '<p>'._('Wenn Du Fragen hast, helfen wir Dir gerne im Support-Bereich oder im Chat weiter. Beides findest Du im Hauptmenü unter <i>Community</i>.').'</p>';
	}
}
?>
<?php } else { ?>
<?php if (isset($_GET['loggedout'])) { ?>
<h1><?php echo _('Du wurdest erfolgreich ausgeloggt!'); ?></h1>
<?php echo FACEBOOK_LIKEBOX; ?>
<?php } else { ?>
<?php if (isMobile()) { ?>
<h1><?php echo _('Einloggen mit bestehendem Account'); ?></h1>
<form action="<?php echo getBaseURL(); ?>/login.php" method="post" accept-charset="utf-8" id="login_form" class="imtext">
<p><?php echo _('E-Mail / Username:'); ?><br /><input type="text" name="lusername" /></p>
<p><?php echo _('Passwort:'); ?><br /><input type="password" name="lpassword" /></p>
<p><input type="hidden" name="returnURL" value="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" /><input type="submit" value="<?php echo _('Einloggen'); ?>" /></p>
<p><b><a href="/passwort_vergessen.php"><?php echo _('Passwort vergessen?'); ?></a></b></p>
</form>
<h1><?php echo _('Jetzt neu registrieren'); ?></h1>
<form method="post" action="/registrieren.php" accept-charset="utf-8" class="imtext">
<p><?php echo _('Dein gewünschter Managername:'); ?><br /><input type="text" name="reg_benutzername" id="reg_benutzername" style="width:200px" /></p>
<p><?php echo _('Deine E-Mail-Adresse:'); ?><br /><input type="text" name="reg_email" id="reg_email" style="width:200px" /></p>
<p><input type="submit" value="<?php echo _('Jetzt kostenlos mitspielen'); ?>" /></p>
<p><?php echo _('Du kannst Deine Daten anschließend noch einmal prüfen, sie werden noch nicht gespeichert. Du darfst Dich nur ein einziges Mal registrieren und nur einen Verein haben.'); ?></p>
</form>
<?php } else { ?>
<h1><?php echo _('Kostenloser Online-Fußball-Manager'); ?></h1>
<p><strong><?php echo _('Du bist der Trainer. Du bist der Manager. Du hast alles in der Hand!'); ?></strong></p>
<p>+ <?php echo _('Übernimm Deinen eigenen Fußballklub!'); ?><br />+ <?php echo _('jeden Tag 1 bis 4 Spiele (Liga + Pokal)'); ?><br />+ <?php echo _('einfach im Browser managen &mdash; keine Installation'); ?><br />+ <?php echo _('garantiert kostenlos &mdash; auch in Zukunft'); ?><br />+ <?php echo _('keine Premium-Accounts &mdash; gleiche Chancen für alle'); ?><br />+ <?php echo _('schneller Einstieg'); ?><br />+ <?php echo _('langfristiger Spielspaß'); ?><br />+ <?php echo _('tolle Community'); ?><br />+ <?php echo _('wenig Zeitaufwand'); ?><br />+ <?php echo _('LIVE-Spiele &mdash; spannend bis zum Ende'); ?><br />+ <?php echo _('Urlaubsvertretung durch den Computer (10-30 Tage)'); ?></p>
<h1><?php echo _('Jetzt registrieren'); ?></h1>
<form method="post" action="/registrieren.php" accept-charset="utf-8" class="imtext">
<p><?php echo _('Dein gewünschter Managername:'); ?><br /><input type="text" name="reg_benutzername" id="reg_benutzername" style="width:200px" /></p>
<p><?php echo _('Deine E-Mail-Adresse:'); ?><br /><input type="text" name="reg_email" id="reg_email" style="width:200px" /></p>
<p><input type="submit" value="<?php echo _('Jetzt kostenlos mitspielen'); ?>" /></p>
<p><?php echo _('Du kannst Deine Daten anschließend noch einmal prüfen, sie werden noch nicht gespeichert. Du darfst Dich nur ein einziges Mal registrieren und nur einen Verein haben.'); ?></p>
</form>
<h1><?php echo __('Gefällt Dir %s?', CONFIG_SITE_NAME); ?></h1>
<?php echo FACEBOOK_LIKEBOX; ?>
<?php } ?>
<?php
if (isset($_GET['r'])) {
	$referralID = mysql_real_escape_string(trim(strip_tags($_GET['r'])));
	if (mb_strlen($referralID) == 32) {
		$_SESSION['referralID'] = $referralID;
	}
}
?>
<?php } ?>
<?php } ?>
<?php include 'zz3.php'; ?>
