<?php include 'zz1.php'; ?>
<title><?php echo _('Jüngste Kader'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
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
        $temp_liga_query = " AND a.liga = '".$temp_liga."'";
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
<h1><?php echo _('Jüngste Kader'); ?></h1>
<p><?php echo _('Welche Vereine setzen am stärksten auf die Jugend? Die folgende Liste zeigt die Teams mit den jüngsten Kadern. Bedingung: mindestens 17 Spieler im Kader.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Alter'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT a.ids, a.name, (AVG(b.wiealt)/365) AS avgAlter, COUNT(*) AS anzSpieler FROM ".$prefix."teams AS a, ".$prefix."spieler AS b WHERE a.ids = b.team".$temp_liga_query." GROUP BY b.team HAVING anzSpieler >= 13 ORDER BY avgAlter ASC LIMIT 0, 26";
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 1) { echo '<tr class="team_'.$sql3['ids'].'">'; } else { echo '<tr class="team_'.$sql3['ids'].' odd">'; }
	echo '<td>'.$counter.'.</td><td class="link"><a href="/team.php?id='.$sql3['ids'].'">'.$sql3['name'].'</a></td><td>'.number_format($sql3['avgAlter'], 1, ',', '.').'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Jüngste Kader'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
