# OpenSoccer

Online Soccer Manager

**Live demo:** [www.opensoccer.org](http://www.opensoccer.org/)

## Setup

 1. Put the PHP files up on a web server
 2. Add the two subdomains `www` and `m` for your domain
 2. Set up an empty MySQL database with collation `utf8_general_ci` and privileges `SELECT, INSERT, UPDATE, DELETE, DROP`
 3. Run the SQL from [Database/STRUCTURE.sql](Database/STRUCTURE.sql) to create the database structure
 4. Run the SQL from [Database/DATA.sql](Database/DATA.sql) to add the initial data for the game
 5. Edit [Website/config.example.php](Website/config.example.php) so that it matches your installation and rename it to `Website/config.php`
 6. Set up all the cron jobs listed below
 7. Change the password for the default user with administrator rights (username: `Admin`, password: `admin`)
 8. Make sure that [GNU gettext](http://php.net/manual/de/book.gettext.php) is installed, e.g. on Ubuntu via

    ```
	sudo apt-get install gettext
	apt-get install locales
	```

 9. Make sure that the [Intl extension](http://php.net/manual/de/book.intl.php) is installed, e.g. on Ubuntu via

    `sudo apt-get install php5-intl`

 10. Make sure the directory `cache` is writable

## Cron jobs

 * [Website/aa_buchungenBuffer.php](Website/aa_buchungenBuffer.php): every 10 minutes; except for hours 10-11, 14-15, 18-19 and 22-23
 * [Website/aa_computer_managen.php](Website/aa_computer_managen.php): every 10 minutes
 * [Website/aa_cup_auslosen.php](Website/aa_cup_auslosen.php): every 30 minutes
 * [Website/aa_db_analyse.php](Website/aa_db_analyse.php): every day
 * [Website/aa_entlassungen.php](Website/aa_entlassungen.php): every hour
 * [Website/aa_gehaelter_abbuchen.php](Website/aa_gehaelter_abbuchen.php): every day
 * [Website/aa_lotto.php](Website/aa_lotto.php): every day
 * [Website/aa_marktwert_berechnen.php](Website/aa_marktwert_berechnen.php): every 5 minutes
 * [Website/aa_multi_detect.php](Website/aa_multi_detect.php): every 5 minutes
 * [Website/aa_npc_transfermarkt.php](Website/aa_npc_transfermarkt.php): every 15 minutes
 * [Website/aa_pokal_auslosen.php](Website/aa_pokal_auslosen.php): every 6 hours
 * [Website/aa_praemienAbrechnung.php](Website/aa_praemienAbrechnung.php): every 15 minutes
 * [Website/aa_saisonende.php](Website/aa_saisonende.php): every day; at hour 22
 * [Website/aa_spieler_erzeugen.php](Website/aa_spieler_erzeugen.php): every 30 minutes
 * [Website/aa_spieler_verbesserung.php](Website/aa_spieler_verbesserung.php): every 15 minutes
 * [Website/aa_spielplan_erstellen.php](Website/aa_spielplan_erstellen.php): every day; at hour 23
 * [Website/aa_spieltag_simulation.php](Website/aa_spieltag_simulation.php): every minute; at hours 10-11, 14-15, 18-19 and 22-23
 * [Website/aa_stadion_kosten.php](Website/aa_stadion_kosten.php): every day; at hour 23
 * [Website/aa_tabellen_berechnen.php](Website/aa_tabellen_berechnen.php): every 2 minutes; at hours 16-17
 * [Website/aa_team_staerke_berechnen.php](Website/aa_team_staerke_berechnen.php): every 5 minutes
 * [Website/aa_tv_einnahmen.php](Website/aa_tv_einnahmen.php): every 6 hours

## Contributing

Any contributions are welcome :) Please fork this repository, apply your changes, and submit your contributions by sending a pull request.

## Translating

In order to provide translations for this project, please refer to our [documentation](https://github.com/delight-im/PHP-I18N) and find the translation files in [Website/i18n](Website/i18n).

### Custom Poedit settings

 * Go to `File` - `Preferences` - `Parsers` - `PHP` - `Edit`. In the list of extensions, add `;*.php.txt` at the end.
 * Go to `File` - `Preferences` - `Translation Memory`. Disable the checkbox for `Use translation memory`.
 * Go to `Catalogue` - `Properties` - `Sources keywords`. Add a new entry `__` (double underscore).

## License

All parts of this project, except for the folder `Website/images`, have been released under the following license:

```
 Copyright (C) 2008-2015 www.delight.im <info@delight.im>
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see {http://www.gnu.org/licenses/}.
```