<?php

namespace core;

class Cookies
{

    /**
     * @param string $key Cookie key
     * @param string $value Value of the cookie
     * @param int $time Default is one month, syntax is time() + $time, which means you need to give the extra time only
     * @param string $location Default is '/'
     * @return void
     */
    public static function setCookie(string $key, string $value = '', int $time = 2630000, string $location = '/'): void
    {
        setcookie($key, $value, time() + $time, $location);
    }
    
    public static function unsetCookie(string $key): void
    {
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            self::setCookie($key, time: -1);
        }
    }

    public static function unsetCookies(array $keys): void
    {
        foreach ($keys as $key) {
            self::unsetCookie($key);
        }
    }

    public static function getCookie($key): mixed
    {
        return $_COOKIE[$key] ?? null;
    }
}