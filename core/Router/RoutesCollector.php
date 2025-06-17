<?php

namespace core\Router;

use core\App;
use JetBrains\PhpStorm\ArrayShape;

class RoutesCollector
{
    protected static array $methods = ["GET", "POST", "PUT", "DELETE", "OPTIONS", "PATCH", "HEAD"];
    public static array $handling = [];

    public function GET(string $route, callable|object|string|array $fn, array $options = []): static
    {
        self::addHandling('GET', $route, $fn, $options);
        return $this;
    }

    public function POST(string $route, callable|object|string|array $fn, array $options = []): static
    {
        self::addHandling('POST', $route, $fn, $options);
        return $this;
    }

    public function GETPOST(string $route, callable|object|string|array $fn, array $options = []): static
    {
        self::addHandling('GET', $route, $fn, $options);
        self::addHandling('POST', $route, $fn, $options);
        return $this;
    }

    protected function addHandling(string $method, string $route, callable|object|string|array $fn, array $options): void
    {
        $route = self::trimRoute($route);
        RoutesCollector::$handling[$route][$method] = new Route($route, $method, $fn, $options);
    }

    #[ArrayShape(['status' => 'int', 'route' => 'core\Router\Route', 'error_message' => 'string|null'])]
    public function routeExists(string $route, string $method = 'GET'): array
    {
        $returns = ['status' => 0, 'route' => null ,'error_message' => null];
        $route = self::trimRoute($route);

        /** @var Route|null $findRoute */
        $findRoute = self::$handling[$route] ?? null;

        // Check if the route exists
        if (is_null($findRoute)) {
            // Handle regex
            $findRoute = $this->handleRegexRoutes($route, $method, $returns) ?? false;
            if ($findRoute === false) {
                $returns['error_message'] = "Route {$route} does not exist";
            }
            return $returns;
        }

        // Check if the method exists for the given route
        if (!isset($findRoute[$method])) {
            $returns['error_message'] = "Method {$method} for route {$route} does not exist";
            return $returns;
        }

        // Both route and method exist, set the status and function
        $returns['route'] = $findRoute[$method];
        $returns['status'] = 1;

        return $returns;
    }

    protected static function trimRoute(string $route): string
    {
        return '/' . trim($route, '/\\');
    }

    private function handleRegexRoutes(string $request_route, string $method, array &$returns): false|array
    {
        foreach (self::$handling as $route => $callbacks) {
            $route = self::trimRoute($route);
            $routeNames = [];

            if (!$route) {
                continue;
            }

            // Save routeNames to var, to pass it on in the future as we rewrite the route
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
                $routeNames = $matches[1];
            }

            // Converting to regex
            $routeRegex = '@^' . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . '$@';

            // Test and match
            if (preg_match_all($routeRegex, $request_route, $valuesMatched)) {
                $values = [];

                // Remove all unnecessary stuff and leave only the value
                for ($i = 1; $i < count($valuesMatched); $i++) {
                    $values[] = $valuesMatched[$i][0];
                }

                App::$app->request->setRouteParams(array_combine($routeNames, $values)); // Set route params

                if (isset($callbacks[$method])) {
                    $returns['status'] = 1;
                    $returns['route'] = $callbacks[$method];
                } else {
                    $returns['error_message'] = "Method {$method} for route {$route} does not exist";
                }

                return $returns;
            }
        }
        return false;
    }

    public function only(string $middleware): static
    {
        foreach (end(self::$handling) as $route) {
            /** @var Route $route */
            $route->addMiddleware($middleware);
        }
        return $this;
    }
}