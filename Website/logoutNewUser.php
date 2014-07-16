<?php include 'zz1.php'; ?>
<?php
if (isset($_POST['logoutOrNot'])) {
	switch ($_POST['logoutOrNot']) {
		case 'Ich komme später wieder - Ausloggen!': header('Location: /logout.php'); exit; break;
		case 'Ich habe ein Problem oder eine Frage - Hilfe!': header('Location: /support.php'); exit; break;
		case 'Ich habe keine Lust mehr - Account löschen!': header('Location: /einstellungen.php#accDel'); exit; break;
	}
}
?>
<title><?php echo _('Ausloggen?'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Ausloggen?'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><strong><?php echo _('Du bist neu hier, deshalb fragen wir Dich:').'</strong><br />'._('Was möchtest Du tun? Wie gefällt der Ballmanager Dir bisher?'); ?></p>
<form action="/logoutNewUser.php" method="post" accept-charset="utf-8">
<p><input type="submit" name="logoutOrNot" value="<?php echo _('Ich komme später wieder - Ausloggen!'); ?>" style="width: 300px" /></p>
<p><input type="submit" name="logoutOrNot" value="<?php echo _('Ich habe ein Problem oder eine Frage - Hilfe!'); ?>" style="width: 300px" /></p>
<p><input type="submit" name="logoutOrNot" value="<?php echo _('Ich habe keine Lust mehr - Account löschen!'); ?>" style="width: 300px" /></p>
</form>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
