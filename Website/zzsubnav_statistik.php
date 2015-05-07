<h1><?php echo _('Statistik wählen'); ?></h1>
<form action="/statistiken_redirect.php" method="post" accept-charset="utf-8">
<p><select name="stat" size="1" style="width:200px">
<?php
$statistiken = array();
$statistiken[] = array('5jahresWertung', _('5-Jahres-Wertung'));
$statistiken[] = array('cupsieger', _('Cup-Sieger'));
$statistiken[] = array('dauerbrenner', _('Dauerbrenner'));
$statistiken[] = array('ergebnisverlauf', _('Ergebnisverlauf'));
$statistiken[] = array('geschichte', _('Geschichte'));
$statistiken[] = array('ligaTausch', _('Getauschte Ligen'));
$statistiken[] = array('globale_tabelle', _('Globale Tabelle'));
$statistiken[] = array('juengste_kader', _('Jüngste Kader'));
$statistiken[] = array('meiste_titel', _('Meiste Titel'));
$statistiken[] = array('meiste_zuschauer', _('Meiste Zuschauer'));
$statistiken[] = array('meister', _('Meister'));
$statistiken[] = array('pokalsieger', _('Pokal-Sieger'));
$statistiken[] = array('reichste_ligen', _('Reichste Ligen'));
$statistiken[] = array('saisonverlauf', _('Saisonverlauf'));
$statistiken[] = array('staerkste_ligen_a', _('Stärkste Ligen - Aufstellung'));
$statistiken[] = array('staerkste_ligen_k', _('Stärkste Ligen - Kader'));
$statistiken[] = array('staerkste_ligen_r', _('Stärkste Ligen - RKP'));
$statistiken[] = array('transfer_uebersicht', _('Teuerste Transfers'));
$statistiken[] = array('torjaegerliste', _('Torjägerliste'));
$statistiken[] = array('treuesteSpieler', _('Treueste Spieler'));
$statistiken[] = array('geldwert', _('Wert des Geldes'));
$statistiken[] = array('wertvollste_spieler', _('Wertvollste Spieler'));
$statistiken[] = array('wertvollste_teams', _('Wertvollste Teams'));
$statistiken[] = array('zuschauerverlauf', _('Zuschauerverlauf'));
foreach ($statistiken as $statistik) {
	echo '<option value="'.$statistik[0].'"';
	if ($_SERVER['SCRIPT_NAME'] == '/stat_'.$statistik[0].'.php') { echo ' selected="selected"'; }
	echo '>'.$statistik[1].'</option>';
}
?>
</select> <input type="submit" value="<?php echo _('Auswählen'); ?>" /></p>
</form>
