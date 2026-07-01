<?php

// REGEX
define('PSEUDO_REGEX', "/^[a-z][a-z0-9_]{2,34}$/");
define('EMAIL_REGEX', "/^[\-\.+a-zA-Z0-9_]+@[\-\.+a-zA-Z0-9_]+\.[a-zA-Z0-9_]{2,}$/");
define('PASSWORD_REGEX', "/^(?=.*[A-Za-z])(?=.*\d).{8,}$/");
define('TOKEN_REGEX', "/^[0-9a-f]{64}$/");
define('DATE_REGEX', "/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/");

// MAX LENGTH
define('MAX_TITLE_LENGTH', 120);
define('MAX_DESCRIPTION_LENGTH', 500);
define('MAX_LOCATION_LENGTH', 50);
define('MAX_NAME_LENGTH', 35);
define('MAX_BIOGRAPHY_LENGTH', 500);

// DEFAULT PICTURES
define('DEFAULT_USER_PICTURE', '/public/Calendar.png');
define('DEFAULT_EVENT_PICTURE', '/public/Calendar.png');

// UPLOADS
define('MAX_FILE_UPLOAD', 1000 * 1024); // 1 000 Ko
define('PICTURE_USER_PATH', '/public/user');
define('PICTURE_EVENT_PATH', '/public/event');

// ALLOWED IMAGES FORMATS
define('IMAGE_FORMATS', [
    'image/png' => 'png',
    'image/jpeg' => 'jpg'
]);

// PARAMS
/**
 * Limite de sélection par défaut notament
 * pour les événements/utilisateurs
 * de préférence entre 4 et 12 et
 * multiple de 4
 */
define('DEFAULT_SELECT_LIMIT', 4);

// ERRORS
define('DB_ERROR_MESSAGE', 'Erreur base de données');

// SITE
define('SITE_NAME', 'GrrEvent');
define('SITE_ICON_PICTURE', '/public/Calendar.png');
define('SITE_FAVICONS_PATH', '/public/favicons');

// EMAIL
define('FROM_EMAIL_NAME', 'GrrEvent');
