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

    public function files(): array
    {
        return $_FILES;
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
            'woff2' => header('Content-Type: application/font-woff2'),
            'woff' => header('Content-Type: application/font-woff'),
            'ttf' => header('Content-Type: application/font-sfnt'),
            'svg' => header('Content-Type: image/svg+xml'),
            'eot' => header('Content-Type: application/vnd.ms-fontobject'),
            'otf' => header('Content-Type: font/otf'),
            'xml' => header('Content-Type: application/xml'),
            'pdf' => header('Content-Type: application/pdf'),
            'txt' => header('Content-Type: text/plain'),
            'csv' => header('Content-Type: text/csv'),
            'zip' => header('Content-Type: application/zip'),
            'mp4' => header('Content-Type: video/mp4'),
            'webm' => header('Content-Type: video/webm'),
            'mp3' => header('Content-Type: audio/mpeg'),
            'wav' => header('Content-Type: audio/wav'),
            'ogg' => header('Content-Type: audio/ogg'),
            'jsonld' => header('Content-Type: application/ld+json'),
            'rss' => header('Content-Type: application/rss+xml'),
            'atom' => header('Content-Type: application/atom+xml'),
            'wasm' => header('Content-Type: application/wasm'),
            'webp' => header('Content-Type: image/webp'),
            'avif' => header('Content-Type: image/avif'),
            'flac' => header('Content-Type: audio/flac'),
            'mkv' => header('Content-Type: video/x-matroska'),
            'mov' => header('Content-Type: video/quicktime'),
            'avi' => header('Content-Type: video/x-msvideo'),
            'm4a' => header('Content-Type: audio/mp4'),
            '3gp' => header('Content-Type: video/3gpp'),
            '3g2' => header('Content-Type: video/3gpp2'),
            default => null,
        };
    }

    public function isResource($extension): bool
    {
        return in_array($extension, $this->validResourceExtensions);
    }
}