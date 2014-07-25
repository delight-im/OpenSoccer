<?php include 'zz1.php'; ?>
<title><?php echo _('Chat-Reports'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<style type="text/css">
<!--
td.sperrRelevant_1 {
	background-color: #f00;
}
td.sperrRelevant_0 {
	background-color: #fff;
}
-->
</style>

<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if ($_SESSION['status'] == 'Helfer' OR $_SESSION['status'] == 'Admin') {
	echo '<h1>'._('Chat-Reports').'</h1>';
	if (isset($_GET['user']) && isset($_GET['reporter']) && isset($_GET['datum'])) {
		$q1 = mysql_real_escape_string(trim(strip_tags($_GET['user'])));
		$q2 = mysql_real_escape_string(trim(strip_tags($_GET['reporter'])));
		$q3 = mysql_real_escape_string(trim(strip_tags($_GET['datum'])));
		$sql1 = "SELECT protokoll FROM ".$prefix."chatroomReportedUsers WHERE user = '".$q1."' AND reporter = '".$q2."' AND datum = '".$q3."' LIMIT 0, 1";
		$sql2 = mysql_query($sql1);
		if (mysql_num_rows($sql2) == 1) {
			$sql3 = mysql_fetch_assoc($sql2);
			echo maskIPs($sql3['protokoll']);
		}
		else {
			echo '<p>'._('Kein Protokoll gefunden!').'</p>';
		}
	}
	else {
		if (isset($_GET['aufheben']) && isset($_GET['reporter']) && isset($_GET['datum'])) {
			$q1 = mysql_real_escape_string(trim(strip_tags($_GET['aufheben'])));
			$q2 = mysql_real_escape_string(trim(strip_tags($_GET['reporter'])));
			$q3 = mysql_real_escape_string(trim(strip_tags($_GET['datum'])));
			$sql1 = "UPDATE ".$prefix."chatroomReportedUsers SET sperrRelevant = 0 WHERE user = '".$q1."' AND reporter = '".$q2."' AND datum = '".$q3."' LIMIT 1";
			$sql2 = mysql_query($sql1);
			if (mysql_affected_rows() == 1) {
				addInfoBox(_('Die Sperre durch den ausgewählten Report wurde aufgehoben.'));
			}
			else {
				addInfoBox(_('Es konnte kein Report gefunden werden, dessen Sperre aufgehoben werden soll.'));
			}
		}
		echo '<p>';
		echo '<table>';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col">&nbsp;</th>';
		echo '<th scope="col">'._('Gemeldet').'</th>';
		echo '<th scope="col">'._('Report von').'</th>';
		echo '<th scope="col">'._('Datum').'</th>';
		echo '<th scope="col">'._('Sperre').'</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$sql1 = "SELECT a.user, a.reporter, a.datum, a.sperrRelevant, b.username AS userNick, c.username AS reporterNick FROM ".$prefix."chatroomReportedUsers AS a JOIN ".$prefix."users AS b ON a.user = b.ids JOIN ".$prefix."users AS c ON a.reporter = c.ids ORDER BY a.datum DESC LIMIT ".$start.", ".$eintraege_pro_seite;
		$sql2 = mysql_query($sql1);
		$blaetter3 = anzahl_datensaetze_gesamt($sql1);
		while ($sql3 = mysql_fetch_assoc($sql2)) {
			echo '<tr>';
			echo '<td class="sperrRelevant_'.intval($sql3['sperrRelevant']).'">&nbsp;</td>';
			echo '<td class="link">'.displayUsername($sql3['userNick'], $sql3['user']).'</td>';
			echo '<td class="link">'.displayUsername($sql3['reporterNick'], $sql3['reporter']).'</td>';
			echo '<td class="link"><a href="/chat_reports.php?user='.urlencode($sql3['user']).'&amp;reporter='.urlencode($sql3['reporter']).'&amp;datum='.urlencode($sql3['datum']).'">'.$sql3['datum'].'</a></td>';
			if ($sql3['sperrRelevant'] == 1) {
				echo '<td class="link"><a href="/chat_reports.php?seite='.intval($seite).'&amp;aufheben='.urlencode($sql3['user']).'&amp;reporter='.urlencode($sql3['reporter']).'&amp;datum='.urlencode($sql3['datum']).'" onclick="return confirm(\''._('Bist Du sicher?').'\')">'._('Aufheben').'</a></td>';
			}
			else {
				echo '<td>&nbsp;</td>';
			}
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</p>';
		echo '<div class="pagebar">';
		$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
		$vorherige = $seite-1;
		if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite=1">'._('Erste').'</a> '; } else { echo '<span class="this-page">'._('Erste').'</span>'; }
		if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vorherige.'">'._('Vorherige').'</a> '; } else { echo '<span class="this-page">'._('Vorherige').'</span> '; }
		$naechste = $seite+1;
		$vor4 = $seite-4; if ($vor4 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor4.'">'.$vor4.'</a> '; }
		$vor3 = $seite-3; if ($vor3 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor3.'">'.$vor3.'</a> '; }
		$vor2 = $seite-2; if ($vor2 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor2.'">'.$vor2.'</a> '; }
		$vor1 = $seite-1; if ($vor1 > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vor1.'">'.$vor1.'</a> '; }
		echo '<span class="this-page">'.$seite.'</span> ';
		$nach1 = $seite+1; if ($nach1 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach1.'">'.$nach1.'</a> '; }
		$nach2 = $seite+2; if ($nach2 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach2.'">'.$nach2.'</a> '; }
		$nach3 = $seite+3; if ($nach3 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach3.'">'.$nach3.'</a> '; }
		$nach4 = $seite+4; if ($nach4 < $wieviel_seiten+1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$nach4.'">'.$nach4.'</a> '; }
		if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$naechste.'">'._('Nächste').'</a> '; } else { echo '<span class="this-page">'._('Nächste').'</span> '; }
		if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.ceil($wieviel_seiten).'">'._('Letzte').'</a>'; } else { echo '<span clss="this-page">'._('Letzte').'</span>'; }
		echo '</div>';
	}
}
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
