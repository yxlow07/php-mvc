<?php

use app\Middleware\MiddlewareMap;
use Dotenv\Dotenv;

$dir = dirname(__DIR__) . '\\';

require_once $dir.'/vendor/autoload.php';

$dotenv = Dotenv::createMutable($dir); // If any issue arises, use back createImmutable, refer discord /errors
$dotenv->load();

return [
    'dir' => $dir,
    'dotenv' => $dotenv,
    'view_path' => $dir . '/views/',
    'cache_path' => $dir . '/views/cache/',
    'layout_path' => $dir . '/views/layouts/',
    'resources_path' => $dir . '/resources/',
    'web_url' => $_ENV['LOCALHOST_URL'],
    'db' => [
        'DSN' => $_ENV['DSN'],
        'USERNAME' => $_ENV['DBUSERNAME'],
        'PASSWORD' => $_ENV['PASSWORD'],
    ],
    'middlewares' => MiddlewareMap::middlewares,
];