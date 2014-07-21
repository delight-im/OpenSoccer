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
define('CONFIG_EMAIL_PHP_MAILER', false); // whether to use PHPMailer (with SMTP) instead of the mail() function
define('CONFIG_EMAIL_HOST', ''); // SMTP server address
define('CONFIG_EMAIL_PORT', ''); // SMTP server port
define('CONFIG_EMAIL_USER', ''); // SMTP username
define('CONFIG_EMAIL_PASS', ''); // SMTP password
define('CONFIG_EMAIL_FROM', ''); // sender address
define('CONFIG_EMAIL_AUTH', true); // whether SMTP requires authentication
define('CONFIG_EMAIL_SECURE', ''); // for PHPMailer->SMTPSecure
define('CONFIG_EMAIL_CHARSET', 'UTF-8'); // mail charset
$config['isLocalInstallation'] = false;

?>