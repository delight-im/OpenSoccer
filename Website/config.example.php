<?php

// database credentials
$database = array();
$database['host'] = 'localhost';
$database['username'] = 'root';
$database['password'] = '';
$database['name'] = 'ballmanager';

// connect to the database
mysql_connect($database['host'], $database['username'], $database['password']) or die ('Falsche MySQL-Daten!');
mysql_select_db($database['name']) or die ('Datenbank existiert nicht!');

// remove the database variable's content again
$database = NULL;

// the installation date (UTC) of this game instance in YYYY-MM-DD format (ISO 8601)
define('CONFIG_INSTALL_DATE', '2008-09-25');

$config = array();
$config['PHP_MAILER'] = false; // whether to use PHPMailer (with SMTP) instead of the mail() function
if ($config['PHP_MAILER']) {
    $config['SMTP_HOST'] = ''; // SMTP server address
    $config['SMTP_PORT'] = ''; // SMTP server port
    $config['SMTP_USER'] = ''; // SMTP username
    $config['SMTP_PASS'] = ''; // SMTP password
    $config['SMTP_FROM'] = ''; // sender address
    $config['SMTP_AUTH'] = true; // whether SMTP requires authentication
    $config['SMTP_SECURE'] = ''; // for PHPMailer->SMTPSecure
    $config['SMTP_CHARSET'] = 'UTF-8'; // mail charset
}
$config['isLocalInstallation'] = false;

?>