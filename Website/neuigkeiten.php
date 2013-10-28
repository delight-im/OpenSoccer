<?php include 'zz1.php'; ?>
<title>Neuigkeiten | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1>Neuigkeiten durchsuchen</h1>
<form action="/neuigkeiten.php" method="get" accept-charset="utf-8">
<p><input type="text" name="q" style="width:200px" /> <input type="submit" value="Suchen" /></p>
</form>
<?php
if (isset($_GET['q'])) { $q = mysql_real_escape_string(trim(strip_tags($_GET['q']))); } else { $q = ''; }
if ($q == '') {
	echo '<h1>Neuigkeiten</h1>';
}
else {
	echo '<h1>Neuigkeiten zum Thema &quot;'.$q.'&quot;</h1>';
}
?>
<?php
$newsList = file('neuigkeiten.txt');
foreach ($newsList as $newsEntry) {
	if ($q != '') {
		if (strpos($newsEntry, $q) === FALSE) {
			continue;
		}
	}
	$newsEntryDate = substr($newsEntry, 0, 10);
	$newsEntryText = substr($newsEntry, 11);
	echo '<p><b>'.$newsEntryDate.'</b> '.$newsEntryText.'</p>';
}
?>
<?php } else { ?>
<h1>Neuigkeiten</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>