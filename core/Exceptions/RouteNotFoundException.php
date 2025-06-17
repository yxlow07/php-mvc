<?php

namespace core\Exceptions;


use Throwable;

class RouteNotFoundException extends BaseException
{
    public function __construct(string $message = "Route not found", int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        parent::setCode($code);
    }
}