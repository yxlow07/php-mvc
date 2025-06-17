<?php

namespace core\Router;

use core\App;
use core\Exceptions\ViewNotFoundException;

class Response
{
    public static function loadResource(string $resourcePath): void
    {
        if (file_exists($resourcePath)) {
            readfile($resourcePath);
        } else {
            header('HTTP/1.1 404 Not Found');
            dd($resourcePath);
        }
        http_response_code(200);
        die;
    }

    public function setStatusCode(int $code = 200): void
    {
        http_response_code($code);
    }

    public function sendJson(mixed $data, bool $exit = false)
    {
        App::$app->request->setHeader('json');
        echo json_encode($data);
        if ($exit) {
            exit(200);
        }
    }
}