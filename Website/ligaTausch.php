<?php include 'zz1.php'; ?>
<title><?php echo _('Liga-Tausch'); ?> - <?php echo CONFIG_SITE_NAME; ?></title>
<?php include 'zz2.php'; ?>
<h1><?php echo _('Liga-Tausch'); ?></h1>
<?php if ($loggedin == 1) { ?>
<p style="text-align:right"><a href="/ligaTauschWuensche.php" class="pagenava"><?php echo _('Tausch-Wünsche'); ?></a></p>
<?php
$ownLand1 = "SELECT land FROM ".$prefix."ligen WHERE ids = '".$cookie_liga."'";
$ownLand2 = mysql_query($ownLand1);
$ownLand3 = mysql_fetch_assoc($ownLand2);
$ownLand4 = $ownLand3['land'];
?>
<p style="color:red"><strong><?php echo _('Wichtig: Vor Liga-Tausch erst lesen:'); ?></strong></p>
<p><?php echo __('Du möchtest in einer anderen Liga spielen? In %s hast Du schon alles gewonnen und jetzt möchtest Du in einem anderen Land um den Titel kämpfen? Oder ein anderes Land gefällt Dir einfach besser?', $ownLand4); ?></p>
<p><?php echo _('Dann ist der Liga-Tausch genau das Richtige für Dich! Tausche einfach mit einem anderen Manager die Liga, der gerne in Deiner Liga spielen würde.'); ?></p>
<p><?php echo _('Versendete Anfragen verfallen automatisch nach 24h. Kläre deshalb am besten vorher schon per Post mit dem anderen Manager, ob Interesse an einem Tausch besteht.'); ?></p>
<p><?php echo _('Wenn beide Manager einem Tausch zustimmen, dann werden die Namen der Teams getauscht und Du spielst alle Wettbewerbe (Liga, Pokal, Cup und Test) für das andere Team zu Ende. Der Rest (Spieler, Konto, Titel usw.) bleibt gleich. Beachte also, dass Testspiele, die Du eventuell schon vereinbart hast, nicht zum neuen Verein mitgenommen werden.'); ?></p>
<?php
$sperre1 = "SELECT MAX(zeit) FROM ".$prefix."ligaChanges WHERE team1 = '".$cookie_team."'";
$sperre2 = mysql_query($sperre1);
$sperre3 = mysql_result($sperre2, 0);
$daysToWait = 45-round((time()-$sperre3)/86400);
if ($daysToWait > 0) {
	echo '<p><strong>'.__('Du musst noch %d Tage warten, bis Du wieder die Liga wechseln kannst.', $daysToWait).'</strong></p>';
	include 'zz3.php';
	exit;
}
elseif (GameTime::getMatchDay() > 5) {
	echo '<p><strong>'._('Der Verband erlaubt einen Liga-Tausch nur an den ersten fünf Spieltagen. Bitte warte bis zur nächsten Saison.').'</strong></p>';
	include 'zz3.php';
	exit;
}
elseif ($live_scoring_spieltyp_laeuft != '') {
	echo '<p><strong>'.__('Zurzeit laufen %s-Spiele. Deshalb kannst Du leider keine Liga-Wechsel durchführen. Bitte warte, bis die Spiele beendet sind.', $live_scoring_spieltyp_laeuft).'</strong></p>';
	include 'zz3.php';
	exit;
}
else {
	$timeout = getTimestamp('-1 day');
	$sql1 = "DELETE FROM ".$prefix."ligaChangeAnfragen WHERE zeit < ".$timeout;
	$sql2 = mysql_query($sql1);
	if (isset($_POST['wishTeam']) && $cookie_id != CONFIG_DEMO_USER) {
		$wishTeam = mysql_real_escape_string(trim(strip_tags($_POST['wishTeam'])));
		$sql1 = "SELECT a.ids, b.land FROM ".$prefix."teams AS a JOIN ".$prefix."ligen AS b ON a.liga = b.ids WHERE a.name = '".$wishTeam."'";
		$sql2 = mysql_query($sql1);
		if (mysql_num_rows($sql2) == 0) {
			addInfoBox(_('Es konnte kein Team mit dem angegebenen Namen gefunden werden.'));
		}
		else {
			$sql3 = mysql_fetch_assoc($sql2);
			if ($sql3['land'] == $ownLand4) {
				addInfoBox(_('Du kannst nicht innerhalb des eigenen Landes die Liga tauschen.'));
			}
			else {
				$sql1 = "INSERT INTO ".$prefix."ligaChangeAnfragen (vonTeam, anTeam, zeit) VALUES ('".$cookie_team."', '".$sql3['ids']."', ".time().")";
				$sql2 = mysql_query($sql1);
				if ($sql2 == FALSE) {
					addInfoBox(_('Du hast diesem Team schon eine Anfrage geschickt. Bitte warte die Entscheidung des Managers ab.'));
				}
				else {
					addInfoBox(_('Dem anderen Manager wurde eine Anfrage zugeschickt, die er jetzt annehmen oder ablehnen kann. Bitte informiere ihn darüber per Post.'));
				}
			}
		}
	}
	if (isset($_POST['newTeam']) && isset($_POST['aktion']) && $cookie_id != CONFIG_DEMO_USER) {
		$newTeam = mysql_real_escape_string(trim(strip_tags($_POST['newTeam'])));
		$sql1 = "DELETE FROM ".$prefix."ligaChangeAnfragen WHERE vonTeam = '".$newTeam."' AND anTeam = '".$cookie_team."'";
		$sql2 = mysql_query($sql1);
		if (mysql_affected_rows() > 0) {
			if ($_POST['aktion'] == 'Annehmen') {
				$otherManager1 = "SELECT ids FROM ".$prefix."users WHERE team = '".$newTeam."'";
				$otherManager2 = mysql_query($otherManager1);
				if (mysql_num_rows($otherManager2) == 1) {
					$otherManager3 = mysql_fetch_assoc($otherManager2);
                    $sql1 = "DELETE FROM ".$prefix."ligaChangeAnfragen WHERE vonTeam = '".$cookie_team."' OR anTeam = '".$cookie_team."'";
                    $sql2 = mysql_query($sql1);
                    $sql1 = "DELETE FROM ".$prefix."ligaChangeAnfragen WHERE vonTeam = '".$newTeam."' OR anTeam = '".$newTeam."'";
                    $sql2 = mysql_query($sql1);
                    // TAUSCH DURCHFUEHREN ANFANG
                    $preventNameDuplicates1 = "UPDATE ".$prefix."teams SET name = CONCAT(name,'TEMP') WHERE ids = '".$cookie_team."' OR ids = '".$newTeam."'";
                    mysql_query($preventNameDuplicates1);
                    $felderToChange = array('name', 'origName', 'liga', 'rank', 'punkte', 'tore', 'gegentore', 'vorjahr_platz', 'vorjahr_liga', 'pokalrunde', 'cuprunde', 'vorjahr_pokalrunde', 'vorjahr_cuprunde', 'sunS', 'sunU', 'sunN', 'elo');
                    $felderWhichAreStrings = array('name', 'origName', 'liga', 'vorjahr_liga');
                    $felderSelect = '';
                    foreach ($felderToChange as $feldToChange) {
                        $felderSelect .= $feldToChange.', ';
                    }
                    $felderSelect = substr($felderSelect, 0, -2);
                    $daten1 = "SELECT ".$felderSelect." FROM ".$prefix."teams WHERE ids = '".$cookie_team."' LIMIT 0, 1";
                    $daten1 = mysql_query($daten1);
                    $daten2 = "SELECT ".$felderSelect." FROM ".$prefix."teams WHERE ids = '".$newTeam."' LIMIT 0, 1";
                    $daten2 = mysql_query($daten2);
                    if (mysql_num_rows($daten1) == 1 && mysql_num_rows($daten2) == 1) {
                        $daten1 = mysql_fetch_assoc($daten1);
                        $daten2 = mysql_fetch_assoc($daten2);
                        $felderChangeSql = '';
                        foreach ($felderToChange as $feldToChange) {
                            $felderChangeSql .= $feldToChange." = ";
                            if (in_array($feldToChange, $felderWhichAreStrings)) { $felderChangeSql .= "'"; }
                            if ($feldToChange == 'name') {
                                $felderChangeSql .= mysql_real_escape_string(substr($daten2[$feldToChange], 0, -4));
                            }
                            else {
                                $felderChangeSql .= mysql_real_escape_string($daten2[$feldToChange]);
                            }
                            if (in_array($feldToChange, $felderWhichAreStrings)) { $felderChangeSql .= "'"; }
                            $felderChangeSql .= ", ";
                        }
                        $felderChangeSql = substr($felderChangeSql, 0, -2);
                        $sql1 = "UPDATE ".$prefix."teams SET ".$felderChangeSql." WHERE ids = '".$cookie_team."'";
                        $sql2 = mysql_query($sql1);
                        $sql1 = "UPDATE ".$prefix."users SET liga = '".mysql_real_escape_string($daten2['liga'])."' WHERE ids = '".$cookie_id."'";
                        $sql2 = mysql_query($sql1);
                        $felderChangeSql = '';
                        foreach ($felderToChange as $feldToChange) {
                            $felderChangeSql .= $feldToChange." = ";
                            if (in_array($feldToChange, $felderWhichAreStrings)) { $felderChangeSql .= "'"; }
                            if ($feldToChange == 'name') {
                                $felderChangeSql .= mysql_real_escape_string(substr($daten1[$feldToChange], 0, -4));
                            }
                            else {
                                $felderChangeSql .= mysql_real_escape_string($daten1[$feldToChange]);
                            }
                            if (in_array($feldToChange, $felderWhichAreStrings)) { $felderChangeSql .= "'"; }
                            $felderChangeSql .= ", ";
                        }
                        $felderChangeSql = substr($felderChangeSql, 0, -2);
                        $sql1 = "UPDATE ".$prefix."teams SET ".$felderChangeSql." WHERE ids = '".$newTeam."'";
                        $sql2 = mysql_query($sql1);
                        $sql1 = "UPDATE ".$prefix."users SET liga = '".mysql_real_escape_string($daten1['liga'])."' WHERE ids = '".$otherManager3['ids']."'";
                        $sql2 = mysql_query($sql1);
                        $tspiel1 = "DELETE FROM ".$prefix."testspiel_anfragen WHERE team1 = '".$cookie_team."' OR team1 = '".$newTeam."'";
                        $tspiel2 = mysql_query($tspiel1); // weil da falscher Name noch gespeichert ist
                        $tspiel1 = "DELETE FROM ".$prefix."transfermarkt_leihe WHERE (bieter = '".$daten1['name']."' OR bieter = '".$daten2['name']."') AND akzeptiert = 0";
                        $tspiel2 = mysql_query($tspiel1); // weil da falscher Name noch gespeichert ist
                        $tspiel1 = "DELETE FROM ".$prefix."ligaChangeWuensche WHERE teamID = '".$cookie_team."' OR teamID = '".$newTeam."'";
                        $tspiel2 = mysql_query($tspiel1); // weil da falscher Name noch gespeichert ist
                        $nameChange1 = "UPDATE ".$prefix."vNameChanges SET sperre = 0 WHERE team = '".$cookie_team."' OR team = '".$newTeam."'";
                        mysql_query($nameChange1);
                        // TAUSCH DURCHFUEHREN ENDE
                        $sql1 = "INSERT INTO ".$prefix."ligaChanges (user1, team1, user2, team2, zeit, newLiga1, newLiga2) VALUES ('".$otherManager3['ids']."', '".$newTeam."', '".$cookie_id."', '".$cookie_team."', ".time().", '".mysql_real_escape_string($daten1['liga'])."', '".mysql_real_escape_string($daten2['liga'])."')";
                        $sql2 = mysql_query($sql1);
                        $sql1 = "INSERT INTO ".$prefix."ligaChanges (user2, team2, user1, team1, zeit, newLiga1, newLiga2) VALUES ('".$otherManager3['ids']."', '".$newTeam."', '".$cookie_id."', '".$cookie_team."', ".time().", '".mysql_real_escape_string($daten2['liga'])."', '".mysql_real_escape_string($daten1['liga'])."')";
                        $sql2 = mysql_query($sql1);
                        addInfoBox(_('Die Anfrage wurde angenommen, eure Ligen wurden getauscht.'));
                        include 'zz3.php';
                        exit;
                    }
				}
			}
			elseif ($_POST['aktion'] == 'Ablehnen') {
				addInfoBox(_('Die Anfrage wurde abgelehnt.'));
			}
		}
	}
	?>
	<h1><?php echo _('Erhaltene Anfragen'); ?></h1>
	<?php
	$sql1 = "SELECT a.vonTeam, a.zeit, b.name FROM ".$prefix."ligaChangeAnfragen AS a JOIN ".$prefix."teams AS b ON a.vonTeam = b.ids WHERE a.anTeam = '".$cookie_team."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) {
		echo '<p>'._('Zurzeit keine Anfragen').'</p>';
	}
	else {
		echo '<p><table><thead><tr class="odd"><th scope="col">'._('Team').'</th><th scope="col">'._('Datum').'</th><th scope="col">&nbsp;</th></tr></thead><tbody>';
		$counter = 0;
		while ($sql3 = mysql_fetch_assoc($sql2)) {
			echo '<tr class="odd">';
			echo '<td class="link"><a href="/team.php?id='.$sql3['vonTeam'].'">'.$sql3['name'].'</a></td>';
			echo '<td>'.date('d.m.Y H:i', $sql3['zeit']).'</td>';
			echo '<td><form action="/ligaTausch.php" method="post" accept-charset="utf-8"><input type="hidden" name="newTeam" value="'.$sql3['vonTeam'].'" /><button type="submit" name="aktion" value="Annehmen"'.noDemoClick($cookie_id).'>'._('Annehmen').'</button> <button type="submit" name="aktion" value="Ablehnen"'.noDemoClick($cookie_id).'>'._('Ablehnen').'</button></form></td>';
			echo '</tr>';
			$counter++;
		}
		echo '</tbody></table></p>';
	}
	?>
	<h1><?php echo _('Anfrage versenden'); ?></h1>
	<p><?php echo _('Falls Du wirklich die Liga tauschen möchtest, gib bitte hier den Namen des Teams an, mit dem Du tauschen willst. Unbeantwortete Anfragen verfallen nach 24 Stunden.'); ?></p>
	<form action="/ligaTausch.php" method="post" accept-charset="utf-8">
	<p><strong><?php echo _('Teamname:'); ?></strong><br /><input type="text" name="wishTeam" style="width:200px" /></p>
	<p><input type="submit" value="<?php echo _('Anfragen'); ?>" onclick="return<?php echo noDemoClick($cookie_id, TRUE); ?> confirm('<?php echo _('Bist Du sicher?'); ?>');" /></p>
	</form>
	<h1><?php echo _('Versendete Anfragen'); ?></h1>
	<?php
	$sql1 = "SELECT a.anTeam, a.zeit, b.name FROM ".$prefix."ligaChangeAnfragen AS a JOIN ".$prefix."teams AS b ON a.anTeam = b.ids WHERE a.vonTeam = '".$cookie_team."'";
	$sql2 = mysql_query($sql1);
	if (mysql_num_rows($sql2) == 0) {
		echo '<p>'._('Zurzeit keine Anfragen').'</p>';
	}
	else {
		echo '<p><table><thead><tr class="odd"><th scope="col">'._('Team').'</th><th scope="col">'._('Datum').'</th><th scope="col">'._('Verfällt').'</th></tr></thead><tbody>';
		$counter = 0;
		while ($sql3 = mysql_fetch_assoc($sql2)) {
			echo '<tr class="odd">';
			echo '<td class="link"><a href="/team.php?id='.$sql3['anTeam'].'">'.$sql3['name'].'</a></td>';
			echo '<td>'.date('d.m.Y H:i', $sql3['zeit']).'</td>';
			echo '<td>in '.intval(24-round((time()-$sql3['zeit'])/3600)).'h</td>';
			echo '</tr>';
			$counter++;
		}
		echo '</tbody></table></p>';
	}
}
?>
<?php } else { ?>
<p><?php echo _('Du musst angemeldet sein, um diese Seite aufrufen zu können!'); ?></p>
<?php } ?>
<?php include 'zz3.php'; ?>
