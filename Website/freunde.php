<?php include 'zz1.php'; ?>
<title><?php echo _('Freunde'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
$an1 = "SELECT ".$prefix."users.username, ".$prefix."freunde_anfragen.von FROM ".$prefix."freunde_anfragen JOIN ".$prefix."users ON ".$prefix."freunde_anfragen.von = ".$prefix."users.ids WHERE ".$prefix."freunde_anfragen.an = '".$cookie_id."'";
$an2 = mysql_query($an1);
$an2a = mysql_num_rows($an2);
if ($an2a > 0) { echo '<h1>'._('Anfragen').'</h1><p><table><thead><tr class="odd"><th scope="col">'._('Manager').'</th><th scope="col">'._('Aktionen').'</th></tr></thead><tbody>';
    while ($an3 = mysql_fetch_assoc($an2)) {
        echo '<tr><td class="link">'.displayUsername($an3['username'], $an3['von']).'</td><td><form action="/freunde_aktion.php" method="get" accept-charset="utf-8"><input type="hidden" name="id" value="'.$an3['von'].'" /><button type="submit" name="aktion" value="Annehmen"'.noDemoClick($cookie_id).'>'._('Annehmen').'</button> <button type="submit" name="aktion" value="Ablehnen"'.noDemoClick($cookie_id).'>'._('Ablehnen').'</button></form></td>';
    }
    echo '</tbody></table></p>';
}
?>
<?php
if (isset($_GET['sort']) && isset($_GET['user'])) {
	$sortSort = intval($_GET['sort']);
	$sortUser = mysql_real_escape_string(trim(strip_tags($_GET['user'])));
	$sql1 = "UPDATE ".$prefix."freunde SET sortOrder = ".$sortSort." WHERE f1 = '".$cookie_id."' AND f2 = '".$sortUser."'";
	$sql2 = mysql_query($sql1);
}
// FREUNDE-LISTE ANFANG
$kontakt1 = "SELECT username, last_login, f2, sortOrder FROM ".$prefix."freunde JOIN ".$prefix."users ON ".$prefix."freunde.f2 = ".$prefix."users.ids WHERE ".$prefix."freunde.f1 = '".$cookie_id."' AND typ = 'F' ORDER BY sortOrder DESC";
$kontakt2 = mysql_query($kontakt1);
$kontakt2a = mysql_num_rows($kontakt2);
if ($kontakt2a == 0) { $kontakt2a = 'noch keine'; }
echo '<h1>'.__('Du hast zurzeit %d Freunde im Spiel', $kontakt2a).'</h1>';
if ($kontakt2a == 'noch keine') { echo '<p>'._('Wenn Du Deine Freunde zu dieser Liste hinzufügen möchtest, dann öffne bitte das Profil des jeweiligen Managers und klicke unten auf den Link &quot;Freundschaft anbieten&quot;.').'</p>'; }
else {
	echo '<p><table><thead><tr class="odd"><th scope="col">'._('Manager').'</th><th scope="col">'._('Letzte Aktion').'</th><th scope="col">&nbsp;</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
    while ($kontakt3 = mysql_fetch_assoc($kontakt2)) {
		$sortUp = $kontakt3['sortOrder']+1;
		if ($sortUp > 125) { $sortUp = 125; }
		$sortDown = $kontakt3['sortOrder']-1;
		if ($sortDown < -125) { $sortDown = -125; }
        echo '<tr><td class="link">'.displayUsername($kontakt3['username'], $kontakt3['f2']).'</td><td>'.date('d.m.Y, H:i', $kontakt3['last_login']).' Uhr</td><td class="link"><a href="/post_schreiben.php?id='.$kontakt3['f2'].'">Nachricht schicken</a></td>';
		echo '<td><a href="/freunde.php?sort='.$sortUp.'&amp;user='.$kontakt3['f2'].'"><img src="/images/arrow_up.png" alt="+" title="'._('Nach oben verschieben').'" /></a> <a href="/freunde.php?sort='.$sortDown.'&amp;user='.$kontakt3['f2'].'"><img src="/images/arrow_down.png" alt="-" title="'._('Nach unten verschieben').'" /></a></td>';
		echo '</tr>';
    }
    echo '</tbody></table></p>';
}
// FREUNDE-LISTE ENDE
// IGNORIER-LISTE ANFANG
$kontakt1 = "SELECT username, last_login, f2, sortOrder FROM ".$prefix."freunde JOIN ".$prefix."users ON ".$prefix."freunde.f2 = ".$prefix."users.ids WHERE ".$prefix."freunde.f1 = '".$cookie_id."' AND typ = 'B' ORDER BY sortOrder DESC";
$kontakt2 = mysql_query($kontakt1);
$kontakt2a = mysql_num_rows($kontakt2);
if ($kontakt2a == 0) { $kontakt2a = 'noch keine'; }
echo '<h1>'.__('Du ignorierst zurzeit %d User im Spiel', $kontakt2a).'</h1>';
if ($kontakt2a == 'noch keine') { echo '<p>'._('Wenn Du User zu dieser Liste hinzufügen möchtest, dann öffne bitte das Profil des jeweiligen Managers und klicke unten auf den Link &quot;Diesen User ignorieren&quot;.').'</p>'; }
else {
	echo '<p><table><thead><tr class="odd"><th scope="col">'._('Manager').'</th><th scope="col">'._('Letzte Aktion').'</th><th scope="col">&nbsp;</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
    while ($kontakt3 = mysql_fetch_assoc($kontakt2)) {
		$sortUp = $kontakt3['sortOrder']+1;
		if ($sortUp > 125) { $sortUp = 125; }
		$sortDown = $kontakt3['sortOrder']-1;
		if ($sortDown < -125) { $sortDown = -125; }
        echo '<tr><td class="link">'.displayUsername($kontakt3['username'], $kontakt3['f2']).'</td><td>'.date('d.m.Y, H:i', $kontakt3['last_login']).' Uhr</td><td class="link"><a href="/post_schreiben.php?id='.$kontakt3['f2'].'">Nachricht schicken</a></td>';
		echo '<td><a href="/freunde.php?sort='.$sortUp.'&amp;user='.$kontakt3['f2'].'"><img src="/images/arrow_up.png" alt="+" title="'._('Nach oben verschieben').'" /></a> <a href="/freunde.php?sort='.$sortDown.'&amp;user='.$kontakt3['f2'].'"><img src="/images/arrow_down.png" alt="-" title="'._('Nach unten verschieben').'" /></a></td>';
		echo '</tr>';
    }
    echo '</tbody></table></p>';
}
// IGNORIER-LISTE ENDE
?>
<?php } else { ?>
<h1><?php echo _('Freunde'); ?></h1>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
