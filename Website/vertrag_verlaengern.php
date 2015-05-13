<?php include 'zz1.php'; ?>
<?php

define('SALARY_AMOUNT_FACTOR', 1.395);
define('GAME_DAYS_TO_REAL_DAYS_RATIO', 365 / 22);

function getPercentageLevels($playerAge) {
    if ($playerAge < 26) {
        // young players want short contracts
        return array(0.006, 0.018, 0.03);
    }
    else {
        // old players want long contracts
        return array(0.03, 0.018, 0.006);
    }
}

function getNewSalary($marketValue, $percentageLevel) {
    return pow(($marketValue / 1000), (SALARY_AMOUNT_FACTOR + $percentageLevel));
}

function getMinMoraleRequired($durationDays) {
    switch ($durationDays) {
        case 22: return 50;
        case 44: return 70;
        case 66: return 90;
        default: throw new Exception('Unknown duration in days: '.$durationDays);
    }
}

function isContractOptionAvailable($playerData, $durationDays) {
    if (!isset($playerData['vertrag'])) {
        throw new Exception('Property "vertrag" not set in player data');
    }
    elseif (!isset($playerData['wiealt'])) {
        throw new Exception('Property "wiealt" not set in player data');
    }
    elseif (!isset($playerData['moral'])) {
        throw new Exception('Property "moral" not set in player data');
    }
    else {
        return
            $playerData['vertrag'] < getTimestamp('+'.$durationDays.' days') &&
            (($playerData['wiealt'] + GAME_DAYS_TO_REAL_DAYS_RATIO * $durationDays) / 365) < 35 &&
            $playerData['moral'] >= getMinMoraleRequired($durationDays);
    }
}

