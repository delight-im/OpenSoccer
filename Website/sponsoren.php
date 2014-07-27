<?php include 'zz1.php'; ?>
<title><?php echo _('Sponsoren'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Sponsoren'); ?></h1>
<?php if ($loggedin == 1) { ?>
<?php if ($cookie_team != '__'.$cookie_id) { ?>
<?php
function get_sponsoren_angebot($prozentsatz, $elo) {
	// Prozentsatz 70 sagt aus: 70 Prozent fuer Antritte 30 Prozent fuer Siege
	$mein_anteil = 36000*$elo+40000000;
	$angebot_a = $mein_anteil*$prozentsatz/100; // bei Prozentsatz 70 sollen 70 Prozent hieraus kommen
	$angebot_b = $mein_anteil*(100-$prozentsatz)/100; // bei Prozentsatz 70 sollen 30 Prozent hieraus kommen
	$angebot_a = round($angebot_a/22); // Antrittspraemie muss auf 22 Spiele verteilt werden
	$angebot_b = round($angebot_b/10); // Siegpraemie muss auf 10 Spiele verteilt werden da das als gut gilt
	return array($angebot_a, $angebot_b);
}
$spon1 = "SELECT sponsor FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$spon2 = mysql_query($spon1);
$spon3 = mysql_fetch_assoc($spon2);
$spon3 = $spon3['sponsor'];
// EIGENES RKP HOLEN ANFANG
$getelo3 = "SELECT elo FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$getelo3 = mysql_query($getelo3);
$getelo3 = mysql_fetch_assoc($getelo3);
$getelo3 = intval($getelo3['elo']);
// EIGENES RKP HOLEN ENDE
if (isset($_GET['id']) && $cookie_id != CONFIG_DEMO_USER) {
	if ($spon3 == 0) {
        $upd1 = "SELECT name, prozentsatz FROM ".$prefix."sponsoren WHERE id = ".intval($_GET['id']);
        $upd2 = mysql_query($upd1);
        $upd2a = mysql_num_rows($upd2);
        if ($upd2a == 1) {
            $upd3 = mysql_fetch_assoc($upd2);
            $earnings = get_sponsoren_angebot($upd3['prozentsatz'], $getelo3);
            $upd4 = "UPDATE ".$prefix."teams SET sponsor = ".intval($_GET['id']).", sponsor_a = ".$earnings[0].", sponsor_s = ".$earnings[1]." WHERE ids = '".$cookie_team."'";
            $upd5 = mysql_query($upd4);
            // PROTOKOLL ANFANG
            $formulierung = __('Du hast %1$s als Sponsor gewählt: Antrittsprämie %2$s € und Siegprämie %3$s €.', $upd3['name'], number_format($earnings[0], 0, ',', '.'), number_format($earnings[1], 0, ',', '.'));
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Finanzen', '".time()."')";
            $sql8 = mysql_query($sql7);
            // PROTOKOLL ENDE
			setTaskDone('get_sponsor');
        }
	}
}
?>
<?php
$spon1 = "SELECT sponsor, sponsor_a, sponsor_s FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$spon2 = mysql_query($spon1);
$spon3a = mysql_fetch_assoc($spon2);
$spon3 = $spon3a['sponsor'];
if ($spon3 != 0) {
	$spon4 = "SELECT name FROM ".$prefix."sponsoren WHERE id = ".$spon3;
	$spon5 = mysql_query($spon4);
	$spon6 = mysql_fetch_assoc($spon5);
	$spon6 = $spon6['name'];
	echo '<p>'.__('Du hast für die aktuelle Saison %s als Sponsor ausgewählt.', $spon6).'</p>';
	echo '<p>'.__('Als Antrittsprämie bekommst Du %1$s € und als Siegprämie %2$s €.', number_format($spon3a['sponsor_a'], 0, ',', '.'), number_format($spon3a['sponsor_s'], 0, ',', '.')).'</p>';
}
else {
?>
<table>
<thead>
<tr class="odd">
<th scope="col"><?php echo _('Sponsor'); ?></th>
<th scope="col"><?php echo _('Antrittsprämie'); ?></th>
<th scope="col"><?php echo _('Siegprämie'); ?></th>
<th scope="col"><?php echo _('Vertrag'); ?></th>
</tr>
</thead>
<tbody>
<?php
$rechner_optionen = '';
$sql1 = "SELECT id, name, prozentsatz FROM ".$prefix."sponsoren";
$sql2 = mysql_query($sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$angebote_vom_sponsor = get_sponsoren_angebot($sql3['prozentsatz'], $getelo3);
	echo '<tr><td>'.$sql3['name'].'</td><td>'.number_format($angebote_vom_sponsor[0], 0, ',', '.').' €</td><td>'.number_format($angebote_vom_sponsor[1], 0, ',', '.').' €</td><td>';
	echo '<a href="/sponsoren.php?id='.$sql3['id'].'" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Abschließen').'</a></td></tr>';
	$rechner_optionen .= '<option value="'.$sql3['id'].'">'.$sql3['name'].'</option>';
}
?>
</tbody>
</table>
<h1><?php echo _('Rechner'); ?></h1>
<p><?php echo _('Mit diesem Rechner kannst Du Dir ausrechnen lassen, wie viel Du von einem Sponsor bekommen würdest. Wähle dazu bitte einen Sponsor
und die erwartete Anzahl von Siegen aus.'); ?></p>
<form action="/sponsoren.php" method="post" accept-charset="utf-8">
<p><select name="rechner_sponsor" size="1" style="width:200px"><?php echo $rechner_optionen; ?></select></p>
<p><select name="rechner_siege" size="1" style="width:200px">
<?php
for ($i = 0; $i <= 22; $i++) {
	echo '<option value="'.$i.'">'.__('%d Siege', $i).'</option>';
}
?>
</select></p>
<p><input type="submit" value="<?php echo _('Rechnen'); ?>" /></p>
</form>
<?php
if (isset($_POST['rechner_sponsor']) && isset($_POST['rechner_siege'])) {
	$rechner_sponsor = intval($_POST['rechner_sponsor']);
	$rechner_siege = intval($_POST['rechner_siege']);
	$r1 = "SELECT name, prozentsatz FROM ".$prefix."sponsoren WHERE id = ".$rechner_sponsor;
	$r2 = mysql_query($r1);
	$r3 = mysql_fetch_assoc($r2);
	$angebote_vom_sponsor = get_sponsoren_angebot($r3['prozentsatz'], $getelo3);
	$angebote_vom_sponsor[0] = $angebote_vom_sponsor[0]*22;
	$angebote_vom_sponsor[1] = $angebote_vom_sponsor[1]*$rechner_siege;
	echo '<p style="color:red">'.__('%1$s würde Dir bei %2$d Siegen %3$s € an Antrittsprämien und %4$s € an Siegprämien zahlen.', $r3['name'], $rechner_siege, number_format($angebote_vom_sponsor[0], 0, ',', '.'), number_format($angebote_vom_sponsor[1], 0, ',', '.')).'</p>';
}
?>
<?php } ?>
<?php } ?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
