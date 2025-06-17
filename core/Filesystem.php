<?php

namespace core;

class Filesystem
{
    public static function isFullPath(string $path): bool
    {
        return preg_match('/^[A-Z]:\\\\/i', $path) === 1;
    }

    public static function rootDir(): string
    {
        return App::$app->config['dir'];
    }

    public static function resources(string $fileLocation = ''): string
    {
        return self::rootDir() . '/resources/' . $fileLocation;
    }

    public static function processPaths(array $paths): array
    {
        $dir = self::rootDir();
        foreach ($paths as &$path) {
            if (!self::isFullPath($path)) {
                $path = $dir . '/' . $path;
            }
        }
        return $paths;
    }
}