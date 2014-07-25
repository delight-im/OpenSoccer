<?php include 'zz1.php'; ?>
<title><?php echo _('Torjäger'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.team_<?php echo $cookie_team; ?> {
	font-weight: bold;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if ($live_scoring_spieltyp_laeuft != '') {
	echo '<h1>'._('Torjäger').'</h1>';
	echo '<p>'.__('Zurzeit laufen %s-Spiele. Deshalb kannst Du leider die Torjäger-Liste nicht ansehen. Bitte warte, bis die Spiele beendet sind.', $live_scoring_spieltyp_laeuft).'</p>';
	include 'zz3.php';
	exit;
}
?>
<h1><?php echo _('Liga wählen'); ?></h1>
<form action="" method="get" accept-charset="utf-8">
<p><select name="liga" size="1" style="width:200px">
	<option value="alle"><?php echo _('Alle Ligen'); ?></option>
    <?php
    if (isset($_GET['liga'])) {
    	$temp_liga = mysql_real_escape_string(trim(strip_tags($_GET['liga'])));
    }
    else {
    	$temp_liga = 'alle';
    }
    if ($temp_liga == 'alle') {
        $temp_liga_query = "";
    }
    else {
        $temp_liga_query = " WHERE liga = '".$temp_liga."'";
    }
    $shsj1 = "SELECT ids, name FROM ".$prefix."ligen ORDER BY name ASC";
    $shsj2 = mysql_query($shsj1);
    while ($shsj3 = mysql_fetch_assoc($shsj2)) {
        echo '<option value="'.$shsj3['ids'].'"';
        if ($shsj3['ids'] == $temp_liga) { echo ' selected="selected"'; }
        echo '>'.$shsj3['name'].'</option>';
    }
    ?>
</select>
<input type="submit" value="<?php echo _('Auswählen'); ?>" /></p>
</form>
<h1><?php echo _('Torjäger'); ?></h1>
<p><?php echo _('In dieser Torjägerliste sind die erfolgreichsten Torschützen aller Ligen aufgelistet.'); ?></p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Name'); ?></th>
<th scope="col"><?php echo _('Tore'); ?></th>
</tr>
</thead>
<tbody>
<?php
setTaskDone('check_scorers');
$torj1 = "SELECT ids, vorname, nachname, tore, team FROM ".$prefix."spieler".$temp_liga_query." ORDER BY tore DESC LIMIT 0, 25";
$torj2 = mysql_query($torj1);
$counter = 1;
while ($torj3 = mysql_fetch_assoc($torj2)) {
	if ($counter % 2 == 1) { echo '<tr class="team_'.$torj3['team'].'">'; } else { echo '<tr class="team_'.$torj3['team'].' odd">'; }
	echo '<td>'.$counter.'</td><td class="link"><a href="/spieler.php?id='.$torj3['ids'].'">'.$torj3['vorname'].' '.$torj3['nachname'].'</a></td><td>'.$torj3['tore'].'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<?php } else { ?>
<h1><?php echo _('Torjäger'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
