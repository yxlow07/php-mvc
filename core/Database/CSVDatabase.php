<?php

namespace core\Database;

use core\App;
use core\Exceptions\FileNotFoundException;

class CSVDatabase
{
    /**
     * Either provide the full path of the file or just the filename (Must be located in \resources\data\ folder)
     * @param $filename
     * @return array
     */
    public static function returnAllData($filename): array
    {
        $path = @file_exists($filename) ? $filename : App::$app->config['resources_path'] . '/data/' . $filename;
        $handler = fopen($path, 'r');
        $data = [];

        if ($handler !== false) {
            while ($row = fgetcsv($handler)) {
                $data[] = $row;
            }
            fclose($handler);
        }

        return $data;
    }

    public static function saveToDatabase($filename, $data, $mode = 'a'): bool
    {
        $path = @file_exists($filename) ? $filename : App::$app->config['resources_path'].'/data/'.$filename;
        $handler = fopen($path, $mode);
        if (!$handler) {
            throw new FileNotFoundException();
        } else {
            foreach ($data as $row) {
                fputcsv($handler, [$row]);
            }
            fclose($handler);
            return true;
        }
    }
}