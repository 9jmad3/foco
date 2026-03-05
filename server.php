<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$path = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($path) && is_file($path)) {
    return false; // sirve el fichero estático tal cual
}

require __DIR__ . '/public/index.php';
