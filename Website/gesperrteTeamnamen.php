<?php include 'zz1.php'; ?>
<title><?php echo _('Gesperrte Teamnamen'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Gesperrte Teamnamen'); ?></h1>
<?php if ($loggedin == 1) { ?>
<?php if ($cookie_team != '__'.$cookie_id) { ?>
<?php if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') { ?>
<?php
  $kuerzelListe = array('AC', 
						'AJ', 
						'AS', 
						'ASC', 
						'ASV', 
						'Athletic', 
						'Atletico', 
						'Austria', 
						'AZ', 
						'BC', 
						'BSV', 
						'BV', 
						'Calcio', 
						'CD', 
						'CF', 
						'City', 
						'Club', 
						'Deportivo', 
						'Espanyol', 
						'FC', 
						'FF', 
						'FK', 
						'FSC', 
						'FSG', 
						'FV', 
						'IF', 
						'KV', 
						'Olympique', 
						'OSC', 
						'PSV', 
						'Racing', 
						'Rapid', 
						'Rapids', 
						'RC', 
						'RCD', 
						'Real', 
						'Rovers', 
						'RS', 
						'SG', 
						'SK', 
						'Spartans', 
						'Sporting', 
						'SSC', 
						'Sturm', 
						'SV', 
						'TSV', 
						'TV', 
						'UD', 
						'Union', 
						'United', 
						'Wanderers');
if (isset($_POST['kuerzel']) && isset($_POST['stadt'])) {
	$kuerzel = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel'])));
	$stadt = mysql_real_escape_string(trim(strip_tags($_POST['stadt'])));
	$sql1 = "INSERT INTO ".$prefix."vNameOriginals (stadt, zusatz, helfer) VALUES ('".$stadt."', '".$kuerzel."', '".$cookie_id."')";
	$sql2 = mysql_query($sql1);
}
?>
<form action="/gesperrteTeamnamen.php" method="post" accept-charset="utf-8">
<p><select name="kuerzel" size="1" style="width:100px"><option value="">&nbsp;-&nbsp;</option>
<?php
foreach ($kuerzelListe as $kuerzel) {
	echo '<option>'.$kuerzel.'</option>';
}
?>
</select> <select name="stadt" size="1" style="width:200px">
<?php
$sql4 = "SELECT name FROM ".$prefix."vNamePool ORDER BY name ASC";
$sql5 = mysql_query($sql4);
while ($sql6 = mysql_fetch_assoc($sql5)) {
	echo '<option>'.$sql6['name'].'</option>';
}
?>
</select></p>
<p><input type="submit" value="Namen sperren"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<?php } ?>
<ul>
<?php
$sql1 = "SELECT stadt, zusatz FROM ".$prefix."vNameOriginals ORDER BY stadt ASC";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	echo '<li>'.$sql3['zusatz'].' '.$sql3['stadt'].' / '.$sql3['stadt'].' '.$sql3['zusatz'].'</li>';
}
?>
</ul>
<?php } ?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
