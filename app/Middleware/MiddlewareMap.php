<?php

namespace app\Middleware;

use app\Controllers\UserController;

class MiddlewareMap
{
    const appMiddlewares = [
        'setUser' => AuthMiddleware::class,
        'loginWithCookies' => AuthMiddleware::class,
        'navItems' => UserController::class,
    ];

    const middlewares = [
        'guest' => GuestMiddleware::class,
        'auth' => AuthMiddleware::class,
        'auth-user' => UserMiddleware::class,
        'auth-admin' => AdminMiddleware::class,
    ];

    public static function getMiddleware(mixed $middleware): false|string
    {
        return self::middlewares[$middleware] ?? false;
    }
}