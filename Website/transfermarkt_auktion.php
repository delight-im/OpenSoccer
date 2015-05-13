<?php include 'zz1.php'; ?>
<?php
if (!isset($_GET['id'])) {
	exit;
}
else {
	$ids = mysql_real_escape_string(trim(strip_tags($_GET['id'])));
	$sql1 = "SELECT spieler, besitzer, bieter_highest, betrag_highest, gebote, ende FROM ".$prefix."transfermarkt WHERE spieler = '".$ids."' AND ende > ".time();
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) {
		$up1 = "UPDATE ".$prefix."spieler SET transfermarkt = 0 WHERE ids = '".$ids."'";
		$up2 = mysql_query($up1);
		exit;
	}
	$sql3 = mysql_fetch_assoc($sql2);
	$sql4 = "SELECT vorname, nachname, marktwert, gehalt FROM ".$prefix."spieler WHERE ids = '".$sql3['spieler']."'";
	$sql5 = mysql_query($sql4);
	if (mysql_num_rows($sql5) == 0) { exit; }
	$sql6 = mysql_fetch_assoc($sql5);
	$spieler_name = $sql6['vorname'].' '.$sql6['nachname'];
	if ($sql3['besitzer'] == 'KEINER') {
		$team_name = 'außerhalb Europas';
		$teamLink = '<td>'.$team_name.'</td>';
		$teamOwnerID = 'NONE';
	}
	else {
		$sql7 = "SELECT name FROM ".$prefix."teams WHERE ids = '".$sql3['besitzer']."'";
		$sql8 = mysql_query($sql7);
		if (mysql_num_rows($sql8) == 0) { exit; }
		$sql9 = mysql_fetch_assoc($sql8);
		$team_name = $sql9['name'];
		$teamLink = '<td class="link"><a href="/team.php?id='.$sql3['besitzer'].'">'.$team_name.'</a></td>';
		$teamOwnerID = $sql3['besitzer'];
	}
}
?>
<title><?php echo __('Transfermarkt: %s', $spieler_name); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<script type="text/javascript">
function number_format(number, decimals, dec_point, thousands_sep) {
  var exponent = '';
  var numberstr = number.toString();
  var eindex = numberstr.indexOf('e');
  if (eindex > -1) {
    exponent = numberstr.substring(eindex);
    number = parseFloat(numberstr.substring(0, eindex));
  }
  if (decimals != null) {
    var temp = Math.pow(10, decimals);
    number = Math.round(number*temp)/temp;
  }
  var sign = number < 0 ? '-' : '';
  var integer = (number > 0 ? 
      Math.floor(number) : Math.abs(Math.ceil(number))).toString ();
  
  var fractional = number.toString().substring(integer.length + sign.length);
  dec_point = dec_point != null ? dec_point : '.';
  fractional = decimals != null && decimals > 0 || fractional.length > 1 ? 
               (dec_point + fractional.substring(1)) : '';
  if (decimals != null && decimals > 0) {
    for (i = fractional.length-1, z = decimals; i < z; ++i)
      fractional += '0';
  }
  thousands_sep = (thousands_sep != dec_point || fractional.length == 0) ? 
                  thousands_sep : null;
  if (thousands_sep != null && thousands_sep != '') {
	for (i = integer.length-3; i > 0; i -= 3)
      integer = integer.substring(0, i)+thousands_sep+integer.substring(i);
  }
  return sign+integer+fractional+exponent;
}
function stepGebot(type) {
	var altesGebot = document.getElementById('aktuellesGebot').value;
	var neuesGebot = altesGebot.split('.').join('');
	if (type == 'dec') {
		neuesGebot = neuesGebot*0.95;
	}
	else {
		neuesGebot = neuesGebot*1.05;
	}
	document.getElementById('aktuellesGebot').value = number_format(neuesGebot, 0, ',', '.');
	return false;
}
</script>
<style type="text/css">
<!--
.miniButton {
	width: 25px;
}
-->
</style>
<?php include 'zz2.php'; ?>
<h1>Transfermarkt: <?php echo $spieler_name; ?></h1>
<p style="text-align:right"><a href="/transfermarkt_auktion.php?id=<?php echo $ids; ?>" onclick="window.location.reload(); return false" class="pagenava"><?php echo _('Seite aktualisieren'); ?></a></p>
<?php if ($loggedin == 1) { ?>
<?php
// AM ANFANG NOCH KEINE TRANSFERS ANFANG
if ($_SESSION['pMaxGebot'] == 0) {
	echo '<p>'._('Bist Du wirklich sicher, dass Du schon eine Verstärkung für Dein Team brauchst?').'</p>';
	echo '<p>'._('Der Vorstand empfiehlt Dir, als neuer Trainer in den ersten zwei Stunden auf Transfers zu verzichten.').'</p>';
	echo '<p>'.__('Du solltest Dir zuerst einmal %1$s ansehen und versuchen, eine erste %2$s daraus zu formen.', '<a href="/kader.php">'._('Deinen Kader').'</a>', '<a href="/aufstellung.php">'._('Mannschaft').'</a>').'</p>';
	include 'zz3.php';
	exit;
}
// AM ANFANG NOCH KEINE TRANSFERS ENDE
// TRANSFER-SPERREN ANFANG
if ($_SESSION['transferGesperrt'] == TRUE) {
	addInfoBox(__('Du bist noch für den Transfermarkt %1$s. Wenn Dir unklar ist, warum, frage bitte ein %2$s.', '<a class="inText" href="/sanktionen.php">'._('gesperrt').'</a>', '<a class="inText" href="/post_schreiben.php?id='.CONFIG_OFFICIAL_USER.'">'._('Team-Mitglied').'</a>'));
	include 'zz3.php';
	exit;
}
// TRANSFER-SPERREN ENDE
// NUR 2 TRANSFERS ZWISCHEN 2 TEAMS ANFANG
if ($team_name != 'außerhalb Europas') {
	$n3t1_a = "SELECT COUNT(*) FROM ".$prefix."transfers WHERE (besitzer = '".$sql3['besitzer']."' AND bieter = '".$cookie_team."') OR (bieter = '".$sql3['besitzer']."' AND besitzer = '".$cookie_team."')";
	$n3t2_a = mysql_query($n3t1_a);
	$n3t3_a = mysql_result($n3t2_a, 0);
	$n3t1_b = "SELECT COUNT(*) FROM ".$prefix."transfermarkt WHERE (besitzer = '".$sql3['besitzer']."' AND bieter_highest = '".$cookie_team."') OR (bieter_highest = '".$sql3['besitzer']."' AND besitzer = '".$cookie_team."')";
	$n3t2_b = mysql_query($n3t1_b);
	$n3t3_b = mysql_result($n3t2_b, 0);
	$n3t3 = intval($n3t3_a+$n3t3_b);
}
else {
	$n3t3 = 0;
}
// NUR 2 TRANSFERS ZWISCHEN 2 TEAMS ENDE
$multiListe = explode('-', $_SESSION['multiAccountList']);
if ($cookie_team != '__'.$cookie_id) {
	$getkonto1 = "SELECT konto FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
	$getkonto2 = mysql_query($getkonto1);
	$getkonto3 = mysql_fetch_assoc($getkonto2);
	$getkonto4 = $getkonto3['konto']-einsatz_in_auktionen($cookie_team);
}
else {
	$getkonto4 = 0;
}
$cntWatcher1 = "SELECT COUNT(*) FROM ".$prefix."transfermarkt_watch WHERE spieler_id = '".$sql3['spieler']."'";
$cntWatcher2 = mysql_query($cntWatcher1);
$cntWatcher3 = mysql_result($cntWatcher2, 0);
?>
<?php
echo '<p><table>';
echo '<thead><tr class="odd"><th scope="col" colspan="2"><a href="/spieler.php?id='.$sql3['spieler'].'">'.$spieler_name.' ('.__('%d Gebote', $sql3['gebote']).' / '.__('%d Beobachter', $cntWatcher3).')</a></th></tr></thead>';
echo '<tbody>';
echo '<tr><td>'._('Gehalt').'</td><td>'.number_format($sql6['gehalt'], 0, ',', '.').' € / '._('Saison').'</td></tr>';
echo '<tr class="odd"><td>'._('Besitzer').'</td>'.$teamLink.'</tr>';
echo '<tr><td>'._('Marktwert').'</td><td>'.number_format($sql6['marktwert'], 0, ',', '.').' €</td></tr>';
echo '<tr class="odd"><td>'._('Höchstgebot').'</td><td>'.number_format($sql3['betrag_highest'], 0, ',', '.').' € ('._('Ablösesumme').')</td></tr>';
echo '<tr><td>Restzeit</td><td>'.__('%1$d Minuten und %2$d Sekunden', floor(($sql3['ende']-time())/60), floor(($sql3['ende']-time())%60)).'</td></tr>';
$mindestgebot = floor($sql6['marktwert']/100);
$mindestgebot_temp = strlen($mindestgebot)-1;
$mindestgebot = floor($mindestgebot/pow(10, $mindestgebot_temp))*pow(10, $mindestgebot_temp);
$maximalgebot = floor($sql6['marktwert']*$_SESSION['pMaxGebot']);
echo '<tr class="odd"><td colspan="2">'.__('Du musst das alte Gebot um mindestens %s € überbieten!', number_format($mindestgebot, 0, ',', '.')).'</td></tr>';
echo '<tr><td colspan="2">Maximalgebot: '.number_format($maximalgebot, 0, ',', '.').' €</td></tr>';
if ($sql3['besitzer'] == $cookie_team) {
	echo '<tr class="odd"><td colspan="2">'._('Du kannst nicht für Deine eigenen Spieler bieten!').'</td></tr>';
}
elseif (in_array($teamOwnerID, $multiListe)) {
	echo '<tr class="odd"><td colspan="2">'._('Du kannst nicht für Spieler von Deinen Accounts bieten (Multi-Account).').'</td></tr>';
}
elseif ($getkonto4 < $sql3['betrag_highest']) {
    echo '<tr class="odd"><td colspan="2">'._('Du hast nicht genug Geld, um diesen Spieler kaufen zu können!').'</td></tr>';
}
elseif ($n3t3 >= 2) {
	echo '<tr class="odd"><td colspan="2">'._('Du kannst diesen Spieler nicht verpflichten, da zwischen zwei Vereinen immer nur 2 Transfers pro Saison erlaubt sind.').'</td></tr>';
}
else {
	$bietenHash = md5('29'.betrag_enkodieren($mindestgebot).$teamOwnerID.betrag_enkodieren($sql3['betrag_highest']).betrag_enkodieren($maximalgebot).'1992');
	echo '<tr class="odd"><td colspan="2"><form action="/transfermarkt_bieten.php" method="post" accept-charset="utf-8"><input type="submit" class="miniButton" name="incGebotButton" value="+" onclick="return stepGebot(\'inc\')" /> <input type="submit" class="miniButton" name="decGebotButton" value="-" onclick="return stepGebot(\'dec\')" /> <input type="hidden" name="wt1" value="'.betrag_enkodieren($mindestgebot).'" /><input type="hidden" name="wt2" value="'.$teamOwnerID.'" /><input type="hidden" name="wt3" value="'.betrag_enkodieren($sql3['betrag_highest']).'" /><input type="hidden" name="wt4" value="'.betrag_enkodieren($maximalgebot).'" /><input type="hidden" name="wt5" value="'.$bietenHash.'" /><input type="text" name="gebot" id="aktuellesGebot" value="'.number_format(ceil($sql3['betrag_highest']+$mindestgebot+1), 0, ',', '.').'" />';
	echo ' <input type="hidden" name="spieler" value="'.$sql3['spieler'].'" /><input type="submit" value="'._('Betrag bieten').'" onclick="return'.noDemoClick($cookie_id, TRUE).' confirm(\''._('Bist Du sicher?').'\')" /></form></td></tr>';
}
if ($sql3['bieter_highest'] == $cookie_team) {
	echo '<tr><td colspan="2" style="color:red">'._('Du bist zurzeit der Höchstbietende!').'</td></tr>';
}
echo '</tbody>';
echo '</table></p>';
require_once('./classes/TransferMarket.php');
echo '<p><strong>'._('Hinweis:').'</strong> '._('Der Verein, der die höchste Ablösesumme bietet, bekommt den Spieler automatisch nach Auktionsende. Der Vertrag wird dann erst einmal für 29 Tage mit dem angegebenen Gehalt abgeschlossen.').'</p><p>'.__('Jedes Gebot verlängert die Laufzeit der Auktion um %d Minuten, damit andere Manager noch die Chance haben, mehr zu bieten.', TransferMarket::AUCTION_TIME_EXTENSION_ON_BID).'</p>';
?>
<h1><?php echo _('Dein Maximalgebot'); ?></h1>
<p><?php echo _('Wenn Du neu im Spiel bist, kannst Du nur bis 125% des Marktwertes bieten. Nach 7 Tagen kannst du dann bis 250% bieten. Wenn Du schon 21 Tage dabei bist, kannst Du bis maximal 400% bieten. Schließlich kannst Du ab dem 42. Tag sogar 800% und ab dem 84. Tag bis 1600% bieten, d.h. das Sechzehnfache vom Marktwert eines Spielers.'); ?></p>
<p><?php echo __('Im Moment liegt Deine persönliche Grenze bei %s des Marktwertes.', '<strong>'.intval($_SESSION['pMaxGebot']*100).'%</strong>'); ?></p>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
