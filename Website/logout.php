<?php
include 'zzserver.php';
session_start();
$hadresse = '/index.php?loggedout=1';
if (isset($_SESSION['loggedin'])) {
    if ($_SESSION['loggedin'] == 1) {
		$loggedOutTime = bigintval(getTimestamp('-301 seconds'));
        $last_login1 = "UPDATE ".$prefix."users SET last_login = ".$loggedOutTime." WHERE ids = '".$_SESSION['userid']."' AND last_login > ".$loggedOutTime;
        $last_login2 = mysql_query($last_login1);
        if ($_SESSION['status'] == 'Bigpoint' OR isset($_SESSION['bigpoint'])) {
            $hadresse = 'http://de.bigpoint.com/';
        }
        session_destroy();
        unset($_SESSION['loggedin']);
        unset($_SESSION['userid']);
        unset($_SESSION['username']);
        unset($_SESSION['liga']);
        unset($_SESSION['team']);
        unset($_SESSION['teamname']);
		unset($_SESSION['anzeigen_wo']);
		unset($_SESSION['transferGesperrt']);
    }
}
$expired = getTimestamp('-1 hour');
setcookie("PHPSESSID", "", $expired, "/", str_replace('www.', '.', CONFIG_SITE_DOMAIN), FALSE, TRUE);
header("Location: ".$hadresse);
?>