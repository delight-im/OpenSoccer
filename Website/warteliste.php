<?php include 'zz1.php'; ?>
<title>Warteliste | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Warteliste</h1>
<p>Zurzeit stehst Du noch auf der Warteliste. Es wird aber nicht lange dauern, bis Dir ein Team zugeteilt wird. Dann informieren wir Dich per E-Mail darüber und Du kannst sofort losspielen.</p>
<?php
if (isset($_GET['since'])) {
	$since = bigintval($_GET['since']);
	echo '<p>Du hast Dich am '.date('d.m.Y, H:i', $since).' Uhr registriert.</p>';
}
?>
<?php include 'zz3.php'; ?>