<h1>Statistik wählen</h1>
<form action="/statistiken_redirect.php" method="post" accept-charset="utf-8">
<p><select name="stat" size="1" style="width:200px">
<?php
$statistiken = array();
$statistiken[] = array('5jahresWertung', '5-Jahres-Wertung');
$statistiken[] = array('cupsieger', 'Cup-Sieger');
$statistiken[] = array('dauerbrenner', 'Dauerbrenner');
$statistiken[] = array('ergebnisverlauf', 'Ergebnisverlauf');
$statistiken[] = array('geschichte', 'Geschichte');
$statistiken[] = array('ligaTausch', 'Getauschte Ligen');
$statistiken[] = array('globale_tabelle', 'Globale Tabelle');
$statistiken[] = array('juengste_kader', 'Jüngste Kader');
$statistiken[] = array('meiste_titel', 'Meiste Titel');
$statistiken[] = array('meiste_zuschauer', 'Meiste Zuschauer');
$statistiken[] = array('meister', 'Meister');
$statistiken[] = array('pokalsieger', 'Pokal-Sieger');
$statistiken[] = array('reichste_ligen', 'Reichste Ligen');
$statistiken[] = array('saisonverlauf', 'Saisonverlauf');
$statistiken[] = array('staerkste_ligen_a', 'Stärkste Ligen - Aufstellung');
$statistiken[] = array('staerkste_ligen_k', 'Stärkste Ligen - Kader');
$statistiken[] = array('staerkste_ligen_r', 'Stärkste Ligen - RKP');
$statistiken[] = array('transfer_uebersicht', 'Teuerste Transfers');
$statistiken[] = array('torjaegerliste', 'Torjägerliste');
$statistiken[] = array('treuesteSpieler', 'Treueste Spieler');
$statistiken[] = array('geldwert', 'Wert des Geldes');
$statistiken[] = array('wertvollste_spieler', 'Wertvollste Spieler');
$statistiken[] = array('wertvollste_teams', 'Wertvollste Teams');
$statistiken[] = array('zuschauerverlauf', 'Zuschauerverlauf');
foreach ($statistiken as $statistik) {
	echo '<option value="'.$statistik[0].'"';
	if ($_SERVER['SCRIPT_NAME'] == '/stat_'.$statistik[0].'.php') { echo ' selected="selected"'; }
	echo '>'.$statistik[1].'</option>';
}
?>
</select> <input type="submit" value="Auswählen" /></p>
</form>