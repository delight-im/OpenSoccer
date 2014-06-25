<?php include 'zz1.php'; ?>
<title><?php echo _('Android™-App'); ?> | Ballmanager.de</title>
<?php if ($loggedin == 1) { ?>
<style type="text/css">
<!--
#user_<?php echo $cookie_id; ?> {
	font-weight: bold;
}
-->
</style>
<?php
}
$filter_land = '';
if (isset($_GET['land'])) {
	$filter_land = htmlspecialchars(trim(strip_tags($_GET['land'])));
}
?>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Android™-App'); ?></h1>
<p><?php echo _('Du hast ein Smartphone oder Tablet mit Android? Dann spiele den Ballmanager ganz einfach von unterwegs:'); ?></p>
<ol>
<li><?php echo _('Aktiviere unter <em>Einstellungen</em> &raquo; <em>Anwendungen</em> das Kästchen bei &quot;Unbekannte Quellen&quot;, damit Du die Ballmanager-App von dieser Seite aus installieren kannst.'); ?></li>
<li><?php echo _('Lade <a href="/Ballmanager.apk">diese Datei</a> auf Dein Gerät und öffne Sie - die Installation startet automatisch.'); ?></li>
<li><?php echo _('Auf Geräten bis einschließlich Android 2.3.3 findest Du alle Bereiche des Spiels im Menü, das sich öffnet, wenn Du die Menü-Taste Deines Gerätes drückst. Auf Geräten ab Android 3.0 findest Du alles im Menü am oberen Rand des Bildschirms. Über die drei Punkte/Striche, die übereinander zu sehen sind, kannst Du die restlichen Menü-Einträge anzeigen lassen.'); ?></li>
</ol>
<?php include 'zz3.php'; ?>
