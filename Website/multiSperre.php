<?php /*$_SESSION['loggedin'] = 0;*/ ?>
<?php include 'zz1.php'; ?>
<title><?php echo _('Multi-Accounts'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Multi-Accounts'); ?></h1>
<p><?php echo __('Hast Du die %s des Ballmanagers gelesen? Pro Person ist nur 1 Account erlaubt! Andere Personen dürfen natürlich vom selben Internetanschluss aus spielen, werden jedoch vom System registriert.', '<a href="/regeln.php#regeln">'._('Nutzungsregeln').'</a>');?></p>
<p><?php echo _('Wir denken, dass niemand wirklich mehr als 4 Freunde oder Verwandte hat, die am selben Anschluss spielen. Um Betrug vorzubeugen, begrenzen wir das Ganze deshalb auf 4 aktive Mitspieler. Und Du hast diese Grenze leider überschritten ...'); ?></p>
<p><?php echo _('Das bedeutet für Dich: Du kannst Dich erst mal nicht mehr einloggen. Deine Teams können also nicht mehr betreut werden. Wir bitten Dich, das zu akzeptieren.'); ?></p>
<p><?php echo _('Falls Du der Meinung bist, Du wurdest zu Unrecht gesperrt, dann kontaktiere uns bitte unter der folgenden E-Mail-Adresse:'); ?></p>
<div style="width:214px; height:32px; margin:10px auto"><img src="/images/multiKontakt.png" width="214" alt="<?php echo _('Kontakt'); ?>" /></div>
<p style="text-align:center"><?php echo __('Möchtest Du %s?', '<a href="/einstellungen.php">'._('Deinen Account löschen').'</a>'); ?></p>
<?php include 'zz3.php'; ?>
