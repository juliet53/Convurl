<?php

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// --------------------------------------------------------
// Trusted proxies pour Heroku
// --------------------------------------------------------
// 31 = HEADER_X_FORWARDED_ALL (For, Host, Proto, Port, Prefix)
// 2  = HEADER_X_FORWARDED_HOST
// On retire HEADER_X_FORWARDED_HOST pour ne pas écraser l'host
Request::setTrustedProxies(
    $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
    31 ^ 2
);

// --------------------------------------------------------
// Debug mode
// --------------------------------------------------------
if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
    Debug::enable();
}

// --------------------------------------------------------
// Autoloader runtime : Symfony 6+
// --------------------------------------------------------
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
