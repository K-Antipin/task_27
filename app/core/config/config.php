<?php

namespace App\core;

define('ROOT', dirname(__DIR__, 3) . DIRECTORY_SEPARATOR);
define('APP', ROOT. 'app' . DIRECTORY_SEPARATOR);
define('CORE', APP. 'core' . DIRECTORY_SEPARATOR);
define('DATA', APP. 'data' . DIRECTORY_SEPARATOR);
define('MODEL', APP. 'models' . DIRECTORY_SEPARATOR);
define('VIEW', APP. 'views' . DIRECTORY_SEPARATOR);
define('CONTROLLER', APP. 'controllers' . DIRECTORY_SEPARATOR);
define('LAYOUT', VIEW. 'layout' . DIRECTORY_SEPARATOR);
define('IMG', ROOT. 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR);
define('UPLOAD_MAX_SIZE', 2000000); // 2mb
define('ALLOWED_TYPES', ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']);
define('UPLOAD_DIR', ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'files');