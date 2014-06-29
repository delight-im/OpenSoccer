<?php include 'zz1.php'; ?>
<title><?php echo _('IP-Info'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php
if ($loggedin == 1) {
if ($_SESSION['status'] != 'Helfer' && $_SESSION['status'] != 'Admin') { exit; }
if (!isset($_GET['ip'])) { exit; }
$getIP = mysql_real_escape_string(trim(strip_tags($_GET['ip'])));
echo '<h1>'._('IP-Info:').' '.$getIP.'</h1>';
echo '<p>'._('Die folgende Liste zeigt, welche User schon mit der IP-Adresse').' &quot;'.$getIP.'&quot; '._('eingeloggt waren.').'</p>';
?>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('User'); ?></th>
<th scope="col"><?php echo _('Letzter Login'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT DISTINCT(a.user), b.username, b.last_login FROM ".$prefix."loginLog AS a JOIN ".$prefix."users AS b ON a.user = b.ids WHERE a.ip = '".$getIP."'";
$sql2 = mysql_query($sql1);
$counter = 0;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td class="link"><a href="/manager.php?id='.$sql3['user'].'">'.$sql3['username'].'</a></td>';
	echo '<td>';
	if ($sql3['last_login'] == 0) {
		echo '&nbsp;-&nbsp;';
	}
	else {
		echo date('d.m.Y', $sql3['last_login']);
	}
	echo '</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<?php } else { ?>
<h1><?php echo _('IP-Info'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
