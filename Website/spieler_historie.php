<?php include 'zz1.php'; ?>
<?php
if (!isset($_GET['id'])) { exit; }
$sql1 = "SELECT ids, vorname, nachname, team, jugendTeam FROM ".$prefix."spieler WHERE ids = '".mysql_real_escape_string($_GET['id'])."'";
$sql2 = mysql_query($sql1);
$sql2a = mysql_num_rows($sql2);
if ($sql2a == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
$tm1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$sql3['team']."'";
$tm2 = mysql_query($tm1);
if (mysql_num_rows($tm2) == 0) {
	$aktuellerVerein = _('außerhalb Europas');
}
else {
	$tm3 = mysql_fetch_assoc($tm2);
	$aktuellerVerein = '<a href="/team.php?id='.$sql3['team'].'">'.$tm3['name'].'</a>';
}
?>
<title><?php echo _('Spieler-Historie:'); ?> <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Spieler-Historie:'); ?> <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?></h1>
<p style="text-align:right"><a href="/spieler.php?id=<?php echo $sql3['ids']; ?>" class="pagenava"><?php echo _('Zum Spieler-Profil'); ?></a></p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Von'); ?></th>
<th scope="col"><?php echo _('Zu'); ?></th>
<th scope="col"><?php echo _('Ablöse'); ?></th>
</tr>
</thead>
<tbody>
<tr><td><?php echo _('jetzt'); ?></td><td colspan="3"><?php echo $aktuellerVerein; ?></td></tr>
<?php
function createTeamLink($ids, $name) {
	$str = '';
	if (strlen($ids) == 32) {
		$str .= '<a href="/team.php?id='.$ids.'">';
	}
	if ($name == '') {
		$str .= _('außerhalb Europas');
	}
	else {
		$str .= $name;
	}
	if (strlen($ids) == 32) {
		$str .= '</a>';
	}
	return $str;
}
$a1a = "SELECT bieter, besitzer, datum, gebot FROM ".$prefix."transfers_old WHERE spieler = '".mysql_real_escape_string($_GET['id'])."'";
$a1b = str_replace('_old', '', $a1a);
$a1 = $a1a." UNION ALL ".$a1b." ORDER BY datum DESC";
$a2 = mysql_query($a1) or die(mysql_error());
$counter = 1;
while ($a3 = mysql_fetch_assoc($a2)) {
	$tm1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$a3['besitzer']."'";
	$tm2 = mysql_query($tm1);
	$tm3 = mysql_fetch_assoc($tm2);
	$vonStr = createTeamLink($a3['besitzer'], $tm3['name']);
	$tm1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$a3['bieter']."'";
	$tm2 = mysql_query($tm1);
	$tm3 = mysql_fetch_assoc($tm2);
	$zuStr = createTeamLink($a3['bieter'], $tm3['name']);
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td>'.date('d.m.Y', $a3['datum']).'</td><td>'.$vonStr.'</td><td>'.$zuStr.'</td>';
	if ($a3['gebot'] == 1) {
		echo '<td>'.('Leihgabe').'</td>';
	}
	else {
		echo '<td>'.abloeseSchaetzen($cookie_team, $a3['gebot'], $a3['bieter'], $a3['besitzer']).'</td>';
	}
	echo '</tr>';
	$counter++;
}
// JUGEND-TEAM ANFANG
$youthTeam1 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$sql3['jugendTeam']."'";
$youthTeam2 = mysql_query($youthTeam1);
if (mysql_num_rows($youthTeam2) == 1) {
	$youthTeam3 = mysql_fetch_assoc($youthTeam2);
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td>'._('Jugend').'</td><td colspan="3"><a href="/team.php?id='.$sql3['jugendTeam'].'">'.$youthTeam3['name'].'</a></td>';
	echo '</tr>';
}
// JUGEND-TEAM ENDE
?>
</tbody>
</table>
<?php if ($loggedin == 1 && ($_SESSION['status'] == 'Helfer' || $_SESSION['status'] == 'Admin')) { ?>
<h1><?php echo _('Letzte Gebote [Sichtbar fürs Team]'); ?></h1>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Bieter-IP'); ?></th>
<th scope="col"><?php echo _('Betrag'); ?></th>
</tr>
</thead>
<tbody>
<?php
$a1 = "SELECT datum, bieter, bieterIP, betrag FROM ".$prefix."transfers_gebote WHERE spieler = '".mysql_real_escape_string($_GET['id'])."' ORDER BY datum DESC LIMIT 0, 25";
$a2 = mysql_query($a1);
$counter = 1;
while ($a3 = mysql_fetch_assoc($a2)) {
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td>';
	if ((time()-$a3['datum']) > 259200) {
		echo '<a href="/team.php?id='.$a3['bieter'].'">'.date('d.m.Y H:i:s', $a3['datum']).'</a>';
	}
	else {
		echo date('d.m.Y H:i:s', $a3['datum']);
	}
	echo '</td>';
	echo '<td class="link"><a href="/ipInfo.php?ip='.urlencode($a3['bieterIP']).'">'.$a3['bieterIP'].'</a></td>';
	if ($a3['betrag'] == 1) {
		echo '<td>'._('Leihe').'</td>';
	}
	else {
		echo '<td>'.number_format($a3['betrag'], 0, ',', '.').' €</td>';
	}
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<?php } ?>
<?php include 'zz3.php'; ?>
