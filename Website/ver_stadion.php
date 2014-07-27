<?php include 'zz1.php'; ?>
<title><?php echo _('Stadion'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php

define('CONSTRUCTION_TIME_EXTEND', 3600 * 24 * 16.75);
define('CONSTRUCTION_TIME_SHRINK', 3600 * 24 * 12);
define('CONSTRUCTION_TIME_BUILDINGS', 3600 * 24 * 7.25);
define('STADIUM_MAX_SEATS', 100000);

function getPhrases($words, $maxTerms = 5) {
	$compositions = array();
    for ($start = 0; $start < count($words); $start++) {
       for ($len = 1; $len <= $maxTerms && $len <= count($words)-$start; $len++) {
          $compositions[] = implode(" ", array_slice($words, $start, $len));
       }
    }
	return $compositions;
}

function getUnderConstructionUntil($teamID) {
    global $prefix;
    $underConstruction1 = "SELECT underConstructionUntil FROM ".$prefix."stadien WHERE team = '".mysql_real_escape_string($teamID)."'";
    $underConstruction2 = mysql_query($underConstruction1);
    return intval(mysql_result($underConstruction2, 0));
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
if (isset($_POST['plaetze']) && isset($_POST['art']) && $cookie_id != CONFIG_DEMO_USER) {
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
            if (($val3['plaetze']+$plaetze_neu) <= STADIUM_MAX_SEATS) {
                if (($val6['konto']-$preis) >= 0) {
                    $underConstructionUntil = getUnderConstructionUntil($cookie_team);
                    if ($underConstructionUntil <= time()) {
                        $sql1 = "UPDATE ".$prefix."stadien SET plaetze = plaetze+".$plaetze_neu.", parkplatz = 0, ubahn = 0, restaurant = 0, bierzelt = 0, pizzeria = 0, imbissstand = 0, vereinsmuseum = 0, fanshop = 0, underConstructionUntil = ".intval(time()+CONSTRUCTION_TIME_EXTEND)." WHERE team = '".$cookie_team."'";
                        $sql2 = mysql_query($sql1);
                        $sql3 = "UPDATE ".$prefix."teams SET konto = konto-".$preis.", stadion_aus = stadion_aus-".$preis." WHERE ids = '".$cookie_team."'";
                        $sql4 = mysql_query($sql3);
                        $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Bauarbeiten', -".$preis.", '".time()."')";
                        $buch2 = mysql_query($buch1);
                        $formulierung = __('Du hast Dein Stadion um %d Plätze vergrößert.', $plaetze_neu);
                        $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Stadion', '".time()."')";
                        $sql8 = mysql_query($sql7);
                    }
                    else {
                        addInfoBox(__('Es gibt noch laufende Bauarbeiten an Deinem Stadion. Du musst noch bis %s warten.', date('d.m.Y H:i', $underConstructionUntil)));
                    }
                }
                else {
                    addInfoBox(_('Bitte überprüfe Deinen Kontostand. Diese Bauarbeiten scheinen zu teuer zu sein.'));
                }
            }
            else {
                addInfoBox(__('Dein Stadion kann nicht mehr als %s Plätze haben.', number_format(STADIUM_MAX_SEATS, 0, ',', '.')));
            }
		}
        else {
            addInfoBox(_('Bitte gib an, wie viele Plätze Du umbauen möchtest.'));
        }
	}
	elseif ($_POST['art'] == 'verkleinert') {
		$plaetze_neu = intval($_POST['plaetze']);
		if ($plaetze_neu > 0) {
            $preis = 3880000+$plaetze_neu*200;
            if (($val3['plaetze']-$plaetze_neu) >= 0) {
                if (($val6['konto']-$preis) >= 0) {
                    $underConstructionUntil = getUnderConstructionUntil($cookie_team);
                    if ($underConstructionUntil <= time()) {
                        $sql1 = "UPDATE ".$prefix."stadien SET plaetze = plaetze-".$plaetze_neu.", parkplatz = 0, ubahn = 0, restaurant = 0, bierzelt = 0, pizzeria = 0, imbissstand = 0, vereinsmuseum = 0, fanshop = 0, underConstructionUntil = ".intval(time()+CONSTRUCTION_TIME_SHRINK)." WHERE team = '".$cookie_team."'";
                        $sql2 = mysql_query($sql1);
                        $sql3 = "UPDATE ".$prefix."teams SET konto = konto-".$preis.", stadion_aus = stadion_aus-".$preis." WHERE ids = '".$cookie_team."'";
                        $sql4 = mysql_query($sql3);
                        $buch1 = "INSERT INTO ".$prefix."buchungen (team, verwendungszweck, betrag, zeit) VALUES ('".$cookie_team."', 'Bauarbeiten', -".$preis.", '".time()."')";
                        $buch2 = mysql_query($buch1);
                        $formulierung = __('Du hast Dein Stadion um %d Plätze verkleinert.', $plaetze_neu);
                        $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Stadion', '".time()."')";
                        $sql8 = mysql_query($sql7);
                    }
                    else {
                        addInfoBox(__('Es gibt noch laufende Bauarbeiten an Deinem Stadion. Du musst noch bis %s warten.', date('d.m.Y H:i', $underConstructionUntil)));
                    }
                }
                else {
                    addInfoBox(_('Bitte überprüfe Deinen Kontostand. Diese Bauarbeiten scheinen zu teuer zu sein.'));
                }
            }
            else {
                addInfoBox(_('Dein Stadion kann nicht ohne Sitzplätze gebaut werden.'));
            }
		}
        else {
            addInfoBox(_('Bitte gib an, wie viele Plätze Du umbauen möchtest.'));
        }
	}
}
if (isset($_POST['preis']) && $cookie_id != CONFIG_DEMO_USER) {
	if (intval($_POST['preis']) > 19 && intval($_POST['preis']) < 71) {
        $sql1 = "UPDATE ".$prefix."stadien SET preis = ".intval($_POST['preis'])." WHERE team = '".$cookie_team."'";
        $sql2 = mysql_query($sql1);
            $formulierung = _('Du hast die Ticketpreise für Dein Stadion auf %d € gesetzt.', intval($_POST['preis']));
            $sql7 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$cookie_team."', '".$formulierung."', 'Stadion', '".time()."')";
            $sql8 = mysql_query($sql7);
			setTaskDone('ticket_prices');
	}
}

