<?php include 'zz1.php'; ?>
<title><?php echo _('Wert des Geldes'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1><?php echo _('Wert des Geldes'); ?></h1>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/stat_geldwert.html';
if (file_exists($tmp_dateiname)) {
	if (filemtime($tmp_dateiname) > getTimestamp('-1 day')) {
		$tmp_liga_cache = file_get_contents($tmp_dateiname);
		if (strlen($tmp_liga_cache) > 0) {
			$ersatz_temp = '<strong>'.$cookie_teamname.'</strong>';
			echo str_replace($cookie_teamname, $ersatz_temp, $tmp_liga_cache);
            $temp = TRUE;
		}
	}
}
if ($temp == FALSE) {
    $tmp_liga_cache = '';
    function shorten_array($liste, $elemente) {
        $liste = array_chunk($liste, count($liste)/$elemente);
        $liste = array_map('shorten_array_avg', $liste);
        return $liste;
    }
    function shorten_array_avg($array) {
        return array_sum($array)/count($array);
    }
    $torj1 = "SELECT datum, median, durchschnitt, durchschnitt_o, durchschnitt_u FROM ".$prefix."spielstatistik ORDER BY datum DESC LIMIT 0, 165";
    $torj2 = mysql_query($torj1);
	$torj2a = mysql_num_rows($torj2);
	$jumpGrenze = round($torj2a/100);
    $wie_viele_zusammenfassen = ceil($torj2a/40);
    $db_felder = array('median', 'durchschnitt', 'durchschnitt_o', 'durchschnitt_u');
    $wertemenge = array();
    $wertearray = array();
	$datumListe = array();
    foreach ($db_felder as $db_feld) {
        $wertemenge[$db_feld] = array();
    }
	$jumpCounter = 1;
    while ($torj3 = mysql_fetch_assoc($torj2)) {
		/*if ($jumpCounter == $jumpGrenze) {
			$jumpCounter = 1;
			continue;
		}*/
        foreach ($db_felder as $db_feld) {
            $wertemenge[$db_feld][] = round($torj3[$db_feld]);
            $wertearray[] = round($torj3[$db_feld]);
			$datumListe[] = str_replace('-', '/', substr($torj3['datum'], 0));
        }
		$jumpCounter++;
    }
    $werte_liste = '';
    foreach ($db_felder as $db_feld) { // die einzelnen Wertemengen in eine gemeinsame Werteliste schreiben fuer URL
        $temp = implode(',', shorten_array(array_reverse($wertemenge[$db_feld]), 22));
        $werte_liste .= $temp.'|';
    }
    $werte_liste = substr($werte_liste, 0, -1);
	$datumListe = array_values(array_unique(array_reverse($datumListe))); // alle Tage sind 4-fach drin wegen verschiedenen wirtschaftlichen Groessen - Indizes wieder richtig machen mit array_values
    $tmp_liga_cache .= '<p>'.('Dieses Liniendiagramm zeigt die Entwicklung der Geldmenge, die beim Ballmanager im Umlauf ist. Der Durschnitt wird dabei auch getrennt
    für die obere und die untere Hälfte der &quot;Konto-Rangliste&quot; angezeigt. So kann man sehen, ob nur die reichen oder auch die
    ärmeren Vereine Geld gewinnen oder verlieren.').'</p>';
    $tmp_liga_cache .= '<p><img src="http://chart.apis.google.com/chart?cht=lc&amp;chco=76A4FB&amp;chls=2.0&amp;chs=470x470&amp;chxt=x&amp;chdlp=t&amp;';
    $tmp_liga_cache .= 'chd=t:'.$werte_liste.'&amp;chdl=Median|&empty;%20Gesamt|&empty;%20obere%20Hälfte|&empty;%20untere Hälfte&amp;chco=';
    $tmp_liga_cache .= 'FF0000,00FF00,0000FF,000000&amp;';
    $tmp_liga_cache .= 'chds='.round(min($wertearray)).','.round(max($wertearray)).'&amp;chxl=0:|'.$datumListe[0].'|'.$datumListe[round(count($datumListe)/2)].'|'.$datumListe[count($datumListe)-1].'&amp;chg=0,5,1,5"';
    $tmp_liga_cache .=' alt="" /></p>';
    $datei = fopen($tmp_dateiname, 'w+');
    fwrite($datei, $tmp_liga_cache);
    fclose($datei);
    echo $tmp_liga_cache;
}
?>
<?php } else { ?>
<h1><?php echo _('Wert des Geldes'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
