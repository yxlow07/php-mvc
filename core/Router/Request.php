<?php

namespace core\Router;

class Request
{
    private array $routeParams = [];
    private array $validResourceExtensions = ['js', 'css', 'ico', 'gif', 'png', 'jpg'];

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? "GET");
    }

    public function isMethod(string $method): bool
    {
        return Request::method() == strtoupper($method);
    }

    public function data(bool $readPhpInput = false): array|string
    {
        $data = [];

        if (Request::method() == 'GET') {
            $data = $_GET;
        }

        if (Request::method() == 'POST') {
            $data = $_POST;
        }
        
        if ($readPhpInput) {
            $data = file_get_contents('php://input');
        }

        return $data;
    }

    public function path($remove = null): string
    {
        $path = preg_replace('#/+#', '/', rawurldecode($_SERVER['REQUEST_URI']));
        $position = strpos($path, '?');

        if (is_int($position)) {
            $path = substr($path, 0, $position);
        }

        if (!is_null($remove) || isset($_ENV['LOCALHOST_URL'])) {
            $path = str_replace($remove ?? $_ENV['LOCALHOST_URL'], '', $path);
        }

        return $path;
    }

    public function queryString($raw = false)
    {
        $rawQueryString = $_SERVER['QUERY_STRING'];
        $sanitised = htmlspecialchars(urlencode($rawQueryString), ENT_QUOTES, 'UTF-8');

        return $raw ? $rawQueryString : $sanitised;
    }

    public function getRequestHeaders(): false|array
    {
        $headers = array();

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        return $headers;
    }

    public function setRouteParams(array $routeParams): Request
    {
        $this->routeParams = $routeParams;
        return $this;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getExtension($path): array|string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function setHeader($extension): void
    {
        match ($extension) {
            'js' => header('Content-Type: application/javascript'),
            'css' => header('Content-Type: text/css'),
            'ico' => header('Content-Type: image/x-icon'),
            'gif' => header('Content-Type: image/gif'),
            'jpg' => header('Content-Type: image/jpeg'),
            'png' => header('Content-Type: image/png'),
            'json' => header('Content-Type: application/json'),
            default => null,
        };
    }

    public function isResource($extension): bool
    {
        return in_array($extension, $this->validResourceExtensions);
    }
}