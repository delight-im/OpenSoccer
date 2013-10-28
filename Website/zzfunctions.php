<?php
function formular_abbrechen() {
	if (isset($_SERVER['HTTP_REFERER'])) {
		echo '<input type="reset" value="Abbrechen" onclick="location.href=\''.$_SERVER['HTTP_REFERER'].'\'" />';
	}
	else {
		echo '<input type="reset" value="Abbrechen" onclick="location.href=\'http://www.dnnetz.de/\'" />';
	}
}
function valzeichen($wort) {
	$wort = mysql_real_escape_string(trim(strip_tags($wort)));
	return $wort;
}
function valzahl($wort) {
	$wort = intval(mysql_real_escape_string(trim(strip_tags($wort))));
	return $wort;
}
function datum($zeitstempel) {
	$start = '14.07.2008 00:00';
	$temp1 = $zeitstempel-1000000000;
	$zeitstempel_unix = 1215986400+$temp1*3600*24;
	return $zeitstempel_unix;
}
?>