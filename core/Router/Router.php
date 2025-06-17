<?php

namespace core\Router;

use app\Middleware\MiddlewareMap;
use core\App;
use core\Exceptions\MiddlewareException;
use core\Exceptions\RouteNotFoundException;
use core\Exceptions\ViewNotFoundException;
use core\Middleware\MiddlewareHandler;
use core\View;

class Router
{
    public MiddlewareHandler $middlewareHandler;

    public function __construct(
        public Response $response,
        public Request $request,
        public RoutesCollector $routesCollector,
    )
    {
        $this->middlewareHandler = new MiddlewareHandler(MiddlewareMap::appMiddlewares);
    }

    /**
     * @throws ViewNotFoundException
     */
    public function dispatch(): void
    {
        $method = Request::method();
        $url = $this->request->path($_ENV['LOCALHOST_URL']);

        try {
            $this->middlewareHandler->handleMiddlewares();
        } catch (\Exception $e) {
            echo View::make()->renderView('error', ['error' => $e]);
            exit;
        }

        // Middlewares passed, test for url
        try {
            echo $this->resolve($url, $method);
        } catch (\Exception $e) {
            echo View::make()->renderView('error', ['error' => $e]);
        }
    }

    /**
     * @throws ViewNotFoundException
     * @throws RouteNotFoundException
     * @throws MiddlewareException
     */
    private function resolve(string $url, string $method)
    {
        // TODO: Instead of whole route becomes a key in an array, explode it and add it dynamically (Allows for grouping)
        // array_values(array_filter(explode('/', $url), 'strlen'));
        $routeExists = $this->routesCollector->routeExists($url, $method);

        if ($routeExists['status']) {
            return $routeExists['route']->dispatch();
        } else {
            throw new RouteNotFoundException($routeExists['error_message']);
        }
    }
}