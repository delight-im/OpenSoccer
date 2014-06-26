<?php include 'zz1.php'; ?>
<title><?php echo _('Tausch-Wünsche'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
$clean22DaysTimeout = getTimestamp('-22 days');
$clean22Days1 = "DELETE FROM ".$prefix."ligaChangeWuensche WHERE zeit < ".$clean22DaysTimeout;
$clean22Days2 = mysql_query($clean22Days1);
$ownLand1 = "SELECT land FROM ".$prefix."ligen WHERE ids = '".$cookie_liga."'";
$ownLand2 = mysql_query($ownLand1);
$ownLand3 = mysql_fetch_assoc($ownLand2);
$ownLand4 = $ownLand3['land'];
if (isset($_POST['meinWunsch'])) {
	$meinWunsch = mysql_real_escape_string(trim(strip_tags($_POST['meinWunsch'])));
	if ($meinWunsch == 'no') {
		$sql1 = "DELETE FROM ".$prefix."ligaChangeWuensche WHERE teamID = '".$cookie_team."'";
		$sql2 = mysql_query($sql1);
	}
	else {
		$sql1 = "INSERT INTO ".$prefix."ligaChangeWuensche (teamID, teamName, landNoch, landWunsch, zeit) VALUES ('".$cookie_team."', '".$cookie_teamname."', '".$ownLand4."', '".$meinWunsch."', ".time().") ON DUPLICATE KEY UPDATE landWunsch = '".$meinWunsch."'";
		$sql2 = mysql_query($sql1);
	}
}
$ownWunsch1 = "SELECT landWunsch FROM ".$prefix."ligaChangeWuensche WHERE teamID = '".$cookie_team."'";
$ownWunsch2 = mysql_query($ownWunsch1);
if (mysql_num_rows($ownWunsch2) == 0) {
	$ownWunsch4 = '';
}
else {
	$ownWunsch3 = mysql_fetch_assoc($ownWunsch2);
	$ownWunsch4 = $ownWunsch3['landWunsch'];
}
echo '<h1>'._('Mein Wunsch').'</h1>';
echo '<p style="text-align:right"><a href="/ligaTausch.php" class="pagenava">'._('Liga tauschen').'</a></p>';
echo '<form action="/ligaTauschWuensche.php" method="post" accept-charset="utf-8">';
echo '<p><select name="meinWunsch" size="1" style="width:200px">';
$wunschLand1 = "SELECT land FROM ".$prefix."ligen GROUP BY land ORDER BY land ASC";
$wunschLand2 = mysql_query($wunschLand1);
echo '<option value="no">'._('Kein Wunsch').'</option>';
while ($wunschLand3 = mysql_fetch_assoc($wunschLand2)) {
	if ($wunschLand3['land'] == $ownLand4) { continue; }
	echo '<option';
	if ($ownWunsch4 == $wunschLand3['land']) { echo ' selected="selected"'; }
	echo '>'.$wunschLand3['land'].'</option>';
}
echo '</select></p>';
echo '<p><input type="submit" value="Speichern"'.noDemoClick($cookie_id).' /></p>';
echo '</form>';
$sql1 = "SELECT teamID, teamName, landNoch, landWunsch FROM ".$prefix."ligaChangeWuensche ORDER BY landWunsch ASC";
$sql2 = mysql_query($sql1);
echo '<h1>'._('Tausch-Wünsche').'</h1>';
echo '<p><table><thead><tr class="odd"><th scope="col">'._('Team').'</th><th scope="col">'._('Land').'</th><th scope="col">'._('Wunsch').'</th></tr></thead><tbody>';
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	echo '<tr'; if ($counter % 2 == 1) { echo ' class="odd"'; } if ($sql3['landWunsch'] == $ownLand4) { echo ' style="font-weight:bold"'; } echo '>';
	echo '<td class="link"><a href="/team.php?id='.$sql3['teamID'].'">'.$sql3['teamName'].'</a></td>';
	echo '<td>'.$sql3['landNoch'].'</td>';
	echo '<td>'.$sql3['landWunsch'].'</td>';
	echo '</tr>';
	$counter++;
}
echo '</tbody></table></p>';
echo '<p><strong>'._('Hinweis:').'</strong> '._('Jeder Eintrag wird nach 22 Tagen wieder gelöscht, kann dann aber erneut eingestellt werden.').'</p>';
?>
<?php } else { ?>
<h1><?php echo _('Tausch-Wünsche'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
