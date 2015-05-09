<?php include 'zz1.php'; ?>
<title><?php echo _('Cupsieger'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
.team_<?php echo md5($cookie_teamname); ?> {
	font-weight: bold;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Land wählen'); ?></h1>
<form action="" method="get" accept-charset="utf-8">
<p><select name="land" size="1" style="width:200px">
    <?php
	$sql1 = "SELECT land FROM ".$prefix."ligen WHERE ids = '".$cookie_liga."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) { exit; }
	$sql3 = mysql_fetch_assoc($sql2);
	$meinLand = mysql_real_escape_string($sql3['land']);
    if (isset($_GET['land'])) {
    	$temp_land = mysql_real_escape_string(trim(strip_tags($_GET['land'])));
    }
    else {
    	$temp_land = $meinLand;
    }
    $shsj1 = "SELECT land FROM ".$prefix."ligen GROUP BY land ORDER BY land ASC";
    $shsj2 = mysql_query($shsj1);
    while ($shsj3 = mysql_fetch_assoc($shsj2)) {
        echo '<option value="'.$shsj3['land'].'"';
        if ($shsj3['land'] == $temp_land) { echo ' selected="selected"'; }
        echo '>'.$shsj3['land'].'</option>';
    }
    ?>
</select>
<input type="submit" value="<?php echo _('Auswählen'); ?>" /></p>
</form>
<h1><?php echo _('Cupsieger'); ?></h1>
<p><?php echo _('Welches Teams haben den Cup schon gewonnen? Wer war ihr Finalgegner?'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Saison'); ?></th>
<th scope="col"><?php echo _('Sieger'); ?></th>
<th scope="col"><?php echo _('Finalgegner'); ?></th>
</tr>
</thead>
<tbody>
<?php
$torj1 = "SELECT saison, sieger, finalgegner FROM ".$prefix."cupsieger WHERE land = '".$temp_land."' ORDER BY saison DESC LIMIT 0, 25";
$torj2 = mysql_query($torj1);
$counter = 1;
while ($torj3 = mysql_fetch_assoc($torj2)) {
	echo '<tr class="team_'.md5($torj3['sieger']).' team_'.md5($torj3['finalgegner']);
	if ($counter % 2 == 0) { echo ' odd'; }
	echo '">';
	echo '<td>'.$torj3['saison'].'</td><td>'.$torj3['sieger'].'</td><td>'.$torj3['finalgegner'].'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Cupsieger'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
