<?php

namespace core\Middleware;

use core\Exceptions\MiddlewareException;

class MiddlewareHandler
{
    /**
     * @throws MiddlewareException
     */
    public function __construct(
        protected array $middlewares = []
    )
    {}

    /**
     * @throws MiddlewareException
     */
    public function handleMiddlewares(): void
    {
        foreach ($this->middlewares as $fn => $middleware) {
            $middlewareName = "$fn @ $middleware";

            if ((new $middleware)->{$fn}() === false) {
                throw new MiddlewareException("$middlewareName has failed");
            }
        }
    }
}