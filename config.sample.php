<?php

//Setting timezone
date_default_timezone_set('America/Sao_Paulo');

//Setting directory separator
define('DS', DIRECTORY_SEPARATOR);

define('MODE', 'production');
define('PATH_ROOT', dirname(__FILE__) . DS);
define('PATH_PUBLIC', PATH_ROOT . 'public' . DS);
define('PATH_CACHE', PATH_ROOT . 'cache' . DS);
define('PATH_LOGS', PATH_ROOT . 'logs' . DS);
define('PATH_TMP', PATH_ROOT . 'tmp' . DS);
define('PATH_CRON', PATH_ROOT . 'cron' . DS);
define('PATH_UPLOADS', PATH_PUBLIC . 'uploads' . DS);
define('PATH_MIGRATIONS', PATH_ROOT . 'migrations' . DS);
define('PATH_ROUTES', PATH_ROOT . 'routes' . DS);

define('SESSION_LIFETIME', (60 * 30));
define('SESSION_NAME', 'webtrack');
define('GZIP',false);

require PATH_ROOT . 'vendor/autoload.php';
