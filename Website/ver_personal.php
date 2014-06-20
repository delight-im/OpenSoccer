<?php include 'zz1.php'; ?>
<title>Personal | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
if (!isset($_SESSION['supplyDemandPrices'])) {
	$supplyDemandPrices1 = "SELECT * FROM ".$prefix."supplyDemandPrices";
	$supplyDemandPrices2 = mysql_query($supplyDemandPrices1);
	while ($supplyDemandPrices3 = mysql_fetch_assoc($supplyDemandPrices2)) {
		$supplyDemandPrices[$supplyDemandPrices3['item']] = $supplyDemandPrices3['price'];
	}
	$_SESSION['supplyDemandPrices'] = serialize($supplyDemandPrices);
}
else {
	$supplyDemandPrices = unserialize($_SESSION['supplyDemandPrices']);
}
// WENN LETZTE AENDERUNG VOM ALTEN MANAGER DANN EGAL ANFANG
$getRegdate1 = "SELECT regdate FROM ".$prefix."users WHERE ids = '".$cookie_id."'";
$getRegdate2 = mysql_query($getRegdate1);
if (mysql_num_rows($getRegdate2) == 0) { exit; }
$getRegdate3 = mysql_fetch_assoc($getRegdate2);
$getRegdate = bigintval($getRegdate3['regdate']);
$timeout = getTimestamp('-22 days');
if ($timeout < $getRegdate) { $timeout = $getRegdate; }
// WENN LETZTE AENDERUNG VOM ALTEN MANAGER DANN EGAL ENDE
$getkonto1 = "SELECT konto FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$getkonto2 = mysql_query($getkonto1);
$getkonto3 = mysql_fetch_assoc($getkonto2);
$getkonto4 = $getkonto3['konto']-einsatz_in_auktionen($cookie_team);
if (isset($_POST['fitness_regeneration']) && $cookie_id != DEMO_USER_ID) {
	$heute_string = date('Y-m-d', time());
	$temp = intval($_POST['fitness_regeneration']);
	if ($temp >= 1 && $temp <= 3) {
		if ($getkonto4 > 0) {
			$ch1 = "SELECT letzte_regeneration FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
			$ch2 = mysql_query($ch1);
			$ch3 = mysql_fetch_assoc($ch2);
			if ($ch3['letzte_regeneration'] != $heute_string) {
				switch ($temp) {
					case 1: $preis = $supplyDemandPrices['Fitness-Trainer']; break;
					case 2: $preis = $supplyDemandPrices['Fitness-Trainer']*2.15; break;
					default: $preis = $supplyDemandPrices['Fitness-Trainer']*3.3; break;
				}
				$upd1 = "UPDATE ".$prefix."spieler SET frische = frische+".$temp." WHERE team = '".$cookie_team."'";
				$upd2 = mysql_query($upd1);
				$upd2a = mysql_affected_rows();
				if ($upd2a > 0) {
					$upd1 = "UPDATE ".$prefix."spieler SET frische = 100 WHERE team = '".$cookie_team."' AND frische > 100";
					$upd2 = mysql_query($upd1);
					$upd3 = "UPDATE ".$prefix."teams SET letzte_regeneration = '".$heute_string."', konto = konto-".$preis." WHERE ids = '".$cookie_team."'";
					$upd4 = mysql_query($upd3);
					$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Regenerations-Camp', -".$preis.", ".time().")";
					$buch2 = mysql_query($buch1);
					// PROTOKOLL ANFANG
					$formulierung = 'Dein Fitness-Trainer hat ein Regenerations-Camp ('.$temp.'%) für '.$upd2a.' Spieler gebucht.';
					$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Assistenten', '".time()."')";
					$sql8 = mysql_query($sql7);
					// PROTOKOLL ENDE
					// PREIS ERHOHEN ANFANG
					$sd1 = "UPDATE ".$prefix."supplyDemandPrices SET price = price*1.04 WHERE item = 'Fitness-Trainer'";
					$sd2 = mysql_query($sd1);
					// PREIS ERHOEHEN ENDE
					echo addInfoBox($formulierung);
				}
			}
			else {
				echo addInfoBox('Deine Mannschaft befindet sich schon im Regenerations-Camp.');
			}
		}
	}
}
if (isset($_POST['physio_behandlung']) && $cookie_id != DEMO_USER_ID) {
	$heute_string = date('Y-m-d', time());
	$temp = intval($_POST['physio_behandlung']);
	if ($temp == 1 OR $temp == 2) {
		if ($getkonto4 > 0) {
			$ch1 = "SELECT letzte_physio FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
			$ch2 = mysql_query($ch1);
			$ch3 = mysql_fetch_assoc($ch2);
			if ($ch3['letzte_physio'] != $heute_string) {
				if ($temp == 1) {
					$preis = $supplyDemandPrices['Physiotherapeut'];
					$tag_singular_plural = 'Tag';
				}
				else {
					$preis = $supplyDemandPrices['Physiotherapeut']*2.15;
					$tag_singular_plural = 'Tage';
				}
				$upd1 = "UPDATE ".$prefix."spieler SET verletzung = verletzung-".$temp." WHERE team = '".$cookie_team."'";
				$upd2 = mysql_query($upd1);
				$upd1 = "UPDATE ".$prefix."spieler SET verletzung = 0 WHERE team = '".$cookie_team."' AND verletzung < 0";
				$upd2 = mysql_query($upd1);
				$upd3 = "UPDATE ".$prefix."teams SET letzte_physio = '".$heute_string."', konto = konto-".$preis." WHERE ids = '".$cookie_team."'";
				$upd4 = mysql_query($upd3);
				$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Physiotherapeut', -".$preis.", ".time().")";
				$buch2 = mysql_query($buch1);
				// PROTOKOLL ANFANG
				$formulierung = 'Dein Physiotherapeut hat Deine verletzten Spieler behandelt und die Ausfallzeit um '.$temp.' '.$tag_singular_plural.' reduziert.';
				$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Assistenten', '".time()."')";
				$sql8 = mysql_query($sql7);
				// PROTOKOLL ENDE
				// PREIS ERHOHEN ANFANG
				$sd1 = "UPDATE ".$prefix."supplyDemandPrices SET price = price*1.06 WHERE item = 'Physiotherapeut'";
				$sd2 = mysql_query($sd1);
				// PREIS ERHOEHEN ENDE
				echo addInfoBox($formulierung);
			}
			else {
				echo addInfoBox('Deine Mannschaft befindet sich schon in Behandlung.');
			}
		}
	}
}
if (isset($_POST['psychologe_behandlung']) && $cookie_id != DEMO_USER_ID) {
	$heute_string = date('Y-m-d', time());
	$temp = intval($_POST['psychologe_behandlung']);
	if ($temp == 2 OR $temp == 5) {
		if ($getkonto4 > 0) {
			$ch1 = "SELECT letzte_psychologe FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
			$ch2 = mysql_query($ch1);
			$ch3 = mysql_fetch_assoc($ch2);
			if ($ch3['letzte_psychologe'] != $heute_string) {
				if ($temp == 2) {
					$preis = $supplyDemandPrices['Psychologe']*2.15;
					$tag_singular_plural = 'Punkte';
				}
				else {
					$preis = $supplyDemandPrices['Psychologe']*5.575;
					$tag_singular_plural = 'Punkte';
				}
				$upd1 = "UPDATE ".$prefix."spieler SET moral = moral+".$temp." WHERE team = '".$cookie_team."'";
				$upd2 = mysql_query($upd1);
				$upd1 = "UPDATE ".$prefix."spieler SET moral = 100 WHERE team = '".$cookie_team."' AND moral > 100";
				$upd2 = mysql_query($upd1);
				$upd3 = "UPDATE ".$prefix."teams SET letzte_psychologe = '".$heute_string."', konto = konto-".$preis." WHERE ids = '".$cookie_team."'";
				$upd4 = mysql_query($upd3);
				$buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Psychologe', -".$preis.", ".time().")";
				$buch2 = mysql_query($buch1);
				// PROTOKOLL ANFANG
				$formulierung = 'Dein Psychologe hat Deine unmotivierten Spieler betreut und ihre Moral um '.$temp.' '.$tag_singular_plural.' erhöht.';
				$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Assistenten', '".time()."')";
				$sql8 = mysql_query($sql7);
				// PROTOKOLL ENDE
				// PREIS ERHOHEN ANFANG
				$sd1 = "UPDATE ".$prefix."supplyDemandPrices SET price = price*1.06 WHERE item = 'Psychologe'";
				$sd2 = mysql_query($sd1);
				// PREIS ERHOEHEN ENDE
				echo addInfoBox($formulierung);
			}
			else {
				echo addInfoBox('Deine Mannschaft wird im Moment schon betreut.');
			}
		}
	}
}
?>
<h1>Achtung</h1>
<p>Du kannst Dein Personal nur alle 22 Tage austauschen. Überlege Dir also gut, welche Assistenten Du wählst.</p>
<h1>Jugendtrainer</h1>
<p>Je höher die Stufe für die Kompetenz des Jugendtrainers ist, desto stärker sind die jungen Spieler, die aus den Nachwuchsmannschaften in Dein Team kommen.<br />
<?php
if (isset($_POST['jugendtrainer']) && $cookie_id != DEMO_USER_ID) {
	$temp = intval($_POST['jugendtrainer']);
	if ($temp >= 1 && $temp <= 5) {
		$ch1 = "UPDATE ".$prefix."personal_changes SET zeit = ".time()." WHERE team = '".$cookie_team."' AND personal = 'Jugendtrainer' AND zeit < ".$timeout;
		$ch2 = mysql_query($ch1);
		if (mysql_affected_rows() > 0) {
            $upd1 = "UPDATE ".$prefix."teams SET jugendarbeit = ".$temp." WHERE ids = '".$cookie_team."'";
            $upd2 = mysql_query($upd1);
            // PROTOKOLL ANFANG
            $formulierung = 'Du hast einen Jugendtrainer der Stufe '.$temp.' eingestellt.';
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Assistenten', '".time()."')";
            $sql8 = mysql_query($sql7);
            // PROTOKOLL ENDE
			setTaskDone('change_workers');
        }
        else {
            $ch1 = "SELECT zeit FROM ".$prefix."personal_changes WHERE team = '".$cookie_team."' AND personal = 'Jugendtrainer'";
            $ch2 = mysql_query($ch1);
            $ch3 = mysql_fetch_assoc($ch2);
        	addInfoBox('Du kannst jeden Assistenten nur alle 22 Tage austauschen. Du musst also noch bis zum '.date('d.m.Y H:i', getTimestamp('+22 days', $ch3['zeit'])).' Uhr warten.');
        }
	}
}
$sql1 = "SELECT jugendarbeit FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
?>
<div style="float:left; width:280px;">
<form action="/ver_personal.php" method="post" accept-charset="utf-8">
<p>
<input type="radio" name="jugendtrainer" value="1" <?php if ($sql3['jugendarbeit'] == 1) { echo 'checked="checked" '; } ?>/> Stufe 1 (8.000.000 € pro Saison)<br />
<input type="radio" name="jugendtrainer" value="2" <?php if ($sql3['jugendarbeit'] == 2) { echo 'checked="checked" '; } ?>/> Stufe 2 (16.000.000 € pro Saison)<br />
<input type="radio" name="jugendtrainer" value="3" <?php if ($sql3['jugendarbeit'] == 3) { echo 'checked="checked" '; } ?>/> Stufe 3 (24.000.000 € pro Saison)<br />
<input type="radio" name="jugendtrainer" value="4" <?php if ($sql3['jugendarbeit'] == 4) { echo 'checked="checked" '; } ?>/> Stufe 4 (32.000.000 € pro Saison)<br />
<input type="radio" name="jugendtrainer" value="5" <?php if ($sql3['jugendarbeit'] == 5) { echo 'checked="checked" '; } ?>/> Stufe 5 (40.000.000 € pro Saison)<br />
<input type="submit" value="Ändern"<?php echo noDemoClick($cookie_id); ?> />
</p>
</form>
</div>
<div style="float:left; width:220px;"><img src="//www.ballmanager.de/images/personal_youth.jpg" alt="Jugendtrainer" width="220" style="width:220; height:150; border:0;" /></div>
<div style="clear:both;"></div>
<h1>Fanbetreuer</h1>
<p>Je höher die Stufe für die Kompetenz des Fanbetreuers ist, desto wohler fühlen sich die Fans Deines Vereins. Sie werden lieber ins Stadion gehen und Du wirst mehr Karten verkaufen.<br />
<?php
if (isset($_POST['fanbetreuer']) && $cookie_id != DEMO_USER_ID) {
	$temp = intval($_POST['fanbetreuer']);
	if ($temp >= 1 && $temp <= 5) {
		$ch1 = "UPDATE ".$prefix."personal_changes SET zeit = ".time()." WHERE team = '".$cookie_team."' AND personal = 'Fanbetreuer' AND zeit < ".$timeout;
		$ch2 = mysql_query($ch1);
		if (mysql_affected_rows() > 0) {
            $upd1 = "UPDATE ".$prefix."teams SET fanbetreuer = ".$temp." WHERE ids = '".$cookie_team."'";
            $upd2 = mysql_query($upd1);
            // PROTOKOLL ANFANG
            $formulierung = 'Du hast einen Fanbetreuer der Stufe '.$temp.' eingestellt.';
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Assistenten', '".time()."')";
            $sql8 = mysql_query($sql7);
            // PROTOKOLL ENDE
			setTaskDone('change_workers');
        }
        else {
            $ch1 = "SELECT zeit FROM ".$prefix."personal_changes WHERE team = '".$cookie_team."' AND personal = 'Fanbetreuer'";
            $ch2 = mysql_query($ch1);
            $ch3 = mysql_fetch_assoc($ch2);
        	addInfoBox('Du kannst jeden Assistenten nur alle 22 Tage austauschen. Du musst also noch bis zum '.date('d.m.Y H:i', getTimestamp('+22 days', $ch3['zeit'])).' Uhr warten.');
        }
	}
}
?>
<?php
$sql1 = "SELECT fanbetreuer FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
?>
<form action="/ver_personal.php" method="post" accept-charset="utf-8">
<p>
<input type="radio" name="fanbetreuer" value="1" <?php if ($sql3['fanbetreuer'] == 1) { echo 'checked="checked" '; } ?>/> Stufe 1 (6.000.000 € pro Saison)<br />
<input type="radio" name="fanbetreuer" value="2" <?php if ($sql3['fanbetreuer'] == 2) { echo 'checked="checked" '; } ?>/> Stufe 2 (12.000.000 € pro Saison)<br />
<input type="radio" name="fanbetreuer" value="3" <?php if ($sql3['fanbetreuer'] == 3) { echo 'checked="checked" '; } ?>/> Stufe 3 (18.000.000 € pro Saison)<br />
<input type="radio" name="fanbetreuer" value="4" <?php if ($sql3['fanbetreuer'] == 4) { echo 'checked="checked" '; } ?>/> Stufe 4 (24.000.000 € pro Saison)<br />
<input type="radio" name="fanbetreuer" value="5" <?php if ($sql3['fanbetreuer'] == 5) { echo 'checked="checked" '; } ?>/> Stufe 5 (30.000.000 € pro Saison)<br />
<input type="submit" value="Ändern"<?php echo noDemoClick($cookie_id); ?> />
</p>
</form>
<h1>Scout</h1>
<p>Je höher die Stufe für die Kompetenz des Scouts ist, desto besser werden seine Schätzungen für die maximale Stärke eines Spielers.<br />
<?php
if (isset($_POST['scout']) && $cookie_id != DEMO_USER_ID) {
	$temp = intval($_POST['scout']);
	if ($temp >= 1 && $temp <= 5) {
		$ch1 = "UPDATE ".$prefix."personal_changes SET zeit = ".time()." WHERE team = '".$cookie_team."' AND personal = 'Scout' AND zeit < ".$timeout;
		$ch2 = mysql_query($ch1);
		if (mysql_affected_rows() > 0) {
            $upd1 = "UPDATE ".$prefix."teams SET scout = ".$temp." WHERE ids = '".$cookie_team."'";
            $upd2 = mysql_query($upd1);
            $_SESSION['scout'] = $temp;
            $cookie_scout = $temp;
            // PROTOKOLL ANFANG
            $formulierung = 'Du hast einen Scout der Stufe '.$temp.' eingestellt.';
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Assistenten', '".time()."')";
            $sql8 = mysql_query($sql7);
            // PROTOKOLL ENDE
			setTaskDone('change_workers');
        }
        else {
            $ch1 = "SELECT zeit FROM ".$prefix."personal_changes WHERE team = '".$cookie_team."' AND personal = 'Scout'";
            $ch2 = mysql_query($ch1);
            $ch3 = mysql_fetch_assoc($ch2);
        	addInfoBox('Du kannst jeden Assistenten nur alle 22 Tage austauschen. Du musst also noch bis zum '.date('d.m.Y H:i', getTimestamp('+22 days', $ch3['zeit'])).' Uhr warten.');
        }
	}
}
?>
<?php
$sql1 = "SELECT scout FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
$sql3 = mysql_fetch_assoc($sql2);
?>
<div style="float:left; width:280px;">
<form action="/ver_personal.php" method="post" accept-charset="utf-8">
<p>
<input type="radio" name="scout" value="1" <?php if ($sql3['scout'] == 1) { echo 'checked="checked" '; } ?>/> Stufe 1 (5.000.000 € pro Saison)<br />
<input type="radio" name="scout" value="2" <?php if ($sql3['scout'] == 2) { echo 'checked="checked" '; } ?>/> Stufe 2 (10.000.000 € pro Saison)<br />
<input type="radio" name="scout" value="3" <?php if ($sql3['scout'] == 3) { echo 'checked="checked" '; } ?>/> Stufe 3 (15.000.000 € pro Saison)<br />
<input type="radio" name="scout" value="4" <?php if ($sql3['scout'] == 4) { echo 'checked="checked" '; } ?>/> Stufe 4 (20.000.000 € pro Saison)<br />
<input type="radio" name="scout" value="5" <?php if ($sql3['scout'] == 5) { echo 'checked="checked" '; } ?>/> Stufe 5 (25.000.000 € pro Saison)<br />
<input type="submit" value="Ändern"<?php echo noDemoClick($cookie_id); ?> />
</p>
</form>
</div>
<div style="float:left; width:220px;"><img src="//www.ballmanager.de/images/personal_scout.jpg" alt="Scout" width="220" style="width:220; height:150; border:0;" /></div>
<div style="clear:both;"></div>
<h1>Fitness-Trainer <span style="color:red">[Angebot und Nachfrage]</span></h1>
<p>Dein Fitness-Trainer bietet Dir an, ein Regenerations-Camp zu buchen. Dies ist ein Mal pro Tag möglich und bringt allen Spielern
Deines Kaders einmalig 1, 2 oder 3 Prozentpunkte Frische zusätzlich.</p>
<div style="float:left; width:280px;">
<p><strong>Wichtig:</strong> Dieses Angebot ist nur für finanzstarke Vereine empfehlenswert, die ihrem Team einen kleinen Vorteil verschaffen wollen. Es ist aber auch sehr gut möglich, ohne dieses Angebot sportlichen Erfolg zu haben.</p>
<?php if ($getkonto4 > 0) { ?>
<form action="/ver_personal.php" method="post" accept-charset="utf-8">
<p>
<input type="radio" name="fitness_regeneration" value="1" checked="checked" /> +1 %-Punkte Frische (<?php echo number_format($supplyDemandPrices['Fitness-Trainer'], 0, ',', '.'); ?> €)<br />
<input type="radio" name="fitness_regeneration" value="2" /> +2 %-Punkte Frische (<?php echo number_format($supplyDemandPrices['Fitness-Trainer']*2.15, 0, ',', '.'); ?> €)<br />
<input type="radio" name="fitness_regeneration" value="3" /> +3 %-Punkte Frische (<?php echo number_format($supplyDemandPrices['Fitness-Trainer']*3.3, 0, ',', '.'); ?> €)<br />
<input type="submit" value="Buchen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" />
</p>
</form>
<?php } ?>
</div>
<div style="float:left; width:220px;"><img src="//www.ballmanager.de/images/personal_fitness.jpg" alt="Fitness-Trainer" width="220" style="width:220; height:150; border:0;" /></div>
<div style="clear:both;"></div>
<h1>Physiotherapeut <span style="color:red">[Angebot und Nachfrage]</span></h1>
<p>Dein Physiotherapeut bietet Dir an, die Behandlung Deiner verletzten Spieler zu übernehmen. Er verspricht dabei, die Verletzungszeit zu verkürzen.</p>
<p><strong>Wichtig:</strong> Dieses Angebot ist nur für finanzstarke Vereine empfehlenswert, die ihrem Team einen kleinen Vorteil verschaffen wollen. Es ist aber auch sehr gut möglich, ohne dieses Angebot sportlichen Erfolg zu haben.</p>
<?php if ($getkonto4 > 0) { ?>
<form action="/ver_personal.php" method="post" accept-charset="utf-8">
<p>
<input type="radio" name="physio_behandlung" value="1" checked="checked" /> 1 Tag kürzere Verletzungen (<?php echo number_format($supplyDemandPrices['Physiotherapeut'], 0, ',', '.'); ?> €)<br />
<input type="radio" name="physio_behandlung" value="2" /> 2 Tage kürzere Verletzungen (<?php echo number_format($supplyDemandPrices['Physiotherapeut']*2.15, 0, ',', '.'); ?> €)<br />
<input type="submit" value="Buchen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" />
</p>
</form>
<?php } ?>
<h1>Psychologe <span style="color:red">[Angebot und Nachfrage]</span></h1>
<p>Der Psychologe Deines Vereins bietet Dir an, die Betreuung Deiner unmotivierten Spieler zu übernehmen. Er verspricht dabei, ihre Moral zu erhöhen.</p>
<p><strong>Wichtig:</strong> Dieses Angebot ist nur für finanzstarke Vereine empfehlenswert, die ihrem Team einen kleinen Vorteil verschaffen wollen. Es ist aber auch sehr gut möglich, ohne dieses Angebot sportlichen Erfolg zu haben.</p>
<?php if ($getkonto4 > 0) { ?>
<form action="/ver_personal.php" method="post" accept-charset="utf-8">
<p>
<input type="radio" name="psychologe_behandlung" value="2" checked="checked" /> 2 Punkte mehr Moral (<?php echo number_format($supplyDemandPrices['Psychologe']*2.15, 0, ',', '.'); ?> €)<br />
<input type="radio" name="psychologe_behandlung" value="5" /> 5 Punkte mehr Moral (<?php echo number_format($supplyDemandPrices['Psychologe']*5.575, 0, ',', '.'); ?> €)<br />
<input type="submit" value="Buchen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" />
</p>
</form>
<?php } ?>
<p style="font-size:80%; color:#999;">Foto des Jugendtrainers: Yatmandu auf Flickr.com (Lizenz: Creative Commons BY)<br />Foto des Scouts: USAG-Humphreys auf Flickr.com (Lizenz: Creative Commons BY)<br />Foto des Fitness-Trainers: Chris Bodine auf Flickr.com (Lizenz: Creative Commons BY)</p>
<?php } else { ?>
<h1>Mein Personal</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>