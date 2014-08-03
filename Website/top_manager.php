<?php include 'zz1.php'; ?>
<title><?php echo _('Rangliste'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php if ($loggedin == 1) { ?>
<style type="text/css">
<!--
#user_<?php echo $cookie_id; ?> {
	font-weight: bold;
}
-->
</style>
<?php
}
$filter_land = '';
if (isset($_GET['land'])) {
	$filter_land = htmlspecialchars(trim(strip_tags($_GET['land'])));
}
?>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Rangliste'); ?><?php if ($filter_land != '') { echo ' für '.$filter_land; } ?></h1>
<?php if ($loggedin == 1) { ?>
<?php
setTaskDone('overall_ranking');
if ($cookie_team != '__'.$cookie_id) {
	$sql1 = "SELECT liga, elo FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) != 0) {
		$sql3 = mysql_fetch_assoc($sql2);
		$nql1 = "SELECT COUNT(*) FROM ".$prefix."teams AS a JOIN ".$prefix."users AS b ON a.ids = b.team WHERE a.elo > ".$sql3['elo'];
		$nql2 = mysql_query($nql1);
		$nql3 = mysql_result($nql2, 0);
		$meinePositionsSeite = ceil((intval($nql3) + 1) / $eintraege_pro_seite);
		echo '<p style="text-align:right"><a href="/top_manager.php?seite='.$meinePositionsSeite.'" class="pagenava">'._('Meine Position').'</a>';
		if (mb_strlen($sql3['liga']) == 32) {
			$ownCountry1 = "SELECT land FROM ".$prefix."ligen WHERE ids = '".$sql3['liga']."'";
			$ownCountry2 = mysql_query($ownCountry1);
			if (mysql_num_rows($ownCountry2) == 1) {
				$ownCountry3 = mysql_fetch_assoc($ownCountry2);
				echo '<a href="/top_manager.php?land='.urlencode($ownCountry3['land']).'" class="pagenava">'._('Mein Land').'</a>';
			}
		}
		echo '</p>';
	}
}
?>
<p><?php echo _('Welcher Manager ist der erfolgreichste? Die Rangliste zeigt alle Manager, geordnet nach dem RKP ihrer Vereine.'); ?></p>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Manager'); ?></th>
<th scope="col"><?php echo _('Team'); ?></th>
<th scope="col"><?php echo _('RKP'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT a.ids, a.username, a.status, b.ids AS team, b.name, b.elo FROM ".$prefix."users AS a";
if ($filter_land != '') {
	$sql1 .= " RIGHT";
}
$sql1 .= " JOIN ".$prefix."teams AS b ON a.team = b.ids";
if ($filter_land != '') {
	$getCountryLigen1 = "SELECT ids FROM ".$prefix."ligen WHERE land = '".mysql_real_escape_string($filter_land)."' LIMIT 0, 4";
	$getCountryLigen2 = mysql_query($getCountryLigen1);
	$getCountryLigen = array();
	while ($getCountryLigen3 = mysql_fetch_assoc($getCountryLigen2)) {
		$getCountryLigen[] = "'".$getCountryLigen3['ids']."'";
	}
	if (count($getCountryLigen) == 4) {
		$sql1 .= " WHERE b.liga IN (".implode(',', $getCountryLigen).")";
	}
}
$sql1 .= " ORDER BY b.elo DESC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
$counter = $start+1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	$showUsername = _('Computer');
	$usernameCSS = '';
	if (!is_null($sql3['username']) && !is_null($sql3['username'])) {
		$showUsername = displayUsername($sql3['username'], $sql3['ids']);
		$usernameCSS = ' class="link"';
	}
	if ($counter % 2 == 1) { echo '<tr id="user_'.$sql3['ids'].'">'; } else { echo '<tr class="odd" id="user_'.$sql3['ids'].'">'; }
	echo '<td>'.$counter.'.</td><td'.$usernameCSS.'>'.$showUsername.'</td>';
	echo '<td class="link"><a href="/team.php?id='.$sql3['team'].'">'.$sql3['name'].'</a></td>';
	echo '<td>'.number_format($sql3['elo'], 0, ',', '.').'</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
$naechste = $seite+1;
$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$vor4.'">'.$vor4.'</a> '; }
$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$vor3.'">'.$vor3.'</a> '; }
$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$vor2.'">'.$vor2.'</a> '; }
$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$vor1.'">'.$vor1.'</a> '; }
echo '<span class="this-page">'.$seite.'</span> ';
$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$nach1.'">'.$nach1.'</a> '; }
$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$nach2.'">'.$nach2.'</a> '; }
$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$nach3.'">'.$nach3.'</a> '; }
$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$nach4.'">'.$nach4.'</a> '; }
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?land='.$filter_land.'&amp;seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
echo '</div>';
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!') ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
