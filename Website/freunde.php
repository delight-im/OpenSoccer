<?php include 'zz1.php'; ?>
<title>Freunde | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<?php if ($loggedin == 1) { ?>
<?php
$an1 = "SELECT ".$prefix."users.username, ".$prefix."freunde_anfragen.von FROM ".$prefix."freunde_anfragen JOIN ".$prefix."users ON ".$prefix."freunde_anfragen.von = ".$prefix."users.ids WHERE ".$prefix."freunde_anfragen.an = '".$cookie_id."'";
$an2 = mysql_query($an1);
$an2a = mysql_num_rows($an2);
if ($an2a > 0) { echo '<h1>Anfragen</h1><p><table><thead><tr class="odd"><th scope="col">Manager</th><th scope="col">Aktionen</th></tr></thead><tbody>';
    while ($an3 = mysql_fetch_assoc($an2)) {
        echo '<tr><td class="link">'.displayUsername($an3['username'], $an3['von']).'</td><td><form action="/freunde_aktion.php" method="get" accept-charset="utf-8"><input type="hidden" name="id" value="'.$an3['von'].'" /><input type="submit" name="aktion" value="Annehmen"'.noDemoClick($cookie_id).' /> <input type="submit" name="aktion" value="Ablehnen"'.noDemoClick($cookie_id).' /></form></td>';
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
echo '<h1>Du hast zurzeit '.$kontakt2a.' Freunde beim Ballmanager</h1>';
if ($kontakt2a == 'noch keine') { echo '<p>Wenn Du Deine Freunde zu dieser Liste hinzufügen möchtest, dann öffne bitte das Profil des jeweiligen Managers und klicke unten auf den Link &quot;Freundschaft anbieten&quot;.</p>'; }
else {
	echo '<p><table><thead><tr class="odd"><th scope="col">Manager</th><th scope="col">Letzte Aktion</th><th scope="col">&nbsp;</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
    while ($kontakt3 = mysql_fetch_assoc($kontakt2)) {
		$sortUp = $kontakt3['sortOrder']+1;
		if ($sortUp > 125) { $sortUp = 125; }
		$sortDown = $kontakt3['sortOrder']-1;
		if ($sortDown < -125) { $sortDown = -125; }
        echo '<tr><td class="link">'.displayUsername($kontakt3['username'], $kontakt3['f2']).'</td><td>'.date('d.m.Y, H:i', $kontakt3['last_login']).' Uhr</td><td class="link"><a href="/post_schreiben.php?id='.$kontakt3['f2'].'">Nachricht schicken</a></td>';
		echo '<td><a href="/freunde.php?sort='.$sortUp.'&amp;user='.$kontakt3['f2'].'"><img src="//www.ballmanager.de/images/arrow_up.png" alt="+" title="Nach oben verschieben" /></a> <a href="/freunde.php?sort='.$sortDown.'&amp;user='.$kontakt3['f2'].'"><img src="//www.ballmanager.de/images/arrow_down.png" alt="-" title="Nach unten verschieben" /></a></td>';
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
echo '<h1>Du ignorierst zurzeit '.$kontakt2a.' User beim Ballmanager</h1>';
if ($kontakt2a == 'noch keine') { echo '<p>Wenn Du User zu dieser Liste hinzufügen möchtest, dann öffne bitte das Profil des jeweiligen Managers und klicke unten auf den Link &quot;Diesen User ignorieren&quot;.</p>'; }
else {
	echo '<p><table><thead><tr class="odd"><th scope="col">Manager</th><th scope="col">Letzte Aktion</th><th scope="col">&nbsp;</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
    while ($kontakt3 = mysql_fetch_assoc($kontakt2)) {
		$sortUp = $kontakt3['sortOrder']+1;
		if ($sortUp > 125) { $sortUp = 125; }
		$sortDown = $kontakt3['sortOrder']-1;
		if ($sortDown < -125) { $sortDown = -125; }
        echo '<tr><td class="link">'.displayUsername($kontakt3['username'], $kontakt3['f2']).'</td><td>'.date('d.m.Y, H:i', $kontakt3['last_login']).' Uhr</td><td class="link"><a href="/post_schreiben.php?id='.$kontakt3['f2'].'">Nachricht schicken</a></td>';
		echo '<td><a href="/freunde.php?sort='.$sortUp.'&amp;user='.$kontakt3['f2'].'"><img src="//www.ballmanager.de/images/arrow_up.png" alt="+" title="Nach oben verschieben" /></a> <a href="/freunde.php?sort='.$sortDown.'&amp;user='.$kontakt3['f2'].'"><img src="//www.ballmanager.de/images/arrow_down.png" alt="-" title="Nach unten verschieben" /></a></td>';
		echo '</tr>';
    }
    echo '</tbody></table></p>';
}
// IGNORIER-LISTE ENDE
?>
<?php } else { ?>
<h1>Freunde</h1>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>
