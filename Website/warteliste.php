<?php include 'zz1.php'; ?>
<title><?php echo _('Warteliste'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Warteliste'); ?></h1>
<p><?php echo _('Zurzeit stehst Du noch auf der Warteliste. Es wird aber nicht lange dauern, bis Dir ein Team zugeteilt wird. Dann informieren wir Dich per E-Mail darüber und Du kannst sofort losspielen.'); ?></p>
<?php
if (isset($_GET['since'])) {
	$since = bigintval($_GET['since']);
    echo '<p>'.__('Du hast Dich am %s Uhr registriert.', date('d.m.Y, H:i', $since)).' </p>';
}
?>
<?php include 'zz3.php'; ?>