if (isset($_POST['laufzeit']) && isset($_POST['spieler'])) {
	$laufzeit = intval(trim($_POST['laufzeit']));
	$spieler_id = mysql_real_escape_string(trim(strip_tags($_POST['spieler'])));
	if ($laufzeit == 22 || $laufzeit == 44 || $laufzeit == 66) {
		if ($cookie_id != CONFIG_DEMO_USER) {
			$laufzeit_end = endOfDay(getTimestamp('+'.$laufzeit.' days'));
			$ina = "SELECT marktwert, wiealt, vertrag, moral FROM ".$prefix."spieler WHERE ids = '".$spieler_id."' AND team = '".$cookie_team."'";
			$inb = mysql_query($ina);
			if (mysql_num_rows($inb) == 0) { exit; }
			$inc = mysql_fetch_assoc($inb);
            if (isContractOptionAvailable($inc, $laufzeit)) {
                $pLevels = getPercentageLevels(round($inc['wiealt']/365));
                switch ($laufzeit) {
                    case 22: $percentageLevel = $pLevels[0]; break;
                    case 44: $percentageLevel = $pLevels[1]; break;
                    case 66: $percentageLevel = $pLevels[2]; break;
                    default: throw new Exception('Invalid contract duration: '.$laufzeit);
                }
                $newSalary = round(getNewSalary($inc['marktwert'], $percentageLevel));
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
                $in1 = "UPDATE ".$prefix."spieler SET gehalt = ".$newSalary.", vertrag = ".$laufzeit_end.$moralPlus." WHERE ids = '".$spieler_id."' AND team = '".$cookie_team."'";
                $in2 = mysql_query($in1);
                $limitMoral1 = "UPDATE ".$prefix."spieler SET moral = 100 WHERE ids = '".$spieler_id."' AND moral > 100";
                $limitMoral2 = mysql_query($limitMoral1);
                // PROTOKOLL ANFANG
                $getmanager1 = "SELECT vorname, nachname FROM ".$prefix."spieler WHERE ids = '".$spieler_id."'";
                $getmanager2 = mysql_query($getmanager1);
                $getmanager3 = mysql_fetch_assoc($getmanager2);
                $getmanager4 = $getmanager3['vorname'].' '.$getmanager3['nachname'];
                $formulierung = __('Du hast den Vertrag mit %1$s auf %2$d Tage verlängert.', '<a href="/spieler.php?id='.$spieler_id.'">'.$getmanager4.'</a>', $laufzeit);
                $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Finanzen', '".time()."')";
                $sql8 = mysql_query($sql7);
                // PROTOKOLL ENDE
                setTaskDone('renew_contract');
            }
		}
	}
    if (isset($_POST['returnToVertraege'])) {
        header ('Location: /vertraege.php');
    }
    else {
        header ('Location: /spieler.php?id='.$spieler_id);
    }
    exit;
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
$pLevels = getPercentageLevels(round($sql3['wiealt']/365));
?>
<title><?php echo _('Vertrag verlängern:'); ?> <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Vertrag verlängern:'); ?> <?php echo $sql3['vorname'].' '.$sql3['nachname']; ?></h1>
<?php if ($loggedin == 1) { ?>
<form action="/vertrag_verlaengern.php" method="post" accept-charset="utf-8" class="imtext">
<?php

function getContractOption($durationDays, $percentageLevel, $playerData, $isDefault) {
    $out = '';
    $durationDays = intval($durationDays);
    if (isContractOptionAvailable($playerData, $durationDays)) {
        if ($isDefault) {
            $out .= '<p>'.__('Der Spieler %1$s hat Dir Angebote für eine Verlängerung seines Vertrags gemacht. Zurzeit verdient er %2$s € pro Saison. Wenn Du mit einem der Angebote einverstanden bist, wähle es aus und klicke anschließend auf <i>Abschließen</i>.', '<a href="/spieler.php?id='.$playerData['ids'].'">'.$playerData['vorname'].' '.$playerData['nachname'].'</a>', number_format($playerData['gehalt'], 0, ',', '.')).'</p>';
        }
        $out .= '<p><input type="radio" name="laufzeit" value="'.$durationDays.'"';
        if ($isDefault) {
            $out .= ' checked="checked"';
        }
        $salaryPreviewStr = number_format(getNewSalary($playerData['marktwert'], $percentageLevel), 0, ',', '.');
        $out .= ' /> '.__('%1$d Tage mit %2$s € Gehalt pro Saison', $durationDays, $salaryPreviewStr).'</p>';
        return $out;
    }
    return '';
}

$contractOptionsHTML = '';
$contractOptionsHTML .= getContractOption(22, $pLevels[0], $sql3, $contractOptionsHTML == '');
$contractOptionsHTML .= getContractOption(44, $pLevels[1], $sql3, $contractOptionsHTML == '');
$contractOptionsHTML .= getContractOption(66, $pLevels[2], $sql3, $contractOptionsHTML == '');

?>
<?php if ($contractOptionsHTML != '') { ?>
<?php echo $contractOptionsHTML; ?>
<p>
<?php if (isset($_SERVER['HTTP_REFERER'])) { if (stripos($_SERVER['HTTP_REFERER'], '/vertraege.php') !== false) { ?><input type="hidden" name="returnToVertraege" value="1" /><?php } } ?>
<input type="hidden" name="spieler" value="<?php echo $sql3['ids']; ?>" />
<input type="submit" value="<?php echo _('Abschließen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" />
</p>
</form>
<?php } else { ?>
</form>
<p><?php echo __('Der Spieler %s möchte seinen Vertrag zurzeit nicht verlängern und hat Dir deshalb kein Angebot gemacht. Vielleicht ist er unzufrieden in Deinem Team. Achte auf seine Moral!', '<a href="/spieler.php?id='.$sql3['ids'].'">'.$sql3['vorname'].' '.$sql3['nachname'].'</a>'); ?></p>
<p><a href="/vertraege.php"><?php echo _('Zurück zur Vertragsübersicht'); ?></a><br /><?php echo '<a href="/spieler.php?id='.$sql3['ids'].'">'._('Zurück zum Spielerprofil').'</a>'; ?></p>
<?php } ?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
