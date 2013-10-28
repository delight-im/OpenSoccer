<?php include 'zz1.php'; ?>
<?php
define('GEHALTSHOEHE', 1.395);
function prozentstufen($spieler_alter) {
    if ($spieler_alter < 26) {
        $prozentstufen = array(1, 3, 5);
    }
    else {
        $prozentstufen = array(5, 3, 1);
    }
    return $prozentstufen;
}
if (isset($_POST['laufzeit']) && isset($_POST['spieler'])) {
	$laufzeit = mysql_real_escape_string(trim(strip_tags($_POST['laufzeit'])));
	$spieler_id = mysql_real_escape_string(trim(strip_tags($_POST['spieler'])));
	if ($laufzeit >= 22 && $laufzeit <= 66) {
		if ($cookie_id != DEMO_USER_ID) {
			$laufzeit_end = endOfDay(getTimestamp('+'.$laufzeit.' days'));
			$ina = "SELECT marktwert, wiealt, vertrag FROM ".$prefix."spieler WHERE ids = '".$spieler_id."' AND team = '".$cookie_team."'";
			$inb = mysql_query($ina);
			if (mysql_num_rows($inb) == 0) { exit; }
			$inc = mysql_fetch_assoc($inb);
			$prozentstufen = prozentstufen(round($inc['wiealt']/365));
			switch ($laufzeit) {
				case 22: $nn = $prozentstufen[0]; break;
				case 44: $nn = $prozentstufen[1]; break;
				case 66: $nn = $prozentstufen[2]; break;
				default: $nn = max($prozentstufen);
			}
			$neuer_marktwert = round(pow(($inc['marktwert']/1000), (GEHALTSHOEHE+0.006*$nn)));
			$moralPlus = "";
			$verlaengerungUmTage = ($laufzeit_end-$inc['vertrag'])/3600/24;
			if ($verlaengerungUmTage > 44) {
				$moralPlus = ", moral = moral+10";
			}
			elseif ($verlaengerungUmTage > 22) {
				$moralPlus = ", moral = moral+7.5";
			}
			elseif ($verlaengerungUmTage > 11) {
				$moralPlus = ", moral = moral+5";
			}
			$in1 = "UPDATE ".$prefix."spieler SET gehalt = ".$neuer_marktwert.", vertrag = ".$laufzeit_end.$moralPlus." WHERE ids = '".$spieler_id."' AND team = '".$cookie_team."'";
			$in2 = mysql_query($in1);
			$limitMoral1 = "UPDATE ".$prefix."spieler SET moral = 100 WHERE ids = '".$spieler_id."' AND moral > 100";
			$limitMoral2 = mysql_query($limitMoral1);
			// PROTOKOLL ANFANG
			$getmanager1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$spieler_id."'";
			$getmanager2 = mysql_query($getmanager1);
			$getmanager3 = mysql_fetch_assoc($getmanager2);
			$getmanager4 = $getmanager3['vorname'].' '.$getmanager3['nachname'];
			$formulierung = 'Du hast den Vertrag mit <a href="/spieler.php?id='.$spieler_id.'">'.$getmanager4.'</a> auf '.$laufzeit.' Tage verlängert.';
			$sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Finanzen', '".time()."')";
			$sql8 = mysql_query($sql7);
			// PROTOKOLL ENDE
			setTaskDone('renew_contract');
		}
		if (isset($_POST['returnToVertraege'])) {
			header ('Location: /vertraege.php');
		}
		else {
			header ('Location: /spieler.php?id='.$spieler_id);
		}
		exit;
	}
}
?>
<?php
if (!isset($_GET['id'])) { exit; }
$sql1 = "SELECT ids, vorname, nachname, marktwert, vertrag, wiealt, moral, leiher, gehalt FROM ".$prefix."spieler WHERE ids = '".mysql_real_escape_string(trim(strip_tags($_GET['id'])))."' AND team = '".$cookie_team."'";
$sql2 = mysql_query($sql1);
$sql2a = mysql_num_rows($sql2);
if ($sql2a == 0) { exit; }
$sql3 = mysql_fetch_assoc($sql2);
if ($sql3['marktwert'] == 0) { exit; }
if ($sql3['leiher'] != 'keiner') { exit; }
$prozentstufen = prozentstufen(round($sql3['wiealt']/365));
?>
<title>Vertrag verlängern: <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Vertrag verlängern: <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?></h1>
<?php if ($loggedin == 1) { ?>
<form action="/vertrag_verlaengern.php" method="post" accept-charset="utf-8" class="imtext">
<?php
$moeglich = 0;
$optionsList = '';
$isFirstPossibleOption = TRUE;
if ($sql3['vertrag'] < getTimestamp('+22 days') && (($sql3['wiealt']+16.5909091*22)/365) < 35 && $sql3['moral'] >= 50) {
	$moeglich++;
	$optionsList .= '<p><input type="radio" name="laufzeit" value="22"';
	if ($isFirstPossibleOption) {
		$optionsList .= ' checked="checked"';
		echo '<p>Der Spieler <a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a> hat Dir Angebote für eine Verlängerung seines Vertrags gemacht. Zurzeit verdient er '.number_format($sql3['gehalt'], 0, ',', '.').' € pro Saison. Wenn Du mit einem der Angebote einverstanden bist, wähle es aus und klicke anschließend auf <i>Abschließen</i>.</p>';
	}
	$optionsList .= ' /> 22 Tage mit '.number_format(pow(($sql3['marktwert']/1000), (GEHALTSHOEHE+0.006*$prozentstufen[0])), 0, ',', '.').' € Gehalt/Saison</p>';
	$isFirstPossibleOption = FALSE;
}
if ($sql3['vertrag'] < getTimestamp('+44 days') && (($sql3['wiealt']+16.5909091*44)/365) < 35 && $sql3['moral'] >= 70) {
	$moeglich++;
	$optionsList .= '<p><input type="radio" name="laufzeit" value="44"';
	if ($isFirstPossibleOption) {
		$optionsList .= ' checked="checked"';
		echo '<p>Der Spieler <a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a> hat Dir Angebote für eine Verlängerung seines Vertrags gemacht. Zurzeit verdient er '.number_format($sql3['gehalt'], 0, ',', '.').' € pro Saison. Wenn Du mit einem der Angebote einverstanden bist, wähle es aus und klicke anschließend auf <i>Abschließen</i>.</p>';
	}
	$optionsList .= ' /> 44 Tage mit '.number_format(pow(($sql3['marktwert']/1000), (GEHALTSHOEHE+0.006*$prozentstufen[1])), 0, ',', '.').' € Gehalt/Saison</p>';
	$isFirstPossibleOption = FALSE;
}
if ($sql3['vertrag'] < getTimestamp('+66 days') && (($sql3['wiealt']+16.5909091*66)/365) < 35 && $sql3['moral'] >= 90) {
	$moeglich++;
	$optionsList .= '<p><input type="radio" name="laufzeit" value="66"';
	if ($isFirstPossibleOption) {
		$optionsList .= ' checked="checked"';
		echo '<p>Der Spieler <a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a> hat Dir Angebote für eine Verlängerung seines Vertrags gemacht. Zurzeit verdient er '.number_format($sql3['gehalt'], 0, ',', '.').' € pro Saison. Wenn Du mit einem der Angebote einverstanden bist, wähle es aus und klicke anschließend auf <i>Abschließen</i>.</p>';
	}
	$optionsList .= ' /> 66 Tage mit '.number_format(pow(($sql3['marktwert']/1000), (GEHALTSHOEHE+0.006*$prozentstufen[2])), 0, ',', '.').' € Gehalt/Saison</p>';
	$isFirstPossibleOption = FALSE;
}
?>
<?php if ($moeglich != 0) { ?>
<?php echo $optionsList; ?>
<p>
<?php if (isset($_SERVER['HTTP_REFERER'])) { if ($_SERVER['HTTP_REFERER'] == 'http://www.ballmanager.de/vertraege.php') { ?><input type="hidden" name="returnToVertraege" value="1" /><?php } } ?>
<input type="hidden" name="spieler" value="<?php echo $sql3['ids']; ?>" />
<input type="submit" value="Abschließen" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('Bist Du sicher?')" />
</p>
</form>
<?php } else { ?>
</form>
<p>Der Spieler <?php echo '<a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a>'; ?> möchte seinen Vertrag zurzeit nicht verlängern und hat Dir deshalb kein Angebot gemacht. Vielleicht ist er unzufrieden in Deinem Team. Achte auf seine Moral!</p>
<p><a href="/vertraege.php">Zurück zur Vertragsübersicht</a><br /><?php echo '<a href="/spieler.php?id='.$sql3['ids'].'">Zurück zum Spielerprofil</a>'; ?></p>
<?php } ?>
<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>