<?php include 'zz1.php'; ?>
<title>Pokalsieger | Ballmanager.de</title>
<style type="text/css">
<!--
.team_<?php echo $cookie_team; ?> {
	font-weight: bold;
}
-->
</style>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<h1>Pokalsieger</h1>
<?php if ($loggedin == 1) { ?>
<p>Welches Teams haben den Pokal schon gewonnen? Wer war ihr Finalgegner?</p>
<p>
<table>
<thead>
<tr class="odd">
<th scope="col">Saison</th>
<th scope="col">Sieger</th>
<th scope="col">Finalgegner</th>
</tr>
</thead>
<tbody>
<?php
$torj1 = "SELECT saison, sieger, finalgegner FROM ".$prefix."pokalsieger ORDER BY saison DESC LIMIT 0, 25";
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
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>