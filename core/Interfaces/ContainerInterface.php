<?php

namespace core\Interfaces;

interface ContainerInterface
{
    public static function bind($key, $resolver);

    public static function resolve($key, $params): mixed;
}