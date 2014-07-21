<?php

// database credentials
define('CONFIG_DATABASE_HOST', 'localhost');
define('CONFIG_DATABASE_USERNAME', 'root');
define('CONFIG_DATABASE_PASSWORD', '');
define('CONFIG_DATABASE_NAME', 'ballmanager');

// the installation date (UTC) of this game instance in YYYY-MM-DD format (ISO 8601)
define('CONFIG_INSTALL_DATE', '2008-09-25');

define('CONFIG_EMAIL_PHP_MAILER', false); // whether to use PHPMailer (with SMTP) instead of the mail() function
define('CONFIG_EMAIL_HOST', ''); // SMTP server address
define('CONFIG_EMAIL_PORT', ''); // SMTP server port
define('CONFIG_EMAIL_USER', ''); // SMTP username
define('CONFIG_EMAIL_PASS', ''); // SMTP password
define('CONFIG_EMAIL_FROM', ''); // sender address
define('CONFIG_EMAIL_AUTH', true); // whether SMTP requires authentication
define('CONFIG_EMAIL_SECURE', ''); // for PHPMailer->SMTPSecure
define('CONFIG_EMAIL_CHARSET', 'UTF-8'); // mail charset
define('CONFIG_IS_LOCAL_INSTALLATION', false); // whether this is an installation on a local computer (no remote server)

?>