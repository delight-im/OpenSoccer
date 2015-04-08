SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE `man_abmeldungen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `zeit` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `liga` varchar(32) NOT NULL,
  `dabei` int(11) unsigned NOT NULL,
  `ip` varchar(32) NOT NULL DEFAULT 'd41d8cd98f00b204e9800998ecf8427e',
  KEY `id` (`id`),
  KEY `dabei` (`dabei`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_accDel` (
  `user` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL,
  `plus` varchar(255) NOT NULL,
  `minus` varchar(255) NOT NULL,
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_aufstellungLog` (
  `team` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `typ` enum('Liga','Pokal','Test','Cup') NOT NULL DEFAULT 'Liga',
  KEY `zeit` (`zeit`),
  KEY `user` (`team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_backendEmails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  `user` varchar(34) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_backendEmails_pending` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  `user` varchar(34) NOT NULL,
  `text` text NOT NULL,
  `votes` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `voters` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `combination` (`votes`,`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_blacklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `until` int(10) unsigned NOT NULL DEFAULT '1577833200',
  `host` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `abfrage` (`email`,`until`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_bp_mails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bpUserID` varchar(255) NOT NULL,
  `userID` varchar(255) NOT NULL,
  `mailSubject` varchar(255) NOT NULL,
  `mailText` text NOT NULL,
  `zeit` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_buchungen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `team` varchar(32) NOT NULL,
  `verwendungszweck` varchar(255) NOT NULL,
  `betrag` decimal(12,2) NOT NULL DEFAULT '0.00',
  `zeit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `verwendungszweck` (`verwendungszweck`),
  KEY `team` (`team`,`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_buchungenBuffer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `teamID` varchar(32) NOT NULL,
  `betrag` decimal(12,2) NOT NULL DEFAULT '0.00',
  `verwendungszweck` varchar(255) NOT NULL,
  `ausfuehren` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ausfuehren` (`ausfuehren`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chatroom` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL,
  `nachricht` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chatroomReportedUsers` (
  `user` varchar(32) NOT NULL,
  `reporter` varchar(32) NOT NULL,
  `datum` varchar(10) NOT NULL DEFAULT '0000-00-00',
  `protokoll` text NOT NULL,
  `sperrRelevant` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`user`,`datum`,`reporter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_chatroom_reported` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reporter` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL,
  `sitzung` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chatroom_sperren` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `sperreBis` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sperreBis` (`sperreBis`),
  KEY `user` (`user`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL,
  `nachricht` varchar(250) NOT NULL,
  `liga` varchar(34) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`),
  KEY `selection` (`liga`,`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chats_markt` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `zeit` int(11) unsigned NOT NULL DEFAULT '0',
  `nachricht` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chats_pokal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL,
  `nachricht` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_chats_tests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `zeit` int(11) unsigned NOT NULL DEFAULT '0',
  `nachricht` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_compensations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `helferID` varchar(34) NOT NULL,
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  `teamID` varchar(34) NOT NULL,
  `reason` text NOT NULL,
  `betrag` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_cronjobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datei` varchar(255) NOT NULL,
  `zuletzt` int(11) NOT NULL DEFAULT '0',
  `intervall` int(5) unsigned NOT NULL DEFAULT '0',
  `stunde_min` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `stunde_max` tinyint(1) unsigned NOT NULL DEFAULT '24',
  PRIMARY KEY (`id`),
  UNIQUE KEY `datei` (`datei`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_cupsieger` (
  `saison` tinyint(1) NOT NULL,
  `land` varchar(255) NOT NULL,
  `sieger` varchar(255) NOT NULL,
  `finalgegner` varchar(255) NOT NULL,
  PRIMARY KEY (`saison`,`land`),
  KEY `sieger` (`sieger`),
  KEY `finalgegner` (`finalgegner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_eloBuffer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `teamID` varchar(32) NOT NULL,
  `pointsGained` decimal(7,2) NOT NULL DEFAULT '0.00',
  `ausfuehren` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ausfuehren` (`ausfuehren`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_forum_beitraege` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `thema` varchar(32) NOT NULL,
  `manager` varchar(32) NOT NULL,
  `postIP` varchar(32) NOT NULL,
  `datum` int(11) NOT NULL,
  `inhalt` text NOT NULL,
  `sichtbar` char(1) NOT NULL DEFAULT 'J',
  `quote` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `thema` (`thema`),
  KEY `sichtbar` (`sichtbar`),
  FULLTEXT KEY `inhalt` (`inhalt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_forum_gelesen` (
  `thema` varchar(32) NOT NULL,
  `user` varchar(32) NOT NULL,
  PRIMARY KEY (`thema`,`user`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_forum_themen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `datum` int(11) NOT NULL,
  `lastposter` varchar(32) NOT NULL,
  `manager` varchar(255) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `sichtbar` char(1) NOT NULL DEFAULT 'J',
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sichtbar_fuer` enum('Alle','Helfer') NOT NULL DEFAULT 'Alle',
  `postCount` smallint(5) unsigned NOT NULL DEFAULT '1',
  `kategorie` varchar(255) NOT NULL DEFAULT 'Sonstiges',
  PRIMARY KEY (`id`),
  KEY `ids` (`ids`),
  KEY `sticky` (`sticky`),
  KEY `kategorie` (`kategorie`),
  FULLTEXT KEY `titel` (`titel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_freunde` (
  `f1` varchar(32) NOT NULL,
  `f2` varchar(32) NOT NULL,
  `typ` enum('F','B') NOT NULL DEFAULT 'F',
  `sortOrder` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`f1`,`f2`),
  KEY `selection` (`f1`,`typ`,`sortOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_freunde_anfragen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `von` varchar(255) NOT NULL,
  `an` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `von` (`von`),
  KEY `an` (`an`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_geschichte_tabellen` (
  `saison` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `spieltag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `liga` varchar(32) NOT NULL,
  `team` varchar(255) NOT NULL,
  `platz` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `punkte` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tore` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gegentore` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`liga`,`saison`,`spieltag`,`team`),
  KEY `statistik_abfrage` (`saison`,`spieltag`),
  KEY `team` (`team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_helferLog` (
  `helfer` varchar(32) NOT NULL,
  `managerBestrafen` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `chatSperre` int(11) NOT NULL DEFAULT '0',
  `transferSperre` int(11) NOT NULL DEFAULT '0',
  `geldStrafe` int(11) NOT NULL DEFAULT '0',
  `verstoss` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `zeitAktuell` (`zeit`,`chatSperre`,`transferSperre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_licenseTasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(255) NOT NULL,
  `task` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortName` (`shortName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_licenseTasks_Completed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task` varchar(255) NOT NULL,
  `user` varchar(34) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taskID` (`user`,`task`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_ligaChangeAnfragen` (
  `vonTeam` varchar(32) NOT NULL,
  `anTeam` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vonTeam`,`anTeam`),
  KEY `anTeam` (`anTeam`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_ligaChanges` (
  `user1` varchar(32) NOT NULL,
  `team1` varchar(32) NOT NULL,
  `newLiga1` varchar(32) NOT NULL,
  `user2` varchar(32) NOT NULL,
  `team2` varchar(32) NOT NULL,
  `newLiga2` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`zeit`,`user1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_ligaChangeWuensche` (
  `teamID` varchar(32) NOT NULL,
  `teamName` varchar(255) NOT NULL,
  `landNoch` varchar(255) NOT NULL,
  `landWunsch` varchar(255) NOT NULL,
  `zeit` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`teamID`),
  KEY `zeit` (`zeit`),
  KEY `landWunsch` (`landWunsch`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_ligen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gespielt` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `hoch` varchar(32) NOT NULL DEFAULT 'KEINE',
  `runter` varchar(32) NOT NULL DEFAULT 'KEINE',
  `land` varchar(255) NOT NULL,
  `isoAlpha2` varchar(2) NOT NULL,
  `pkt_saison5` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `pkt_saison4` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `pkt_saison3` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `pkt_saison2` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `pkt_saison1` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `pkt_gesamt` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `lastCupSelection` int(11) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ids` (`ids`),
  KEY `hoch` (`hoch`),
  KEY `runter` (`runter`),
  KEY `pkt_gesamt` (`pkt_gesamt`),
  KEY `land` (`land`),
  KEY `lastCupSelection` (`lastCupSelection`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_loginLog` (
  `user` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(32) NOT NULL,
  `userAgent` varchar(255) NOT NULL,
  `uniqueHash` varchar(32) NOT NULL,
  KEY `zeit` (`zeit`),
  KEY `user` (`user`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_lotto` (
  `jackpot` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `zahlen_gestern` varchar(11) NOT NULL,
  PRIMARY KEY (`jackpot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_lotto_gewinner` (
  `team` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL,
  `summe` decimal(12,2) unsigned NOT NULL,
  `richtige` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `summe` (`summe`),
  KEY `zeit` (`zeit`),
  KEY `richtige` (`richtige`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_lotto_tipps` (
  `team` varchar(32) NOT NULL,
  `datum` varchar(10) NOT NULL DEFAULT '0000-00-00',
  `zahlen` varchar(11) NOT NULL,
  PRIMARY KEY (`datum`,`team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_multiChanges` (
  `helfer` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `user1` varchar(32) NOT NULL,
  `user2` varchar(32) NOT NULL,
  `type` enum('connect','unconnect') NOT NULL,
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_mysqlQuerys` (
  `datei` varchar(255) NOT NULL,
  `queryText` varchar(255) NOT NULL,
  `fehler` varchar(255) NOT NULL,
  PRIMARY KEY (`datei`(166),`fehler`(166))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_nameChanges` (
  `helfer` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `vonID` varchar(32) NOT NULL,
  `vonName` varchar(255) NOT NULL,
  `zuName` varchar(255) NOT NULL,
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_namen_pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `typ` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kombination` (`name`,`typ`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_personal_changes` (
  `team` varchar(32) NOT NULL,
  `personal` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`team`,`personal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_php_fehler` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datei` varchar(255) NOT NULL,
  `zeile` varchar(5) NOT NULL,
  `beschreibung` text NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `datei` (`datei`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_pn` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `von` varchar(32) NOT NULL,
  `an` varchar(32) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `inhalt` longtext NOT NULL,
  `zeit` int(11) NOT NULL,
  `geloescht_von` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `geloescht_an` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gelesen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_reply_to` varchar(32) NOT NULL,
  PRIMARY KEY (`ids`),
  UNIQUE KEY `id` (`id`),
  KEY `an` (`an`,`geloescht_an`,`gelesen`,`zeit`),
  KEY `von` (`von`,`geloescht_von`,`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_pokalsieger` (
  `saison` tinyint(1) NOT NULL,
  `sieger` varchar(255) NOT NULL,
  `finalgegner` varchar(255) NOT NULL,
  PRIMARY KEY (`saison`),
  KEY `sieger` (`sieger`),
  KEY `finalgegner` (`finalgegner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_press` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `manager` varchar(32) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `reviewed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `auflistung` (`zeit`,`reviewed`),
  KEY `ids` (`ids`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_pressGelesen` (
  `articleID` varchar(32) NOT NULL,
  `userID` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `mark` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`articleID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_press_gelesen` (
  `thema` varchar(32) NOT NULL,
  `user` varchar(32) NOT NULL,
  PRIMARY KEY (`thema`,`user`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team` varchar(32) NOT NULL,
  `text` varchar(255) NOT NULL,
  `typ` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `selection_without_filter` (`team`,`zeit`),
  KEY `selection_with_filter` (`team`,`typ`,`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_referrals` (
  `werber` varchar(32) NOT NULL,
  `geworben` varchar(32) NOT NULL,
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  `billed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`werber`,`geworben`),
  KEY `geworben` (`geworben`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_spiele` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liga` varchar(32) NOT NULL,
  `datum` int(11) NOT NULL,
  `team1` varchar(255) NOT NULL,
  `team2` varchar(255) NOT NULL,
  `zuschauer` int(11) unsigned NOT NULL DEFAULT '0',
  `ergebnis` varchar(255) NOT NULL DEFAULT '-:-',
  `simuliert` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tore1` text NOT NULL,
  `tore2` text NOT NULL,
  `typ` enum('Liga','Pokal','Test','Cup') NOT NULL,
  `bericht` text NOT NULL,
  `ballbesitz1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ballbesitz2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fouls1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fouls2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `karte_gelb1` varchar(255) NOT NULL,
  `karte_gelb2` varchar(255) NOT NULL,
  `karte_rot1` varchar(255) NOT NULL,
  `karte_rot2` varchar(255) NOT NULL,
  `abseits1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `abseits2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `schuesse1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `schuesse2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `kennung` varchar(32) NOT NULL,
  `land` varchar(255) NOT NULL,
  `simulationError` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `kombination` (`team1`(166),`team2`(166)),
  KEY `datum` (`datum`),
  KEY `kennung` (`kennung`),
  KEY `land` (`land`),
  KEY `simuliert` (`simuliert`),
  KEY `team2` (`team2`),
  KEY `typ_land` (`typ`,`land`),
  KEY `typ_simuliert` (`typ`,`simuliert`),
  KEY `liga` (`liga`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_spieler` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `position` char(1) NOT NULL,
  `wiealt` smallint(6) unsigned NOT NULL DEFAULT '0',
  `frische` tinyint(1) NOT NULL DEFAULT '100',
  `startelf` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `spiele` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `spiele_gesamt` smallint(5) unsigned NOT NULL DEFAULT '0',
  `spiele_verein` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tore` int(11) unsigned NOT NULL DEFAULT '0',
  `karten` decimal(4,3) unsigned NOT NULL DEFAULT '0.000',
  `liga` varchar(32) NOT NULL,
  `team` varchar(32) NOT NULL,
  `staerke` decimal(2,1) unsigned NOT NULL,
  `talent` decimal(2,1) unsigned NOT NULL,
  `marktwert` int(11) unsigned NOT NULL DEFAULT '0',
  `verhandlungsbasis` int(11) NOT NULL DEFAULT '0',
  `gehalt` int(11) unsigned NOT NULL DEFAULT '0',
  `transfermarkt` int(11) unsigned NOT NULL DEFAULT '0',
  `letzte_verbesserung` int(11) unsigned NOT NULL DEFAULT '0',
  `vertrag` int(11) unsigned NOT NULL DEFAULT '0',
  `leiher` varchar(32) NOT NULL DEFAULT 'keiner',
  `jugendTeam` varchar(32) NOT NULL,
  `verletzung` tinyint(1) NOT NULL DEFAULT '0',
  `startelf_Liga` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `startelf_Pokal` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `startelf_Cup` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `startelf_Test` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `praemieProEinsatz` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `praemienAbrechnung` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pokalNurFuer` varchar(32) NOT NULL,
  `moral` decimal(5,2) NOT NULL DEFAULT '100.00',
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `liga` (`liga`),
  KEY `ids` (`ids`),
  KEY `transfermarkt` (`transfermarkt`),
  KEY `spiele_gesamt` (`spiele_gesamt`),
  KEY `wiealt` (`wiealt`),
  KEY `staerke` (`staerke`),
  KEY `tore` (`tore`),
  KEY `verletzung` (`verletzung`),
  KEY `moral` (`moral`),
  KEY `frische` (`frische`),
  KEY `vertrag_team` (`vertrag`,`team`),
  KEY `team_position` (`team`,`position`),
  KEY `marktwert_wiealt` (`marktwert`,`wiealt`),
  KEY `leiher_praemieProEinsatz` (`leiher`,`praemieProEinsatz`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_spielerEntwicklung` (
  `team` varchar(32) NOT NULL,
  `spieler` varchar(32) NOT NULL,
  `zeit` int(10) NOT NULL DEFAULT '0',
  `staerkeAlt` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  `staerkeNeu` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  KEY `spieler` (`spieler`),
  KEY `team` (`team`,`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_spieler_mark` (
  `team` varchar(32) NOT NULL,
  `spieler` varchar(32) NOT NULL,
  `farbe` enum('Keine','Blau','Gelb','Rot','Gruen','Pink','Aqua','Silber','Lila','Oliv') NOT NULL DEFAULT 'Keine',
  PRIMARY KEY (`team`,`spieler`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_spiele_kommentare` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spiel` int(11) unsigned NOT NULL,
  `minute` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `kommentar` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `minute` (`minute`),
  KEY `spiel_minute` (`spiel`,`minute`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_spielplan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spieltag` int(11) unsigned NOT NULL,
  `team1` int(11) unsigned NOT NULL,
  `team2` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kombination` (`team1`,`team2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_spielstatistik` (
  `datum` varchar(10) NOT NULL,
  `median` varchar(25) NOT NULL DEFAULT '-1',
  `durchschnitt` varchar(25) NOT NULL DEFAULT '-1',
  `durchschnitt_o` varchar(25) NOT NULL DEFAULT '-1',
  `durchschnitt_u` varchar(25) NOT NULL DEFAULT '-1',
  `marktwert_sum` varchar(25) NOT NULL DEFAULT '-1',
  `marktwert_avg` varchar(25) NOT NULL DEFAULT '-1',
  `preisniveau` varchar(25) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`datum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_sponsoren` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `prozentsatz` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_stadien` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `team` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `namePrefix` varchar(20) NOT NULL DEFAULT '',
  `namePostfix` varchar(20) NOT NULL DEFAULT 'Arena',
  `plaetze` int(11) unsigned NOT NULL DEFAULT '15000',
  `preis` tinyint(1) unsigned NOT NULL DEFAULT '20',
  `parkplatz` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ubahn` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `restaurant` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bierzelt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pizzeria` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `imbissstand` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vereinsmuseum` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fanshop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `underConstructionUntil` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `team` (`team`),
  KEY `plaetze` (`plaetze`),
  KEY `ids` (`ids`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_supplyDemandPrices` (
  `item` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_supportComments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` varchar(32) NOT NULL,
  `requestID` int(10) unsigned NOT NULL DEFAULT '0',
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `likes` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `requestID_zeit` (`requestID`,`zeit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_supportLikes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` varchar(32) NOT NULL,
  `commentID` int(10) unsigned NOT NULL DEFAULT '0',
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kombination` (`userID`,`commentID`),
  KEY `requestID` (`commentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_supportRead` (
  `userID` varchar(32) NOT NULL,
  `anfrageID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`userID`,`anfrageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_supportRequests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `visibilityLevel` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pro` int(10) unsigned NOT NULL DEFAULT '1',
  `contra` int(10) unsigned NOT NULL DEFAULT '0',
  `spamReports` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `timeAdded` int(10) unsigned NOT NULL DEFAULT '0',
  `lastAction` int(10) unsigned NOT NULL DEFAULT '0',
  `author` varchar(32) NOT NULL,
  `category` enum('Frage','Fehlerbericht','Vorschlag') NOT NULL DEFAULT 'Frage',
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `abfrageSortierung` (`visibilityLevel`,`open`,`lastAction`),
  KEY `author` (`author`),
  KEY `throttling` (`timeAdded`,`author`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_supportUsers` (
  `userID` varchar(32) NOT NULL,
  `replies` int(10) NOT NULL DEFAULT '0',
  `fastReplies` int(10) NOT NULL DEFAULT '0',
  `thanksReceived` int(10) NOT NULL DEFAULT '0',
  `votes` int(10) NOT NULL DEFAULT '0',
  `points` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`),
  KEY `points` (`points`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_supportVotes` (
  `request` int(10) unsigned NOT NULL DEFAULT '0',
  `userID` varchar(32) NOT NULL,
  `vote` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`request`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_taktiken` (
  `team` varchar(32) NOT NULL,
  `spieltyp` enum('Liga','Pokal','Test','Cup') NOT NULL DEFAULT 'Liga',
  `ausrichtung` tinyint(1) NOT NULL DEFAULT '2',
  `geschw_auf` tinyint(1) NOT NULL DEFAULT '2',
  `pass_auf` tinyint(1) NOT NULL DEFAULT '2',
  `risk_pass` tinyint(1) NOT NULL DEFAULT '2',
  `druck` tinyint(1) NOT NULL DEFAULT '2',
  `aggress` tinyint(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`team`,`spieltyp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_taktiken_vorlagen` (
  `team` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `zeit` int(11) unsigned NOT NULL DEFAULT '0',
  `ausrichtung` tinyint(1) NOT NULL DEFAULT '2',
  `geschw_auf` tinyint(1) NOT NULL DEFAULT '2',
  `pass_auf` tinyint(1) NOT NULL DEFAULT '2',
  `risk_pass` tinyint(1) NOT NULL DEFAULT '2',
  `druck` tinyint(1) NOT NULL DEFAULT '2',
  `aggress` tinyint(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`team`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_teamChangeCodes` (
  `team` varchar(32) NOT NULL,
  `code` varchar(64) NOT NULL,
  `gueltigBis` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`team`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_teamChanges` (
  `team1` varchar(32) NOT NULL,
  `team2` varchar(32) NOT NULL,
  `zeit` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`team1`,`team2`,`zeit`),
  KEY `zeit` (`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `origName` varchar(255) NOT NULL,
  `liga` varchar(32) NOT NULL,
  `rank` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `punkte` int(11) NOT NULL DEFAULT '0',
  `tore` int(11) NOT NULL DEFAULT '0',
  `gegentore` int(11) NOT NULL DEFAULT '0',
  `staerke` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  `aufstellung` decimal(4,1) unsigned NOT NULL DEFAULT '0.0',
  `meisterschaften` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pokalsiege` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cupsiege` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `friendlies` smallint(5) unsigned NOT NULL DEFAULT '0',
  `friendlies_ges` smallint(5) unsigned NOT NULL DEFAULT '0',
  `jugendarbeit` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fanbetreuer` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `scout` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `konto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `vorjahr_konto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gewinnGeld` decimal(12,2) NOT NULL DEFAULT '0.00',
  `renommee` int(11) NOT NULL DEFAULT '0',
  `sponsor` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sponsor_a` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `sponsor_s` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `taktik` char(1) NOT NULL DEFAULT 'N',
  `einsatz` tinyint(1) unsigned NOT NULL DEFAULT '100',
  `fanaufkommen` int(11) unsigned NOT NULL DEFAULT '8000',
  `vorjahr_platz` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vorjahr_liga` varchar(32) NOT NULL,
  `pokalrunde` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cuprunde` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_cookie_user` varchar(32) NOT NULL,
  `vorjahr_pokalrunde` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vorjahr_cuprunde` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `stadion_aus` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tv_ein` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `last_managed` int(11) NOT NULL DEFAULT '0',
  `letzte_jugend` int(11) NOT NULL DEFAULT '0',
  `last_renommeeCalc` int(11) NOT NULL DEFAULT '0',
  `letzte_regeneration` varchar(10) NOT NULL DEFAULT '2000-01-01',
  `letzte_physio` varchar(10) NOT NULL DEFAULT '2000-01-01',
  `letzte_psychologe` varchar(10) NOT NULL DEFAULT '2000-01-01',
  `posToSearch` char(1) NOT NULL DEFAULT 'M',
  `wantTests` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sunS` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sunU` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sunN` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `elo` decimal(7,2) unsigned NOT NULL DEFAULT '1200.00',
  `vorjahr_elo` decimal(7,2) unsigned NOT NULL DEFAULT '1200.00',
  PRIMARY KEY (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `ids` (`ids`),
  KEY `liga` (`liga`),
  KEY `last_managed` (`last_managed`),
  KEY `last_cookie_user` (`last_cookie_user`),
  KEY `konto` (`konto`),
  KEY `rank` (`rank`),
  KEY `letzte_jugend` (`letzte_jugend`),
  KEY `elo` (`elo`),
  KEY `vorjahr_liga` (`vorjahr_liga`),
  KEY `vorjahr_pokalrunde` (`vorjahr_pokalrunde`),
  KEY `punkte` (`punkte`),
  KEY `cuprunde` (`cuprunde`),
  KEY `tv_ein` (`tv_ein`),
  KEY `pokalrunde` (`pokalrunde`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_testspiel_anfragen` (
  `team1` varchar(32) NOT NULL,
  `team1_name` varchar(255) NOT NULL,
  `team2` varchar(32) NOT NULL,
  `datum` int(11) unsigned NOT NULL DEFAULT '0',
  `zeit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`team1`,`team2`),
  KEY `team2` (`team2`),
  KEY `datum` (`datum`),
  KEY `team1_name` (`team1_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_transfermarkt` (
  `spieler` varchar(32) NOT NULL,
  `besitzer` varchar(32) NOT NULL,
  `gehalt` int(11) unsigned NOT NULL DEFAULT '0',
  `bieter_highest` varchar(32) NOT NULL DEFAULT 'keiner',
  `betrag_highest` int(11) NOT NULL DEFAULT '0',
  `gebote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ende` int(11) NOT NULL DEFAULT '0',
  `typ` enum('Kauf','Leihe') NOT NULL DEFAULT 'Kauf',
  `sofortkauf` int(11) unsigned NOT NULL DEFAULT '999999999',
  `autorestart` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`spieler`),
  KEY `ende` (`ende`),
  KEY `besitzer` (`besitzer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_transfermarkt_leihe` (
  `spieler` varchar(32) NOT NULL,
  `besitzer` varchar(32) NOT NULL,
  `bieter` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL,
  `praemie` int(11) unsigned NOT NULL DEFAULT '0',
  `akzeptiert` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `besitzer` (`besitzer`),
  KEY `bieter` (`bieter`),
  KEY `spieler` (`spieler`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_transfermarkt_watch` (
  `team` varchar(32) NOT NULL,
  `spieler_id` varchar(32) NOT NULL,
  `spieler_name` varchar(255) NOT NULL,
  PRIMARY KEY (`team`,`spieler_id`),
  KEY `spieler_id` (`spieler_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_transfers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spieler` varchar(32) NOT NULL,
  `besitzer` varchar(255) NOT NULL,
  `bieter` varchar(255) NOT NULL,
  `datum` int(11) NOT NULL,
  `gebot` int(11) NOT NULL,
  `spiele_verein` smallint(5) unsigned NOT NULL DEFAULT '0',
  `damaligerWert` int(11) unsigned NOT NULL DEFAULT '1',
  `leihgebuehr` int(11) unsigned NOT NULL DEFAULT '0',
  `damaligeStaerke` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `duplikateVermeiden` (`spieler`,`datum`),
  KEY `besitzer` (`besitzer`),
  KEY `bieter` (`bieter`),
  KEY `datum` (`datum`),
  KEY `gebot` (`gebot`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_transfers_gebote` (
  `spieler` varchar(32) NOT NULL,
  `bieter` varchar(32) NOT NULL,
  `datum` int(11) unsigned NOT NULL DEFAULT '0',
  `bieterIP` varchar(32) NOT NULL,
  `betrag` int(11) NOT NULL DEFAULT '0',
  KEY `spieler` (`spieler`),
  KEY `bieterIP` (`bieterIP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_transfers_old` (
  `id` int(11) unsigned NOT NULL,
  `spieler` varchar(32) NOT NULL,
  `besitzer` varchar(255) NOT NULL,
  `bieter` varchar(255) NOT NULL,
  `datum` int(11) NOT NULL,
  `gebot` int(11) NOT NULL,
  `spiele_verein` smallint(5) unsigned NOT NULL DEFAULT '0',
  `damaligerWert` int(11) unsigned NOT NULL DEFAULT '1',
  `leihgebuehr` int(11) unsigned NOT NULL DEFAULT '0',
  `damaligeStaerke` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  KEY `besitzer` (`besitzer`),
  KEY `bieter` (`bieter`),
  KEY `datum` (`datum`),
  KEY `gebot` (`gebot`),
  KEY `spieler` (`spieler`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_urlaub` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `team` varchar(32) NOT NULL,
  `ende` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `ende` (`ende`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `regdate` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_chat` int(11) NOT NULL DEFAULT '0',
  `last_urlaub_kurz` int(11) NOT NULL DEFAULT '0',
  `last_urlaub_lang` int(11) NOT NULL DEFAULT '0',
  `liga` varchar(32) NOT NULL DEFAULT '0',
  `team` varchar(34) NOT NULL DEFAULT '0',
  `meisterschaften` int(11) unsigned NOT NULL DEFAULT '0',
  `urlaub` tinyint(1) unsigned NOT NULL DEFAULT '10',
  `last_ip` varchar(32) NOT NULL,
  `last_uniqueHash` varchar(32) NOT NULL,
  `last_uagent` varchar(255) NOT NULL,
  `last_provider` varchar(255) NOT NULL,
  `cheated` int(11) NOT NULL DEFAULT '0',
  `aufstellung_change` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` enum('Benutzer','Admin','Helfer','Bigpoint') NOT NULL DEFAULT 'Benutzer',
  `verwarnt` int(11) NOT NULL DEFAULT '0',
  `infotext` text NOT NULL,
  `readSticky` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `facebookActive` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `acceptedRules` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `hasLicense` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `multiSperre` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `anzSanktionen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `welcomedByStaff` tinyint(1) NOT NULL DEFAULT '0',
  `lastBackendEmail` varchar(10) NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `team_2` (`team`),
  UNIQUE KEY `ids_2` (`ids`),
  KEY `login` (`email`(166),`password`),
  KEY `last_login` (`last_login`),
  KEY `cheated` (`cheated`),
  KEY `verwarnt` (`verwarnt`),
  KEY `last_chat` (`last_chat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_users_mds` (
  `manager` varchar(32) NOT NULL,
  `voter` varchar(32) NOT NULL,
  PRIMARY KEY (`voter`),
  KEY `manager` (`manager`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_users_mds_sieger` (
  `saison` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ids` varchar(32) NOT NULL,
  `username` varchar(255) NOT NULL,
  `stimmen` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`saison`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_users_multis` (
  `user1` varchar(32) NOT NULL,
  `user2` varchar(32) NOT NULL,
  `found_ip` varchar(39) NOT NULL DEFAULT 'd41d8cd98f00b204e9800998ecf8427e',
  `found_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user1`,`user2`),
  KEY `found_time` (`found_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_users_newpw` (
  `user` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL,
  `keywert` varchar(32) NOT NULL,
  `newpw` varchar(32) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_users_notizen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `text` varchar(250) NOT NULL,
  `textColor` varchar(6) NOT NULL DEFAULT '000000',
  `backgroundColor` varchar(6) NOT NULL DEFAULT 'ffffff',
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `man_verletzungen` (
  `spieler` varchar(32) NOT NULL,
  `team` varchar(32) NOT NULL,
  `verletzung` varchar(255) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`spieler`,`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_vNameChanges` (
  `team` varchar(32) NOT NULL,
  `zeit` int(11) NOT NULL DEFAULT '0',
  `vonName` varchar(255) NOT NULL,
  `zuName` varchar(255) NOT NULL,
  `sperre` tinyint(1) unsigned NOT NULL DEFAULT '1',
  KEY `zeit` (`zeit`),
  KEY `team` (`team`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_vNameOriginals` (
  `stadt` varchar(255) NOT NULL,
  `zusatz` varchar(255) NOT NULL,
  `helfer` varchar(32) NOT NULL,
  PRIMARY KEY (`stadt`(150),`zusatz`(150))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_vNamePool` (
  `name` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  PRIMARY KEY (`land`(166),`name`(166)),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_wartezeiten` (
  `regdate` int(11) NOT NULL DEFAULT '0',
  `wartezeit` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `regdate` (`regdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `man_zeitrechnung` (
  `zeit` int(11) NOT NULL,
  `last` varchar(255) NOT NULL,
  `saison` int(11) unsigned NOT NULL DEFAULT '1',
  `letzte_abbuchung` varchar(255) NOT NULL DEFAULT '2000-01-01',
  `letzte_stadionkosten` varchar(255) NOT NULL DEFAULT '2000-01-01',
  `letzte_simulation` varchar(255) NOT NULL DEFAULT '2000-01-01',
  `letzte_jugend` int(11) NOT NULL DEFAULT '0',
  `letzte_entlassung` varchar(255) NOT NULL DEFAULT '2000-01-01',
  PRIMARY KEY (`zeit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
