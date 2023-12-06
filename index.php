<?php

session_start();

use core\Router;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $router = new Router($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    $router->handle();
} catch (Throwable $e) {
    die($e->getMessage());
}

