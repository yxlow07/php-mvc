<?php

namespace core;

use Random\RandomException;

class Session
{
    protected const FLASH_KEY = 'nfc_attendance_flash';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$message) {
            $message['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlashMessage($key, $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = ['remove' => false, 'value' => $message];
    }

    public function getFlashMessage($key)
    {
        return $_SESSION[self::FLASH_KEY][$key] ?? false;
    }

    public function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $want_null_return = false)
    {
        return $_SESSION[$key] ?? ($want_null_return ? null : false);
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) unset($flashMessages[$key]);
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function generateSessionID(): string
    {
        try {
            return bin2hex(random_bytes(32));
        } catch (RandomException $e) {
            return password_hash("sessionID0001", PASSWORD_DEFAULT);
        }
    }

    public function getSession(): array
    {
        return $_SESSION;
    }

    public function generateUUID($table): string
    {
        try {
            $uuid = bin2hex(random_bytes(16));
            $found = App::$app->database->findOne($table, ['uuid' => $uuid], fetchObject: false) == $uuid;
            while ($found) {
                $uuid = bin2hex(random_bytes(16));
                $found = App::$app->database->findOne($table, ['uuid' => $uuid], fetchObject: false) == $uuid;
            }
            return $uuid;
        } catch (RandomException $e) {
            return password_hash("uuid0001", PASSWORD_DEFAULT);
        }
    }
}