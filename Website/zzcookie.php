<?php
@session_set_cookie_params(14*3600*24, '/', '.ballmanager.de');
@session_start();
if (isset($_SESSION['loggedin']) AND $_SESSION['loggedin'] == 1) {
	$loggedin = $_SESSION['loggedin'];
	$cookie_id = $_SESSION['userid'];
	$cookie_username = $_SESSION['username'];
	$cookie_liga = $_SESSION['liga'];
	$cookie_team = $_SESSION['team'];
	$cookie_teamname = $_SESSION['teamname'];
	$cookie_scout = $_SESSION['scout'];
	if (mt_rand(1, 3) == 2) { // nur bei jedem dritten Seitenaufruf
		$last_login1 = "UPDATE ".$prefix."users SET verwarnt = 0, last_login = ".time();
		$last_login1 .= ", last_ip = '".getUserIP()."'";
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$last_login1 .= ", last_uagent = '".mysql_real_escape_string(trim(strip_tags($_SERVER['HTTP_USER_AGENT'])))."'";
		}
		if (isset($_COOKIE['uniqueHash'])) {
			$last_login1 .= ", last_uniqueHash = '".mysql_real_escape_string(trim(strip_tags($_COOKIE['uniqueHash'])))."'";
		}
		$last_login1 .= " WHERE ids = '".$cookie_id."'";
		$last_login2 = mysql_query($last_login1);
	}
}
else {
	$loggedin = 0;
	$cookie_id = '';
	$cookie_username = '';
	$cookie_liga = '';
	$cookie_team = '';
	$cookie_teamname = '';
	$cookie_saison = 0;
	$cookie_spieltag = 0;
	$cookie_scout = 0;
}
if ($loggedin == 1) {
	$ohneRegeln = array('/index.php', 
						'/impressum.php', 
						'/passwort_vergessen.php', 
						'/regeln.php', 
						'/tour.php', 
						'/registrieren.php', 
						'/registrierung.php', 
						'/notizen.php', 
						'/einstellungen.php', 
						'/forum.php', 
						'/forum_thema.php', 
						'/post.php', 
						'/posteingang.php', 
						'/postausgang.php', 
						'/tipps_des_tages.php', 
						'/freunde.php', 
						'/support.php',
						'/supportRequest.php', 
						'/login.php', 
						'/logout.php');
	if (!isset($_SESSION['acceptedRules'])) { $_SESSION['acceptedRules'] = 0; }
	if (!in_array($_SERVER['SCRIPT_NAME'], $ohneRegeln) && $_SESSION['acceptedRules'] == 0) {
		header('Location: /regeln.php');
		exit;
	}
	if ($cookie_team == '__'.$cookie_id) {
		$publicPages = array('/index.php', 
							'/impressum.php', 
							'/passwort_vergessen.php', 
							'/regeln.php', 
							'/tour.php', 
							'/registrieren.php', 
							'/registrierung.php', 
							'/wio.php', 
							'/manager.php', 
							'/notizen.php', 
							'/einstellungen.php', 
							'/lig_tabelle.php', 
							'/transfermarkt.php', 
							'/transfermarkt_leihe.php', 
							'/lig_transfers.php', 
							'/pokal.php', 
							'/cup.php', 
							'/forum.php', 
							'/forum_thema.php', 
							'/chat.php', 
							'/chat_engine.php', 
							'/team.php', 
							'/transfermarkt_auktion.php', 
							'/spieler.php', 
							'/spielbericht.php', 
							'/spieler_historie.php', 
							'/top_manager.php', 
							'/freunde_aktion.php', 
							'/post_schreiben.php', 
							'/post_geschrieben.php', 
							'/post.php', 
							'/forum_eintrag_hinzufuegen.php', 
							'/forum_eintrag_hinzugefuegt.php', 
							'/posteingang.php', 
							'/postausgang.php', 
							'/tipps_des_tages.php', 
							'/freunde.php', 
							'/support.php',
							'/supportRequest.php', 
							'/supportAdd.php', 
							'/login.php', 
							'/logout.php');
		if (!in_array($_SERVER['SCRIPT_NAME'], $publicPages) && substr($_SERVER['SCRIPT_NAME'], 0, 3) != '/aa') {
			header('Location: /index.php');
			exit;
		}
	}
	if ($_SESSION['multiSperre'] == 1 && $_SERVER['SCRIPT_NAME'] != '/multiSperre.php' && $_SERVER['SCRIPT_NAME'] != '/impressum.php' && $_SERVER['SCRIPT_NAME'] != '/regeln.php' && $_SERVER['SCRIPT_NAME'] != '/logout.php' && $_SERVER['SCRIPT_NAME'] != '/einstellungen.php') {
		header('Location: /multiSperre.php');
		exit;
	}
}
?>