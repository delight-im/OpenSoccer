<?php include 'zz1.php'; ?>
<title><?php echo _('Freunde einladen'); ?> | Ballmanager.de</title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Freunde einladen'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p><strong><?php echo _('Deine Freunde sind noch nicht beim Ballmanager?').'</strong><br />'._('Dann nutze den folgenden Link, um sie einzuladen.').'</p><p>'._('Wenn sie sich nach Deiner Empfehlung über den Link registrieren, bekommt <strong>ihr beide</strong> jeweils <strong>7,5 Mio.</strong> als Prämie aufs Vereinskonto:'); ?></p>
<p style="display:block; width:360px; margin:0 auto; padding:2px 4px; background-color:#00f; color:#fff;"><a target="_blank" style="color:#fff;" href="http://www.ballmanager.de/?r=<?php echo $cookie_id; ?>">www.ballmanager.de/?r=<?php echo $cookie_id; ?></a></p>
<p>
    <a target="_blank" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://www.ballmanager.de/?r='.$cookie_id); ?>">
        <img alt="Facebook" src="/images/brands/facebook-32.png" width="32" />
    </a>
    <a target="_blank" title="Twitter" href="https://twitter.com/share?url=<?php echo urlencode('http://www.ballmanager.de/?r='.$cookie_id); ?>">
        <img alt="Twitter" src="/images/brands/twitter-32.png" width="32" />
    </a>
    <a target="_blank" title="Google Plus" href="https://plus.google.com/share?url=<?php echo urlencode('http://www.ballmanager.de/?r='.$cookie_id); ?>">
        <img alt="Google Plus" src="/images/brands/google-plus-32.png" width="32" />
    </a>
</p>
<p style="font-size:80%; color:#666;"><?php echo _('Dein Verein erhält die Prämie, sobald der andere Manager seine Manager-Prüfung abgeschlossen hat. Voraussetzung ist allerdings, dass Dein Account und der geworbene Account nicht vom selben Computer aus gesteuert werden.'); ?></p>
<h1><?php echo _('Geworbene Manager'); ?></h1>
<table>
<thead>
<tr class="odd">
<th scope="col">&nbsp;</th>
<th scope="col"><?php echo _('Datum'); ?></th>
<th scope="col"><?php echo _('Geworben'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "SELECT a.geworben, a.billed, a.zeit, b.username FROM ".$prefix."referrals AS a JOIN ".$prefix."users AS b ON a.geworben = b.ids WHERE a.werber = '".$cookie_id."' ORDER BY zeit DESC LIMIT 0, 100";
$sql2 = mysql_query($sql1);
if (mysql_num_rows($sql2) == 0) {
	echo '<tr><td colspan="2">'._('Du hast bisher noch keine User geworben!').'</td></tr>';
}
else {
	$counter = 0;
	while ($sql3 = mysql_fetch_assoc($sql2)) {
		if ($counter % 2 == 0) { echo '<tr>'; } else { echo '<tr class="odd">'; }
		echo '<td>';
		if ($sql3['billed'] == 1) {
			echo '<img src="/images/erfolg.png" width="16" alt="X" title="'._('Prämie erhalten').'" />';
		}
		else {
			echo '<img src="/images/fehler.png" width="16" alt="O" title="'._('Prämie noch nicht ausgezahlt').'" />';
		}
		echo '</td>';
		echo '<td>'.displayUsername($sql3['username'], $sql3['geworben']).'</td>';
		echo '<td>'.date('d.m.Y', $sql3['zeit']).'</td>';
		echo '</tr>';
		$counter++;
	}
}
?>
</tbody>
</table>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
