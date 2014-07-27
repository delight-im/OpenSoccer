<?php
if (!isset($_GET['id']) OR !isset($_GET['datum'])) { exit; }
include 'zzserver.php';
include 'zzcookie.php';
$team = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
if ($cookie_id != CONFIG_DEMO_USER) {
	$datum = bigintval($_GET['datum']);
	$gt1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$team."'";
	$gt2 = mysql_query($gt1);
	if (mysql_num_rows($gt2) == 0) { exit; }
	$gt3 = mysql_fetch_assoc($gt2);
	$name = $gt3['name'];
	$sql1 = "INSERT INTO ".$prefix."testspiel_anfragen (team1, team1_name, team2, datum, zeit) VALUES ('".$cookie_team."', '".$cookie_teamname."', '".$team."', ".$datum.", ".time().")";
	$sql2 = mysql_query($sql1);
	// PROTOKOLL ANFANG
	$formulierung = 'Du hast dem Verein <a href="/team.php?id='.$team.'">'.$name.'</a> ein Angebot für ein Testspiel gemacht.';
	$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Termine', ".time().")";
	$sql8 = mysql_query($sql7);
	$formulierung = 'Du hast eine Anfrage für ein Testspiel vom Verein <a href="/team.php?id='.$cookie_team.'">'.$cookie_teamname.'</a> bekommen.';
	$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$team."', '".$formulierung."', 'Termine', ".time().")";
	$sql8 = mysql_query($sql7);
	// PROTOKOLL ENDE
}
header('Location: /team.php?id='.$team.'&action=requestedFriendly');
?>
