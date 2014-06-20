<?php include 'zz1.php'; ?>
<title>Stadion | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
function getPhrases($words, $maxTerms = 5) {
	$compositions = array();
    for ($start = 0; $start < count($words); $start++) {
       for ($len = 1; $len <= $maxTerms && $len <= count($words)-$start; $len++) {
          $compositions[] = implode(" ", array_slice($words, $start, $len));
       }
    }
	return $compositions;
}
$teamNameParts = getPhrases(explode(' ', $cookie_teamname));
$stadiumAffixes = array('Arena', 'Stadium', 'Stade', 'Estadio', 'Estádio', 'Parc', 'Park', 'Stadio', 'Stadyum');
$stadionPhotos = array(
	0 => array(0, '/images/arena_01.jpg', 'Richard Matthews auf Flickr (Lizenz: Creative Commons BY)'),
	1 => array(30000, '/images/arena_03.jpg', 'Jon Candy auf Flickr (Lizenz: Creative Commons BY-SA)'),
	2 => array(45000, '/images/arena_04.jpg', 'poolie auf Flickr (Lizenz: Creative Commons BY)'),
	3 => array(60000, '/images/arena_05.jpg', 'Lawrie Cate auf Flickr (Lizenz: Creative Commons BY)'),
	4 => array(75000, '/images/arena_06.jpg', 'Steve Cadman auf Flickr (Lizenz: Creative Commons BY-SA)'),
	5 => array(90000, '/images/arena_07.jpg', 'Ralf Peter Reimann auf Flickr (Lizenz: Creative Commons BY-SA)')
);
if (isset($_POST['plaetze']) && isset($_POST['art']) && $cookie_id != DEMO_USER_ID) {
    $val1 = "SELECT plaetze FROM ".$prefix."stadien WHERE team = '".$cookie_team."'";
    $val2 = mysql_query($val1);
    $val3 = mysql_fetch_assoc($val2);
    $val4 = "SELECT konto FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
    $val5 = mysql_query($val4);
    $val6 = mysql_fetch_assoc($val5);
	if ($_POST['art'] == 'erweitert') {
		$plaetze_neu = intval($_POST['plaetze']);
		if ($plaetze_neu > 0) {
            $preis = 11880000+$plaetze_neu*1200;
            if (($val3['plaetze']+$plaetze_neu) <= 100000 && ($val6['konto']-$preis) >= 0) {
                $sql1 = "UPDATE ".$prefix."stadien SET plaetze = plaetze+".$plaetze_neu.", parkplatz = 0, ubahn = 0, restaurant = 0, bierzelt = 0, pizzeria = 0, imbissstand = 0, vereinsmuseum = 0, fanshop = 0 WHERE team = '".$cookie_team."'";
                $sql2 = mysql_query($sql1);
                $sql3 = "UPDATE ".$prefix."teams SET konto = konto-".$preis.", stadion_aus = stadion_aus-".$preis." WHERE ids = '".$cookie_team."'";
                $sql4 = mysql_query($sql3);
                $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Bauarbeiten', -".$preis.", '".time()."')";
                $buch2 = mysql_query($buch1);
                $formulierung = 'Du hast Dein Stadion um '.$plaetze_neu.' Plätze vergrößert.';
                $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Stadion', '".time()."')";
                $sql8 = mysql_query($sql7);
            }
		}
	}
	elseif ($_POST['art'] == 'verkleinert') {
		$plaetze_neu = intval($_POST['plaetze']);
		if ($plaetze_neu > 0) {
            $preis = 3880000+$plaetze_neu*200;
            if (($val3['plaetze']-$plaetze_neu) >= 0 && ($val6['konto']-$preis) >= 0) {
                $sql1 = "UPDATE ".$prefix."stadien SET plaetze = plaetze-".$plaetze_neu.", parkplatz = 0, ubahn = 0, restaurant = 0, bierzelt = 0, pizzeria = 0, imbissstand = 0, vereinsmuseum = 0, fanshop = 0 WHERE team = '".$cookie_team."'";
                $sql2 = mysql_query($sql1);
                $sql3 = "UPDATE ".$prefix."teams SET konto = konto-".$preis.", stadion_aus = stadion_aus-".$preis." WHERE ids = '".$cookie_team."'";
                $sql4 = mysql_query($sql3);
                $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Bauarbeiten', -".$preis.", '".time()."')";
                $buch2 = mysql_query($buch1);
                $formulierung = 'Du hast Dein Stadion um '.$plaetze_neu.' Plätze verkleinert.';
                $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Stadion', '".time()."')";
                $sql8 = mysql_query($sql7);
            }
		}
	}
}
if (isset($_POST['preis']) && $cookie_id != DEMO_USER_ID) {
	if (intval($_POST['preis']) > 19 && intval($_POST['preis']) < 71) {
        $sql1 = "UPDATE ".$prefix."stadien SET preis = ".intval($_POST['preis'])." WHERE team = '".$cookie_team."'";
        $sql2 = mysql_query($sql1);
            $formulierung = 'Du hast die Ticketpreise für Dein Stadion auf '.intval($_POST['preis']).' € gesetzt.';
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Stadion', '".time()."')";
            $sql8 = mysql_query($sql7);
			setTaskDone('ticket_prices');
	}
}
if (isset($_POST['umfeld_bearbeiten']) && $cookie_id != DEMO_USER_ID) {
	if (isset($_POST['parkplatz'])) { $ub_parkplatz = intval($_POST['parkplatz']); } else { $ub_parkplatz = 0; }
	if (isset($_POST['ubahn'])) { $ub_ubahn = intval($_POST['ubahn']); } else { $ub_ubahn = 0; }
	if (isset($_POST['restaurant'])) { $ub_restaurant = intval($_POST['restaurant']); } else { $ub_restaurant = 0; }
	if (isset($_POST['bierzelt'])) { $ub_bierzelt = intval($_POST['bierzelt']); } else { $ub_bierzelt = 0; }
	if (isset($_POST['pizzeria'])) { $ub_pizzeria = intval($_POST['pizzeria']); } else { $ub_pizzeria = 0; }
	if (isset($_POST['imbissstand'])) { $ub_imbissstand = intval($_POST['imbissstand']); } else { $ub_imbissstand = 0; }
	if (isset($_POST['vereinsmuseum'])) { $ub_vereinsmuseum = intval($_POST['vereinsmuseum']); } else { $ub_vereinsmuseum = 0; }
	if (isset($_POST['fanshop'])) { $ub_fanshop = intval($_POST['fanshop']); } else { $ub_fanshop = 0; }
	$sql1 = "UPDATE ".$prefix."stadien SET parkplatz = ".$ub_parkplatz.", ubahn = ".$ub_ubahn.", restaurant = ".$ub_restaurant.", bierzelt = ".$ub_bierzelt.", pizzeria = ".$ub_pizzeria.", imbissstand = ".$ub_imbissstand.", vereinsmuseum = ".$ub_vereinsmuseum.", fanshop = ".$ub_fanshop." WHERE team = '".$cookie_team."'";
	$sql2 = mysql_query($sql1);
}
if (isset($_POST['kuerzel1']) && isset($_POST['kuerzel2']) && isset($_POST['stadt']) && $cookie_id != DEMO_USER_ID) {
	if ($live_scoring_spieltyp_laeuft == '') {
		$kuerzel1 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel1'])));
		$kuerzel2 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel2'])));
		$stadt = mysql_real_escape_string(trim(strip_tags($_POST['stadt'])));
		if ((in_array($kuerzel1, $stadiumAffixes) OR in_array($kuerzel2, $stadiumAffixes)) && in_array($stadt, $teamNameParts)) {
			if ($kuerzel1 != '' && $kuerzel2 != '') {
				echo addInfoBox('Du darfst nur <i>einen</i> Zusatz für den Namen wählen. Bitte entscheide Dich für einen und versuche es noch einmal.');
			}
			else {
				if ($kuerzel2 == '') {
					$neuerName = $kuerzel1.' '.$stadt;
				}
				else {
					$neuerName = $stadt.' '.$kuerzel2;
				}
				$sql1 = "UPDATE ".$prefix."stadien SET name = '".$neuerName."', namePrefix = '".$kuerzel1."', namePostfix = '".$kuerzel2."' WHERE team = '".$cookie_team."'";
				$sql2 = mysql_query($sql1);
				if ($sql2 == FALSE) {
					echo addInfoBox('Es ist ein Fehler aufgetreten.');
				}
				else {
					echo addInfoBox('Dein Stadion heißt jetzt: '.$neuerName);
				}
			}
		}
		else {
			echo addInfoBox('Du musst <i>einen</i> Zusatz wählen. Bitte versuche es noch einmal.');
		}
	}
	else {
		echo addInfoBox('Du kannst den Namen Deines Stadions nur außerhalb der Spielzeiten ändern. Zurzeit laufen '.$live_scoring_spieltyp_laeuft.'spiele.');
	}
}
?>
<?php
$sql1 = "SELECT name, namePrefix, namePostfix, plaetze, preis, parkplatz, ubahn, restaurant, bierzelt, pizzeria, imbissstand, vereinsmuseum, fanshop FROM ".$prefix."stadien WHERE team = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
echo '<h1>'.$sql3['name'].'</h1>';
$sql4 = "SELECT zuschauer FROM ".$prefix."spiele WHERE team1 = '".$cookie_teamname."' AND simuliert = 1 ORDER BY datum DESC LIMIT 0, 1";
$sql5 = mysql_query($sql4);
$sql6 = mysql_fetch_assoc($sql5);
$sql6 = $sql6['zuschauer'];
$gelaende_kosten = $sql3['parkplatz']*30000+$sql3['ubahn']*90000+$sql3['restaurant']*320000+$sql3['bierzelt']*74000+$sql3['pizzeria']*90000+$sql3['imbissstand']*45000+$sql3['vereinsmuseum']*655000+$sql3['fanshop']*160000;
$selectedStadionPhoto = array(0, '', '');
for ($c = 5; $c >= 0; $c--) {
	if ($sql3['plaetze'] >= $stadionPhotos[$c][0]) {
		$selectedStadionPhoto = $stadionPhotos[$c];
		break;
	}
}
echo '<img src="'.$selectedStadionPhoto[1].'" alt="Dein Stadion" title="Dein Stadion" width="540" style="display:block; width:540px; height:200px; border:0;" />';
?>
<table>
<thead>
<tr class="odd">
<th scope="col">Bereich</th>
<th scope="col">Wert</th>
</tr>
</thead>
<tbody>
<tr><td>Kapazität</td><td><?php echo number_format($sql3['plaetze'], 0, ',', '.'); ?> Plätze</td></tr>
<tr class="odd"><td>Preis/Platz</td><td><?php echo $sql3['preis']; ?> €</td></tr>
<tr><td colspan="2">Einnahmen bei voller Auslastung: <?php echo number_format($sql3['plaetze']*$sql3['preis'], 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><td colspan="2">Instandhaltungskosten: <?php echo number_format(1550000+$sql3['plaetze']*250, 0, ',', '.'); ?> € / Saison</td></tr>
<tr class="odd"><td colspan="2">Gelände-Kosten: <?php echo number_format($gelaende_kosten, 0, ',', '.'); ?> € / Saison</td></tr>
<tr><td colspan="2">Zuschauer beim letzten Spiel: <?php echo number_format($sql6, 0, ',', '.'); ?></td></tr>
</tbody>
</table>
</p>
<h1>Namen ändern</h1>
<form action="/ver_stadion.php" method="post" accept-charset="utf-8">
<p><select name="kuerzel1" size="1" style="width:100px"><option value="">&nbsp;-&nbsp;</option>
<?php
foreach ($stadiumAffixes as $stadiumAffix) {
	echo '<option'.($stadiumAffix == $sql3['namePrefix'] ? ' selected="selected"' : '').'>'.$stadiumAffix.'</option>';
}
?>
</select> <select name="stadt" size="1" style="width:200px">
<?php
foreach ($teamNameParts as $teamNamePart) {
	echo '<option>'.$teamNamePart.'</option>';
}
?>
</select> <select name="kuerzel2" size="1" style="width:100px"><option value="">&nbsp;-&nbsp;</option>
<?php
foreach ($stadiumAffixes as $stadiumAffix) {
	echo '<option'.($stadiumAffix == $sql3['namePostfix'] ? ' selected="selected"' : '').'>'.$stadiumAffix.'</option>';
}
?>
</select></p>
<p><input type="submit" value="Namen ändern"<?php echo noDemoClick($cookie_id); ?> /></p>
</form>
<h1>Preis ändern</h1>
<form action="/ver_stadion.php" method="post" accept-charset="utf-8">
<p><select name="preis" size="1" style="width:60px">
	<?php
	for ($i = 20; $i < 71; $i++) {
		echo '<option';
		if ($sql3['preis'] == $i) { echo ' selected="selected"'; }
		echo '>'.$i.' €</option>';
	}
	?>
</select> <input type="submit" value="Ändern" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" /></p>
</form>
<h1>Umbau</h1>
<form action="/ver_stadion.php" method="post" accept-charset="utf-8">
<p>Das Stadion soll um&nbsp;
	<select name="plaetze" size="1" style="width:100px">
		<?php
		$nochBis100Tausend = intval(100000-$sql3['plaetze']);
		if ($nochBis100Tausend > 0) {
			echo '<option value="'.$nochBis100Tausend.'">'.number_format($nochBis100Tausend, 0, ',', '.').'</option>';
		}
		for ($i = 1; $i <= 85; $i++) {
			if ($i > 20 && $i % 5 != 0) { continue; } // ueber 20 nur noch in 5er Schritten
			$platzZahl = $i*1000;
			echo '<option value="'.$platzZahl.'">'.number_format($platzZahl, 0, ',', '.').'</option>';
		}
		?>
	</select>
&nbsp;Plätze <select name="art" size="1" style="width:140px"><option>erweitert</option><option>verkleinert</option></select> werden.</p>
<p><input type="submit" value="Bauen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" /></p>
</form>
<p><strong>Kosten für Erweiterung:</strong> 11.880.000 € + 1.200 €/Platz</p>
<p><strong>Kosten für Verkleinerung:</strong> 3.880.000 € + 200 €/Platz</p>
<p><strong>Instandhaltungskosten:</strong> 1.550.000 € + 250 €/Platz</p>
<p><strong>Maximale Kapazität:</strong> 100.000 Plätze</p>
<p><strong>Wichtig:</strong> Der Stadion-Umbau kann nicht stattfinden, wenn dadurch Schulden entstehen würden.</p>
<p><strong>Information:</strong> Sowohl eine Erweiterung als auch eine Verkleinerung kostet viel Geld. Bei einer Erweiterung steigen die möglichen Einnahmen durch den Kartenverkauf, dagegen steigen jedoch auch die Kosten für die Instandhaltung. Durch eine Verkleinerung kann man die Instandhaltungskosten senken, wodurch aber auch nur noch geringere Einnahmen durch den Kartenverkauf möglich sind.</p>
<h1>Gelände</h1>
<form action="/ver_stadion.php" method="post" accept-charset="utf-8">
<p>
<?php
// kurzname, vollname, 1_pro_x_zuschauer, kosten_pro_1
$gebaeude_a = array(
	array('parkplatz', 'Parkplatz', 7500, 30000),
	array('ubahn', 'U-Bahn', 40000, 90000),
	array('restaurant', 'Restaurant', 15000, 320000),
	array('bierzelt', 'Bierzelt', 20000, 74000),
	array('pizzeria', 'Pizzeria', 12000, 90000),
	array('imbissstand', 'Imbissstand', 10000, 45000),
	array('vereinsmuseum', 'Vereinsmuseum', 50000, 655000),
	array('fanshop', 'Fanshop', 30000, 160000),
);
foreach ($gebaeude_a as $tm) {
	$anzahl = ceil($sql3['plaetze']/$tm[2]);
	$kosten = round($tm[3]*$anzahl);
	echo '<input type="checkbox" name="'.$tm[0].'" value="'.$anzahl.'" ';
	if ($sql3[$tm[0]] > 0) { echo 'checked="checked" '; }
	echo '/> ';
	if ($anzahl < 10) { echo '0'; }
	echo $anzahl.'x '.$tm[1].' &raquo; '.number_format($kosten, 0, ',', '.').' € / Saison<br />';
}
?>
</p>
<p><input type="hidden" name="umfeld_bearbeiten" value="1" /><input type="submit" value="Ändern" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" /></p>
</form>
<p><strong>Information:</strong> Je mehr Service und Unterhaltung Du auf dem Gelände rund ums Stadion anbietest, desto höher werden die Betriebs- und Instandhaltungskosten. Die Fans werden aber lieber ins Stadion gehen und der Kartenverkauf wird besser laufen.</p>
<?php
if (isset($selectedStadionPhoto)) {
	if (isset($selectedStadionPhoto[2])) {
		echo '<p style="font-size:80%; color:#999;">Foto des Stadions: '.$selectedStadionPhoto[2].'</p>';
	}
}
?>
<?php } else { ?>
<h1>Stadion</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>