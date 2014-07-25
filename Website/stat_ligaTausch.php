<?php include 'zz1.php'; ?>
<title><?php echo _('Getauschte Ligen'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<h1><?php echo _('Getauschte Ligen'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><?php echo _('Welcher Manager ist wann in welches Land gegangen? Mit welchem Team hat er getauscht?'); ?></p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Manager'); ?></th>
<th scope="col"><?php echo _('Von'); ?></th>
<th scope="col"><?php echo _('Nach'); ?></th>
</tr>
</thead>
<tbody>
<?php
// LIGA-ID TO LAND ANFANG
$sql1 = "SELECT ids, land FROM ".$prefix."ligen";
$sql2 = mysql_query($sql1);
$ligaIDToLand = array();
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$ligaIDToLand[$sql3['ids']] = $sql3['land'];
}
// LIGA-ID TO LAND ANFANG
$sql1 = "SELECT a.user1, a.newLiga1, a.newLiga2, a.zeit, b.username FROM ".$prefix."ligaChanges AS a JOIN ".$prefix."users AS b ON a.user1 = b.ids ORDER BY a.zeit DESC";
$sql2 = mysql_query($sql1);
$counter = 1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 1) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td>'.date('d.m.Y H:i', $sql3['zeit']).'</td>';
	echo '<td>'.displayUsername($sql3['username'], $sql3['user1']).'</td>';
	echo '<td class="link"><a href="/lig_tabelle.php?liga='.$sql3['newLiga2'].'">'; if (isset($ligaIDToLand[$sql3['newLiga2']])) { echo $ligaIDToLand[$sql3['newLiga2']]; } else { echo '?'; } echo '</a></td>';
	echo '<td class="link"><a href="/lig_tabelle.php?liga='.$sql3['newLiga1'].'">'; if (isset($ligaIDToLand[$sql3['newLiga1']])) { echo $ligaIDToLand[$sql3['newLiga1']]; } else { echo '?'; } echo '</a></td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
