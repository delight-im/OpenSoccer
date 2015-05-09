<?php include 'zz1.php'; ?>
<title><?php echo _('Meiste Titel'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
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
        $temp_liga_query = " WHERE a.liga = '".$temp_liga."'";
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
<h1><?php echo _('Meiste Titel'); ?></h1>
<?php
// TYPEN-FILTER ANFANG
$filterSQL = "(a.cupsiege+a.meisterschaften+a.pokalsiege)";
$filterTyp = '';
if (isset($_GET['typ'])) {
	$filterTyp = mysql_real_escape_string(trim(strip_tags($_GET['typ'])));
	switch ($filterTyp) {
		case 'Cup': $filterSQL = "a.cupsiege"; break;
		case 'Liga': $filterSQL = "a.meisterschaften"; break;
		case 'Pokal': $filterSQL = "a.pokalsiege"; break;
		default: $filterSQL = "titelGesamt"; break;
	}
}
echo '<p style="text-align:right">';
$standardLink = '<a href="/stat_meiste_titel.php?liga='.$temp_liga.'&amp;typ=';
echo $standardLink.'" class="pagenava'; if ($filterTyp == '') { echo ' aktiv'; } echo '">'._('Alle').'</a> '.$standardLink.'Cup" class="pagenava'; if ($filterTyp == 'Cup') { echo ' aktiv'; } echo '">'._('Cup').'</a> '.$standardLink.'Liga" class="pagenava'; if ($filterTyp == 'Liga') { echo ' aktiv'; } echo '">'._('Liga').'</a> '.$standardLink.'Pokal" class="pagenava'; if ($filterTyp == 'Pokal') { echo ' aktiv'; } echo '">'._('Pokal').'</a>';
echo '</p>';
// TYPEN FILTER ENDE
?>
<p><?php echo _('In dieser Tabelle sind die 20 Teams mit den meisten gewonnenen Titeln aufgelistet. Pokalsiege, Cupsiege und Meisterschaften werden für das Ranking einfach addiert.'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Manager'); ?></th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('Titel'); ?></th>
<th scope="col"><?php echo _('Cup'); ?></th>
<th scope="col"><?php echo _('Liga'); ?></th>
<th scope="col"><?php echo _('Pokal'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT b.ids, b.team AS teamID, b.username, a.name, a.cupsiege, a.meisterschaften, a.pokalsiege, (a.cupsiege+a.meisterschaften+a.pokalsiege) AS titelGesamt FROM ".$prefix."users AS b JOIN ".$prefix."teams AS a ON b.team = a.ids".$temp_liga_query." ORDER BY ".$filterSQL." DESC LIMIT 0, 20";
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 1) { echo '<tr class="team_'.$sql3['ids'].'">'; } else { echo '<tr class="team_'.$sql3['ids'].' odd">'; }
	echo '<td>'.$counter.'.</td><td class="link"><a href="/manager.php?id='.$sql3['ids'].'">'.$sql3['username'].'</a></td><td class="link"><a href="/team.php?id='.$sql3['teamID'].'">'.$sql3['name'].'</a></td><td>'.$sql3['titelGesamt'].'</td><td>'.$sql3['cupsiege'].'</td><td>'.$sql3['meisterschaften'].'</td><td>'.$sql3['pokalsiege'].'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<h1><?php echo _('Meiste Titel'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
