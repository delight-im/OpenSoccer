<?php include 'zz1.php'; ?>
<title>Abmeldungen 2 | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php include 'zzsubnav_statistik.php'; ?>
<?php if ($loggedin == 1) { ?>
<h1>Abmeldungen 2</h1>
<?php
$temp = FALSE;
$tmp_dateiname = 'cache/stat_abmeldungen2.html';
if (file_exists($tmp_dateiname)) {
	if (filemtime($tmp_dateiname) > getTimestamp('+1 day')) {
		$tmp_liga_cache = file_get_contents($tmp_dateiname);
		if (strlen($tmp_liga_cache) > 0) {
			echo $tmp_liga_cache;
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
	$timeout = getTimestamp('-28 days');
    $torj1 = "SELECT MAX(dabei) FROM ".$prefix."abmeldungen WHERE zeit > ".$timeout;
    $torj2 = mysql_query($torj1);
	$torj3 = mysql_result($torj2, 0);
    $torj1 = "SELECT dabei FROM ".$prefix."abmeldungen WHERE zeit > ".$timeout;
    $torj2 = mysql_query($torj1);
    $wertemenge = array();
    while ($torj3 = mysql_fetch_assoc($torj2)) {
		$wertemenge[] = intval(round($torj3['dabei']/86400));
    }
	$wertemenge = array_count_values($wertemenge);
	ksort($wertemenge);
	$geloeschteUserCount = array_sum($wertemenge);
	echo '<ul>';
	foreach ($wertemenge as $key=>$value) {
		$stufeProzent = round($value/$geloeschteUserCount*100);
		if ($stufeProzent == 0) { continue; }
		echo '<li>nach '.$key.' Tagen: '.$stufeProzent.'%</li>';
	}
	echo '</ul>';
    //$datei = fopen($tmp_dateiname, 'w+');
    //fwrite($datei, $tmp_liga_cache);
    //fclose($datei);
    echo $tmp_liga_cache;
}
?>
<?php } else { ?>
<h1>Abmeldungen</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>