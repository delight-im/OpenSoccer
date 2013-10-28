<?php include 'zz1.php'; ?>
<title>Wer ist online? | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1>Wer ist online?</h1>
<?php if ($loggedin == 1) { ?>
<p><table><thead><tr class="odd"><th scope="col">Manager</th><th scope="col">Aktiv</th><th scope="col">Chat</th><th scope="col">Team</th><th scope="col">Aktion</th></tr></thead><tbody>
<?php
setTaskDone('check_wio');
// FREUNDESLISTE LADEN ANFANG
$kontaktListe1 = "SELECT f2 FROM ".$prefix."freunde WHERE f1 = '".$cookie_id."' AND typ = 'F'";
$kontaktListe2 = mysql_query($kontaktListe1);
$kontaktListe = array();
while ($kontaktListe3 = mysql_fetch_assoc($kontaktListe2)) {
	$kontaktListe[$kontaktListe3['f2']] = 1;
}
// FREUNDESLISTE LADEN ENDE
$timeout = getTimestamp('-5 minutes');
$kontakt1 = "SELECT a.ids, a.username, a.last_login, a.last_chat, a.liga, a.team, b.name FROM ".$prefix."users AS a JOIN ".$prefix."teams AS b ON a.team = b.ids WHERE a.last_login > ".$timeout." ORDER BY a.last_login DESC LIMIT 0, 20";
$kontakt2 = mysql_query($kontakt1);
while ($kontakt3 = mysql_fetch_assoc($kontakt2)) {
	if (isset($kontaktListe[$kontakt3['ids']])) { $mark1 = '<strong>'; $mark2 = '</strong>'; } else { $mark1 = ''; $mark2 = ''; }
    echo '<tr><td class="link">'.$mark1.displayUsername($kontakt3['username'], $kontakt3['ids']);
    echo $mark2.'</td><td>'.$mark1.date('H:i', $kontakt3['last_login']).$mark2.'</td>';
	if ($kontakt3['last_chat'] == 0 OR date('Y-m-d', $kontakt3['last_chat']) != date('Y-m-d')) { // wenn nicht im Chat bisher oder nicht heute
		echo '<td>'.$mark1.'&nbsp;-&nbsp;'.$mark2.'</td>';
	}
	else {
		echo '<td>'.$mark1.date('H:i', $kontakt3['last_chat']).$mark2.'</td>';
	}
	echo '<td class="link">'.$mark1.'<a href="/team.php?id='.$kontakt3['team'].'">'.$kontakt3['name'].'</a>'.$mark2.'</td>';
	echo '<td class="link">'.$mark1.'<a href="/post_schreiben.php?id='.$kontakt3['ids'].'">Post schicken</a>'.$mark2.'</td></tr>';
}
?>
</tbody></table></p>
<p><strong>Hinweis:</strong> Deine <a href="/freunde.php">Freunde</a> sind fett markiert.</p>

<h1 id="teamList">Support-Team</h1>
<p><table><thead><tr class="odd"><th scope="col">Manager</th><th scope="col">Aktiv</th><th scope="col">Chat</th><th scope="col">Aktion</th></tr></thead><tbody>
<?php
$kontakt1 = "SELECT ids, username, last_login, last_chat FROM ".$prefix."users WHERE status = 'Helfer'";
$kontakt2 = mysql_query($kontakt1);
while ($kontakt3 = mysql_fetch_assoc($kontakt2)) {
    echo '<tr><td class="link">'.displayUsername($kontakt3['username'], $kontakt3['ids']).'</td><td>'.date('d.m.Y H:i', $kontakt3['last_login']).'</td>';
	if ($kontakt3['last_chat'] == 0 OR date('Y-m-d', $kontakt3['last_chat']) != date('Y-m-d')) { // wenn nicht im Chat bisher oder nicht heute
		echo '<td>&nbsp;-&nbsp;</td>';
	}
	else {
		echo '<td>'.date('H:i', $kontakt3['last_chat']).'</td>';
	}	
	echo '<td class="link"><a href="/post_schreiben.php?id='.$kontakt3['ids'].'">Post schicken</a></td></tr>';
}
?>
</tbody></table></p>

<?php } else { ?>
<p>Du musst angemeldet sein, um diese Seite aufrufen zu können!</p>
<?php } ?>
<?php include 'zz3.php'; ?>