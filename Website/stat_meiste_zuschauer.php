<?php include 'zz1.php'; ?>
<title><?php echo _('Meiste Zuschauer'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
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
<h1><?php echo _('Meiste Zuschauer'); ?></h1>
<p><?php echo _('Hier sind die Teams mit dem höchsten Zuschauerschnitt in der aktuellen Saison aufgelistet.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Zuschauer i.D.'); ?></th>
</tr>
</thead>
<tbody>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/stat_meiste_zuschauer'.$temp_liga.'.html';
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
    $torj1 = "SELECT a.ids, a.name, AVG(b.zuschauer) AS zuschauerschnitt FROM ".$prefix."teams AS a JOIN ".$prefix."spiele AS b ON a.name = b.team1 WHERE b.simuliert = 1".$temp_liga_query." GROUP BY b.team1 ORDER BY zuschauerschnitt DESC LIMIT 0, 25";
    $torj2 = mysql_query($torj1);
    $counter = 1;
    while ($torj3 = mysql_fetch_assoc($torj2)) {
		if ($counter % 2 == 1) { $tmp_liga_cache .= '<tr class="team_'.$torj3['ids'].'">'; } else { $tmp_liga_cache .= '<tr class="team_'.$torj3['ids'].' odd">'; }
		$tmp_liga_cache .= '<td>'.$counter.'</td><td class="link"><a href="/team.php?id='.$torj3['ids'].'">'.$torj3['name'].'</a></td><td>'.number_format($torj3['zuschauerschnitt'], 0, ',', '.').'</td>';
		$tmp_liga_cache .= '</tr>';
		$counter++;
    }
    $datei = fopen($tmp_dateiname, 'w+');
    fwrite($datei, $tmp_liga_cache);
    fclose($datei);
    echo $tmp_liga_cache;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Meiste Zuschauer'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
