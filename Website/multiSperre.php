<?php /*$_SESSION['loggedin'] = 0;*/ ?>
<?php include 'zz1.php'; ?>
<title><?php echo _('Multi-Accounts'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Multi-Accounts'); ?></h1>
<p><?php echo __('Hast Du die %s des Spiels gelesen? Pro Person ist nur 1 Account erlaubt! Andere Personen dürfen natürlich vom selben Internetanschluss aus spielen, werden jedoch vom System registriert.', '<a href="/regeln.php#regeln">'._('Nutzungsregeln').'</a>');?></p>
<p><?php echo _('Wir denken, dass niemand wirklich mehr als 4 Freunde oder Verwandte hat, die am selben Anschluss spielen. Um Betrug vorzubeugen, begrenzen wir das Ganze deshalb auf 4 aktive Mitspieler. Und Du hast diese Grenze leider überschritten ...'); ?></p>
<p><?php echo _('Das bedeutet für Dich: Du kannst Dich erst mal nicht mehr einloggen. Deine Teams können also nicht mehr betreut werden. Wir bitten Dich, das zu akzeptieren.'); ?></p>
<p><?php echo _('Falls Du der Meinung bist, Du wurdest zu Unrecht gesperrt, dann kontaktiere uns bitte unter der folgenden E-Mail-Adresse:'); ?><br /><?php echo CONFIG_SITE_EMAIL; ?></p>
<p style="text-align:center"><?php echo __('Möchtest Du %s?', '<a href="/einstellungen.php">'._('Deinen Account löschen').'</a>'); ?></p>
<?php include 'zz3.php'; ?>
