<?php

// database credentials
define('CONFIG_DATABASE_HOST', 'localhost');
define('CONFIG_DATABASE_USERNAME', 'root');
define('CONFIG_DATABASE_PASSWORD', '');
define('CONFIG_DATABASE_NAME', 'ballmanager');

// general settings
define('CONFIG_INSTALL_DATE', '2008-09-25'); // the installation date (UTC) of this game instance in YYYY-MM-DD format (ISO 8601)
define('CONFIG_IS_LOCAL_INSTALLATION', false); // whether this is an installation on a local computer (no remote server)
define('CONFIG_USE_HTTPS', false); // whether to use secure connections over HTTPS (SSL/TLS)
define('CONFIG_USE_HTTPS_HSTS', false); // whether to guarantee and force HTTPS for a long period of time

// site information
define('CONFIG_SITE_NAME', 'OpenSoccer');
define('CONFIG_SITE_DOMAIN', 'www.opensoccer.org'); // must include <www> subdomain
define('CONFIG_SITE_EMAIL', 'info@opensoccer.org');
define('CONFIG_CONTACT_PAGE_HTML', '<p>Contact information goes here ...</p>');

// special users and roles
define('CONFIG_OFFICIAL_USER', '18a393b5e23e2b9b4da106b06d8235f3');
define('CONFIG_DEMO_USER', '1d0a7ce36ffa946eea1a52394fcdaebf');
define('CONFIG_PROTECTED_USERS', serialize(array( // IDs of users that are excluded from multiple account detection, HTML filtering in support area, bans etc.
    CONFIG_OFFICIAL_USER,
    CONFIG_DEMO_USER,
    'c4ca4238a0b923820dcc509a6f75849b'
)));

// email settings (PHPMailer may be used instead of mail() function)
define('CONFIG_EMAIL_PHP_MAILER', false); // whether to use PHPMailer (with SMTP) instead of the mail() function
define('CONFIG_EMAIL_HOST', ''); // SMTP server address
define('CONFIG_EMAIL_PORT', ''); // SMTP server port
define('CONFIG_EMAIL_USER', ''); // SMTP username
define('CONFIG_EMAIL_PASS', ''); // SMTP password
define('CONFIG_EMAIL_FROM', ''); // sender address
define('CONFIG_EMAIL_AUTH', true); // whether SMTP requires authentication
define('CONFIG_EMAIL_SECURE', ''); // for PHPMailer->SMTPSecure
define('CONFIG_EMAIL_CHARSET', 'UTF-8'); // mail charset

?>