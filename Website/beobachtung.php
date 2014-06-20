<?php include 'zz1.php'; ?>
<title>Beobachtung | Ballmanager.de</title>
<script type="text/javascript">
function checkAll(quelle) {
	for (i = 0; i < document.forms[0].length; i++) {
		if (document.forms[0][i].type == 'checkbox') {
			if (document.forms[0][i].name != quelle.name) {
				document.forms[0][i].checked = !document.forms[0][i].checked;
			}
		}
	}
}
</script>
<?php include 'zz2.php'; ?>
<h1>Beobachtung</h1>
<?php if ($loggedin == 1) { ?>
<p>Die folgenden Spieler stehen auf Deiner Beobachtungsliste. Mit einem Klick auf den Namen kommst Du zum Spielerprofil. Wenn der Spieler zu verkaufen
oder zu verleihen ist, dann ist der Transferstatus auch verlinkt.</p>
<?php
if (isset($_POST['markedAction'])) {
	if (isset($_POST['auswahl'])) {
		if (is_array($_POST['auswahl'])) {
			foreach ($_POST['auswahl'] as $markedEntry) {
				$sql1 = "DELETE FROM ".$prefix."transfermarkt_watch WHERE team = '".$cookie_team."' AND spieler_id = '".$markedEntry."'";
				$sql2 = mysql_query($sql1);
			}
			echo addInfoBox('Es wurden '.count($_POST['auswahl']).' Spieler von Deiner Beobachtungsliste gelöscht.');
		}
	}
}
?>
<form action="/beobachtung.php" name="checkBoxForm" method="post" accept-charset="utf-8">
<p>
<table>
<thead>
<tr class="odd">
<th scope="col"><input type="checkbox" name="checkUncheckAll" onclick="checkAll(this)" /></th>
<th scope="col">MT</th>
<th scope="col">Spieler</th>
<th scope="col">Status</th>
<th scope="col">Alter</th>
<th scope="col">Stärke</th>
<th scope="col">&nbsp;</th>
<th scope="col">Noch</th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT a.spieler_id, a.spieler_name, b.transfermarkt, b.position, b.wiealt, b.staerke, c.bieter_highest, c.ende FROM ".$prefix."transfermarkt_watch AS a JOIN ".$prefix."spieler AS b ON a.spieler_id = b.ids LEFT JOIN ".$prefix."transfermarkt AS c ON a.spieler_id = c.spieler WHERE a.team = '".$cookie_team."' ORDER BY b.transfermarkt DESC, c.ende ASC LIMIT ".$start.", ".$eintraege_pro_seite;
$sql2 = mysql_query($sql1);
$blaetter3 = anzahl_datensaetze_gesamt($sql1);
$counter = $start+1;
while ($sql3 = mysql_fetch_assoc($sql2)) {
	if ($counter % 2 == 1) { echo '<tr>'; } else { echo '<tr class="odd">'; }
	echo '<td><input type="checkbox" name="auswahl[]" value="'.$sql3['spieler_id'].'" /></td>';
	echo '<td>'.$sql3['position'].'</td>';
	echo '<td class="link"><a href="/spieler.php?id='.$sql3['spieler_id'].'">'.$sql3['spieler_name'].'</a></td><td>';
	if ($sql3['transfermarkt'] == 0) { echo 'Unverkäuflich'; }
	elseif ($sql3['transfermarkt'] == 1) { echo '<a href="/transfermarkt_auktion.php?id='.$sql3['spieler_id'].'">Zum Verkauf</a>'; }
	elseif ($sql3['transfermarkt'] > 999998) { echo '<a href="/spieler.php?id='.$sql3['spieler_id'].'">Zur Leihgabe</a>'; }
	echo '</td>';
	echo '<td>'.floor($sql3['wiealt']/365).'</td>';
	echo '<td>'.number_format($sql3['staerke'], 1, ',', '.').'</td>';
	if ($sql3['bieter_highest'] == $cookie_team) {
		echo '<td><img src="//www.ballmanager.de/images/erfolg.png" alt="+" title="Du bist zurzeit der Höchstbietende" /></td>';
	}
	else {
		echo '<td><img src="//www.ballmanager.de/images/fehler.png" alt="-" title="Du bist nicht der Höchstbietende" /></td>';
	}
	echo '<td>';
	if (is_null($sql3['ende'])) {
		echo '&nbsp;';
	}
	else {
		$noch_zeit = intval(($sql3['ende']-time())/60);
		if ($noch_zeit < 61) {
			echo '<span style="color:red" title="'.date('d.m.Y H:i', $sql3['ende']).'">'.$noch_zeit.' min</span>';
		}
		else {
			if ($noch_zeit < 121) {
				echo '<span title="'.date('d.m.Y H:i', $sql3['ende']).'">'.$noch_zeit.' min</span>';
			}
			else {
				echo '<span title="'.date('d.m.Y H:i', $sql3['ende']).'">'.intval($noch_zeit/60).' h</span>';
			}
		}
	}
	echo '</td>';
	echo '</tr>';
	$counter++;
}
?>
</tbody>
</table>
</p>
<p><select name="markedAction" size="1" style="width:200px">
	<option value="DEL">Markierte löschen</option>
</select></p>
<p><input type="submit" value="Ausführen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" /></p>
</form>
<?php
echo '<div class="pagebar">';
$wieviel_seiten = $blaetter3/$eintraege_pro_seite; // ERMITTELN DER SEITENANZAHL FÜR DAS INHALTSVERZEICHNIS
$vorherige = $seite-1;
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite=1">Erste</a> '; } else { echo '<span class="this-page">Erste</span>'; }
if ($seite > 1) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$vorherige.'">Vorherige</a> '; } else { echo '<span class="this-page">Vorherige</span> '; }
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
if ($seite < $wieviel_seiten) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.$naechste.'">Nächste</a> '; } else { echo '<span class="this-page">Nächste</span> '; }
if ($wieviel_seiten > 0) { echo '<a href="'.$_SERVER['SCRIPT_NAME'].'?seite='.ceil($wieviel_seiten).'">Letzte</a>'; } else { echo '<span clss="this-page">Letzte</span>'; }
echo '</div>';
?>
<p><strong>Überschriften:</strong> MT: Mannschaftsteil</p>
<p><strong>Mannschaftsteile:</strong> T: Torwart, A: Abwehr, M: Mittelfeld, S: Sturm</p>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>