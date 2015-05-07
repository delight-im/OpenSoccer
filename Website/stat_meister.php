<?php include 'zz1.php'; ?>
<title><?php echo _('Meister'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.team_<?php echo cleanCSSclass($cookie_teamname); ?> {
	font-weight: bold;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Liga wählen'); ?></h1>
<form action="" method="get" accept-charset="utf-8">
<p><select name="liga" size="1" style="width:200px">
    <?php
    if (isset($_GET['liga'])) {
    	$temp_liga = mysql_real_escape_string(trim(strip_tags($_GET['liga'])));
    }
    else {
    	$temp_liga = $cookie_liga;
    }
	$temp_liga_query = " WHERE liga = '".$temp_liga."' AND spieltag = 22 AND platz = 1";
    $shsj1 = "SELECT ids, name FROM ".$prefix."ligen WHERE hoch = 'KEINE' ORDER BY name ASC";
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
<h1><?php echo _('Meister'); ?></h1>
<p><?php echo _('Wer wurde in meiner Liga in den letzten Saisons Meister? Wie viele Punkte hatten diese Teams jeweils?'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Saison'); ?></th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Punkte'); ?></th>
<th scope="col"><?php echo _('Tore'); ?></th>
</tr>
</thead>
<tbody>
<?php
$torj1 = "SELECT saison, team, punkte, tore, gegentore FROM ".$prefix."geschichte_tabellen".$temp_liga_query." ORDER BY saison DESC LIMIT 0, 25";
$torj2 = mysql_query($torj1);
$counter = 1;
while ($torj3 = mysql_fetch_assoc($torj2)) {
	if ($counter % 2 == 1) { echo '<tr class="team_'.cleanCSSclass($torj3['team']).'">'; } else { echo '<tr class="team_'.cleanCSSclass($torj3['team']).' odd">'; }
	echo '<td>'.$torj3['saison'].'</td><td>'.$torj3['team'].'</td><td>'.$torj3['punkte'].'</td><td>'.$torj3['tore'].':'.$torj3['gegentore'].'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Meister'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
