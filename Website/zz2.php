<meta name="description" content="<?php echo _('Übernimm beim Online-Fußball-Manager Dein eigenes Team als Trainer und Manager! Lasse alle anderen Manager hinter Dir und hole Dir Pokalsieg und Meisterschaft!'); ?>" />
<script type="text/javascript">
function noDemoPopup() {
	alert('<?php echo _('Diese Funktion ist mit dem Demo-Account leider nicht verfügbar!'); ?>');
	return false;
}
</script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1037542-32']);
  _gaq.push(['_gat._anonymizeIp']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' === document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>
<body>
<div id="wrap">
<div id="header">
<div class="logo_top" style="position:relative"><img src="/images/logo.png" alt="<?php echo CONFIG_SITE_NAME; ?> - <?php echo _('Online-Fußball-Manager'); ?>" title="<?php echo CONFIG_SITE_NAME; ?> - <?php echo _('Online-Fußball-Manager'); ?>" width="224" style="border:0; width:224px; height:60px" />
<?php
$topWidget = '<h1>'._('Top-Manager').'</h1>';
$topWidget .= '<div class="left-box navBlockLinks">';
$topWidget1 = "SELECT a.ids, a.username, a.team, a.status, b.name, b.elo FROM ".$prefix."users AS a JOIN ".$prefix."teams AS b ON a.team = b.ids ORDER BY b.elo DESC LIMIT 0, 5";
$topWidget2 = mysql_query($topWidget1);
$topWidgetPlace = 1;
while ($topWidget3 = mysql_fetch_assoc($topWidget2)) {
    $topWidget .= '<a href="/manager.php?id='.$topWidget3['ids'].'">'.$topWidgetPlace.'. '.$topWidget3['username'].' ('.number_format($topWidget3['elo'], 0, ',', '.').')</a>';
    $topWidgetPlace++;
}
$topWidget .= '</div>';
if (isset($_GET['via_android']) && $_GET['via_android'] == 1) {
    $_SESSION['via_android'] = 1;
}
else {
	$_SESSION['via_android'] = 0;
}
if (!isset($_SESSION['pMaxGebot'])) { $_SESSION['pMaxGebot'] = 0; }
/*if ($loggedin == 1 && !isMobile() && $_SESSION['pMaxGebot'] > 2) {
	$communityAds = array('/chat.php', '/posteingang.php', '/postausgang.php', '/post.php', '/post_schreiben.php', '/support.php');
	echo '<div style="width:468px; height:60px; overflow:hidden; background-color:#fff; position:absolute; top:0px; left:256px">';
	if (!in_array($_SERVER['SCRIPT_NAME'], $communityAds)) {
		echo '<script type="text/javascript"><!-- google_ad_client = "ca-pub-5616874035428509"; google_ad_slot = "5071587014"; google_ad_width = 468; google_ad_height = 60; //--> </script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>';
	}
	else {
		echo '<iframe src="http://rcm-de.amazon.de/e/cm?t=ballmanager-21&o=3&p=26&l=ur1&category=generic&banner=1M3FZGK72Q1YXAM9GT82&f=ifr" width="468" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
	}
	echo '</div>';
}*/
?>
</div> <!-- logo_top ENDE -->
</div>
<div id="menu">
<?php if ($_SESSION['via_android'] == 0) { ?>
<ul id="nav">
<?php if ($loggedin == 0) { ?>
<li<?php if ($_SERVER['SCRIPT_NAME'] == '/index.php') { echo ' id="current"'; } ?>><a href="/"><?php echo _('Startseite'); ?></a></li>
<?php
if (isMobile()) {
	echo '<li><a href="'.getBaseURL(false).'/">'._('Desktop').'</a></li>';
}
else {
	echo '<li><a href="'.getBaseURL(true).'/">'._('Mobil').'</a></li>';
}
?>
<li<?php if ($_SERVER['SCRIPT_NAME'] == '/tour.php') { echo ' id="current"'; } ?>><a href="#" onclick="document.getElementById('lusername').value = 'Demo'; document.getElementById('lpassword').value = 'demo'; document.getElementById('login_form').submit(); return false;">Demo-Account</a></li>
<?php } else { ?>
<?php
$vor3Minuten = getTimestamp('-3 minutes');
// PN-ANZAHL ANFANG
if (!isset($_SESSION['last_pn_check'])) { $_SESSION['last_pn_check'] = 0; }
if (!isset($_SESSION['last_pn_anzahl'])) { $_SESSION['last_pn_anzahl'] = 0; }
if ($_SESSION['last_pn_anzahl'] < 0) { $_SESSION['last_pn_anzahl'] = 0; }
if ($_SESSION['last_pn_check'] < $vor3Minuten) {
    $pnew1 = "SELECT COUNT(*) FROM ".$prefix."pn WHERE an = '".$cookie_id."' AND gelesen = 0 AND geloescht_an = 0";
    $pnew2 = mysql_query($pnew1);
    $_SESSION['last_pn_anzahl'] = mysql_result($pnew2, 0);
    $_SESSION['last_pn_check'] = time()+mt_rand(0, 20);
}
// PN-ANZAHL ENDE
// FREUNDE-ANZAHL ANFANG
if (!isset($_SESSION['last_freunde_check'])) { $_SESSION['last_freunde_check'] = 0; }
if (!isset($_SESSION['last_freunde_anzahl'])) { $_SESSION['last_freunde_anzahl'] = 0; }
if ($_SESSION['last_freunde_anzahl'] < 0) { $_SESSION['last_freunde_anzahl'] = 0; }
if ($_SESSION['last_freunde_check'] < $vor3Minuten) {
    $pnew1 = "SELECT COUNT(*) FROM ".$prefix."freunde_anfragen WHERE an = '".$cookie_id."'";
    $pnew2 = mysql_query($pnew1);
    $_SESSION['last_freunde_anzahl'] = mysql_result($pnew2, 0);
    $_SESSION['last_freunde_check'] = time()+mt_rand(0, 20);
}
// FREUNDE-ANZAHL ENDE
// LEIHGABEN-ANGEBOTE ANFANG
if (!isset($_SESSION['last_leihgaben_check'])) { $_SESSION['last_leihgaben_check'] = 0; }
if (!isset($_SESSION['last_leihgaben_anzahl'])) { $_SESSION['last_leihgaben_anzahl'] = 0; }
if ($_SESSION['last_leihgaben_anzahl'] < 0) { $_SESSION['last_leihgaben_anzahl'] = 0; }
if ($cookie_team != '__'.$cookie_id) {
	if ($_SESSION['last_leihgaben_check'] < $vor3Minuten) {
		$pnew1 = "SELECT COUNT(*) FROM ".$prefix."transfermarkt_leihe WHERE besitzer = '".$cookie_team."' AND akzeptiert = 0";
		$pnew2 = mysql_query($pnew1);
		$_SESSION['last_leihgaben_anzahl'] = mysql_result($pnew2, 0);
		$_SESSION['last_leihgaben_check'] = time()+mt_rand(0, 20);
	}
}
// LEIHGABEN-ANGEBOTE ENDE
// TESTSPIEL-ANFRAGEN ANFANG
if (!isset($_SESSION['last_testspiele_check'])) { $_SESSION['last_testspiele_check'] = 0; }
if (!isset($_SESSION['last_testspiele_anzahl'])) { $_SESSION['last_testspiele_anzahl'] = 0; }
if ($_SESSION['last_testspiele_anzahl'] < 0) { $_SESSION['last_testspiele_anzahl'] = 0; }
if ($cookie_team != '__'.$cookie_id) {
	if ($_SESSION['last_testspiele_check'] < $vor3Minuten) {
		$pnew1 = "SELECT COUNT(*) FROM ".$prefix."testspiel_anfragen WHERE team2 = '".$cookie_team."'";
		$pnew2 = mysql_query($pnew1);
		$_SESSION['last_testspiele_anzahl'] = mysql_result($pnew2, 0);
		$_SESSION['last_testspiele_check'] = time()+mt_rand(0, 20);
	}
}
// TESTSPIEL-ANFRAGEN ENDE
// CHATTER ONLINE ANFANG
if (!isset($_SESSION['last_chatter_check'])) { $_SESSION['last_chatter_check'] = 0; }
if (!isset($_SESSION['last_chatter_anzahl'])) { $_SESSION['last_chatter_anzahl'] = 0; }
if ($_SESSION['last_chatter_anzahl'] < 0) { $_SESSION['last_chatter_anzahl'] = 0; }
if ($_SESSION['last_chatter_check'] < $vor3Minuten) {
	$pnew1 = "SELECT COUNT(*) FROM ".$prefix."users WHERE last_chat > ".getTimestamp('-2 minutes');
	$pnew2 = mysql_query($pnew1);
	$_SESSION['last_chatter_anzahl'] = mysql_result($pnew2, 0);
	$_SESSION['last_chatter_check'] = time()+mt_rand(0, 20);
}
// CHATTER ONLINE ENDE
// LIGA-TAUSCH-ANFRAGEN ANFANG
if (!isset($_SESSION['last_ligaTausch_check'])) { $_SESSION['last_ligaTausch_check'] = 0; }
if (!isset($_SESSION['last_ligaTausch_anzahl'])) { $_SESSION['last_ligaTausch_anzahl'] = 0; }
if ($_SESSION['last_ligaTausch_anzahl'] < 0) { $_SESSION['last_ligaTausch_anzahl'] = 0; }
if ($_SESSION['last_ligaTausch_check'] < $vor3Minuten) {
	$pnew1 = "SELECT COUNT(*) FROM ".$prefix."ligaChangeAnfragen WHERE anTeam = '".$cookie_team."'";
	$pnew2 = mysql_query($pnew1);
	$_SESSION['last_ligaTausch_anzahl'] = mysql_result($pnew2, 0);
	$_SESSION['last_ligaTausch_check'] = time()+mt_rand(0, 20);
}
// LIGA-TAUSCH-ANFRAGEN ENDE
?>
<li class="menueintrag"<?php if ($_SERVER['SCRIPT_NAME'] == '/index.php' OR $_SERVER['SCRIPT_NAME'] == '/notizen.php' OR $_SERVER['SCRIPT_NAME'] == '/protokoll.php' OR $_SERVER['SCRIPT_NAME'] == '/einstellungen.php') { echo ' id="current"'; } ?>><a href="/"><?php echo _('Büro'); ?></a>
	<ul>
		<li><a href="/"><?php echo _('Zentrale'); ?></a></li>
		<?php if ($cookie_team != '__'.$cookie_id) { ?><li><a href="/protokoll.php"><?php echo _('Protokoll'); ?></a></li><?php } ?>
		<li><a href="/notizen.php"><?php echo _('Notizen'); ?></a></li>
		<li><a href="/einstellungen.php"><?php echo _('Einstellungen'); ?></a></li>
        <?php if (isMobile()) { ?><li><a href="/logout.php"><?php echo _('Logout'); ?></a></li><?php } ?>
	</ul>
</li>
<?php if (!isMobile()) { ?><li class="menueintrag"<?php if (substr($_SERVER['SCRIPT_NAME'], 1, 5) == 'stat_' OR $_SERVER['SCRIPT_NAME'] == '/top_manager.php' OR $_SERVER['SCRIPT_NAME'] == '/manager_der_saison.php') { echo ' id="current"'; } ?>><a href="/top_manager.php"><?php echo _('Ranking'); ?></a>
	<?php if ($cookie_team != '__'.$cookie_id) { ?>
		<ul>
			<li><a href="/top_manager.php"><?php echo _('Ranking'); ?></a></li>
			<li><a href="/stat_5jahresWertung.php"><?php echo _('Statistiken'); ?></a></li>
			<li><a href="/manager_der_saison.php"><?php echo _('Manager-Wahl'); ?></a></li>
		</ul>
	<?php } ?>
</li><?php } ?>
<li class="menueintrag"<?php if ($_SERVER['SCRIPT_NAME'] == '/transfermarkt.php' OR $_SERVER['SCRIPT_NAME'] == '/marktschreier.php' OR $_SERVER['SCRIPT_NAME'] == '/transfermarkt_leihe.php' OR $_SERVER['SCRIPT_NAME'] == '/lig_transfers.php' OR $_SERVER['SCRIPT_NAME'] == '/beobachtung.php' OR $_SERVER['SCRIPT_NAME'] == '/transferliste.php') { echo ' id="current"'; } ?>><a href="/transfermarkt.php"><?php echo _('Transfers'); ?></a>
	<ul>
		<li><a href="/transfermarkt.php"><?php echo _('Kaufen'); ?></a></li>
		<li><a href="/transfermarkt_leihe.php"><?php echo _('Leihen'); ?></a></li>
		<?php if ($cookie_team != '__'.$cookie_id) { ?><li><a href="/beobachtung.php"><?php echo _('Beobachtung'); ?></a></li><?php } ?>
		<li><a href="/lig_transfers.php"><?php echo _('Abgeschlossen'); ?></a></li>
		<li><a href="/marktschreier.php"><?php echo _('Marktschreier'); ?></a></li>
	</ul>
</li>
<?php if ($cookie_team != '__'.$cookie_id) { ?><li class="menueintrag"<?php if ($_SERVER['SCRIPT_NAME'] == '/kader.php' OR $_SERVER['SCRIPT_NAME'] == '/aufstellung.php' OR $_SERVER['SCRIPT_NAME'] == '/taktik.php' OR $_SERVER['SCRIPT_NAME'] == '/vertraege.php' OR $_SERVER['SCRIPT_NAME'] == '/kalender.php' OR $_SERVER['SCRIPT_NAME'] == '/entwicklung.php') { echo ' id="current"'; } ?>><a href="/aufstellung.php"><?php echo _('Team'); ?></a>
	<ul>
		<li><a href="/aufstellung.php"><?php echo _('Aufstellung'); ?></a></li>
		<li><a href="/taktik.php"><?php echo _('Taktik'); ?></a></li>
		<li><a href="/kader.php"><?php echo _('Kader'); ?></a></li>
		<li><a href="/entwicklung.php"><?php echo _('Entwicklung'); ?></a></li>
		<li><a href="/vertraege.php"><?php echo _('Verträge'); ?></a></li>
		<li><a href="/kalender.php"><?php echo _('Kalender'); ?></a></li>
	</ul>
</li><?php } ?>
<li class="menueintrag"<?php if ($_SERVER['SCRIPT_NAME'] == '/lig_tabelle.php' OR $_SERVER['SCRIPT_NAME'] == '/pokal.php' OR $_SERVER['SCRIPT_NAME'] == '/cup.php' OR $_SERVER['SCRIPT_NAME'] == '/lig_testspiele_liste.php' OR $_SERVER['SCRIPT_NAME'] == '/testWuensche.php') { echo ' id="current"'; } ?>><a href="/lig_tabelle.php"><?php echo _('Saison'); ?></a>
	<ul>
		<li><a href="/lig_tabelle.php"><?php echo _('Liga'); ?></a></li>
		<li><a href="/pokal.php"><?php echo _('Int. Pokal'); ?></a></li>
		<li><a href="/cup.php"><?php echo _('Nat. Cup'); ?></a></li>
		<li><a href="/lig_testspiele_liste.php"><?php echo _('Testspiele'); ?></a></li>
		<li><a href="/testWuensche.php"><?php echo _('Testwünsche'); ?></a></li>
	</ul>
</li>
<?php if ($cookie_team != '__'.$cookie_id && !isMobile()) { ?><li class="menueintrag"<?php if (substr($_SERVER['SCRIPT_NAME'], 1, 4) == 'ver_') { echo ' id="current"'; } ?>><a href="/ver_finanzen.php"><?php echo _('Verein'); ?></a>
	<ul>
		<li><a href="/ver_finanzen.php"><?php echo _('Finanzen'); ?></a></li>
		<li><a href="/ver_buchungen.php"><?php echo _('Buchungen'); ?></a></li>
		<li><a href="/ver_personal.php"><?php echo _('Personal'); ?></a></li>
		<li><a href="/ver_stadion.php"><?php echo _('Stadion'); ?></a></li>
		<li><a href="/ver_lotto.php"><?php echo _('Lotto'); ?></a></li>
	</ul>
</li><?php } ?>
<?php if ($cookie_team != '__'.$cookie_id && !isMobile()) { ?><li class="menueintrag"<?php if ($_SERVER['SCRIPT_NAME'] == '/leihgaben.php' OR $_SERVER['SCRIPT_NAME'] == '/testspiele.php' OR $_SERVER['SCRIPT_NAME'] == '/ligaTausch.php') { echo ' id="current"'; } ?>><a href="/leihgaben.php"><?php echo _('Anfragen'); ?> (<?php echo intval($_SESSION['last_testspiele_anzahl']+$_SESSION['last_leihgaben_anzahl']+$_SESSION['last_ligaTausch_anzahl']); ?>)</a>
	<ul>
		<li><a href="/leihgaben.php"><?php echo _('Leihgaben'); ?> (<?php echo $_SESSION['last_leihgaben_anzahl']; ?>)</a></li>
		<li><a href="/testspiele.php"><?php echo _('Testspiele'); ?> (<?php echo $_SESSION['last_testspiele_anzahl']; ?>)</a></li>
		<li><a href="/ligaTausch.php"><?php echo _('Ligatausch'); ?> (<?php echo $_SESSION['last_ligaTausch_anzahl']; ?>)</a></li>
	</ul>
</li><?php } ?>
<?php if (!isMobile()) { ?><li class="menueintrag"<?php if (substr($_SERVER['SCRIPT_NAME'], 0, 8) == '/support' OR $_SERVER['SCRIPT_NAME'] == '/tipps_des_tages.php' OR $_SERVER['SCRIPT_NAME'] == '/regeln.php' OR $_SERVER['REQUEST_URI'] == ('/post_schreiben.php?id='.CONFIG_OFFICIAL_USER)) { echo ' id="current"'; } ?>><a href="/support.php"><?php echo _('Support'); ?></a>
	<ul>
		<li><a href="/support.php"><?php echo _('Support'); ?></a></li>
		<li><a href="/wio.php#teamList"><?php echo _('Post ans Team'); ?></a></li>
		<li><a href="/tipps_des_tages.php"><?php echo _('Kurztipps'); ?></a></li>
		<li><a href="/regeln.php"><?php echo _('Regeln'); ?></a></li>
		<?php if ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin') { ?><li><a href="/forum.php"><?php echo _('Archiv'); ?></a></li><?php } ?>
	</ul>
</li><?php } ?>
<?php if (!isMobile()) { ?><li class="menueintrag"><a href="/<?php if ($_SESSION['pMaxGebot'] == 1) { echo 'logoutNewUser.php'; } else { echo 'logout.php'; } ?>"><?php echo _('Logout'); ?></a></li><?php } ?>
<?php } ?>
</ul>
<?php } ?>
</div>
<div id="content-wrap">
<div id="sidebar">
<?php if ($loggedin == 0) { ?>
<h1><?php echo _('Login'); ?></h1>
<div class="left-box">
<form action="<?php echo getBaseURL(); ?>/login.php" method="post" accept-charset="utf-8" id="login_form" class="imtext">
<p>
<label for="lusername"><?php echo _('E-Mail / Username:'); ?></label><input type="text" name="lusername" id="lusername" /><br />
<label for="lpassword"><?php echo _('Passwort:'); ?></label><input type="password" name="lpassword" id="lpassword" />
</p>
<p>
<input type="hidden" name="returnURL" value="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" /><input type="submit" value="Einloggen" />
</p>
<p><b><a href="/passwort_vergessen.php"><?php echo _('Passwort vergessen?'); ?></a></b></p>
</form>
</div>
<h1><?php echo _('Demo-Account'); ?></h1>
<div class="left-box">
<p><strong><?php echo _('Username:'); ?></strong> Demo<br /><strong><?php echo _('Passwort:'); ?></strong> demo</p>
</div>
<?php echo $topWidget; ?>
<?php } else { ?>
<div id="top_box_nav">
<a href="/wio.php" class="blue"><?php echo _('Wer ist online?'); ?></a>
<a href="/posteingang.php" class="lightgrey"><?php echo _('Posteingang'); ?> (<?php echo (isset($_SESSION['last_pn_anzahl']) ? $_SESSION['last_pn_anzahl'] : 0); ?> <?php echo _('ungelesen'); ?>)</a>
<a href="/freunde.php" class="red"><?php echo _('Freunde'); ?> (<?php echo (isset($_SESSION['last_freunde_anzahl']) ? $_SESSION['last_freunde_anzahl'] : 0); ?> <?php echo _('Anfragen'); ?>)</a>
<a href="/chat.php" class="grey"><?php echo _('Chat'); ?> (<?php echo (isset($_SESSION['last_chatter_anzahl']) ? $_SESSION['last_chatter_anzahl'] : 0); ?> <?php echo _('online'); ?>)</a>
<a href="/manager.php?id=<?php echo $cookie_id; ?>" class="green"><?php echo _('Mein Profil'); ?></a>
</div>
<?php
if ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin') { // fuer Team das Helfer-Menue
	echo '<h1>'._('Support-Menü').'</h1>';
	echo '<div class="left-box navBlockLinks">';
	echo '<a href="/multiAccounts.php">'._('Multi-Accounts').'</a>';
	echo '<a href="/chat_reports.php">'._('Chat-Reports').'</a>';
	echo '<a href="/geloeschteAccounts.php">'._('Gelöschte Accounts').'</a>';
	echo '<a href="/neueAccounts.php">'._('Neue Accounts').'</a>';
	echo '<a href="/gruendeFuerLoeschung.php">'._('Gründe für Löschung').'</a>';
	echo '<a href="/sanktionen.php">'._('Kontrollzentrum').'</a>';
	echo '</div>';
}

// show random short hints
echo '<h1>'._('Tipp des Tages').' (<a href="/tipps_des_tages.php">'._('Alle').'</a>)</h1>';
echo '<div class="left-box"><p>';
$tipps_des_tages = file('tipps_des_tages.php.txt');
// ignore the first line (PHP tag)
$randomHintIndex = mt_rand(1, count($tipps_des_tages)-1);
// be careful with the input for eval() here (which should only contain a gettext call)
$randomHint = eval($tipps_des_tages[$randomHintIndex]);
echo $randomHint;
echo '</p></div>';

?>
<?php if ($cookie_team != '__'.$cookie_id) { ?>
<?php
$nextGamesHTML = '<h1>'._('Nächste Spiele').'</h1><div class="left-box navBlockLinks matchList">';
$nxt3_zeit = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
$nxt1 = "SELECT id, team1, team2, ergebnis, typ, datum FROM ".$prefix."spiele WHERE (team1 = '".$cookie_teamname."' OR team2 = '".$cookie_teamname."') AND (datum > ".$nxt3_zeit.") ORDER BY datum ASC LIMIT 0, 5";
$nxt2 = mysql_query($nxt1);
while ($nxt3 = mysql_fetch_assoc($nxt2)) {
	if ($nxt3['team1'] == $cookie_teamname) {
		$nxt3_gegner = $nxt3['team2'];
		$nxt3_ergebnis = $nxt3['ergebnis'];
		$homeGuest = '<img width="16" src="/images/house.png" style="vertical-align:middle;">';
	}
	else {
		$nxt3_gegner = $nxt3['team1'];
		$nxt3_ergebnis = ergebnis_drehen($nxt3['ergebnis']);
        $homeGuest = '<img width="16" src="/images/car.png" style="vertical-align:middle;">';
	}
	// LIVE ODER ERGEBNIS ANFANG
	if ($nxt3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $nxt3['datum'])) {
		$ergebnis_live = 'LIVE';
		$lastActionZusatz = '#lastAction';
	}
	else {
		$ergebnis_live = $nxt3_ergebnis;
		$lastActionZusatz = '';
	}
	// LIVE ODER ERGEBNIS ENDE
	$nxt3_typ = substr($nxt3['typ'], 0, 1);
	$nextGamesHTML .= '<a href="/spielbericht.php?id='.$nxt3['id'].$lastActionZusatz.'">'.$homeGuest.' '.$nxt3_typ.': '.$nxt3_gegner.' ('.$ergebnis_live.')</a>';
}
$nextGamesHTML .= '</div>';
echo $nextGamesHTML;
?>
<?php } ?>
<?php echo $topWidget; ?>
<?php } ?>
</div>
<div id="main<?php if ($_SERVER['SCRIPT_NAME'] == '/amazonShop.php') { echo '_full'; } ?>">
