<?php

if (!isset($_GET['mode'])) { include 'zzserver.php'; }
set_time_limit(0);

$vereinsKurzelEntfernung = array();
foreach ($kuerzelListe as $kuerzelEntry) {
	$vereinsKurzelEntfernung[] = $kuerzelEntry.' ';
	$vereinsKurzelEntfernung[] = ' '.$kuerzelEntry;
}
$tore = array();
function create_verletzung() {
	if (mt_rand(10, 20) < 17) { $vName = _('wegen einer Muskelzerrung'); $vDauer = 1; }
	elseif (mt_rand(10, 20) < 17) { $vName = _('aufgrund einer Verstauchung'); $vDauer = 3; }
	elseif (mt_rand(10, 20) < 17) { $vName = _('wegen einer Prellung'); $vDauer = 5; }
	elseif (mt_rand(10, 20) < 17) { $vName = _('aufgrund eines Muskelfaserrisses'); $vDauer = 7; }
	elseif (mt_rand(10, 20) < 17) { $vName = _('wegen eines Bänderrisses'); $vDauer = 9; }
	elseif (mt_rand(10, 20) < 17) { $vName = _('aufgrund eines Knorpelschadens'); $vDauer = 11; }
	else { $vName = _('durch einen Knochenbruch'); $vDauer = 13; }
	$daten = array('name' => $vName, 'dauer' => $vDauer);
	return $daten;
}
function get_minutes($anzahl_angriffe, $ballbesitz_team1) {
	$minuten_array = array();
    $intervall = 90/$anzahl_angriffe;
    $spielraum = ceil($intervall)-1;
    $minute = 1;
    $next = 0;
    $noch_angriffe_fuer_team1 = round($anzahl_angriffe*$ballbesitz_team1/100);
    for ($i = 1; $i <= $anzahl_angriffe; $i++) {
    	// ENTSCHEIDEN WER ANGREIFT ANFANG
    	$p_team1_greift_an = $noch_angriffe_fuer_team1/($anzahl_angriffe-$i+1)*100;
    	$zufallszahl = mt_rand(0, 100);
    	if ($zufallszahl < $p_team1_greift_an) {
    		$angreifendes_team = 1;
    	}
    	else {
    		$angreifendes_team = 2;
    	}
    	if ($angreifendes_team == 1) {
    		$noch_angriffe_fuer_team1--;
    	}
    	// ENTSCHEIDEN WER ANGREIFT ENDE

        if ($next != 0) {
            $zufall_zeit = $next;
            $next = 0;
        }
        else {
            $zufall_zeit = mt_rand(-$spielraum, $spielraum);
            $next = -$zufall_zeit;
        }
        $minute = $minute + $intervall + $zufall_zeit;
        $minuten_array[] = array(round($minute), $angreifendes_team);
    }
    return $minuten_array;
}
function tactics_weight($wert) {
	$neuerWert = $wert*0.25+0.5;
	return $neuerWert;
}
function strengths_weight($wert) {
	//$neuerWert = log10($wert+1)+0.35;
	$neuerWert = 0.125*$wert+0.0625;
	return $neuerWert;
}
function staerkeBenoten($wert) {
	for ($i = 6; $i > 0; $i--) {
		$grenze = 1.485626-0.2759375*($i-1);
		if ($wert <= $grenze) { return $i; }
	}
	return '?';
}
function kommentar($ersetzung, $typ) {
	$formulierungen = array();
	$formulierungen['start_game'] = array(
		_('Anstoß!'),
		_('Das Publikum ist gut gelaunt beim Anpfiff des heutigen Spiels.'),
		_('Das Spiel beginnt.'),
		_('Das Spiel fängt mit dem Anstoß vor begeisterten Zuschauern an.'),
		_('Bei windigem und kaltem Wetter beginnt das Spiel.'),
		_('Dunkle Wolken bildeten sich, als die Spieler das Feld betraten. Anstoß.'),
		_('Die Sonne scheint, das Gras ist grün und das Publikum freut sich aufs Spiel.'),
		_('Das Spielfeld ist etwas nass nach einigen Tagen Regen. Die erste Halbzeit beginnt.'),
		_('Los geht\'s mit der ersten Halbzeit!')
    );
	$formulierungen['mid_game'] = array(
		_('Halbzeit!'),
		_('Der Wind nimmt zu, die zweite Halbzeit beginnt.'),
        _('Los geht\'s mit der zweiten Halbzeit!'),
        _('Anpfiff zur zweiten Halbzeit.'),
        _('15 Minuten Pause.'),
		_('Die Spieler der Gäste sehen konzentrierter aus zu Beginn der zweiten Halbzeit.'),
		_('Der Schiedsrichter pfeift zur zweiten Halbzeit.'),
		_('Die Spieler des Gastgebers winken den Zuschauern zu, das Spiel geht weiter.'),
		_('Die Spieler des Gastgebers sehen frischer aus, währen die das Spielfeld zur zweiten Halbzeit betreten.')
    );
	$formulierungen['attack'] = array(
		_('XYZ am Ball.'),
		_('XYZ greift an.'),
		_('XYZ macht wieder Druck.'),
		_('Ballbesitz für XYZ.'),
		_('XYZ hat den Ball.')
    );
	$formulierungen['advance'] = array(
		_('Der Ballführende spielt den öffnenden Pass.'),
		_('Den Mitspieler mit einem öffnenden Pass bedient.'),
		_('Starker Diagonalpass.'),
		_('Pass zum Mitspieler.'),
		_('Schönes Dribbling im Mittelfeld.'),
		_('Pass von der rechten Seite nach innen.'),
		_('Pass von der linken Seite nach innen.'),
		_('Der Mitspieler steht völlig frei.'),
		_('Schöner Pass zum Mitspieler.'),
		_('Der präzise Pass kommt genau im richtigen Moment an.'),
		_('Der Ballführende spielt einen feinen Pass nach vorne.'),
		_('Sehenswertes Dribbling.'),
		_('Schöne Ballstafette in den eigenen Reihen.'),
		_('Schneller Doppelpass im Mittelfeld.'),
		_('Schönes Dribbling.'),
		_('Herrliche Kombination der Offensiv-Spieler.'),
		_('Der ungenaue Pass kommt mit Glück an.'),
		_('Unpräzises Anspiel, aber der Gegner kommt zu spät.'),
		_('Der Spieler nutzt seine Schnelligkeit.'),
		_('Guter Querpass zum Teamkollegen.')
    );
	$formulierungen['advance_further'] = array(
		_('Schöne Vorlage.'),
		_('Tolle Vorlage.'),
		_('Ein Dribbling wie aus dem Lehrbuch.'),
		_('Der Mitspieler dribbelt ein, zwei Spieler aus.'),
		_('Der Mitspieler dribbelt einen, nein zwei, Spieler aus.'),
		_('Der Mitspieler wird schön in Szene gesetzt.'),
		_('Der Angreifer lässt seine Gegenspieler stehen.'),
		_('Geschickt freigespielt.'),
		_('Schönes Dribbling des Stürmers.'),
		_('Der Stürmer wird herrlich von seinem Mitspieler bedient.'),
		_('Steilpass auf den linken Flügel.'),
		_('Der Spieler lässt seinen Gegner stehen.'),
		_('Der Spieler tunnelt seinen Gegner.'),
		_('Dribbling über den halben Platz.'),
		_('Der Ballführende vernascht '.mt_rand(2, 3).' Abwehrspieler.'),
		_('Steilpass auf den rechten Flügel.'),
		_('Den Kollegen mustergültig freigespielt.'),
		_('Traumpass auf den linken Flügelspieler.'),
		_('Traumpass auf den rechten Flügelspieler.'),
		_('Traumpass in die Spitze.'),
		_('Ball selbstlos quergelegt.'),
		_('Der kreuzende Mitspieler wird geschickt.'),
		_('Der Angreifer wird halblinks geschickt.'),
		_('Der Angreifer wird halbrechts geschickt.'),
		_('Steilpass auf den Stürmer, der jetzt alleine aufs Tor zuläuft.'),
		_('Die Offensivabteilung arbeitet eine Chance heraus.'),
		_('Jetzt wirds gefährlich ...'),
		_('Zuspiel in die Spitze.'),
		_('Langer Pass auf den Stürmer.'),
		_('Flanke aus dem Halbfeld.'),
		_('Schöne Flanke von rechts.'),
		_('Tolle Flanke von links.'),
		_('Schöne Flanke von links.'),
		_('Tolle Flanke von rechts.'),
		_('Schöne Flanke.'),
		_('Gute Flanke.'),
		_('Der Flügelspieler flankt auf den Stürmer.'),
		_('Der Spieler legt perfekt für seinen Teamkollegen auf.'),
		_('Der Angreifer legt für seinen Mitspieler zurück.')
    );
	$formulierungen['yellow'] = array(
		_('<span style="padding:2px;background-color:#ff0;">Gelbe Karte</span> für XYZ.'),
		_('Der Schiedsrichter zeigt <span style="padding:2px;background-color:#ff0;">Gelb</span>.'),
		_('Der Referee zückt die <span style="padding:2px;background-color:#ff0;">Gelbe Karte</span>.'),
		_('XYZ erhält eine <span style="padding:2px;background-color:#ff0;">Gelbe Karte</span>.'),
		_('XYZ kassiert eine <span style="padding:2px;background-color:#ff0;">Gelbe Karte</span>.'),
		_('Der Spieler von XYZ sieht <span style="padding:2px;background-color:#ff0;">Gelb</span>.'),
		_('Der Schiedsrichter zögert nicht lange: <span style="padding:2px;background-color:#ff0;">Gelb</span>!'),
		_('Der Schiedsrichter zögert nicht lange: <span style="padding:2px;background-color:#ff0;">Verwarnung</span>!'),
		_('Der Spieler von XYZ holt sich die <span style="padding:2px;background-color:#ff0;">Gelbe Karte</span> ab.'),
		_('Klare <span style="padding:2px;background-color:#ff0;">Gelbe Karte</span> für XYZ.')
    );
	$formulierungen['red'] = array(
		'<span style="padding:2px;background-color:#f00;color:#fff;">Platzverweis</span> für XYZ!',
		'Dem Schiedsrichter bleibt keine andere Wahl als hier die <span style="padding:2px;background-color:#f00;color:#fff;">Rote Karte</span> zu zeigen.',
		'<span style="padding:2px;background-color:#f00;color:#fff;">Rote Karte</span> für XYZ!',
		'Der Spieler von XYZ wird <span style="padding:2px;background-color:#f00;color:#fff;">des Feldes verwiesen</span>!',
		'Der Spieler von XYZ darf <span style="padding:2px;background-color:#f00;color:#fff;">vorzeitig duschen gehen</span>!',
		'Der Schiedsrichter <span style="padding:2px;background-color:#f00;color:#fff;">schickt den Übeltäter vom Platz</span>!',
		'Der Schiedsrichter zögert nicht lange: <span style="padding:2px;background-color:#f00;color:#fff;">Rot</span>!',
		'Der Schiedsrichter zögert nicht lange: <span style="padding:2px;background-color:#f00;color:#fff;">Platzverweis</span>!',
		'Der Schiedsrichter entscheidet auf Notbremse und zieht die <span style="padding:2px;background-color:#f00;color:#fff;">Rote Karte</span>!',
		'XYZ sieht nach einer Unsportlichkeit <span style="padding:2px;background-color:#f00;color:#fff;">Rot</span>!',
		'Der Unparteiische ahndet diese Aktion mit der <span style="padding:2px;background-color:#f00;color:#fff;">Roten Karte</span>!',
		'Der Spieler von XYZ kassiert <span style="padding:2px;background-color:#f00;color:#fff;">Rot</span>!',
		'Der Referee zückt die <span style="padding:2px;background-color:#f00;color:#fff;">Rote Karte</span>!',
		'Der Schiedsrichter zeigt <span style="padding:2px;background-color:#f00;color:#fff;">Rot</span>!'
    );
	$formulierungen['iFreeKick_shot_save'] = array(
		_('Der Torwart hat den Ball sicher.'),
		_('Der Torwart von XYZ hält.'),
		_('Der Ball fliegt Richtung Eckfahne.'),
		_('Leichte Aufgabe für den Schlussmann von XYZ.'),
		_('Kein Problem für den Torhüter.'),
		_('Der Keeper fängt den Ball locker ab.')
    );
	$formulierungen['dFreeKick_shot_save'] = array(
		_('Glanzparade vom Schlussmann!'),
		_('Was für ein Reflex!'),
		_('Souverän gehalten.')
    );
	$formulierungen['foul'] = array(
		_('Grobes Foul!'),
		_('Foul von XYZ.'),
		_('Bösartiges Foul des Abwehrspielers!'),
		_('Der Verteidiger setzt zur Grätsche an.'),
		_('Handspiel von XYZ.'),
		_('Diese Grätsche ging nur in die Beine des Gegners.'),
		_('Den Ball wollte er mit dieser Aktion sicher nicht holen. Grobes Foul!'),
		_('Was für eine Schwalbe! Hat der Schiedsrichter da etwa mehr gesehen als wir?'),
		_('War das wirklich ein Foul? Da kann man drüber streiten.'),
		_('Der Stürmer wird von den Beinen geholt.'),
		_('Foul vom Abwehrspieler.'),
		_('Der Spieler setzt sich mit dem Ellenbogen durch. Foul!'),
		_('Das hat der Referee gesehen - Foulspiel.'),
		_('Der Spieler wird zu Fall gebracht.'),
		_('Das sah nach einem Revanchefoul aus.'),
		_('Der Angreifer wird umgestoßen.')
    );
	$formulierungen['penalty'] = array(
		'<span style="text-transform:uppercase;">'._('Der Schiedsrichter zeigt auf den Punkt.').'</span>',
		'<span style="text-transform:uppercase;">'._('Elfmeter!').'</span>',
		'<span style="text-transform:uppercase;">'._('Der Unparteiische entscheidet auf Elfmeter.').'</span>',
		'<span style="text-transform:uppercase;">'._('Strafstoß!').'</span>',
		'<span style="text-transform:uppercase;">'._('Strafstoß für XYZ!').'</span>',
		'<span style="text-transform:uppercase;">'._('Elfmeter für XYZ!').'</span>'
    );
	$formulierungen['penalty_save'] = array(
		_('Der Torwart springt in die richtige Ecke ... und hält!'),
		_('Der Torwart bleibt stehen - Gehalten! Schwacher Schuss.'),
		_('Der Schlussmann von XYZ hält!'),
		_('Den hat der Keeper sicher!'),
		_('Schwach geschossen.'),
		_('Sensationelle Parade!'),
		_('Den hat der Keeper sicher. Schlecht geschossen!'),
		_('Toll gehalten vom Torwart!')
    );
	$formulierungen['penalty_miss'] = array(
		_('Drüber!'),
		_('XYZ schießt drüber!'),
		_('In die Wolken!'),
		_('An den Pfosten!'),
		_('XYZ schießt daneben!'),
		_('XYZ trifft nur den Pfosten.'),
		_('Schwacher Schuss vom Mittelfeldspieler!'),
		_('Der Stürmer setzt den Elfmeter an die Latte!'),
		_('Er vergibt die Chance!'),
		_('Das war knapp. Da fehlten nur Zentimeter.'),
		_('Der geht daneben!')
    );
	$formulierungen['iFreeKick'] = array(
		_('Indirekter Freistoß für XYZ!'),
		_('Indirekter Freistoß!'),
		_('Der Schiedsrichter entscheidet auf Freistoß, indirekt.'),
		'Freistoß für XYZ, ca. '.mt_rand(19, 35).'m vor dem gegnerischen Tor.'
    );
	$formulierungen['dFreeKick'] = array(
		_('Direkter Freistoß für XYZ!'),
		_('Direkter Freistoß!'),
		_('Freistoß an der Strafraumgrenze.'),
		_('Der Schiedsrichter entscheidet auf Freistoß, direkt.'),
		_('Direkter Freistoß vor dem Strafraum. 6 Mann in der Mauer.'),
		'Freistoß für XYZ, ca. '.mt_rand(17, 23).'m vor dem gegnerischen Tor.'
    );
	$formulierungen['iFreeKick_clear'] = array(
		_('Per Kopfball geklärt.'),
		_('XYZ klärt.'),
		_('Schuss geht daneben.'),
		_('Der Abwehrspieler klärt.'),
		_('Der Verteidiger bereinigt die Situation.'),
		_('Der Schuss geht auf die Tribüne. Hoffentlich hat sich kein Zuschauer verletzt.'),
		_('Der Schuss geht auf die Tribüne. Die Fans freuen sich über den gefangenen Ball.'),
		_('Der Schuss geht weit daneben.')
    );
	$formulierungen['stopped'] = array(
		_('Ballverlust im Mittelfeld.'),
		_('XYZ macht Druck und holt sich den Ball wieder.'),
		_('Perfektes Pressing, Ballverlust.'),
		_('XYZ steht zu gut.'),
		_('Keine Anspielstation.'),
		_('Unnötiger Fehlpass!'),
		_('Erstklassig verteidigt von XYZ.'),
		_('Schön verteidigt von XYZ.'),
		_('An diesem Verteidiger kommt heute wohl keiner vorbei!'),
		_('Der Gegner rutscht aus und XYZ holt sich den Ball wieder.'),
		_('Schwerer Ballverlust im Mittelfeld.'),
		_('Da ist kein Durchkommen.'),
		_('Die Verteidigung von XYZ steht sicher.'),
		_('XYZ stoppt den Angriff.'),
		_('Pass abgefangen.'),
		_('XYZ holt sich den Ball wieder.'),
		_('Fehlpass. Der Gegner hat den Ball.'),
		_('Der Gegner holt sich den Ball wieder.')
    );
	$formulierungen['iFreeKick_shot'] = array(
		_('Ein Spieler von XYZ kommt zum Kopfball.'),
		_('Per Kopf in den Strafraum verlängert.'),
		'Der Ball wird aus '.mt_rand(25, 45).'m hoch in den Strafraum gebracht.',
		'Schuss aus '.mt_rand(25, 45).'m, halbrechte Position.',
		'Schuss aus '.mt_rand(25, 45).'m, halblinke Position.',
		_('Der Ball wird abgefälscht ...'),
		'Flatterball aus '.mt_rand(25, 45).'m.',
		_('Der Ball wird verlängert.'),
		_('Zur Seite gelegt und Schuss!')
    );
	$formulierungen['dFreeKick_shot'] = array(
		_('Flachschuss.'),
		_('Da war noch jemand dran.'),
		_('Abgefälschter Schuss.'),
		_('Der Ball streift die Mauer.'),
		_('Schöne Freistoß-Variante.'),
		_('Der Ball geht durch die Mauer.'),
		_('Schön um die Mauer gezirkelt.')
    );
	$formulierungen['dFreeKick_clear'] = array(
		_('Der Ball geht in die Mauer.'),
		_('Der Schuss geht daneben.'),
		_('Der Schuss geht auf die Tribüne. Hoffentlich hat sich kein Zuschauer verletzt.'),
		_('Der Schuss geht auf die Tribüne. Die Fans freuen sich über den gefangenen Ball.'),
		_('Der Schuss geht weit drüber.'),
		_('Da fehlten nur Zentimeter.'),
		_('Der Verteidiger wirft sich in den Schuss.')
    );
	$formulierungen['shot'] = array(
		_('Schuss und ...'),
		_('Kopfball und ...'),
		'Der Stürmer zieht aus '.mt_rand(18, 30).'m ab.',
		_('Der Angreifer schließt perfekt ins kurze Eck ab.'),
		_('Der Stürmer zirkelt den Ball ins lange Eck.'),
		'Der Spieler zieht ab aus '.mt_rand(18, 30).'m.',
		_('XYZ setzt einen Schuss flach ins lange Eck.'),
		'Flatterball aus '.mt_rand(18, 30).'m.',
		_('Der Stürmer kommt am langen Eck unbedrängt zum Kopfball.'),
		_('Direktschuss aus spitzem Winkel.'),
		_('Einfach mal aufs Tor geschossen, der Schlussmann hat damit nicht gerechnet.'),
		_('Schuss aus spitzem Winkel.'),
		_('Schuss aus dem Rückraum.'),
		_('Ein Versuch aus der Distanz!'),
		_('Der Stürmer versucht den Ball über den Torwart zu lupfen.'),
		_('Der Angreifer donnert den Ball mit einem Seitfallzieher aufs Tor.'),
		_('Jetzt muss er nur noch den Keeper überwinden ...'),
		_('Nur noch der Torwart steht vor dem Angreifer.'),
		_('Der Angreifer versucht, den Torwart mit einem Heber zu überwinden.'),
		_('Kaltschnäuziger Abschluss.'),
		_('Toller Weitschuss von XYZ.')
    );
	$formulierungen['shot_score'] = array(
		'<strong>'._('Der passt genau!').'</strong>',
		'<strong>'._('Den hätte der Keeper haben müssen!').'</strong>',
		'<strong>'._('Der Ball donnert ins Netz!').'</strong>',
		'<strong>'._('Keine Chance für den Keeper, der Ball flatterte zu sehr!').'</strong>',
		'<strong>'._('Der Torwart springt ... und kommt nicht mehr dran. Tor!').'</strong>',
		'<strong>'._('XYZ netzt mit Glück ein. Der Torwart war dran.').'</strong>',
		'<strong>'._('XYZ trifft.').'</strong>',
		'<strong>'._('Tor des Tages!').'</strong>',
		'<strong>'._('Direkt unter die Querlatte. Tor!').'</strong>',
		'<strong>'._('XYZ netzt ein.').'</strong>',
		'<strong>'._('Tor!').'</strong>',
		'<strong>'._('Ein super Tor!').'</strong>',
		'<strong>'._('Gooolaso!').'</strong>',
		'<strong>'._('Der Ball kullert irgendwie ins Tor!').'</strong>',
		'<strong>'._('Der Ball geht ins Tor!').'</strong>',
		'<strong>'._('Abstauber ins Tor!').'</strong>',
		'<strong>'._('Nachschuss und ... Tor!').'</strong>',
		'<strong>'._('Keine Chance für den Keeper!').'</strong>',
		'<strong>'._('Treffer!').'</strong>',
		'<strong>'._('Toooor!').'</strong>',
		'<strong>'._('Der Schlussmann zeigt keine Reaktion und sieht den Ball im Netz zappeln!').'</strong>',
		'<strong>'._('Was für ein Treffer!').'</strong>',
		'<strong>'._('Sensationelles Tor!').'</strong>',
		'<strong>'._('Unglaublich! Toooor!').'</strong>',
		'<strong>'._('Tor für XYZ!').'</strong>'
    );
	$formulierungen['shot_block'] = array(
		_('Der Schuss wird vom Verteidiger abgeblockt.'),
		_('Der Abwehrspieler kann noch an den Pfosten abfälschen.'),
		_('Der Stürmer verzieht leicht nach links.'),
		_('Das war knapp, der Schuss geht über die Latte.'),
		_('Der Schuss geht auf die Tribüne. Hoffentlich hat sich kein Zuschauer verletzt.'),
		_('Der Schuss geht auf die Tribüne. Die Fans freuen sich über den gefangenen Ball.'),
		_('XYZ verfehlt das Tor nur um Zentimeter.'),
		_('Der Ball knallt an den Pfosten. Ein Raunen geht durch das Stadion.'),
		_('Der Schuss geht weit daneben.'),
		_('Der Ball knallt ans Lattenkreuz.'),
		_('Der Stürmer kommt angerauscht und schlägt ein wunderbares Luftloch.'),
		_('Ball zur Ecke geklärt.')
    );
	$formulierungen['shot_save'] = array(
		_('Gehalten!'),
		_('Tolle Parade vom Torwart.'),
		_('Der Keeper wehrt den Ball mit den Fäusten ab.'),
		_('Der Torwart wehrt den Ball ab.'),
		_('Der Schlussmann kann den Ball mit einer herrlichen Parade retten.'),
		_('Der Torhüter fliegt ... und hält! Tolle Parade!'),
		_('Der Torwart klatscht nur ab, aber der Nachschuss geht vorbei.'),
		_('Der Schlussmann fängt sicher.'),
		_('Der Torhüter vereitelt die Chance mit einem tollen Reflex.'),
		_('Der Torwart bewahrt sein Team mit einer Glanzparade vor dem Gegentreffer.')
    );
	$formulierungen['offside'] = array(
		_('Abseits.'),
		_('XYZ im Abseits.'),
		_('Schönes Tor von XYZ, doch leider Abseits.'),
		_('Ein Spieler von XYZ läuft ins Abseits.'),
		_('Der Spieler von XYZ steht im Abseits.'),
		_('XYZ ist im Abseits.'),
		_('Der Spieler von XYZ wird in Abseitsposition angespielt.'),
		_('Der Schiedsrichter erkennt zu Recht auf Abseits.'),
		_('Der Referee entscheidet auf Abseitsstellung.'),
		_('Abseits! Die Spieler können es kaum glauben.'),
		_('Der Spieler steht im Abseits.')
    );
	$formulierungen['quickCounterAttack'] = array(
		_('XYZ kontert.'),
		_('Schneller Gegenangriff von XYZ.'),
		_('Sofort der Konter von XYZ.'),
		_('Konterchance für XYZ.'),
		_('Kontermöglichkeit für XYZ.'),
		_('XYZ hat sofort wieder die Kugel und geht in die Offensive.'),
		_('Schnelles Umschalten von XYZ.'),
		_('XYZ schaltet direkt in den Angriff um.'),
		_('Direkter Konter von XYZ.'),
		_('Schönes Konterspiel von XYZ.'),
		_('Schneller Konter von XYZ.')
    );
	$formulierungen['throwIn'] = array(
		_('Ball im Aus. Einwurf.'),
		_('Der Ball geht ins Aus.'),
		_('Die Kugel rollt ins Aus. Einwurf.'),
		_('Einwurf tief in der gegnerischen Hälfte.')
    );
	$formulierungen['throwIn_def'] = array(
		_('Der Gegner hat den Ball.'),
		_('Ballbesitz für den Gegner.')
    );
	$formulierungen['throwIn_att'] = array(
		_('XYZ hat den Ball.'),
		_('Ballbesitz für XYZ.')
    );
	if (isset($formulierungen[$typ])) { $formulierung = $formulierungen[$typ]; } else { return $typ; }
	shuffle($formulierung);
	$ausgabe = str_replace('XYZ', $ersetzung, $formulierung[0]);
	return $ausgabe;
}
function weakenTeam($team1Name, $defensivName, $cardType) {
	global $staerke_team1, $staerke_team2;
	if ($cardType == 'red') {
		$weakenFactor = 0.9;
	}
	elseif ($cardType == 'yellow') {
		$weakenFactor = 0.98;
	}
	else {
		$weakenFactor = 1;
	}
	if ($defensivName == $team1Name) {
		$staerke_team1['A'] *= $weakenFactor;
		$staerke_team1['M'] *= $weakenFactor;
		$staerke_team1['S'] *= $weakenFactor;
	}
	else {
		$staerke_team2['A'] *= $weakenFactor;
		$staerke_team2['M'] *= $weakenFactor;
		$staerke_team2['S'] *= $weakenFactor;
	}
}
function starte_angriff($teamname_att, $teamname_def, $stark_att, $stark_def) {
	global $minute, $tore, $sql3, $spielbericht, $fouls, $gelbe_karten, $rote_karten, $abseits, $schuesse, $taktiken;
	// input values: attacker's name, defender's name, attacker's strength array, defender's strength array
	// players' strength values vary from 0.1 to 9.9
	$spielbericht .= '<p>'.$minute.'\': '.kommentar($teamname_att, 'attack');
	if (Chance_Percent(50*strengths_weight($stark_att['M'])/strengths_weight($stark_def['M'])*tactics_weight($taktiken[$teamname_att][0])*tactics_weight($taktiken[$teamname_def][0])*tactics_weight($taktiken[$teamname_att][1])/tactics_weight($taktiken[$teamname_att][2]))) {
		// attacking team passes 1st third of opponent's field side
		$spielbericht .= ' '.kommentar($teamname_def, 'advance');
		if (Chance_Percent(25*tactics_weight($taktiken[$teamname_def][5]))) {
			// the defending team fouls the attacking team
			$fouls[$teamname_def]++;
			$spielbericht .= ' '.kommentar($teamname_def, 'foul');
			if (Chance_Percent(30)) {
				// yellow card for the defending team
				$gelbe_karten[$teamname_def]++;
				weakenTeam($sql3['team1'], $teamname_def, 'yellow');
				$spielbericht .= ' '.kommentar($teamname_def, 'yellow');
			}
			elseif (Chance_Percent(3)) {
				// red card for the defending team
				$rote_karten[$teamname_def]++;
				weakenTeam($sql3['team1'], $teamname_def, 'red');
				$spielbericht .= ' '.kommentar($teamname_def, 'red');
			}
			// indirect free kick
			$spielbericht .= ' '.kommentar($teamname_att, 'iFreeKick');
			$schuesse[$teamname_att]++;
			if (Chance_Percent(30*strengths_weight($stark_att['S'])/strengths_weight($stark_def['A']))) {
				// shot at the goal
				$spielbericht .= ' '.kommentar($teamname_att, 'iFreeKick_shot');
				if (Chance_Percent(30*strengths_weight($stark_att['S'])/strengths_weight($stark_def['T']))) {
					// attacking team scores
					$tore[$teamname_att]++;
					$spielbericht .= ' '.kommentar($teamname_att, 'shot_score');
				}
				else {
					// defending goalkeeper saves
					$spielbericht .= ' '.kommentar($teamname_def, 'iFreeKick_shot_save');
				}
			}
			else {
				// defending team cleares the ball
				$spielbericht .= ' '.kommentar($teamname_def, 'iFreeKick_clear');
			}
		}
		elseif (Chance_Percent(17)*tactics_weight($taktiken[$teamname_att][0])*tactics_weight($taktiken[$teamname_att][2])) {
			// attacking team is caught offside
			$abseits[$teamname_att]++;
			$spielbericht .= ' '.kommentar($teamname_att, 'offside');
		}
		else {
			// attack isn't interrupted
			// attack passes the 2nd third of the opponent's field side - good chance
			$spielbericht .= ' '.kommentar($teamname_def, 'advance_further');
			if (Chance_Percent(25*tactics_weight($taktiken[$teamname_def][5]))) {
				// the defending team fouls the attacking team
				$fouls[$teamname_def]++;
				$spielbericht .= ' '.kommentar($teamname_def, 'foul');
				if (Chance_Percent(33)) {
					// yellow card for the defending team
					$gelbe_karten[$teamname_def]++;
					weakenTeam($sql3['team1'], $teamname_def, 'yellow');
					$spielbericht .= ' '.kommentar($teamname_def, 'yellow');
				}
				elseif (Chance_Percent(3)) {
					// red card for the defending team
					$rote_karten[$teamname_def]++;
					weakenTeam($sql3['team1'], $teamname_def, 'red');
					$spielbericht .= ' '.kommentar($teamname_def, 'red');
				}
				if (Chance_Percent(19*strengths_weight($stark_att['S'])/strengths_weight($stark_def['A']))) {
					// penalty for the attacking team
					$schuesse[$teamname_att]++;
					$spielbericht .= ' '.kommentar($teamname_att, 'penalty');
					if (Chance_Percent(77)/strengths_weight($stark_def['T'])) {
						// attacking team scores
						$tore[$teamname_att]++;
						$spielbericht .= ' '.kommentar($teamname_att, 'shot_score');
					}
					elseif (Chance_Percent(50)) {
						// shot misses the goal
						$spielbericht .= ' '.kommentar($teamname_att, 'penalty_miss');
					}
					else {
						// defending goalkeeper saves
						$spielbericht .= ' '.kommentar($teamname_def, 'penalty_save');
					}
				}
				else {
					// direct free kick
					$spielbericht .= ' '.kommentar($teamname_att, 'dFreeKick');
					$schuesse[$teamname_att]++;
					if (Chance_Percent(40*strengths_weight($stark_att['S']))) {
						// shot at the goal
						$spielbericht .= ' '.kommentar($teamname_att, 'dFreeKick_shot');
						if (Chance_Percent(40/strengths_weight($stark_def['T']))) {
							// attacking team scores
							$tore[$teamname_att]++;
							$spielbericht .= ' '.kommentar($teamname_att, 'shot_score');
						}
						else {
							// defending goalkeeper saves
							$spielbericht .= ' '.kommentar($teamname_def, 'dFreeKick_shot_save');
						}
					}
					else {
						// defending team cleares the ball
						$spielbericht .= ' '.kommentar($teamname_def, 'dFreeKick_clear');
					}
				}
			}
			elseif (Chance_Percent(62*strengths_weight($stark_att['S'])/strengths_weight($stark_def['A'])*tactics_weight($taktiken[$teamname_att][2])*tactics_weight($taktiken[$teamname_att][3]))) {
				// shot at the goal
				$schuesse[$teamname_att]++;
				$spielbericht .= ' '.kommentar($teamname_att, 'shot');
				if (Chance_Percent(30*strengths_weight($stark_att['S'])/strengths_weight($stark_def['T']))) {
					// the attacking team scores
					$tore[$teamname_att]++;
					$spielbericht .= ' '.kommentar($teamname_att, 'shot_score');
				}
				else {
					if (Chance_Percent(50*strengths_weight($stark_def['A']))) {
						// the defending defenders block the shot
						$spielbericht .= ' '.kommentar($teamname_att, 'shot_block');
					}
					else {
						// the defending goalkeeper saves
						$spielbericht .= ' '.kommentar($teamname_def, 'shot_save');
					}
				}
			}
			else {
				// attack is stopped
				$spielbericht .= ' '.kommentar($teamname_def, 'stopped');
				if (Chance_Percent(15*strengths_weight($stark_def['A'])/strengths_weight($stark_att['M'])*tactics_weight($taktiken[$teamname_att][1])*tactics_weight($taktiken[$teamname_att][3])*tactics_weight($taktiken[$teamname_def][4]))) {
					// quick counter attack - playing on the break
					$stark_att['A'] = $stark_att['A']*0.8; // weaken the current attacking team's defense
					$spielbericht .= ' '.kommentar($teamname_def, 'quickCounterAttack');
					$spielbericht .= ' ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>'; // close comment line
					return starte_angriff($teamname_def, $teamname_att, $stark_def, $stark_att); // new attack - this one is finished
				}
			}
		}
	}
	// attacking team doesn't pass 1st third of opponent's field side
	elseif (Chance_Percent(15*strengths_weight($stark_def['A'])/strengths_weight($stark_att['S'])*tactics_weight($taktiken[$teamname_att][3])*tactics_weight($taktiken[$teamname_def][4]))) {
		// attack is stopped
		// quick counter attack - playing on the break
		$spielbericht .= ' '.kommentar($teamname_def, 'stopped');
		$stark_att['A'] = $stark_att['A']*0.8; // weaken the current attacking team's defense
		$spielbericht .= ' '.kommentar($teamname_def, 'quickCounterAttack');
		$spielbericht .= ' ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>'; // close comment line
		return starte_angriff($teamname_def, $teamname_att, $stark_def, $stark_att); // new attack - this one is finished
	}
	else {
		// ball goes into touch - out of the field
		$spielbericht .= ' '.kommentar($teamname_def, 'throwIn');
		if (Chance_Percent(33)) {
			// if a new chance is created
			if (Chance_Percent(50*strengths_weight($stark_att['M'])/strengths_weight($stark_def['M']))) {
				// throw-in for the attacking team
				$spielbericht .= ' '.kommentar($teamname_def, 'throwIn_att');
				$spielbericht .= ' ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>'; // close comment line
				return starte_angriff($teamname_att, $teamname_def, $stark_att, $stark_def); // new attack - this one is finished
			}
			else {
				// throw-in for the defending team
				$spielbericht .= ' '.kommentar($teamname_def, 'throwIn_def');
				$spielbericht .= ' ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>'; // close comment line
				return starte_angriff($teamname_def, $teamname_att, $stark_def, $stark_att); // new attack - this one is finished
			}
		}
	}
	$spielbericht .= ' ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>'; // close comment line
	return TRUE; // finish the attack
}
$heute_tag = date('d');
$heute_monat = date('m');
$heute_jahr = date('Y');
$heute_stunde = intval(date('H'));
$datum_min = mktime(00, 00, 01, $heute_monat, $heute_tag, $heute_jahr);
$datum_max = mktime(23, 59, 59, $heute_monat, $heute_tag, $heute_jahr);
if ($heute_stunde == 22 OR $heute_stunde == 23) { $to_simulate = 'Test'; }
elseif ($heute_stunde == 14 OR $heute_stunde == 15) { $to_simulate = 'Liga'; }
elseif ($heute_stunde == 18 OR $heute_stunde == 19) { $to_simulate = 'Pokal'; }
elseif ($heute_stunde == 10 OR $heute_stunde == 11) { $to_simulate = 'Cup'; }
else { exit; }
$sql1 = "SELECT id, liga, team1, team2, typ, kennung FROM ".$prefix."spiele WHERE datum > ".$datum_min." AND datum < ".$datum_max." AND simuliert = 0 AND ergebnis = '-:-' AND typ = '".$to_simulate."' ORDER BY RAND() LIMIT 0, 20";
$sql2 = mysql_query($sql1) or reportError(mysql_error(), $sql1);
while ($sql3 = mysql_fetch_assoc($sql2)) {
	// SPIELE NICHT DOPPELT SIMULIEREN ANFANG
	$in1 = "UPDATE ".$prefix."spiele SET simuliert = 1 WHERE id = ".$sql3['id']." AND simuliert = 0";
	$in2 = mysql_query($in1) or reportError(mysql_error(), $in1);
	if (mysql_affected_rows() == 0) { continue; }
	// SPIELE NICHT DOPPELT SIMULIEREN ENDE
	// VARIABLEN INITIALISIEREN ANFANG
	$ballbesitz_team1 = 0;
	$ballbesitz_team2 = 0;
	$tore_team1 = '';
	$tore_team2 = '';
	$gelb_team1 = '';
	$gelb_team2 = '';
	$rot_team1 = '';
	$rot_team2 = '';
	$erschoepungswert1 = 0;
	$erschoepungswert2 = 0;
	$fouls = array(); $fouls[$sql3['team1']] = 0; $fouls[$sql3['team2']] = 0;
	$gelbe_karten = array(); $gelbe_karten[$sql3['team1']] = 0; $gelbe_karten[$sql3['team2']] = 0;
	$rote_karten = array(); $rote_karten[$sql3['team1']] = 0; $rote_karten[$sql3['team2']] = 0;
	$abseits = array(); $abseits[$sql3['team1']] = 0; $abseits[$sql3['team2']] = 0;
	$schuesse = array(); $schuesse[$sql3['team1']] = 0; $schuesse[$sql3['team2']] = 0;
	// VARIABLEN INITIALISIEREN ENDE
    $daten_team1a = "SELECT ids, fanaufkommen, sponsor_a, sponsor_s, elo FROM ".$prefix."teams WHERE name = '".mysql_real_escape_string(trim($sql3['team1']))."'";
    $daten_team1b = mysql_query($daten_team1a) or reportError(mysql_error(), $daten_team1a);
    $daten_team1 = mysql_fetch_assoc($daten_team1b);
    $team1_id = $daten_team1['ids'];
    $daten_team2a = "SELECT ids, rank, sponsor_a, sponsor_s, elo FROM ".$prefix."teams WHERE name = '".mysql_real_escape_string(trim($sql3['team2']))."'";
    $daten_team2b = mysql_query($daten_team2a) or reportError(mysql_error(), $daten_team2a);
    $daten_team2 = mysql_fetch_assoc($daten_team2b);
    $team2_id = $daten_team2['ids'];
    $spielbericht = '';
	// GUCKEN OB DAS SPIEL EIN DERBY IST ANFANG
	$team1OhneKurzel = str_replace($vereinsKurzelEntfernung, '', $sql3['team1']);
	$team2OhneKurzel = str_replace($vereinsKurzelEntfernung, '', $sql3['team2']);
	if ($team1OhneKurzel == $team2OhneKurzel) { // selbe Stadt also Derby
		$is_derby = TRUE;
	}
	else {
		$is_derby = FALSE;
	}
	// GUCKEN OB DAS SPIEL EIN DERBY IST ENDE
    // TAKTIKEN ANFANG
    $taktiken = array();
    $ta1 = "SELECT ausrichtung, geschw_auf, pass_auf, risk_pass, druck, aggress FROM ".$prefix."taktiken WHERE team = '".$team1_id."' AND spieltyp = '".$to_simulate."'";
    $ta2 = mysql_query($ta1) or reportError(mysql_error(), $ta1);
	if (mysql_num_rows($ta2) == 0) {
		$taktiken[$sql3['team1']] = array(2, 2, 2, 2, 2, 2);
	}
	else {
		$ta3 = mysql_fetch_assoc($ta2);
		$taktiken[$sql3['team1']] = array($ta3['ausrichtung'], $ta3['geschw_auf'], $ta3['pass_auf'], $ta3['risk_pass'], $ta3['druck'], $ta3['aggress']);
	}
    $ta1 = "SELECT ausrichtung, geschw_auf, pass_auf, risk_pass, druck, aggress FROM ".$prefix."taktiken WHERE team = '".$team2_id."' AND spieltyp = '".$to_simulate."'";
    $ta2 = mysql_query($ta1) or reportError(mysql_error(), $ta1);
	if (mysql_num_rows($ta2) == 0) {
		$taktiken[$sql3['team2']] = array(2, 2, 2, 2, 2, 2);
	}
	else {
		$ta3 = mysql_fetch_assoc($ta2);
		$taktiken[$sql3['team2']] = array($ta3['ausrichtung'], $ta3['geschw_auf'], $ta3['pass_auf'], $ta3['risk_pass'], $ta3['druck'], $ta3['aggress']);
	}
	if ($is_derby) { // bei Derby mehr Aggressivität und Offensive
		$taktiken[$sql3['team1']][5] += 1;
		$taktiken[$sql3['team2']][5] += 1;
		$taktiken[$sql3['team1']][0] += 1;
		$taktiken[$sql3['team2']][0] += 1;
	}
    // TAKTIKEN ENDE
    // ZUSCHAUER ANFANG
    $watcher1 = "SELECT name, plaetze, preis FROM ".$prefix."stadien WHERE team = '".$team1_id."'";
    $watcher2 = mysql_query($watcher1) or reportError(mysql_error(), $watcher1);
    $watcher3 = mysql_fetch_assoc($watcher2);
    $temp_fanaufkommen = $daten_team1['fanaufkommen']+15000/pow(1.4, ($daten_team2['rank']-1)); // Gegner-Platzierung
	switch ($sql3['typ']) {
		case 'Pokal': $temp_fanaufkommen += 30000; break;
		case 'Cup': $temp_fanaufkommen += 10000; break;
		case 'Liga': $temp_fanaufkommen += 15000; break;
		default: $temp_fanaufkommen += 5000; break;
	}
    $temp_fanaufkommen += (70-$watcher3['preis'])*750; // Ticketpreis
	if ($is_derby) { // bei Derby mehr Zuschauer
		$temp_fanaufkommen += 20000;
	}
	$watcher4 = intval(min($watcher3['plaetze'], $temp_fanaufkommen));
    if ($sql3['typ'] == 'Test') {
        $watcher_einkommen_h = 0;
        $watcher_einkommen_a = 0;
    }
	elseif ($sql3['liga'] == 'Pokal_Runde_5' OR $sql3['typ'] == 'Cup') {
        $watcher_einkommen_h = round($watcher4*$watcher3['preis']/2);
        $watcher_einkommen_a = round($watcher4*$watcher3['preis']/2);	
	}
    else {
        $watcher_einkommen_h = round($watcher4*$watcher3['preis']);
        $watcher_einkommen_a = 0;
    }
	$stadiumName = $watcher3['name'];
	if (stripos($stadiumName, 'Arena') !== FALSE) {
		$stadiumPreposition = 'in der';
	}
	else {
		$stadiumPreposition = 'im';
	}
	if ($watcher4 >= 90000) {
		$spielbericht .= '<p>'._('Knapp 100.000 Zuschauer hier ').''.$stadiumPreposition.' '.$stadiumName.'!</p>';
	}
	elseif ($watcher4 >= 70000) {
		$spielbericht .= '<p>'._('Eine super Stimmung ').''.$stadiumPreposition.' '.$stadiumName.'!</p>';
	}
	elseif ($watcher4 >= 50000) {
		$spielbericht .= '<p>'._('Gute Stimmung hier ').''.$stadiumPreposition.' '.$stadiumName.'!</p>';
	}
	elseif ($watcher4 >= 30000) {
		$spielbericht .= '<p>'._('Die Fans freuen sich auf das Spiel ').''.$stadiumPreposition.' '.$stadiumName.'!</p>';
	}
	else {
		$spielbericht .= '<p>'._('Es sind nicht viele Zuschauer hier ').''.$stadiumPreposition.' '.$stadiumName.'!</p>';
	}
	$spielbericht .= '<p>'._('Ticketpreis: ').''.$watcher3['preis'].' €</p>';
    // ZUSCHAUER ENDE
    $sponsor_einkommen1 = $daten_team1['sponsor_a'];
    $sponsor_einkommen2 = $daten_team2['sponsor_a'];
	$spieler_team1a = "SELECT ids, vorname, nachname, position, frische, staerke FROM ".$prefix."spieler WHERE team = '".$team1_id."' AND startelf_".$to_simulate." != 0 AND verletzung = 0 ORDER BY position DESC LIMIT 0, 11";
	$spieler_team1b = mysql_query($spieler_team1a) or reportError(mysql_error(), $spieler_team1a);
	$spieler_team1 = array();
	$scorers1 = array();
	$spielbericht .= '<p>Aufstellung von '.$sql3['team1'].': ';
	$templist = '';
	$positionsCounter = array('T'=>1, 'A'=>4, 'M'=>4, 'S'=>2);
	$verletzungenVorauswahlTeam1 = array();
	$frischeWerteTeam1 = array();
	$staerkenArray1 = array();
	while ($spieler_team1c = mysql_fetch_assoc($spieler_team1b)) {
		$verletzungenVorauswahlTeam1[$spieler_team1c['ids']] = mt_rand(0, $spieler_team1c['frische']);
		$frischeWerteTeam1[] = $spieler_team1c['frische'];
		$temp = mb_substr($spieler_team1c['vorname'], 0, 1, 'UTF-8').'. '.$spieler_team1c['nachname'];
		$templist .= '<a href="/spieler.php?id='.$spieler_team1c['ids'].'">'.$temp.'</a>, ';
		$spieler_team1[] = array('name'=>$temp, 'ids'=>$spieler_team1c['ids'], 'position'=>$spieler_team1c['position'], 'frische'=>$spieler_team1c['frische'], 'staerke'=>$spieler_team1c['staerke']);
        $positionsCounter[$spieler_team1c['position']]--;
		switch($spieler_team1c['position']) {
            case 'T': continue 2;
            case 'A': $temp = round($spieler_team1c['staerke']*0.3); break;
            case 'M': $temp = round($spieler_team1c['staerke']*1.8); break;
            case 'S': $temp = round($spieler_team1c['staerke']*3.7); break;
        }
        $temp = mt_rand($temp, 45);
        $scorers1[$spieler_team1c['ids']] = $temp;
		$staerkenArray1[] = $spieler_team1c['staerke'];
	}
	// AUFFUELLEN ANFANG
	$strafeWegenUnvollstaendigkeitTeam1 = -2;
	if (count($staerkenArray1) == 0 OR array_sum($staerkenArray1) < 11) {
		$amateurStaerke1 = 1;
	}
	else {
		$amateurStaerke1 = array_sum($staerkenArray1)/count($staerkenArray1)/2;
	}
	foreach (array_keys($positionsCounter) as $positionsBuchstabe) {
		for ($auffuellen = 0; $auffuellen < $positionsCounter[$positionsBuchstabe]; $auffuellen++) {
			$amateurStaerkeCurrent = mt_rand(80, 120)*$amateurStaerke1/100;
			$templist .= 'Amateurspieler ('.$positionsBuchstabe.'/'.number_format($amateurStaerkeCurrent, 1, ',', '.').'), ';
			$spieler_team1[] = array('name'=>'Amateurspieler', 'ids'=>'Amateurspieler', 'position'=>$positionsBuchstabe, 'frische'=>mt_rand(50, 100), 'staerke'=>$amateurStaerkeCurrent);
			$strafeWegenUnvollstaendigkeitTeam1++;
		}
	}
	if ($sql3['typ'] != 'Test') {
		if ($strafeWegenUnvollstaendigkeitTeam1 > 0) {
			$strafeWegenUnvollstaendigkeitTeam1 *= 1000000;
			$sponsor_einkommen1 = $sponsor_einkommen1-$strafeWegenUnvollstaendigkeitTeam1;
		}
	}
	else {
		$strafeWegenUnvollstaendigkeitTeam1 = 0;
	}
	// AUFFUELLEN ENDE
	$spielbericht .= substr($templist, 0, -2);
	if ($strafeWegenUnvollstaendigkeitTeam1 > 0) { $spielbericht .= ' [Sponsor-Strafe: '.number_format($strafeWegenUnvollstaendigkeitTeam1, 0, ',', '.').' €]'; }
	$spielbericht .= '</p>';
	$spieler_team2a = "SELECT ids, vorname, nachname, position, frische, staerke FROM ".$prefix."spieler WHERE team = '".$team2_id."' AND startelf_".$to_simulate." != 0 AND verletzung = 0 ORDER BY position DESC LIMIT 0, 11";
	$spieler_team2b = mysql_query($spieler_team2a) or reportError(mysql_error(), $spieler_team2a);
	$spieler_team2 = array();
	$scorers2 = array();
	$spielbericht .= '<p>Aufstellung von '.$sql3['team2'].': ';
	$templist = '';
	$positionsCounter = array('T'=>1, 'A'=>4, 'M'=>4, 'S'=>2);
	$verletzungenVorauswahlTeam2 = array();
	$frischeWerteTeam2 = array();
	$staerkenArray2 = array();
	while ($spieler_team2c = mysql_fetch_assoc($spieler_team2b)) {
		$verletzungenVorauswahlTeam2[$spieler_team2c['ids']] = mt_rand(0, $spieler_team2c['frische']);
		$frischeWerteTeam2[] = $spieler_team2c['frische'];
		$temp = mb_substr($spieler_team2c['vorname'], 0, 1, 'UTF-8').'. '.$spieler_team2c['nachname'];
		$templist .= '<a href="/spieler.php?id='.$spieler_team2c['ids'].'">'.$temp.'</a>, ';
		$spieler_team2[] = array('name'=>$temp, 'ids'=>$spieler_team2c['ids'], 'position'=>$spieler_team2c['position'], 'frische'=>$spieler_team2c['frische'], 'staerke'=>$spieler_team2c['staerke']);
        $positionsCounter[$spieler_team2c['position']]--;
		switch($spieler_team2c['position']) {
            case 'T': continue 2;
            case 'A': $temp = round($spieler_team2c['staerke']*0.3); break;
            case 'M': $temp = round($spieler_team2c['staerke']*1.8); break;
            case 'S': $temp = round($spieler_team2c['staerke']*3.7); break;
        }
        $temp = mt_rand($temp, 45);
        $scorers2[$spieler_team2c['ids']] = $temp;
		$staerkenArray2[] = $spieler_team2c['staerke'];
	}
	// AUFFUELLEN ANFANG
	$strafeWegenUnvollstaendigkeitTeam2 = -2;
	if (count($staerkenArray2) == 0 OR array_sum($staerkenArray2) < 11) {
		$amateurStaerke2 = 1;
	}
	else {
		$amateurStaerke2 = array_sum($staerkenArray2)/count($staerkenArray2)/2;
	}
	foreach (array_keys($positionsCounter) as $positionsBuchstabe) {
		for ($auffuellen = 0; $auffuellen < $positionsCounter[$positionsBuchstabe]; $auffuellen++) {
			$amateurStaerkeCurrent = mt_rand(80, 120)*$amateurStaerke2/100;
			$templist .= 'Amateurspieler ('.$positionsBuchstabe.'/'.number_format($amateurStaerkeCurrent, 1, ',', '.').'), ';
			$spieler_team2[] = array('name'=>'Amateurspieler', 'ids'=>'Amateurspieler', 'position'=>$positionsBuchstabe, 'frische'=>mt_rand(50, 100), 'staerke'=>$amateurStaerkeCurrent);
			$strafeWegenUnvollstaendigkeitTeam2++;
		}
	}
	if ($sql3['typ'] != 'Test') {
		if ($strafeWegenUnvollstaendigkeitTeam2 > 0) {
			$strafeWegenUnvollstaendigkeitTeam2 *= 1000000;
			$sponsor_einkommen2 = $sponsor_einkommen2-$strafeWegenUnvollstaendigkeitTeam2;
		}
	}
	else {
		$strafeWegenUnvollstaendigkeitTeam2 = 0;
	}
	// AUFFUELLEN ENDE
	$spielbericht .= substr($templist, 0, -2);
	if ($strafeWegenUnvollstaendigkeitTeam2 > 0) { $spielbericht .= ' [Sponsor-Strafe: '.number_format($strafeWegenUnvollstaendigkeitTeam2, 0, ',', '.').' €]'; }
	$spielbericht .= '</p>';
	$erschoepungswert1 = $taktiken[$sql3['team1']][4]+1;
	$erschoepungswert2 = $taktiken[$sql3['team2']][4]+1;
	$tore[$sql3['team1']] = 0;
	$tore[$sql3['team2']] = 0;
	if ($taktiken[$sql3['team1']][0] == 4) { $taktik1v = 70; $taktik1a = 125; }
	elseif ($taktiken[$sql3['team1']][0] == 3) { $taktik1v = 80; $taktik1a = 115; }
	elseif ($taktiken[$sql3['team1']][0] == 1) { $taktik1v = 115; $taktik1a = 80; }
	else { $taktik1v = 100; $taktik1a = 100; }
	$staerke_team1 = array('T'=>0.0, 'A'=>0.0, 'M'=>0.0, 'S'=>0.0);
	for ($i = 0; $i < 11; $i++) {
		if (isset($spieler_team1[$i])) {
			$staerke_team1[$spieler_team1[$i]['position']] += $spieler_team1[$i]['staerke']*(0.33+0.67*$spieler_team1[$i]['frische']/100);
		}
		else { // keine 11 Spieler
			$staerke_team1['T'] += 0.1;
			$staerke_team1['A'] += 0.1;
			$staerke_team1['M'] += 0.1;
			$staerke_team1['S'] += 0.1;
		}
	}
	$gesamtForm_team1 = $staerke_team1['T']+$staerke_team1['A']+$staerke_team1['M']+$staerke_team1['S'];
	$staerke_team1['A'] = $staerke_team1['A']/4;
	$staerke_team1['M'] = $staerke_team1['M']/4;
	$staerke_team1['S'] = $staerke_team1['S']/2;
	$staerke_team1['A'] *= $taktik1v/100;
	$staerke_team1['S'] *= $taktik1a/100;
	if ($taktiken[$sql3['team2']][0] == 4) { $taktik2v = 70; $taktik2a = 125; }
	elseif ($taktiken[$sql3['team2']][0] == 3) { $taktik2v = 90; $taktik2a = 110; }
	elseif ($taktiken[$sql3['team2']][0] == 1) { $taktik2v = 110; $taktik2a = 90; }
	else { $taktik2v = 100; $taktik2a = 100; }
	$staerke_team2 = array('T'=>0.0, 'A'=>0.0, 'M'=>0.0, 'S'=>0.0);
	for ($i = 0; $i < 11; $i++) {
		if (isset($spieler_team2[$i])) {
			$staerke_team2[$spieler_team2[$i]['position']] += $spieler_team2[$i]['staerke']*(0.33+0.67*$spieler_team2[$i]['frische']/100);
		}
		else { // keine 11 Spieler
			$staerke_team2['T'] += 0.1;
			$staerke_team2['A'] += 0.1;
			$staerke_team2['M'] += 0.1;
			$staerke_team2['S'] += 0.1;
		}
	}
	$gesamtForm_team2 = $staerke_team2['T']+$staerke_team2['A']+$staerke_team2['M']+$staerke_team2['S'];
	$staerke_team2['A'] = $staerke_team2['A']/4;
	$staerke_team2['M'] = $staerke_team2['M']/4;
	$staerke_team2['S'] = $staerke_team2['S']/2;
	$staerke_team2['A'] *= $taktik2v/100;
	$staerke_team2['S'] *= $taktik2a/100;
	$form1 = array_sum($staerke_team1);
	$form2 = array_sum($staerke_team2);
	// FORM IN SPIELBERICHT EINTRAGEN ANFANG
	$spielbericht .= '<p>Form von '.$sql3['team1'].': '.number_format($gesamtForm_team1, 1, ',', '.').' - Form von '.$sql3['team2'].': '.number_format($gesamtForm_team2, 1, ',', '.').'</p>';
	$spielbericht .= '<p><strong>'._('Die Zeitschrift &quot;Bolzblatt&quot; bewertet die Leistung der Teams wie folgt:').'</strong></p>';
	$spielbericht .= '<p>Noten für '.$sql3['team1'].': Torwart '.staerkeBenoten(strengths_weight($staerke_team1['T'])).' - Abwehr '.staerkeBenoten(strengths_weight($staerke_team1['A'])).' - Mittelfeld '.staerkeBenoten(strengths_weight($staerke_team1['M'])).' - Sturm '.staerkeBenoten(strengths_weight($staerke_team1['S'])).'</p>';
	$spielbericht .= '<p>Noten für '.$sql3['team2'].': Torwart '.staerkeBenoten(strengths_weight($staerke_team2['T'])).' - Abwehr '.staerkeBenoten(strengths_weight($staerke_team2['A'])).' - Mittelfeld '.staerkeBenoten(strengths_weight($staerke_team2['M'])).' - Sturm '.staerkeBenoten(strengths_weight($staerke_team2['S'])).'</p>';
	// FORM IN SPIELBERICHT EINTRAGEN ENDE
	// BALLBESITZ BERECHNEN ANFANG
	$ballbesitz_team1 = round(100/($staerke_team2['M']/$staerke_team1['M']+1));
	$ballbesitz_team2 = 100-$ballbesitz_team1;
	if ($sql3['typ'] != 'Test' && $sql3['liga'] != 'Pokal_Runde_5' && $sql3['typ'] != 'Cup') { // kein Heimvorteil in Testspielen und Cupspielen und im Pokalfinale
		$ballbesitz_team1 += 4;
		$ballbesitz_team2 -= 4;
	}
	if ($ballbesitz_team1 > 100) {
		$ballbesitz_team1 = 100;
		$ballbesitz_team2 = 0;
	}
	elseif ($ballbesitz_team2 > 100) {
		$ballbesitz_team2 = 100;
		$ballbesitz_team1 = 0;
	}
	// BALLBESITZ BERECHNEN ENDE
	$get_minuten = get_minutes(20, $ballbesitz_team1);
	$tempmax = max($get_minuten);
	$spielzeit = $tempmax[0]+mt_rand(0, 3);
	$nachSpielZeit = $spielzeit-90;
	if ($spielzeit < 90) { $spielzeit = 90; }
	$spielbericht .= '<p>1\': '.kommentar('', 'start_game');
	if ($is_derby) {
		$spielbericht .= ' '._('Die Zuschauer fiebern dem Derby schon lange entgegen!');
	}
	$spielbericht .= '</p>';
	foreach ($get_minuten as $minute_angreifer) {
		$minute = $minute_angreifer[0];
		if ($minute_angreifer[1] == 1) { // wenn Team 1 angreift
			starte_angriff($sql3['team1'], $sql3['team2'], $staerke_team1, $staerke_team2);
		}
		else { // wenn Team 2 angreift
			starte_angriff($sql3['team2'], $sql3['team1'], $staerke_team2, $staerke_team1);
		}
		if ($minute == 45) {
			$spielbericht .= '<p>45\': '.kommentar('', 'mid_game');
		}
		elseif ($minute > 90) {
			$spielbericht .= '<p>90\': Der Assistent zeigt '.$nachSpielZeit.' Minuten Nachspielzeit an. Die Fans treiben ihr Team noch einmal richtig an.</p>';
		}
		elseif (Chance_Percent(1)) {
			if (Chance_Percent(50)) {
				$spielbericht .= '<p>'.$minute.'\': Ein Flitzer belästigt den Stürmerstar von '.$sql3['team1'].'. Die anderen Spieler können den übermütigen Fan gerade noch bändigen!</p>';
			}
			else {
				$spielbericht .= '<p>'.$minute.'\': '._('Was ist das denn? Ein Flitzer auf dem Spielfeld! Der Schiedsrichter unterbricht die Partie!').'</p>';
			}
		}
	}
	if ($sql3['typ'] == 'Pokal' OR $sql3['typ'] == 'Cup') {
		$kenn1 = "SELECT ergebnis FROM ".$prefix."spiele WHERE kennung = '".$sql3['kennung']."' LIMIT 0, 1"; // automatische Sortierung, Hinspiel wird zuerst gefunden
		$kenn2 = mysql_query($kenn1) or reportError(mysql_error(), $kenn1);
		if (mysql_num_rows($kenn2) == 0) { // auch im Cup wird 1 Spiel gefunden, naemlich das aktuelle, sonst Fehler
			$errRep1 = "UPDATE ".$prefix."spiele SET simuliert = 0, simulationError = 'exit_kennung_not_found' WHERE id = ".$sql3['id'];
			$errRep2 = mysql_query($errRep1) or reportError(mysql_error(), $errRep1);
			exit;
		}
		$kenn3 = mysql_fetch_assoc($kenn2);
		$aktuelles_ergebnis = $tore[$sql3['team1']].':'.$tore[$sql3['team2']];
		if (($kenn3['ergebnis'] == $aktuelles_ergebnis) OR (($sql3['liga'] == 'Pokal_Runde_5' OR $sql3['typ'] == 'Cup') && $tore[$sql3['team1']] == $tore[$sql3['team2']])) { // wenn beide Spiele gleich enden wuerden
			$get_minuten = array();
			$get_minuten[] = array(mt_rand(96, 98), 1);
			$get_minuten[] = array(mt_rand(99, 101), 2);
			$get_minuten[] = array(mt_rand(102, 104), 1);
			$get_minuten[] = array(mt_rand(105, 107), 2);
			$get_minuten[] = array(mt_rand(108, 110), 1);
			$get_minuten[] = array(mt_rand(111, 113), 2);
			$get_minuten[] = array(mt_rand(114, 116), 1);
			$get_minuten[] = array(mt_rand(117, 119), 2);
			/*// TEAMS SCHWAECHEN FUER MEHR TORE ANFANG
			for ($wT = 0; $wT < 3; $wT++) {
				weakenTeam($sql3['team1'], $sql3['team1'], 'red');
				weakenTeam($sql3['team1'], $sql3['team2'], 'red');
			}
			// TEAMS SCHWAECHEN FUER MEHR TORE ENDE*/
			foreach ($get_minuten as $minute_angreifer) {
				$minute = $minute_angreifer[0];
				if ($minute_angreifer[1] == 1) { // wenn Team 1 angreift
					starte_angriff($sql3['team1'], $sql3['team2'], $staerke_team1, $staerke_team2);
				}
				else { // wenn Team 2 angreift
					starte_angriff($sql3['team2'], $sql3['team1'], $staerke_team2, $staerke_team1);
				}
				$aktuelles_ergebnis = $tore[$sql3['team1']].':'.$tore[$sql3['team2']];
				if ($minute > 116 && (($kenn3['ergebnis'] == $aktuelles_ergebnis) OR (($sql3['liga'] == 'Pokal_Runde_5' OR $sql3['typ'] == 'Cup') && $tore[$sql3['team1']] == $tore[$sql3['team2']]))) { // wenn beide Spiele gleich enden wuerden
					if (Chance_Percent(50)) {
						$tore[$sql3['team2']]++;
						$spielbericht .= '<p>120\': '.$sql3['team2'].' gewinnt im <strong>Elfmeterschießen!</strong> ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>';
					}
					else {
						$tore[$sql3['team1']]++;
						$spielbericht .= '<p>120\': '.$sql3['team1'].' gewinnt im <strong>Elfmeterschießen!</strong> ['.$tore[$sql3['team1']].':'.$tore[$sql3['team2']].']</p>';
					}
				}
			}
			$erschoepungswert1 = $erschoepungswert1+1;
			$erschoepungswert2 = $erschoepungswert2+1;
			$spielzeit = 120;
		}
	}
	$resultat = $tore[$sql3['team1']].':'.$tore[$sql3['team2']];
	if ($tore[$sql3['team1']] > $tore[$sql3['team2']]) {
		$punkte1 = 3; $punkte2 = 0;
		$sunAdd1 = "sunS = sunS+1, ";
		$sunAdd2 = "sunN = sunN+1, ";
	}
	elseif ($tore[$sql3['team2']] > $tore[$sql3['team1']]) {
		$punkte1 = 0; $punkte2 = 3;
		$sunAdd1 = "sunN = sunN+1, ";
		$sunAdd2 = "sunS = sunS+1, ";
	}
	else {
		$punkte1 = 1; $punkte2 = 1;
		$sunAdd1 = "sunU = sunU+1, ";
		$sunAdd2 = "sunU = sunU+1, ";
	}
	$spielbericht .= '<p>'.$spielzeit.'\': '._('Der Schiedsrichter pfeift das Spiel ab.').'</p>';
	if ($punkte1 == 3) {
		$sponsor_einkommen1 += $daten_team1['sponsor_s'];
	}
	elseif ($punkte2 == 3) {
		$sponsor_einkommen2 += $daten_team2['sponsor_s'];
	}
	// TESTSPIEL-WERTE AKTUALISIEREN ANFANG
    $test1sql = "friendlies_ges = friendlies_ges";
    $test2sql = "friendlies_ges = friendlies_ges";
    if ($sql3['typ'] == 'Test') {
    	// BEI TESTSPIELEN KEIN SPONSORING ANFANG
    	$sponsor_einkommen1 = 0;
    	$sponsor_einkommen2 = 0;
    	// BEI TESTSPIELEN KEIN SPONSORING ENDE
        $test1sql = "friendlies_ges = friendlies_ges+1";
        $test2sql = "friendlies_ges = friendlies_ges+1";
        if ($punkte1 == 3) {
            $test1sql .= ", friendlies = friendlies+1";
        }
        elseif ($punkte2 == 3) {
            $test2sql .= ", friendlies = friendlies+1";
        }
    }
	// TESTSPIEL-WERTE AKTUALISIEREN ENDE
    // AUSWAERTSTORREGEL FALLS KAMPFLOS ANFANG
    if ($sql3['typ'] == 'Pokal' OR $sql3['typ'] == 'Cup') {
		if ($sql3['typ'] == 'Pokal') {
			$sponsor_einkommen1 += 1000000;
			$sponsor_einkommen2 += 1000000;
		}
		else {
			$sponsor_einkommen1 += 500000;
			$sponsor_einkommen2 += 500000;		
		}
    	if (!isset($kenn3['ergebnis'])) {
            $kenn1 = "SELECT ergebnis FROM ".$prefix."spiele WHERE kennung = '".$sql3['kennung']."' LIMIT 0, 1";
            $kenn2 = mysql_query($kenn1) or reportError(mysql_error(), $kenn1);
            if (mysql_num_rows($kenn2) == 0) {
				$errRep1 = "UPDATE ".$prefix."spiele SET simuliert = 0, simulationError = 'exit_previous_result_not_found' WHERE id = ".$sql3['id'];
				$errRep2 = mysql_query($errRep1) or reportError(mysql_error(), $errRep1);
				exit;
			}
            $kenn3 = mysql_fetch_assoc($kenn2);
        }
        $aktuelles_ergebnis = $tore[$sql3['team1']].':'.$tore[$sql3['team2']];
        if ($kenn3['ergebnis'] == $aktuelles_ergebnis) { // wenn beide Spiele gleich enden wuerden
            $zufallsergebnis = mt_rand(1, 2);
            if ($zufallsergebnis == 1) {
                $tore[$sql3['team2']]++;
            }
            else {
                $tore[$sql3['team1']]++;
            }
            $resultat = $tore[$sql3['team1']].':'.$tore[$sql3['team2']];
            $spielbericht .= '<p>'._('Es konnte nach beiden Partien kein Sieger ermittelt werden. Deshalb hat das Los entschieden und das Spiel wurde mit').' '.$resultat.' '.('gewertet.').'</p>';
        }
    }
    // AUSWAERTSTORREGEL FALLS KAMPFLOS ENDE
	// FRISCHE ABZIEHEN ANFANG
	$pflichtspielPlus = ", spiele = spiele+1, moral = moral+1.8";
	if ($to_simulate == 'Test') {
		$pflichtspielPlus = ", moral = moral+1";
	}
	$pokalNurFuerSQL = "";
	if ($to_simulate == 'Pokal') {
		$pokalNurFuerSQL = ", pokalNurFuer = team";
	}
	if ($cookie_spieltag >= 22) { // Spieler ermüden am letzten Spieltag nicht mehr
		$erschoepungswert1 = 0;
		$erschoepungswert2 = 0;
	}
	$ermued1 = "UPDATE ".$prefix."spieler SET frische = frische-".$erschoepungswert1.$pflichtspielPlus.", spiele_gesamt = spiele_gesamt+1, spiele_verein = spiele_verein+1".$pokalNurFuerSQL." WHERE team = '".$team1_id."' AND startelf_".$to_simulate." != 0 AND verletzung = 0 LIMIT 11";
	$ermued1 = mysql_query($ermued1) or reportError(mysql_error(), $ermued1);
	$ermued2 = "UPDATE ".$prefix."spieler SET frische = frische-".$erschoepungswert2.$pflichtspielPlus.", spiele_gesamt = spiele_gesamt+1, spiele_verein = spiele_verein+1 WHERE team = '".$team2_id."' AND startelf_".$to_simulate." != 0 AND verletzung = 0 LIMIT 11";
	$ermued2 = mysql_query($ermued2) or reportError(mysql_error(), $ermued2);
	// FRISCHE ABZIEHEN ENDE
	// TORSCHUETZEN-, GELB-, ROT-AUSWAHL ANFANG
	if (isset($scorers1)) {
		if (is_array($scorers1)) {
			if (count($scorers1) > 3) {
				// TORE ANFANG
				arsort($scorers1);
				for ($i=0; $i<$tore[$sql3['team1']]; $i++) {
					if ($temp = each($scorers1)) {
						if ($to_simulate != 'Test') {
							$torj1 = "UPDATE ".$prefix."spieler SET tore = tore+1 WHERE ids = '".$temp['key']."'";
							$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						}
						$tore_team1 .= $temp['key'].'-';
					}
					if (mt_rand(0, 3) == 2) { reset($scorers1); }
				}
				$tore_team1 = substr($tore_team1, 0, -1);
				reset($scorers1);
				// TORE ENDE
				// VERLETZUNGEN ANFANG
				if (count($frischeWerteTeam1) == 0) { $frischeAvgTeam1 = 0; } else { $frischeAvgTeam1 = array_sum($frischeWerteTeam1)/count($frischeWerteTeam1); }
				if ($cookie_spieltag < 3 || $cookie_spieltag >= 22) { $frischeAvgTeam1 = 100; } // an den ersten beiden Spieltagen und am letzten keine Verletzungen
				$risikoFuerVerletzungTeam1 = floor((100-$frischeAvgTeam1)*1.35);
				if (Chance_Percent($risikoFuerVerletzungTeam1)) {
					asort($verletzungenVorauswahlTeam1, SORT_NUMERIC);
					if ($verletzter = each($verletzungenVorauswahlTeam1)) {
						$verletzungsDaten = create_verletzung();
						$torj1 = "UPDATE ".$prefix."spieler SET verletzung = ".$verletzungsDaten['dauer'].", startelf_".$to_simulate." = 0 WHERE ids = '".$verletzter['key']."'";
						$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						$formulierung = '<a href="/spieler.php?id='.$verletzter['key'].'">Einer Deiner Spieler</a> fällt '.$verletzungsDaten['name'].' für '.$verletzungsDaten['dauer'].' Tage aus.';
						$vlog1 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$team1_id."', '".mysql_real_escape_string($formulierung)."', 'Verletzung', ".time().")";
						$vlog2 = mysql_query($vlog1) or reportError(mysql_error(), $vlog1);
					}
				}
				// VERLETZUNGEN ENDE
				// GELB ANFANG
				asort($scorers1);
				for ($i= 0 ; $i < $gelbe_karten[$sql3['team1']]; $i++) {
					if ($temp = each($scorers1)) {
						//$torj1 = "UPDATE ".$prefix."spieler SET karten = karten+0.001 WHERE ids = '".$temp['key']."'";
						//$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						$gelb_team1 .= $temp['key'].'-';
						$scorers1[$temp['key']] *= 10; // damit der Scorer-Wert sehr hoch ist und der Spieler nicht noch mal gezogen wird
					}
				}
				$gelb_team1 = substr($gelb_team1, 0, -1);
				reset($scorers1);
				// GELB ENDE
				// ROT ANFANG
				asort($scorers1);
				for ($i = 0; $i < $rote_karten[$sql3['team1']]; $i++) {
					if ($temp = each($scorers1)) {
						//$torj1 = "UPDATE ".$prefix."spieler SET karten = karten+1 WHERE ids = '".$temp['key']."'";
						//$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						$rot_team1 .= $temp['key'].'-';
						$scorers1[$temp['key']] *= 10; // damit der Scorer-Wert sehr hoch ist und der Spieler nicht noch mal gezogen wird
					}
				}
				$rot_team1 = substr($rot_team1, 0, -1);
				// ROT ENDE
			}
		}
	}
	if (isset($scorers2)) {
		if (is_array($scorers2)) {
			if (count($scorers2) > 3) {
				// TORE ANFANG
				arsort($scorers2);
				for ($i = 0; $i < $tore[$sql3['team2']]; $i++) {
					if ($temp = each($scorers2)) {
						if ($to_simulate != 'Test') {
							$torj1 = "UPDATE ".$prefix."spieler SET tore = tore+1 WHERE ids = '".$temp['key']."'";
							$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						}
						$tore_team2 .= $temp['key'].'-';
					}
					if (mt_rand(0, 3) == 2) { reset($scorers2); }
				}
				$tore_team2 = substr($tore_team2, 0, -1);
				reset($scorers2);
				// TORE ENDE
				// VERLETZUNGEN ANFANG
				if (count($frischeWerteTeam2) == 0) { $frischeAvgTeam2 = 0; } else { $frischeAvgTeam2 = array_sum($frischeWerteTeam2)/count($frischeWerteTeam2); }
				if ($cookie_spieltag < 3 || $cookie_spieltag >= 22) { $frischeAvgTeam2 = 100; } // an den ersten beiden Spieltagen und am letzten keine Verletzungen
				$risikoFuerVerletzungTeam2 = floor((100-$frischeAvgTeam2)*1.35);
				if (Chance_Percent($risikoFuerVerletzungTeam2)) {
					asort($verletzungenVorauswahlTeam2, SORT_NUMERIC);
					if ($verletzter = each($verletzungenVorauswahlTeam2)) {
						$verletzungsDaten = create_verletzung();
						$torj1 = "UPDATE ".$prefix."spieler SET verletzung = ".$verletzungsDaten['dauer'].", startelf_".$to_simulate." = 0 WHERE ids = '".$verletzter['key']."'";
						$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						$formulierung = '<a href="/spieler.php?id='.$verletzter['key'].'">Einer Deiner Spieler</a> fällt '.$verletzungsDaten['name'].' für '.$verletzungsDaten['dauer'].' Tage aus.';
						$vlog1 = "INSERT INTO ".$prefix."protokoll (team, text, typ, zeit) VALUES ('".$team2_id."', '".mysql_real_escape_string($formulierung)."', 'Verletzung', ".time().")";
						$vlog2 = mysql_query($vlog1) or reportError(mysql_error(), $vlog1);
					}
				}
				// VERLETZUNGEN ENDE
				// GELB ANFANG
				asort($scorers2);
				for ($i = 0; $i < $gelbe_karten[$sql3['team2']]; $i++) {
					if ($temp = each($scorers2)) {
						//$torj1 = "UPDATE ".$prefix."spieler SET karten = karten+0.001 WHERE ids = '".$temp['key']."'";
						//$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						$gelb_team2 .= $temp['key'].'-';
						$scorers2[$temp['key']] *= 10; // damit der Scorer-Wert sehr hoch ist und der Spieler nicht noch mal gezogen wird
					}
				}
				$gelb_team2 = substr($gelb_team2, 0, -1);
				reset($scorers2);
				// GELB ENDE
				// ROT ANFANG
				asort($scorers2);
				for ($i = 0; $i < $rote_karten[$sql3['team2']]; $i++) {
					if ($temp = each($scorers2)) {
						//$torj1 = "UPDATE ".$prefix."spieler SET karten = karten+1 WHERE ids = '".$temp['key']."'";
						//$torj2 = mysql_query($torj1) or reportError(mysql_error(), $torj1);
						$rot_team2 .= $temp['key'].'-';
						$scorers2[$temp['key']] *= 10; // damit der Scorer-Wert sehr hoch ist und der Spieler nicht noch mal gezogen wird
					}
				}
				$rot_team2 = substr($rot_team2, 0, -1);
				// ROT ENDE
			}
		}
	}
	// TORSCHUETZEN-, GELB-, ROT-AUSWAHL ENDE
	$in1 = "UPDATE ".$prefix."spiele SET ergebnis = '".$resultat."', zuschauer = ".intval($watcher4).", tore1 = '".$tore_team1."', tore2 = '".$tore_team2."', ";
	$in1 .= "bericht = '".mysql_real_escape_string($spielbericht)."', ballbesitz1 = ".$ballbesitz_team1.", ballbesitz2 = ".$ballbesitz_team2.", fouls1 = ".$fouls[$sql3['team1']].", fouls2 = ".$fouls[$sql3['team2']].", karte_gelb1 = '".$gelb_team1."', karte_gelb2 = '".$gelb_team2."', karte_rot1 = '".$rot_team1."', karte_rot2 = '".$rot_team2."', abseits1 = ".$abseits[$sql3['team1']].", abseits2 = ".$abseits[$sql3['team2']].", schuesse1 = ".$schuesse[$sql3['team1']].", schuesse2 = ".$schuesse[$sql3['team2']]." WHERE id = ".$sql3['id'];
	$in2 = mysql_query($in1) or reportError(mysql_error(), $in1);
	// KOMMENTARE EINZELN IN DB FUER LIVE-SPIELBERICHTE ANFANG
    $spielbericht_kommentare = array();
    $spielbericht_lines = explode('</p><p>', $spielbericht);
    foreach ($spielbericht_lines as $spielbericht_line) {
        $spielbericht_line_str = trim(strip_tags($spielbericht_line, '<strong><a><span>'));
        $spielbericht_line = explode("': ", $spielbericht_line_str, 2);
        if (count($spielbericht_line) == 1) {
            $spielbericht_kommentare[] = array(0, $spielbericht_line[0]);
        }
        elseif (count($spielbericht_line) == 2) {
            $spielbericht_kommentare[] = array($spielbericht_line[0], $spielbericht_line[1]);
        }
        else {
            $spielbericht_kommentare[] = array(0, $spielbericht_line_str);
        }
    }
    $comm1_values = "";
    foreach ($spielbericht_kommentare as $spielbericht_kommentare_entry) {
        $comm1_values .= "(".$sql3['id'].", ".$spielbericht_kommentare_entry[0].", '".mysql_real_escape_string(trim($spielbericht_kommentare_entry[1]))."'),";
    }
    if (strlen($comm1_values) > 5) {
        $comm1_values = substr($comm1_values, 0, -1);
        $comm1 = "INSERT INTO ".$prefix."spiele_kommentare (spiel, minute, kommentar) VALUES ".$comm1_values;
        $comm2 = mysql_query($comm1) or reportError(mysql_error(), $comm1);
	}
	// KOMMENTARE EINZELN IN DB FUER LIVE-SPIELBERICHTE ENDE
	// ELO-AENDERUNG BERECHNEN ANFANG
	$eloGewinn1 = eloChange($resultat, $daten_team1['elo'], $daten_team2['elo'], $to_simulate);
	$eloGewinn2 = -eloChange($resultat, $daten_team1['elo'], $daten_team2['elo'], $to_simulate);
	$eloBuff1 = "INSERT INTO ".$prefix."eloBuffer (teamID, pointsGained, ausfuehren) VALUES ";
	$eloBuff1 .= "('".$team1_id."', ".$eloGewinn1.", ".getTimestamp('+2 hours')."),";
	$eloBuff1 .= "('".$team2_id."', ".$eloGewinn2.", ".getTimestamp('+2 hours').")";
	$eloBuff2 = mysql_query($eloBuff1) or reportError(mysql_error(), $eloBuff1);
	// ELO-AENDERUNG BERECHNEN ENDE
	if ($sql3['typ'] == 'Liga') {
		$in3 = "UPDATE ".$prefix."teams SET ".$sunAdd1."tore = tore+".$tore[$sql3['team1']].", gegentore = gegentore+".$tore[$sql3['team2']].", punkte = punkte+".$punkte1." WHERE ids = '".$team1_id."'";
		$in4 = mysql_query($in3) or reportError(mysql_error(), $in3);
	}
	elseif ($sql3['typ'] == 'Test') {
		$in3 = "UPDATE ".$prefix."teams SET ".$test1sql." WHERE ids = '".$team1_id."'";
		$in4 = mysql_query($in3) or reportError(mysql_error(), $in3);
	}
	$buch1 = "INSERT INTO ".$prefix."buchungenBuffer (teamID, verwendungszweck, betrag, ausfuehren) VALUES ('".$team1_id."', 'Ticketverkauf', ".$watcher_einkommen_h.", ".getTimestamp('+2 hours')."),('".$team1_id."', 'Sponsoring', ".$sponsor_einkommen1.", ".getTimestamp('+2 hours').")";
	$buch2 = mysql_query($buch1) or reportError(mysql_error(), $buch1);
	
	if ($sql3['typ'] == 'Liga') {
		$in3 = "UPDATE ".$prefix."teams SET ".$sunAdd2."tore = tore+".$tore[$sql3['team2']].", gegentore = gegentore+".$tore[$sql3['team1']].", punkte = punkte+".$punkte2." WHERE ids = '".$team2_id."'";
		$in4 = mysql_query($in3) or reportError(mysql_error(), $in3);
	}
	elseif ($sql3['typ'] == 'Test') {
		$in3 = "UPDATE ".$prefix."teams SET ".$test2sql." WHERE ids = '".$team2_id."'";
		$in4 = mysql_query($in3) or reportError(mysql_error(), $in3);
	}	
	if ($watcher_einkommen_a > 0) {
		$buch1 = "INSERT INTO ".$prefix."buchungenBuffer (teamID, verwendungszweck, betrag, ausfuehren) VALUES ('".$team2_id."', 'Ticketverkauf', ".$watcher_einkommen_a.", ".getTimestamp('+2 hours')."),('".$team2_id."', 'Sponsoring', ".$sponsor_einkommen2.", ".getTimestamp('+2 hours').")";
	}
	else {
		$buch1 = "INSERT INTO ".$prefix."buchungenBuffer (teamID, verwendungszweck, betrag, ausfuehren) VALUES ('".$team2_id."', 'Sponsoring', ".$sponsor_einkommen2.", ".getTimestamp('+2 hours').")";
	}
	$buch2 = mysql_query($buch1) or reportError(mysql_error(), $buch1);
	unset($resultat);
}
if ($to_simulate == 'Liga') {
	$temp = date('Y-m-d', time());
	$letzte_simulation1 = "UPDATE ".$prefix."zeitrechnung SET letzte_simulation = '".$temp."'";
	$letzte_simulation2 = mysql_query($letzte_simulation1) or reportError(mysql_error(), $letzte_simulation1);
	if (mysql_affected_rows() > 0) {
		$spieltag1 = "UPDATE ".$prefix."ligen SET gespielt = gespielt+1";
		$spieltag2 = mysql_query($spieltag1) or reportError(mysql_error(), $spieltag1);
		$erhol1 = "UPDATE ".$prefix."spieler SET frische = frische+1, moral = moral-1 WHERE verletzung = 0";
		$erhol2 = mysql_query($erhol1) or reportError(mysql_error(), $erhol1);
		$heilung1 = "UPDATE ".$prefix."spieler SET verletzung = verletzung-1 WHERE verletzung != 0";
		$heilung2 = mysql_query($heilung1) or reportError(mysql_error(), $heilung1);
	}
}
$max100 = "UPDATE ".$prefix."spieler SET frische = 100 WHERE frische > 100";
$max100 = mysql_query($max100) or reportError(mysql_error(), $max100);
$min0 = "UPDATE ".$prefix."spieler SET startelf = 0 WHERE frische < 5";
$min0 = mysql_query($min0) or reportError(mysql_error(), $min0);
$max100 = "UPDATE ".$prefix."spieler SET moral = 100 WHERE moral > 100";
$max100 = mysql_query($max100) or reportError(mysql_error(), $max100);
$min0 = "UPDATE ".$prefix."spieler SET moral = 0 WHERE moral < 0";
$min0 = mysql_query($min0) or reportError(mysql_error(), $min0);
?>
