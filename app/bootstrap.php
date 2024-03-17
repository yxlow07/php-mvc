<?php

use core\App;
use core\Exceptions\BadBindingException;

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../core/helpers.php';

define('START', microtime(true));
define('BASEDIR', realpath(__DIR__ . '/../'));

$app = new App(BASEDIR);

try {
    App::bind('app', function () {
        return new App(BASEDIR);
    })::bind('ap', $app);
} catch (BadBindingException $e) {
    dd($e);
    // TODO: Display user friendly message
}