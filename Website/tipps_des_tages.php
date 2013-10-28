<?php include 'zz1.php'; ?>
<title>Tipps des Tages | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1>Tipps durchsuchen</h1>
<form action="/tipps_des_tages.php" method="get" accept-charset="utf-8">
<p><input type="text" name="q" style="width:200px" /> <input type="submit" value="Suchen" /></p>
</form>
<?php
setTaskDone('open_shorttips');
if (isset($_GET['q'])) { $q = mysql_real_escape_string(trim(strip_tags($_GET['q']))); } else { $q = ''; }
if ($q == '') {
	echo '<h1>Tipps des Tages</h1>';
}
else {
	echo '<h1>Tipps des Tages zum Thema &quot;'.$q.'&quot;</h1>';
}
?>
<p><strong>Hier findest Du alle <i>Tipps des Tages</i> - gesammelt auf einer Seite.</strong></p>
<?php
$tippList = file('tipps_des_tages.txt');
$counter = 0;
foreach ($tippList as $tippEntry) {
	$counter++;
	if ($q != '') {
		if (strpos($tippEntry, $q) === FALSE) {
			continue;
		}
	}
	echo '<p><b>'.sprintf('%03s', $counter).'.</b> '.$tippEntry.'</p>';
}
?>
<?php } else { ?>
<h1>Tipps des Tages</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>