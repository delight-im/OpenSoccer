<?php
$kontakt_link1 = '';
$kontakt_link2 = '';
$kontakt1 = "SELECT typ FROM ".$prefix."freunde WHERE f1 = '".$cookie_id."' AND f2 = '".mysql_real_escape_string(trim($_GET['id']))."'";
$kontakt2 = mysql_query($kontakt1);
if ($kontakt2 == FALSE) { exit; }
$kontakt2a = mysql_num_rows($kontakt2);
if ($kontakt2a == 0) {
    $kontakt_link1 = '<tr class="odd"><td colspan="2" class="link"><a href="/freunde_aktion.php?id='.trim($_GET['id']).'&amp;aktion=einladen"'.noDemoClick($cookie_id).'><img width="16" style="vertical-align: middle;" alt="freunde" src="images/user_add.png"> Freundschaft anbieten</a></td></tr>';
	$kontakt_link2 = '<tr><td colspan="2" class="link"><a href="/freunde_aktion.php?id='.trim($_GET['id']).'&amp;aktion=Ignorieren"'.noDemoClick($cookie_id).'><img width="16" style="vertical-align: middle;" alt="block" src="images/user_delete.png"> Diesen User ignorieren</a></td></tr>';
}
else {
	$kontakt3 = mysql_fetch_assoc($kontakt2);
	if ($kontakt3['typ'] == 'F') {
		$kontakt_link1 = '<tr class="odd"><td colspan="2" class="link"><a href="/freunde_aktion.php?id='.trim($_GET['id']).'&amp;aktion=beenden"'.noDemoClick($cookie_id).'><img width="16" style="vertical-align: middle;" alt="freunde" src="images/user_add.png"> Freundschaft beenden</a></td></tr>';
		$kontakt_link2 = '<tr><td colspan="2" class="link"><a href="/freunde_aktion.php?id='.trim($_GET['id']).'&amp;aktion=Ignorieren"'.noDemoClick($cookie_id).'><img width="16" style="vertical-align: middle;" alt="block" src="images/user_delete.png"> Diesen User ignorieren</a></td></tr>';
	}
	elseif ($kontakt3['typ'] == 'B') {
		$kontakt_link1 = '<tr class="odd"><td colspan="2" class="link"><a href="/freunde_aktion.php?id='.trim($_GET['id']).'&amp;aktion=einladen"'.noDemoClick($cookie_id).'><img width="16" style="vertical-align: middle;" alt="freunde" src="images/user_add.png"> Freundschaft anbieten</a></td></tr>';
		$kontakt_link2 = '<tr><td colspan="2" class="link"><a href="/freunde_aktion.php?id='.trim($_GET['id']).'&amp;aktion=StopBlock"'.noDemoClick($cookie_id).'><img width="16" style="vertical-align: middle;" alt="block" src="images/user_delete.png"> Diesen User nicht mehr ignorieren</a></td></tr>';
	}
}
$kanfrage1 = "SELECT COUNT(*) FROM ".$prefix."freunde_anfragen WHERE (von = '".$cookie_id."' AND an = '".mysql_real_escape_string(trim($_GET['id']))."') OR (von = '".mysql_real_escape_string(trim($_GET['id']))."' AND an = '".$cookie_id."')";
$kanfrage2 = mysql_query($kanfrage1);
$kanfrage3 = mysql_result($kanfrage2, 0);
if ($kanfrage3 != 0 OR $_GET['id'] == $cookie_id) {
	$kontakt_link1 = '';
}
$kontakt_link = $kontakt_link1.$kontakt_link2;
?>
