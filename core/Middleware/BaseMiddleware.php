<?php

namespace core\Middleware;

abstract class BaseMiddleware
{
    abstract public function execute();
    abstract public function next();
}