<?php
$dbtype = 'mysql';
$dbhost = 'localhost';
$dbport = '3306';
$dbuser = 'root';
$dbpass = '';
$dbname = 'ballmanager';
mysql_connect($dbhost, $dbuser, $dbpass) or die ('Falsche MySQL-Daten!');
mysql_select_db($dbname) or die ('Datenbank existiert nicht!');

$config_isLocalInstallation = true;
?>