<?php

class Friendlies {

    public static function getPrice($leagueID, $databasePrefix) {
        $bql1 = "SELECT name FROM ".$databasePrefix."ligen WHERE ids = '".$leagueID."'";
        $bql2 = mysql_query($bql1);
        $bql3 = mysql_fetch_assoc($bql2);
        $leagueLevel = intval(substr($bql3['name'], -1));
        switch ($leagueLevel) {
            case 4: return 50000;
            case 3: return 100000;
            case 2: return 500000;
            default: return 1000000;
        }
    }

}

?>
