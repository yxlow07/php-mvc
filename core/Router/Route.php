<?php

namespace core\Router;

use app\Middleware\MiddlewareMap;
use core\App;
use core\Exceptions\MiddlewareException;
use core\Exceptions\ViewNotFoundException;
use core\Middleware\BaseMiddleware;
use core\Session;
use core\View;

class Route
{
    public bool $isView = false;
    public bool $hasController;
    protected mixed $controller;

    public function __construct(
        protected string $path,
        protected string $method,
        protected mixed  $handler,
        protected array  $options = [],
        protected array  $middlewares = [],
    )
    {
        $this->hasController = is_array($this->handler);
        $this->isView = is_string($this->handler);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHandler(): mixed
    {
        return $this->handler;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function addMiddleware($middleware): static
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @throws ViewNotFoundException
     * @throws MiddlewareException
     */
    public function dispatch(): mixed
    {
        $this->options = array_merge($this->options, App::$app->request->getRouteParams());

        foreach ($this->middlewares as $middleware) {
            $middlewareName = $middleware;
            $middleware = MiddlewareMap::getMiddleware($middleware);

            if (!$middleware) {
                throw new MiddlewareException('Middleware not found');
            }

            /** @var BaseMiddleware $middleware */
            if (!(new $middleware)->execute()) {
//                throw new MiddlewareException("Middleware {$middlewareName} failed!");
                App::$app->session->setFlashMessage('error', "Middleware {$middlewareName} failed!");
                redirect('/');
            }
        }

        if ($this->isView) {
           return View::make()->renderView($this->handler, $this->options);
        }

        $this->controller = $this->handler;
        $this->controller[0] = new $this->handler[0];
        return call_user_func_array($this->controller, $this->options);
    }
}