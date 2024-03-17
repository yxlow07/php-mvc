<?php

if (!function_exists('redirect')) {
    function redirect(string $location = '/', int $status = 302): void
    {
        header("Location: {$location}", true, $status);
    }
}

if (!function_exists('dd')) {
    function dd(mixed $values)
    {
        echo "<pre>";
        var_dump($values);
        echo "</pre>";
        die;
    }
}