<?php

// REGEX
define('PSEUDO_REGEX', "/^[a-z][a-z0-9_]{2,34}$/");
define('EMAIL_REGEX', "/^[\-\.+a-zA-Z0-9_]+@[\-\.+a-zA-Z0-9_]+\.[a-zA-Z0-9_]{2,}$/");
define('PASSWORD_REGEX', "/^(?=.*[A-Za-z])(?=.*\d).{8,}$/");
define('TOKEN_REGEX', "/^[0-9a-f]{64}$/");

// DEFAULT PICTURES
define('DEFAULT_USER_PICTURE', '/public/default.webp');
define('DEFAULT_EVENT_PICTURE', '/public/default.webp');

// PARAMS
define('DEFAULT_SELECT_LIMIT', 4);

// ERRORS
define('DB_ERROR_MESSAGE', 'Erreur base de données');

// SITE
define('SITE_NAME', 'Nom Site');
define('SITE_ICON', '/public/default.webp');

// EMAIL
define('FROM_EMAIL_NAME', 'Nom Site');
