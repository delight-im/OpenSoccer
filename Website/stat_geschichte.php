<?php include 'zz1.php'; ?>
<title><?php echo _('Geschichte'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Tabelle wählen'); ?></h1>
<?php
$spieltag = 22;
$sql1 = "SELECT saison FROM ".$prefix."zeitrechnung LIMIT 0, 1";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
$saison = $sql3['saison']-1;
if (isset($_GET['saison_spieltag']) && isset($_GET['liga'])) {
    $temp_ge = mysql_real_escape_string(trim(strip_tags($_GET['saison_spieltag'])));
    $temp_li = mysql_real_escape_string(trim(strip_tags($_GET['liga'])));
}
else {
    $temp_ge = $saison.'-'.$spieltag;
    $temp_li = $cookie_liga;
}
?>
<form action="" method="get" accept-charset="utf-8">
<p><select name="saison_spieltag" size="1" style="width:200px">
    <?php
    $temp = explode('-', $temp_ge, 2);
    $saison_ak = $temp[0];
    $spieltag_ak = $temp[1];
    $temp_ge_query = "WHERE liga = '".$temp_li."' AND saison = ".$saison_ak." AND spieltag = ".$spieltag_ak;
    $shsj1 = "SELECT saison, spieltag FROM ".$prefix."geschichte_tabellen GROUP BY saison, spieltag ORDER BY saison DESC, spieltag DESC LIMIT 0, 44";
    $shsj2 = mysql_query($shsj1);
    while ($shsj3 = mysql_fetch_assoc($shsj2)) {
    	$sa_sp_string = $shsj3['saison'].'-'.$shsj3['spieltag'];
        echo '<option value="'.$sa_sp_string.'"';
        if ($shsj3['saison'] == $saison_ak && $shsj3['spieltag'] == $spieltag_ak) { echo ' selected="selected"'; }
        echo '>Saison '.$shsj3['saison'].', Spieltag '.$shsj3['spieltag'].'</option>';
    }
    ?>
</select></p>
<p><select name="liga" size="1" style="width:200px">
    <?php
    $shsj1 = "SELECT ids, name FROM ".$prefix."ligen ORDER BY name ASC";
    $shsj2 = mysql_query($shsj1);
    while ($shsj3 = mysql_fetch_assoc($shsj2)) {
        echo '<option value="'.$shsj3['ids'].'"';
        if ($shsj3['ids'] == $temp_li) { echo ' selected="selected"'; }
        echo '>'.$shsj3['name'].'</option>';
    }
    ?>
</select></p>
<p><input type="submit" value="<?php echo _('Auswählen') ?>" /></p>
</form>
<h1><?php echo _('Geschichte'); ?></h1>
<p><?php echo _('Hier kannst Du Dir die Tabellen aller Ligen von jedem Spieltag der letzten Saisons ansehen.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Platz'); ?></th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Punkte'); ?></th>
<th scope="col"><?php echo _('Tore'); ?></th>
<th scope="col"><?php echo _('Differenz'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT team, platz, punkte, tore, gegentore FROM ".$prefix."geschichte_tabellen ".$temp_ge_query." ORDER BY platz ASC";
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 1) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td>'.$counter.'</td><td>'.$sql3['team'].'</td><td>'.$sql3['punkte'].'</td><td>'.$sql3['tore'].':'.$sql3['gegentore'].'</td><td>'.intval($sql3['tore']-$sql3['gegentore']).'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Geschichte'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
