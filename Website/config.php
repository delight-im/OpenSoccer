<?php
$dbtype = '';
$dbhost = '';
$dbport = '';
$dbuser = '';
$dbpass = '';
$dbname = '';

$domain="";

$config['PHP_MAILER']=true;				// set to true if mail() doesn't work
$config['SMTP_HOST']="";				// SMTP server adress
$config['SMTP_PORT']="";				// SMTP server port
$config['SMTP_USER']="";				// SMTP username
$config['SMTP_PASS']="";				// SMTP password
$config['SMTP_FROM']="";				// From adress
$config['SMTP_AUTH']=true;				// SMTP requires authentication
$config['SMTP_SECURE']='';				// for PHPMailer ->SMTPSecure
$config['SMTP_CHARSET']="UTF-8";		// Mail CharSet

mysql_connect($dbhost, $dbuser, $dbpass) or die ('Falsche MySQL-Daten!');
mysql_select_db($dbname) or die ('Datenbank existiert nicht!');

$config_isLocalInstallation = false;
?>