require_once('./classes/StadiumBuildings.php');

if (isset($_POST['umfeld_bearbeiten']) && $cookie_id != CONFIG_DEMO_USER) {
    $stadiumSeats1 = "SELECT plaetze FROM ".$prefix."stadien WHERE team = '".$cookie_team."'";
    $stadiumSeats2 = mysql_query($stadiumSeats1);
    $stadiumSeats3 = mysql_result($stadiumSeats2, 0);
    $stadiumSeats3 = intval($stadiumSeats3);
    $buildingSQL = "";
    foreach (StadiumBuildings::getList() as $gebaeude) {
        if (isset($_POST[$gebaeude[0]])) {
            $value = intval($_POST[$gebaeude[0]]) > 0 ? ceil($stadiumSeats3 / $gebaeude[2]) : 0;
            $buildingSQL .= mysql_real_escape_string($gebaeude[0])." = ".$value;
        }
        else {
            $buildingSQL .= mysql_real_escape_string($gebaeude[0])." = 0";
        }
        $buildingSQL .= ", ";
    }
    $underConstructionUntil = getUnderConstructionUntil($cookie_team);
    if ($underConstructionUntil <= time()) {
        $sql1 = "UPDATE ".$prefix."stadien SET ".mb_substr($buildingSQL, 0, -2).", underConstructionUntil = ".intval(time()+CONSTRUCTION_TIME_BUILDINGS)." WHERE team = '".$cookie_team."'";
        $sql2 = mysql_query($sql1);
    }
    else {
        addInfoBox(__('Es gibt noch laufende Bauarbeiten an Deinem Stadion. Du musst noch bis %s warten.', date('d.m.Y H:i', $underConstructionUntil)));
    }
}
if (isset($_POST['kuerzel1']) && isset($_POST['kuerzel2']) && isset($_POST['stadt']) && $cookie_id != CONFIG_DEMO_USER) {
	if ($live_scoring_spieltyp_laeuft == '') {
		$kuerzel1 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel1'])));
		$kuerzel2 = mysql_real_escape_string(trim(strip_tags($_POST['kuerzel2'])));
		$stadt = mysql_real_escape_string(trim(strip_tags($_POST['stadt'])));
		if ((in_array($kuerzel1, $stadiumAffixes) OR in_array($kuerzel2, $stadiumAffixes)) && in_array($stadt, $teamNameParts)) {
			if ($kuerzel1 != '' && $kuerzel2 != '') {
				addInfoBox(_('Du darfst nur <i>einen</i> Zusatz für den Namen wählen. Bitte entscheide Dich für einen und versuche es noch einmal.'));
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
					addInfoBox(_('Es ist ein Fehler aufgetreten.'));
				}
				else {
					addInfoBox(__('Dein Stadion heißt jetzt: %s', $neuerName));
				}
			}
		}
		else {
			addInfoBox(_('Du musst <i>einen</i> Zusatz wählen. Bitte versuche es noch einmal.'));
		}
	}
	else {
		addInfoBox(__('Du kannst den Namen Deines Stadions nur außerhalb der Spielzeiten ändern. Zurzeit laufen %s-Spiele.', $live_scoring_spieltyp_laeuft));
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
<th scope="col"><?php echo _('Bereich'); ?></th>
<th scope="col"><?php echo _('Wert'); ?></th>
</tr>
</thead>
<tbody>
<tr><td><?php echo _('Kapazität').'</td><td>'.__('%s Plätze', number_format($sql3['plaetze'], 0, ',', '.')); ?></td></tr>
<tr class="odd"><td><?php echo _('Preis/Platz').'</td><td>'.$sql3['preis']; ?> €</td></tr>
<tr><td><?php echo _('Einnahmen bei voller Auslastung').'</td><td>'.number_format($sql3['plaetze']*$sql3['preis'], 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><td><?php echo _('Instandhaltungskosten').'</td><td>'.__('%s € / Saison', number_format(1550000+$sql3['plaetze']*250, 0, ',', '.')); ?></td></tr>
<tr class="odd"><td><?php echo _('Gelände-Kosten').'</td><td>'.__('%s € / Saison', number_format($gelaende_kosten, 0, ',', '.')); ?></td></tr>
<tr><td><?php echo _('Zuschauer beim letzten Spiel').'</td><td>'.number_format($sql6, 0, ',', '.'); ?></td></tr>
</tbody>
</table>
<h1><?php echo _('Namen ändern'); ?></h1>
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
<p><input type="submit" value="<?php echo _('Namen ändern'); ?>"<?php echo noDemoClick($cookie_id); ?> /></p>
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
</select> <input type="submit" value="<?php echo _('Ändern'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<h1><?php echo _('Umbau'); ?></h1>
<form action="/ver_stadion.php" method="post" accept-charset="utf-8">
<p><?php echo _('Anzahl der Plätze:'); ?>&nbsp;<select name="plaetze" size="1" style="width:100px">
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
</select></p>
<p><?php echo _('Art des Umbaus:'); ?>&nbsp;<select name="art" size="1" style="width:140px">
    <option value="erweitert"><?php echo _('Erweiterung'); ?></option>
    <option value="verkleinert"><?php echo _('Verkleinerung'); ?></option>
</select></p>
<p><input type="submit" value="<?php echo _('Bauen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<p>
    <strong><?php echo _('Kosten für Erweiterung:').'</strong> '.__('%s € / Platz', '11.880.000 + 1.200'); ?><br />
    <strong><?php echo _('Kosten für Verkleinerung:').'</strong> '.__('%s € / Platz', '3.880.000 + 200'); ?><br />
    <strong><?php echo _('Instandhaltungskosten:').'</strong> '.__('%s € / Platz', '1.550.000 + 250'); ?><br />
    <strong><?php echo _('Bauzeit für Erweiterung:').'</strong> '.__('bis %s', date('d.m.Y H:i', time()+CONSTRUCTION_TIME_EXTEND)); ?><br />
    <strong><?php echo _('Bauzeit für Verkleinerung:').'</strong> '.__('bis %s', date('d.m.Y H:i', time()+CONSTRUCTION_TIME_SHRINK)); ?><br />
    <strong><?php echo _('Maximale Kapazität:').'</strong> '.__('%s Plätze', '100.000'); ?>
</p>
<p><strong><?php echo _('Wichtig:').'</strong> '._('Der Stadion-Umbau kann nicht stattfinden, wenn dadurch Schulden entstehen würden.'); ?></p>
<p><strong><?php echo _('Information:').'</strong> '._('Sowohl eine Erweiterung als auch eine Verkleinerung kostet viel Geld. Bei einer Erweiterung steigen die möglichen Einnahmen durch den Kartenverkauf, dagegen steigen jedoch auch die Kosten für die Instandhaltung. Durch eine Verkleinerung kann man die Instandhaltungskosten senken, wodurch aber auch nur noch geringere Einnahmen durch den Kartenverkauf möglich sind.'); ?></p>
<h1><?php echo _('Gelände'); ?></h1>
<form action="/ver_stadion.php" method="post" accept-charset="utf-8">
<p>
<?php
foreach (StadiumBuildings::getList() as $tm) {
	$anzahl = ceil($sql3['plaetze']/$tm[2]);
	$kosten = round($tm[3]*$anzahl);
	echo '<input type="checkbox" name="'.$tm[0].'" value="'.$anzahl.'" ';
	if ($sql3[$tm[0]] > 0) { echo 'checked="checked" '; }
	echo '/> ';
	echo '<strong>'.($anzahl < 10 ? '0' : '').$anzahl.'&times; '.$tm[1].'</strong> &mdash; '.number_format($kosten, 0, ',', '.').' € / Saison<br />';
}
?>
</p>
<p><input type="hidden" name="umfeld_bearbeiten" value="1" /><input type="submit" value="<?php echo _('Ändern'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>')" /></p>
</form>
<p><strong><?php echo _('Bauzeit für Umbau:').'</strong> '.__('bis %s', date('d.m.Y H:i', time()+CONSTRUCTION_TIME_BUILDINGS)); ?></p>
<p><strong><?php echo _('Information:').'</strong> '._('Je mehr Service und Unterhaltung Du auf dem Gelände rund ums Stadion anbietest, desto höher werden die Betriebs- und Instandhaltungskosten. Die Fans werden aber lieber ins Stadion gehen und der Kartenverkauf wird besser laufen.'); ?></p>
<?php
if (isset($selectedStadionPhoto)) {
	if (isset($selectedStadionPhoto[2])) {
		echo '<p style="font-size:80%; color:#999;">'.__('Foto des Stadions: %s', $selectedStadionPhoto[2]).'</p>';
	}
}
?>
<?php } else { ?>
<h1><?php echo _('Stadion'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
