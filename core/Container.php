<?php

namespace core;

use core\Exceptions\BadBindingException;
use core\Interfaces\ContainerInterface;

class Container implements ContainerInterface
{
    protected static array $bindings = [];
    private static array $resolved = [];

    /**
     * @param string $key
     * @param mixed $resolver
     * @throws BadBindingException
     */
    public static function bind($key, $resolver)
    {
        if (isset(self::$bindings[$key])) {
            throw new BadBindingException('Cannot bind multiple instances to the same key');
        }

        self::$bindings[$key] = $resolver;
    }

    /**
     * @param string $key
     * @param array $params Passed on when resolver is function
     * @return mixed
     */
    public static function resolve($key, $params = []): mixed
    {
        if (!isset(self::$bindings[$key])) {
            return null;
        }

        $resolver = self::$bindings[$key];

        if (self::isResolved($key)) {
            return $resolver;
        }

        if (is_callable($resolver)) {
            $resolver = call_user_func_array($resolver, $params);
            self::$bindings[$key] = $resolver;
        }

        // Ensure only a single instance is created
        self::resolved($key);
        return $resolver;
    }

    private static function isResolved(string $key): bool
    {
        return in_array($key, self::$resolved);
    }

    private static function resolved(string $key)
    {
        self::$resolved[] = $key;
    }

    /**
     * @return array
     */
    public static function bindings(): array
    {
        return self::$bindings;
    }
}