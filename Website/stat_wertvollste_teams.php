<?php include 'zz1.php'; ?>
<title><?php echo _('Wertvollste Teams'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
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
<input type="submit" value="Auswählen" /></p>
</form>
<h1><?php echo _('Wertvollste Teams'); ?></h1>
<p><?php echo _('Der Marktwert eines Teams ist die Summer aller Marktwerte der einzelnen Spieler. In dieser Tabelle wurden alle Teams nach ihrem Marktwert sortiert aufgelistet.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Marktwert'); ?></th>
</tr>
</thead>
<tbody>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/stat_wertvollste_teams'.$temp_liga.'.html';
if (file_exists($tmp_dateiname)) {
	if (filemtime($tmp_dateiname) > getTimestamp('-1 day')) {
		$tmp_liga_cache = file_get_contents($tmp_dateiname);
		if (strlen($tmp_liga_cache) > 0) {
			$ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
			echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
            $temp = TRUE;
		}
	}
}
if ($temp == FALSE) {
    $tmp_liga_cache = '';
    $sql1 = "SELECT a.ids, a.name, SUM(b.marktwert) AS teamwert FROM ".$prefix."teams AS a JOIN ".$prefix."spieler AS b ON a.ids = b.team WHERE a.staerke > 1".$temp_liga_query." GROUP BY b.team ORDER BY teamwert DESC LIMIT 0, 20";
    $sql2 = mysql_query($sql1);
    $counter = 1;
    while ($sql3 = mysql_fetch_assoc($sql2)) {
        if ($counter % 2 == 1) { $tmp_liga_cache .= '<tr class="team_'.$sql3['ids'].'">'; } else { $tmp_liga_cache .= '<tr class="team_'.$sql3['ids'].' odd">'; }
        $tmp_liga_cache .= '<td>'.$counter.'.</td><td class="link"><a href="/team.php?id='.$sql3['ids'].'">'.$sql3['name'].'</a></td><td>'.number_format($sql3['teamwert'], 0, ',', '.').' €</td>';
        $tmp_liga_cache .= '</tr>';
        $counter++;
    }
    $datei = fopen($tmp_dateiname, 'w+');
    fwrite($datei, $tmp_liga_cache);
    fclose($datei);
    $ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
    echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Wertvollste Teams'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
