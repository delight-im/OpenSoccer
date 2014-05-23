DELETE FROM man_buchungen WHERE zeit < (UNIX_TIMESTAMP()-3600*24*44);
DELETE FROM man_protokoll WHERE zeit < (UNIX_TIMESTAMP()-3600*24*44);
DELETE FROM man_spielerEntwicklung WHERE zeit < (UNIX_TIMESTAMP()-3600*24*44);
DELETE FROM man_loginLog WHERE zeit < (UNIX_TIMESTAMP()-3600*24*44);
DELETE FROM man_aufstellungLog WHERE zeit < (UNIX_TIMESTAMP()-3600*24*44);
DELETE FROM man_geschichte_tabellen WHERE saison < ((SELECT tempTable.maxSaison FROM (SELECT MAX(saison) AS maxSaison FROM man_geschichte_tabellen) AS tempTable) - 28);