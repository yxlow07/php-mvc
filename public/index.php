<?php

global $dir;
define('START', microtime(true));

use core\App;
use core\Router\Request;
use core\Router\Response;
use core\Router\RoutesCollector;

$config = require_once __DIR__.'/../config/config.php';

require_once __DIR__ . '/../config/functions.php';

/** @var RoutesCollector $routes */
$routes = require_once $dir."/routes/web.php";
$app = new App($routes, $config);

// Handle resource requests
$request = new Request();
$resourcePath = dirname(__DIR__) . $request->path();
$extension = $request->getExtension($resourcePath);

if ($request->isResource($extension)) {
    $request->setHeader($extension);
    Response::loadResource($resourcePath);
}

$app->run('web');