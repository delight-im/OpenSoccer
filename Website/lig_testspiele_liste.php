<?php include 'zz1.php'; ?>
<title><?php echo _('Testspiele'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php if ($loggedin == 1) { ?>
<style type="text/css">
<!--
#team_<?php echo md5($cookie_teamname); ?> {
	font-weight: bold;
}
-->
</style>
<?php } ?>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Spieltag auswählen'); ?></h1>
<form action="/lig_testspiele_liste.php" method="get" accept-charset="utf-8">
<p><select name="tag" size="1" style="width:200px">
<?php
if (isset($_GET['tag'])) {
	$chosenStamp = bigintval($_GET['tag']);
}
else {
	$chosenStamp = mktime(23, 00, 00, date('m', time()), date('d', time()), date('Y', time()));
}
for ($i = 1; $i <= 22; $i++) {
	$abzugTemp = '-'.intval(GameTime::getMatchDay()-$i).' days';
	$abzug = getTimestamp($abzugTemp);
	$abzugStamp = mktime(23, 00, 00, date('m', $abzug), date('d', $abzug), date('Y', $abzug));
	$abzugStr = $i.'. Spieltag ('.date('d.m.Y', $abzugStamp).')';
	echo '<option value="'.$abzugStamp.'"';
	if (date('d.m.Y', $abzugStamp) == date('d.m.Y', $chosenStamp)) { echo ' selected="selected"'; }
	echo '>'.$abzugStr.'</option>';
}
?>
</select></p>
<p><input type="submit" value="<?php echo _('Auswählen'); ?>" /></p>
</form>
<h1><?php echo _('Testspiele'); ?></h1>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Heim'); ?></th>
<th scope="col"><?php echo _('Auswärts'); ?></th>
<th scope="col"><?php echo _('Ergebnis'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT id, datum, team1, team2, ergebnis, typ FROM ".$prefix."spiele WHERE typ = 'Test' AND datum = ".$chosenStamp;
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($sql3['team1'] == $cookie_teamname) {
		$teamHier = md5($sql3['team1']);
	}
	else {
		$teamHier = md5($sql3['team2']);
	}
	if ($counter % 2 == 1) { echo '<tr id="team_'.$teamHier.'">'; } else { echo '<tr id="team_'.$teamHier.'" class="odd">'; }
    // LIVE ODER ERGEBNIS ANFANG
    if ($sql3['typ'] == $live_scoring_spieltyp_laeuft && date('d', time()) == date('d', $sql3['datum'])) {
        $ergebnis_live = 'LIVE';
    }
    else {
        $ergebnis_live = $sql3['ergebnis'];
    }
    // LIVE ODER ERGEBNIS ENDE
	echo '<td>'.date('d.m.Y', $sql3['datum']).'</td><td>'.$sql3['team1'].'</td><td>'.$sql3['team2'].'</td><td class="link"><a href="/spielbericht.php?id='.$sql3['id'].'">'.$ergebnis_live.'</a></td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Testspiele'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
