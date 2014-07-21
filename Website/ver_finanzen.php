<?php include 'zz1.php'; ?>
<title><?php echo _('Finanzen'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Finanzen'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><?php echo _('Hier im Finanz-Bereich findest Du eine Bilanz für die aktuelle Saison. Durch Transfers, Austausch von Personal und Stadionausbau kann die Prognose vom tatsächlichen Saisonergebnis abweichen.'); ?></p>
<?php
setTaskDone('finance_prognosis');
// WERTE INITIALISIEREN ANFANG
$ausgaben_jugendtrainer = 0;
$fitnesstrainer3 = 0;
$physiotherapeut = 0;
$abloesesteuer3 = 0;
$sponsor_einkommen = 0;
$leihPraemien_ein = 0;
$leihPraemien_aus = 0;
$sonstiges_aus = 0;
$sonstiges_ein = 0;
$ausgaben_stadion = 0;
// WERTE INITIALISIEREN ENDE
$buchungsBeginn = endOfDay(getTimestamp('-'.$cookie_spieltag.' days'));
$spe1 = "SELECT verwendungszweck, SUM(betrag), AVG(betrag) FROM ".$prefix."buchungen WHERE team = '".$cookie_team."' AND zeit > ".$buchungsBeginn." AND betrag != 0 GROUP BY verwendungszweck";
$spe2 = mysql_query($spe1);
while ($spe3 = mysql_fetch_assoc($spe2)) {
	switch ($spe3['verwendungszweck']) {
		case 'Sponsoring': if ($cookie_spieltag != 0) { $sponsor_einkommen = $spe3['AVG(betrag)']*22; } break;
		case 'Transfersteuer': $abloesesteuer3 = -$spe3['SUM(betrag)']; break;
		case 'Regenerations-Camp': $fitnesstrainer3 = -$spe3['SUM(betrag)']; break;
		case 'Physiotherapeut': $physiotherapeut = -$spe3['SUM(betrag)']; break;
		case 'Jugendtrainer': $ausgaben_jugendtrainer = -$spe3['SUM(betrag)']; break;
		case 'Lottoschein': $sonstiges_aus += -$spe3['SUM(betrag)']; break;
		case 'Testspiel': $sonstiges_aus += -$spe3['SUM(betrag)']; break;
		case 'Entlassungskosten': $sonstiges_aus += -$spe3['SUM(betrag)']; break;
		case 'Bauarbeiten': $ausgaben_stadion += -$spe3['SUM(betrag)']; break;
		case 'Lottogewinn': $sonstiges_ein += $spe3['SUM(betrag)']; break;
		case 'Sanktion': if ($spe3['SUM(betrag)'] < 0) { $sonstiges_aus += -$spe3['SUM(betrag)']; } else { $sonstiges_ein += $spe3['SUM(betrag)']; } break;
		case 'Leihprämie': $leihPraemien_ein += $spe3['SUM(betrag)']; break;
		case 'Prämienzahlung': $leihPraemien_aus += -$spe3['SUM(betrag)']; break;
		case 'Manager-Prüfung': $sonstiges_ein += $spe3['SUM(betrag)']; break;
	}
}
// HOCHRECHNUNG FUER GANZE SAISON ANFANG
$leihPraemien_aus = $leihPraemien_aus/$cookie_spieltag*22;
$leihPraemien_ein = $leihPraemien_ein/$cookie_spieltag*22;
// HOCHRECHNUNG FUER GANZE SAISON ENDE
$daten1 = "SELECT jugendarbeit, fanbetreuer, scout, konto, vorjahr_konto, stadion_aus, tv_ein, fanaufkommen FROM ".$prefix."teams WHERE ids = '".$cookie_team."'";
$daten2 = mysql_query($daten1);
$daten3 = mysql_fetch_assoc($daten2);
$gehalt1 = "SELECT SUM(gehalt) FROM ".$prefix."spieler WHERE team = '".$cookie_team."'";
$gehalt2 = mysql_query($gehalt1);
$gehalt3 = mysql_fetch_assoc($gehalt2);
$gehalt3 = $gehalt3['SUM(gehalt)'];
$ausgaben_jugendtrainer += $daten3['jugendarbeit']*7000000;
$ausgaben_fanbetreuer = $daten3['fanbetreuer']*3000000;
$ausgaben_scout = $daten3['scout']*4000000;
$einnahmen_transfers = "SELECT SUM(gebot) FROM ".$prefix."transfers WHERE besitzer = '".$cookie_team."' AND gebot != 1";
$einnahmen_transfers = mysql_query($einnahmen_transfers);
$einnahmen_transfers_zahl = mysql_num_rows($einnahmen_transfers);
if ($einnahmen_transfers_zahl == 0) { $einnahmen_transfers = 0; } else { $einnahmen_transfers = mysql_fetch_assoc($einnahmen_transfers); $einnahmen_transfers = $einnahmen_transfers['SUM(gebot)']; }
$ausgaben_transfers = "SELECT SUM(gebot) FROM ".$prefix."transfers WHERE bieter = '".$cookie_team."' AND gebot != 1";
$ausgaben_transfers = mysql_query($ausgaben_transfers);
$ausgaben_transfers_zahl = mysql_num_rows($ausgaben_transfers);
if ($ausgaben_transfers_zahl == 0) { $ausgaben_transfers = 0; } else { $ausgaben_transfers = mysql_fetch_assoc($ausgaben_transfers); $ausgaben_transfers = $ausgaben_transfers['SUM(gebot)'];}
$stad1 = "SELECT plaetze, preis, parkplatz, ubahn, restaurant, bierzelt, pizzeria, imbissstand, vereinsmuseum, fanshop FROM ".$prefix."stadien WHERE team = '".$cookie_team."'";
$stad2 = mysql_query($stad1);
$stad3 = mysql_fetch_assoc($stad2);
$fanaufkommen = $daten3['fanaufkommen']+7000+1000*(70-$stad3['preis']);
if ($stad3['plaetze'] < $fanaufkommen) {
	$fans_im_stadion = $stad3['plaetze'];
}
else {
	$fans_im_stadion = $fanaufkommen;
}
$einnahmen_stadion = $fans_im_stadion*$stad3['preis']*11;
$ausgaben_stadion += 1550000+$stad3['plaetze']*250;
$ausgaben_stadion += $stad3['parkplatz']*30000+$stad3['ubahn']*90000+$stad3['restaurant']*320000+$stad3['bierzelt']*74000+$stad3['pizzeria']*90000+$stad3['imbissstand']*45000+$stad3['vereinsmuseum']*655000+$stad3['fanshop']*160000;
$bilanzEinnahmen = ($daten3['tv_ein']+$sponsor_einkommen+$sonstiges_ein+$leihPraemien_ein+$einnahmen_transfers+$einnahmen_stadion);
$bilanzAusgaben = ($gehalt3+$ausgaben_jugendtrainer+$ausgaben_fanbetreuer+$fitnesstrainer3+$physiotherapeut+$ausgaben_scout+$sonstiges_aus+$leihPraemien_aus+$ausgaben_transfers+$ausgaben_stadion+$abloesesteuer3);
$bilanz = $bilanzEinnahmen-$bilanzAusgaben;
$naechster_kontostand = $daten3['vorjahr_konto']+$bilanz;
function showSymbolIcon($which) {
	return '<td style="width:22px; padding:2px 0 2px 4px;"><img src="/images/icon_'.$which.'.png" alt="O" width="18" style="width:18px; height:18px; border:0;" /></td>';
}
?>
<table>
<thead>
<tr class="odd">
<th scope="col" style="width:22px; padding:2px 0 2px 4px;">&nbsp;</th>
<th scope="col"><?php echo _('Bereich'); ?></th>
<th scope="col"><?php echo _('Einnahmen'); ?></th>
<th scope="col"><?php echo _('Ausgaben'); ?></th>
</tr>
</thead>
<tbody>
<tr><?php echo showSymbolIcon('tvgelder'); ?><td><?php echo _('TV-Gelder'); ?></td><td style="text-align:right"><?php echo number_format($daten3['tv_ein'], 0, ',', '.'); ?> €</td><td style="text-align:right">0 €</td></tr>
<tr class="odd"><?php echo showSymbolIcon('sponsor'); ?><td class="link"><a href="/sponsoren.php"><?php echo _('Sponsor'); ?> <sup>2)</sup></a></td><td style="text-align:right"><?php echo showKontostand($sponsor_einkommen).' €'; ?></td><td style="text-align:right">0 €</td></tr>
<tr><?php echo showSymbolIcon('spieler'); ?><td class="link"><a href="/vertraege.php"><?php echo _('Spielergehälter'); ?> <sup>2)</sup></a></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($gehalt3, 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><?php echo showSymbolIcon('personal'); ?><td class="link"><a href="/ver_personal.php"><?php echo _('Jugendtrainer'); ?> <sup>2)</sup></a></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($ausgaben_jugendtrainer, 0, ',', '.'); ?> €</td></tr>
<tr><?php echo showSymbolIcon('personal'); ?><td class="link"><a href="/ver_personal.php"><?php echo _('Fanbetreuer'); ?> <sup>2)</sup></a></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($ausgaben_fanbetreuer, 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><?php echo showSymbolIcon('personal'); ?><td class="link"><a href="/ver_personal.php"><?php echo _('Scout'); ?> <sup>2)</sup></a></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($ausgaben_scout, 0, ',', '.'); ?> €</td></tr>
<tr><?php echo showSymbolIcon('personal'); ?><td class="link"><a href="/ver_personal.php"><?php echo _('Fitness-Trainer'); ?></a></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($fitnesstrainer3, 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><?php echo showSymbolIcon('personal'); ?><td class="link"><a href="/ver_personal.php"><?php echo _('Physiotherapeut'); ?></a></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($physiotherapeut, 0, ',', '.'); ?> €</td></tr>
<tr><?php echo showSymbolIcon('transfers'); ?><td class="link"><a href="/lig_transfers.php?team=<?php echo $cookie_team; ?>"><?php echo _('Transfers'); ?></a></td><td style="text-align:right"><?php echo number_format($einnahmen_transfers, 0, ',', '.'); ?> €</td><td style="text-align:right"><?php echo number_format($ausgaben_transfers, 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><?php echo showSymbolIcon('steuer'); ?><td><?php echo _('Ablösesteuer'); ?></td><td style="text-align:right">0 €</td><td style="text-align:right"><?php echo number_format($abloesesteuer3, 0, ',', '.'); ?> €</td></tr>
<tr><?php echo showSymbolIcon('stadion'); ?><td class="link"><a href="/ver_stadion.php"><?php echo _('Stadion'); ?> <sup>2)</sup></a></td><td style="text-align:right"><?php echo number_format($einnahmen_stadion, 0, ',', '.'); ?> €</td><td style="text-align:right"><?php echo number_format($ausgaben_stadion, 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><?php echo showSymbolIcon('transfers'); ?><td class="link"><a href="/leihgaben.php"><?php echo _('Leihprämien'); ?> <sup>2)</sup></a></td><td style="text-align:right"><?php echo number_format($leihPraemien_ein, 0, ',', '.'); ?> €</td><td style="text-align:right"><?php echo number_format($leihPraemien_aus, 0, ',', '.'); ?> €</td></tr>
<tr><?php echo showSymbolIcon('sonstiges'); ?><td><?php echo _('Sonstiges'); ?> <sup>3)</sup></td><td style="text-align:right"><?php echo number_format($sonstiges_ein, 0, ',', '.'); ?> €</td><td style="text-align:right"><?php echo number_format($sonstiges_aus, 0, ',', '.'); ?> €</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right"><?php echo number_format($bilanzEinnahmen, 0, ',', '.'); ?> €</td><td style="text-align:right"><?php echo number_format($bilanzAusgaben, 0, ',', '.'); ?> €</td></tr>
<tr class="odd"><td colspan="4">&nbsp;</td></tr>
<tr><td colspan="4"><?php echo _('Aktueller Kontostand:'); ?> <?php echo showKontostand($daten3['konto']).' €'; ?></td></tr>
<tr class="odd"><td colspan="4"><?php echo _('Alter Kontostand'); ?> <sup>1)</sup>: <?php echo number_format($daten3['vorjahr_konto'], 0, ',', '.'); ?> €</td></tr>
<tr><td colspan="4"><?php echo _('Voraussichtlicher Gewinn'); ?> <sup>2)</sup>: <?php if ($bilanz > 0) { echo '+'; } echo showKontostand($bilanz); ?> €</td></tr>
<tr class="odd"><td colspan="4"><?php echo _('Erwarteter Kontostand am Ende der Saison'); ?> <sup>2)</sup>: <?php echo showKontostand($naechster_kontostand).' €'; ?></td></tr>
</tbody>
</table>
<p><sup>1)</sup> <?php echo _('Dieser Wert gibt den Kontostand an, den Dein Team direkt vor dem Saisonwechsel hatte, also am Ende der letzten Saison.'); ?></p>
<p><sup>2)</sup> <?php echo _('Dieser Wert ist eine Hochrechnung für die gesamte Saison'); ?></p>
<p><sup>3)</sup> <?php echo _('Lotto, Testspiele, Entlassungen, Sanktionen'); ?></p>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
